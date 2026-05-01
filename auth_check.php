<?php
// ใส่บรรทัดแรกของทุกหน้าที่ต้องการป้องกัน:
// require_once __DIR__ . '/auth_check.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// bypass login — set default staff
$_SESSION['kds_staff_id']   = $_SESSION['kds_staff_id']   ?? 0;
$_SESSION['kds_staff_name'] = $_SESSION['kds_staff_name'] ?? 'Guest';
$_SESSION['kds_staff_code'] = $_SESSION['kds_staff_code'] ?? '';
