<?php
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_check.php';

$action = isset($_REQUEST['action']) ? trim((string)$_REQUEST['action']) : '';

try {
    $conn = getDbConnection();
    ensureServeLogTable($conn);

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
                CASE WHEN sl.ProcessID IS NOT NULL THEN 1 ELSE 0 END AS ServeStatus
            FROM orderprocessdetailfront o
            LEFT JOIN kds_serve_log sl
                ON sl.ProductLevelID = o.ProductLevelID
               AND sl.ProcessID      = o.ProcessID
               AND sl.SubProcessID   = o.SubProcessID
               AND sl.PrinterID      = o.PrinterID
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
            INSERT INTO kds_serve_log
                (ProductLevelID, ProcessID, SubProcessID, PrinterID, ServedDateTime, ServedStaffID)
            VALUES (?, ?, ?, ?, NOW(), ?)
            ON DUPLICATE KEY UPDATE ServedDateTime = NOW(), ServedStaffID = ?
        ");
        $stmt->bind_param('iiiiii', $plid, $pid, $spid, $prid, $staff, $staff);
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
            DELETE FROM kds_serve_log
            WHERE ProductLevelID = ? AND ProcessID = ? AND SubProcessID = ? AND PrinterID = ?
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
            INSERT INTO kds_serve_log
                (ProductLevelID, ProcessID, SubProcessID, PrinterID, ServedDateTime, ServedStaffID)
            SELECT ProductLevelID, ProcessID, SubProcessID, PrinterID, NOW(), ?
            FROM orderprocessdetailfront
            WHERE TableID = ? AND ProcessStatus = 1 AND DATE(FinishDateTime) = CURDATE()
            ON DUPLICATE KEY UPDATE ServedDateTime = NOW(), ServedStaffID = ?
        ");
        $stmt->bind_param('iii', $staff, $tableId, $staff);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        $conn->close();
        jsonResponse(['success' => true]);

    } else {
        $conn->close();
        jsonResponse(['success' => false, 'message' => 'unknown action'], 400);
    }

} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}

function ensureServeLogTable(mysqli $conn): void
{
    $conn->query("
        CREATE TABLE IF NOT EXISTS kds_serve_log (
            ProductLevelID  INT         NOT NULL DEFAULT 0,
            ProcessID       INT         NOT NULL,
            SubProcessID    INT         NOT NULL DEFAULT 0,
            PrinterID       INT         NOT NULL DEFAULT 0,
            ServedDateTime  DATETIME    NOT NULL,
            ServedStaffID   INT         NOT NULL DEFAULT 0,
            PRIMARY KEY (ProductLevelID, ProcessID, SubProcessID, PrinterID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
}
