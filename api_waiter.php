<?php
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_check.php';

$action = isset($_REQUEST['action']) ? trim((string)$_REQUEST['action']) : '';

/* ── Settings actions ทำงานได้แม้ DB ยังไม่ connect ── */
if ($action === 'get_settings') {
    $s  = getLocalSettings();
    $db = [];
    try { $db = getDbConfig(); } catch (Exception $e) {}
    jsonResponse([
        'success'              => true,
        'db_host'              => $db['host']              ?? '',
        'db_port'              => $db['port']              ?? 3306,
        'db_name'              => $db['name']              ?? '',
        'db_user'              => $db['user']              ?? '',
        'has_pass'             => !empty($db['pass']),
        'current_computer_id'  => CURRENT_COMPUTER_ID,
        'current_computer_name'=> CURRENT_COMPUTER_NAME,
        'sound_enabled'        => (int)localSetting($s, 'sound_enabled', 0),
        'version'              => 'v1.1.0',
    ]);
}

if ($action === 'save_settings') {
    $file    = getSettingsLocalFilePath();
    $existing = getLocalSettings();
    if (!is_array($existing)) $existing = [];

    $updated = $existing;
    $strFields = ['db_host', 'db_name', 'db_user', 'current_computer_name'];
    $intFields = ['db_port', 'current_computer_id', 'sound_enabled'];
    foreach ($strFields as $k) {
        if (isset($_POST[$k])) $updated[$k] = trim((string)$_POST[$k]);
    }
    foreach ($intFields as $k) {
        if (isset($_POST[$k])) $updated[$k] = (int)$_POST[$k];
    }
    // อัปเดต password เฉพาะเมื่อไม่ว่าง
    if (!empty($_POST['db_pass'])) {
        $updated['db_pass'] = (string)$_POST['db_pass'];
    }

    $php = "<?php\nreturn " . var_export($updated, true) . ";\n";
    if (@file_put_contents($file, $php) === false) {
        jsonResponse(['success' => false, 'message' => 'ไม่สามารถเขียน settings.local.php ได้ — ตรวจสอบ permission'], 500);
    }
    jsonResponse(['success' => true]);
}

