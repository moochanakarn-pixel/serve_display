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
                CASE WHEN o.ServingDateTime IS NOT NULL THEN 1 ELSE 0 END AS ServeStatus
            FROM orderprocessdetailfront o
            WHERE o.ProcessStatus = 1
              AND DATE(o.FinishDateTime) = CURDATE()
            ORDER BY o.FinishDateTime ASC
        ";
        $result = $conn->query($sql);
        if (!$result) throw new Exception($conn->error);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $conn->close();
        jsonResponse(['success' => true, 'rows' => $rows]);

    } elseif ($action === 'serve_item') {
        $plid  = (int)($_POST['ProductLevelID'] ?? 0);
        $pid   = (int)($_POST['ProcessID']      ?? 0);
        $spid  = (int)($_POST['SubProcessID']   ?? 0);
        $prid  = (int)($_POST['PrinterID']       ?? 0);
        $staff = (int)($_POST['StaffID']         ?? 0);

        $stmt = $conn->prepare("
            UPDATE orderprocessdetailfront
            SET ServingStaffID = ?, ServingDateTime = NOW()
            WHERE ProductLevelID = ? AND ProcessID = ? AND SubProcessID = ? AND PrinterID = ?
              AND ProcessStatus = 1
        ");
        $stmt->bind_param('iiiii', $staff, $plid, $pid, $spid, $prid);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        $conn->close();
        jsonResponse(['success' => true]);

    } elseif ($action === 'unserve_item') {
        $plid = (int)($_POST['ProductLevelID'] ?? 0);
        $pid  = (int)($_POST['ProcessID']      ?? 0);
        $spid = (int)($_POST['SubProcessID']   ?? 0);
        $prid = (int)($_POST['PrinterID']       ?? 0);

        $stmt = $conn->prepare("
            UPDATE orderprocessdetailfront
            SET ServingStaffID = 0, ServingDateTime = NULL
            WHERE ProductLevelID = ? AND ProcessID = ? AND SubProcessID = ? AND PrinterID = ?
              AND ProcessStatus = 1
        ");
        $stmt->bind_param('iiii', $plid, $pid, $spid, $prid);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        $conn->close();
        jsonResponse(['success' => true]);

    } elseif ($action === 'serve_table') {
        $tableId = (int)($_POST['TableID'] ?? 0);
        $staff   = (int)($_POST['StaffID'] ?? 0);

        $stmt = $conn->prepare("
            UPDATE orderprocessdetailfront
            SET ServingStaffID = ?, ServingDateTime = NOW()
            WHERE TableID = ? AND ProcessStatus = 1 AND DATE(FinishDateTime) = CURDATE()
              AND ServingStaffID = 0
        ");
        $stmt->bind_param('ii', $staff, $tableId);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        $conn->close();
        jsonResponse(['success' => true]);

    } elseif ($action === 'lookup_staff') {
        $code = trim((string)($_GET['staff_code'] ?? ''));
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
            'staff_name' => trim($firstName . ' ' . $lastName),
        ]);

    } else {
        $conn->close();
        jsonResponse(['success' => false, 'message' => 'unknown action'], 400);
    }

} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}
