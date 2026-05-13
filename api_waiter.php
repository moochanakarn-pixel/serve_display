<?php
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_check.php';

$action = isset($_REQUEST['action']) ? trim((string)$_REQUEST['action']) : '';

try {
    $conn = getDbConnection();

    if ($action === 'list_pending') {
        $sql = "
            SELECT
                o.ProductLevelID,
                o.ProcessID,
                o.SubProcessID,
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
                TRIM(COALESCE(tr.QueueName, '')) AS QueueName
            FROM orderprocessdetailfront o
            LEFT JOIN staffs s ON s.StaffID = o.ServingStaffID AND o.ServingStaffID > 0
            LEFT JOIN ordertransactionfront tr ON tr.TransactionID = o.TransactionID AND tr.ComputerID = o.ComputerID
            WHERE o.ProcessStatus = 1
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
            SELECT TableID, ProcessID,
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

        $conn->close();
        jsonResponse(['success' => true, 'rows' => $rows, 'cooking' => $cooking, 'cooking_rows' => $cookingRows]);

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
              AND ProcessStatus = 1
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
              AND ProcessStatus = 1
              AND COALESCE(FinishDateTime, SubmitOrderDateTime) >= NOW() - INTERVAL 24 HOUR
        ");
        $stmt->bind_param('iiiii', $plid, $pid, $spid, $prid, $tbl);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        $conn->close();
        jsonResponse(['success' => true]);

    } elseif ($action === 'serve_table') {
        $tableId = (int)($_POST['TableID'] ?? 0);
        $staff   = (int)($_POST['StaffID'] ?? 0);
        if ($staff <= 0) {
            $conn->close();
            jsonResponse(['success' => false, 'message' => 'ต้องระบุ StaffID'], 400);
        }

        $stmt = $conn->prepare("
            UPDATE orderprocessdetailfront
            SET ServingStaffID = ?, ServingDateTime = NOW()
            WHERE TableID = ? AND ProcessStatus = 1
              AND COALESCE(FinishDateTime, SubmitOrderDateTime) >= NOW() - INTERVAL 24 HOUR
              AND ServingDateTime IS NULL
        ");
        $stmt->bind_param('ii', $staff, $tableId);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        $conn->close();
        jsonResponse(['success' => true]);

    } elseif ($action === 'unserve_table') {
        $tableId = (int)($_POST['TableID'] ?? 0);
        $staff   = (int)($_POST['StaffID'] ?? 0);
        if ($staff <= 0) {
            $conn->close();
            jsonResponse(['success' => false, 'message' => 'ต้องระบุ StaffID'], 400);
        }

        $stmt = $conn->prepare("
            UPDATE orderprocessdetailfront
            SET ServingStaffID = 0, ServingDateTime = NULL
            WHERE TableID = ? AND ProcessStatus = 1
              AND COALESCE(FinishDateTime, SubmitOrderDateTime) >= NOW() - INTERVAL 24 HOUR
              AND ServingDateTime IS NOT NULL
        ");
        $stmt->bind_param('i', $tableId);
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