try {
    $conn = getDbConnection();

    if ($action === 'list_pending') {
        $sql = "
            SELECT
                o.ProductLevelID,
                o.ProcessID,
                o.SubProcessID,
                o.TransactionID,
                o.PrinterID,
                o.TableID,
                COALESCE(o.DisplayTableName, o.TableID) AS DisplayTableName,
                o.ProductName,
                o.ProductAmount,
                o.ProductSetType,
                o.ParentProcessID,
                o.SubmitOrderDateTime,
                o.FinishDateTime,
                o.ServingStaffID,
                o.ServingDateTime,
                CASE WHEN o.ServingDateTime IS NOT NULL THEN 1 ELSE 0 END AS ServeStatus,
                TRIM(COALESCE(s.StaffFirstName, '')) AS ServingStaffName,
                TRIM(COALESCE(tr.QueueName, '')) AS QueueName,
                COALESCE(o.SaleModeID, 0) AS SaleModeID,
                TRIM(COALESCE(od.Comment, '')) AS ItemComment,
                COALESCE(od.Price, 0) AS ItemPrice
            FROM orderprocessdetailfront o
            LEFT JOIN staffs s ON s.StaffID = o.ServingStaffID AND o.ServingStaffID > 0
            LEFT JOIN ordertransactionfront tr ON tr.TransactionID = o.TransactionID AND tr.ComputerID = o.ComputerID
            LEFT JOIN orderdetailfront od ON od.ProcessID = o.ProcessID AND od.ProcessID > 0
            WHERE o.ProcessStatus IN (1, 4)
              AND COALESCE(o.FinishDateTime, o.SubmitOrderDateTime) >= NOW() - INTERVAL 24 HOUR
            ORDER BY o.FinishDateTime ASC
        ";
        $result = $conn->query($sql);
        if (!$result) throw new Exception($conn->error);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        // ดึงรายละเอียดรายการที่ยังอยู่ในครัว (ProcessStatus 0=รอทำ, 2=กำลังทำ)
        $cookSql = "
            SELECT TableID, ProcessID, PrinterID,
                   COALESCE(DisplayTableName, TableID) AS DisplayTableName,
                   ProductName, ProductAmount, ProductSetType, ParentProcessID,
                   ProcessStatus, SubmitOrderDateTime
            FROM orderprocessdetailfront
            WHERE ProcessStatus IN (0, 2)
              AND SubmitOrderDateTime >= NOW() - INTERVAL 24 HOUR
            ORDER BY TableID ASC, SubmitOrderDateTime ASC
        ";
        $cookResult = $conn->query($cookSql);
        if (!$cookResult) throw new Exception($conn->error);
        $cookingRows = [];
        $cooking     = [];
        while ($row = $cookResult->fetch_assoc()) {
            $cookingRows[] = $row;
            // นับเฉพาะ set header และ standalone ไม่นับ sub-item (ProductSetType < 0)
            if ((int)$row['ProductSetType'] >= 0) {
                $tid = (int)$row['TableID'];
                $cooking[$tid] = ($cooking[$tid] ?? 0) + 1;
            }
        }

        // ดึง PrinterID ที่ station นี้ดูแล (จาก checkeraccessprinter)
        $allowedPrinterIds = [];
        if (CURRENT_COMPUTER_ID > 0) {
            $cid   = CURRENT_COMPUTER_ID;
            $pStmt = $conn->prepare("SELECT PrinterID FROM checkeraccessprinter WHERE ComputerID = ?");
            $pStmt->bind_param('i', $cid);
            $pStmt->execute();
            $pStmt->bind_result($pid);
            while ($pStmt->fetch()) {
                $allowedPrinterIds[] = (int)$pid;
            }
            $pStmt->close();
        }

        // ดึงชื่อ SaleMode
        $saleModes = [];
        try {
            $smResult = $conn->query("SELECT SaleModeID, SaleModeName FROM salemode WHERE Deleted = 0");
            if ($smResult) {
                while ($sm = $smResult->fetch_assoc()) {
                    $saleModes[(int)$sm['SaleModeID']] = $sm['SaleModeName'];
                }
            }
        } catch (Exception $e) {}

        $conn->close();
        jsonResponse(['success' => true, 'rows' => $rows, 'cooking' => $cooking, 'cooking_rows' => $cookingRows, 'allowed_printer_ids' => $allowedPrinterIds, 'sale_modes' => $saleModes]);

    } elseif ($action === 'serve_item') {
        $plid  = (int)($_POST['ProductLevelID'] ?? 0);
        $pid   = (int)($_POST['ProcessID']      ?? 0);
        $spid  = (int)($_POST['SubProcessID']   ?? 0);
        $prid  = (int)($_POST['PrinterID']      ?? 0);
        $staff = (int)($_POST['StaffID']        ?? 0);
        $tbl   = (int)($_POST['TableID']        ?? 0);
        if ($staff <= 0) {
            $conn->close();
            jsonResponse(['success' => false, 'message' => 'ต้องระบุ StaffID'], 400);
        }

        $stmt = $conn->prepare("
            UPDATE orderprocessdetailfront
            SET ServingStaffID = ?, ServingDateTime = NOW()
            WHERE ProductLevelID = ? AND ProcessID = ? AND SubProcessID = ? AND PrinterID = ? AND TableID = ?
              AND ProcessStatus IN (1, 4)
              AND COALESCE(FinishDateTime, SubmitOrderDateTime) >= NOW() - INTERVAL 24 HOUR
              AND ServingDateTime IS NULL
        ");
        $stmt->bind_param('iiiiii', $staff, $plid, $pid, $spid, $prid, $tbl);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        $conn->close();
        jsonResponse(['success' => true]);

    } elseif ($action === 'unserve_item') {
        $plid  = (int)($_POST['ProductLevelID'] ?? 0);
        $pid   = (int)($_POST['ProcessID']      ?? 0);
        $spid  = (int)($_POST['SubProcessID']   ?? 0);
        $prid  = (int)($_POST['PrinterID']      ?? 0);
        $tbl   = (int)($_POST['TableID']        ?? 0);
        $staff = (int)($_POST['StaffID']        ?? 0);
        if ($staff <= 0) {
            $conn->close();
            jsonResponse(['success' => false, 'message' => 'ต้องระบุ StaffID'], 400);
        }

        $stmt = $conn->prepare("
            UPDATE orderprocessdetailfront
            SET ServingStaffID = 0, ServingDateTime = NULL
            WHERE ProductLevelID = ? AND ProcessID = ? AND SubProcessID = ? AND PrinterID = ? AND TableID = ?
              AND ProcessStatus IN (1, 4)
              AND COALESCE(FinishDateTime, SubmitOrderDateTime) >= NOW() - INTERVAL 24 HOUR
        ");
        $stmt->bind_param('iiiii', $plid, $pid, $spid, $prid, $tbl);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        $conn->close();
        jsonResponse(['success' => true]);

    } elseif ($action === 'serve_table') {
        $tableId = (int)($_POST['TableID'] ?? 0);
        $txId    = (int)($_POST['TransactionID'] ?? 0);
        $staff   = (int)($_POST['StaffID'] ?? 0);
        if ($staff <= 0) {
            $conn->close();
            jsonResponse(['success' => false, 'message' => 'ต้องระบุ StaffID'], 400);
        }

        if ($txId > 0) {
            // delivery order — filter ด้วย TransactionID ไม่ให้ serve ข้ามบิล
            $stmt = $conn->prepare("
                UPDATE orderprocessdetailfront
                SET ServingStaffID = ?, ServingDateTime = NOW()
                WHERE TableID = ? AND TransactionID = ? AND ProcessStatus IN (1, 4)
                  AND COALESCE(FinishDateTime, SubmitOrderDateTime) >= NOW() - INTERVAL 24 HOUR
                  AND ServingDateTime IS NULL
            ");
            $stmt->bind_param('iii', $staff, $tableId, $txId);
        } else {
            $stmt = $conn->prepare("
                UPDATE orderprocessdetailfront
                SET ServingStaffID = ?, ServingDateTime = NOW()
                WHERE TableID = ? AND ProcessStatus IN (1, 4)
                  AND COALESCE(FinishDateTime, SubmitOrderDateTime) >= NOW() - INTERVAL 24 HOUR
                  AND ServingDateTime IS NULL
            ");
            $stmt->bind_param('ii', $staff, $tableId);
        }
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        $conn->close();
        jsonResponse(['success' => true]);

    } elseif ($action === 'unserve_table') {
        $tableId = (int)($_POST['TableID'] ?? 0);
        $txId    = (int)($_POST['TransactionID'] ?? 0);
        $staff   = (int)($_POST['StaffID'] ?? 0);
        if ($staff <= 0) {
            $conn->close();
            jsonResponse(['success' => false, 'message' => 'ต้องระบุ StaffID'], 400);
        }

        if ($txId > 0) {
            $stmt = $conn->prepare("
                UPDATE orderprocessdetailfront
                SET ServingStaffID = 0, ServingDateTime = NULL
                WHERE TableID = ? AND TransactionID = ? AND ProcessStatus IN (1, 4)
                  AND COALESCE(FinishDateTime, SubmitOrderDateTime) >= NOW() - INTERVAL 24 HOUR
                  AND ServingDateTime IS NOT NULL
            ");
            $stmt->bind_param('ii', $tableId, $txId);
        } else {
            $stmt = $conn->prepare("
                UPDATE orderprocessdetailfront
                SET ServingStaffID = 0, ServingDateTime = NULL
                WHERE TableID = ? AND ProcessStatus IN (1, 4)
                  AND COALESCE(FinishDateTime, SubmitOrderDateTime) >= NOW() - INTERVAL 24 HOUR
                  AND ServingDateTime IS NOT NULL
            ");
            $stmt->bind_param('i', $tableId);
        }
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        $conn->close();
        jsonResponse(['success' => true]);

    } elseif ($action === 'lookup_staff') {
        $code = trim((string)($_POST['staff_code'] ?? $_GET['staff_code'] ?? ''));
        if ($code === '') {
            $conn->close();
            jsonResponse(['success' => false, 'message' => 'กรุณากรอกรหัสพนักงาน'], 400);
        }
        $stmt = $conn->prepare("
            SELECT StaffID, StaffCode, StaffFirstName, StaffLastName
            FROM staffs
            WHERE StaffCode = ? AND Deleted = 0 AND Activated = 1
            LIMIT 1
        ");
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $stmt->bind_result($staffId, $staffCode, $firstName, $lastName);
        $found = $stmt->fetch();
        $stmt->close();
        $conn->close();
        if (!$found) {
            jsonResponse(['success' => false, 'message' => 'ไม่พบรหัสพนักงาน'], 404);
        }
        jsonResponse([
            'success'    => true,
            'staff_id'   => (int)$staffId,
            'staff_code' => $staffCode,
            'staff_name' => trim($firstName),
        ]);

    } else {
        $conn->close();
        jsonResponse(['success' => false, 'message' => 'unknown action'], 400);
    }

} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}
