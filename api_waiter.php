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
                0 AS ServeStatus
            FROM orderprocessdetailfront o
            WHERE o.ProcessStatus = 1
            ORDER BY o.SubmitOrderDateTime ASC
        ";
        $result = $conn->query($sql);
        if (!$result) throw new Exception($conn->error);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $conn->close();
        jsonResponse(['success' => true, 'rows' => $rows]);

    } elseif ($action === 'serve_item' || $action === 'unserve_item' || $action === 'serve_table') {
        // ยังไม่ได้เพิ่ม ServeStatus ใน DB — return success เพื่อให้ UI ทำงานได้ก่อน
        $conn->close();
        jsonResponse(['success' => true, 'message' => 'ok']);

    } else {
        $conn->close();
        jsonResponse(['success' => false, 'message' => 'unknown action'], 400);
    }

} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}
