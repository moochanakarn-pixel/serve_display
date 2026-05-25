<?php
// =============================
// Runtime local settings
// =============================
function loadLocalSettings()
{
    $file = __DIR__ . DIRECTORY_SEPARATOR . 'settings.local.php';
    if (!is_file($file)) {
        return array();
    }

    $settings = require $file;
    return is_array($settings) ? $settings : array();
}

function localSetting(array $settings, $key, $default)
{
    if (!array_key_exists($key, $settings)) {
        return $default;
    }
    return $settings[$key];
}

$__localSettings = loadLocalSettings();

// =============================
// App configuration
// =============================
define('APP_TITLE', 'Serve Display');
define('APP_TIMEZONE', 'Asia/Bangkok');
define('APP_REFRESH_MS', 15000);
define('FINISHED_REFRESH_EVERY', 3);
define('DEFAULT_FINISH_STAFF_ID', (int)localSetting($__localSettings, 'finish_staff_id', 3));
define('CURRENT_COMPUTER_ID', (int)localSetting($__localSettings, 'current_computer_id', 2));
define('CURRENT_COMPUTER_NAME', trim((string)localSetting($__localSettings, 'current_computer_name', '')));
define('ALLOW_CHECKOUT_PRINTER_SELECTION', false);
define('CHECKOUT_PRINT_PROVIDER', 'none'); // ปิดระบบพิมพ์ Checkout
define('DEFAULT_CHECKOUT_PRINTER_NAME', '');
define('ENABLE_BARCODE_CHECKOUT', true);
define('BARCODE_AUTO_SUBMIT_DEFAULT', true);
define('BARCODE_MIN_LENGTH', 1);
define('BARCODE_DIGITS_DISPLAY', 6);

define('BARCODE_CAMERA_ENABLED_DEFAULT', (bool)localSetting($__localSettings, 'barcode_camera_enabled', true));
define('KDS_TWO_STEP_CHECKOUT_DEFAULT', (bool)localSetting($__localSettings, 'kds_two_step_checkout', false));
define('ALERT_THRESHOLD_YELLOW_DEFAULT', (int)localSetting($__localSettings, 'threshold_yellow', 10));
define('ALERT_THRESHOLD_RED_DEFAULT', (int)localSetting($__localSettings, 'threshold_red', 20));
define('SOUND_ALERT_ENABLED_DEFAULT', (bool)localSetting($__localSettings, 'sound_enabled', false));
define('RECENT_FINISHED_LIMIT', 0); // 0 = ไม่จำกัด
define('FINISHED_PREVIEW_LIMIT', 3);
define('ENABLE_OUT_OF_STOCK_CONTROL', true);
define('OUT_OF_STOCK_SHOW_LIMIT', 300);

define('PROCESS_STATUS_ACTIVE', 0);
define('PROCESS_STATUS_IN_PROCESS', 2);
define('PROCESS_STATUS_FINISHED', 1);
define('PROCESS_STATUS_VOIDED', 98);
define('PROCESS_STATUS_RESOLVED', 4);

// Performance filter
define('ACTIVE_ROWS_TODAY_ONLY', false);
define('FINISHED_ROWS_TODAY_ONLY', false);

// =============================
// Secret / DB configuration
// =============================
define('KDS_ENV_DB_HOST', 'KDS_DB_HOST');
define('KDS_ENV_DB_PORT', 'KDS_DB_PORT');
define('KDS_ENV_DB_NAME', 'KDS_DB_NAME');
define('KDS_ENV_DB_USER', 'KDS_DB_USER');
define('KDS_ENV_DB_PASS', 'KDS_DB_PASS');

// Inline DB config (รวมจาก kds_db_secret.php แล้ว)

if (function_exists('date_default_timezone_set')) {
    @date_default_timezone_set(APP_TIMEZONE);
}

function getLocalSettings()
{
    static $settings = null;
    if ($settings === null) {
        $settings = loadLocalSettings();
    }
    return $settings;
}

function getMachineDisplayName()
{
    if (defined('CURRENT_COMPUTER_NAME') && trim((string)CURRENT_COMPUTER_NAME) !== '') {
        return trim((string)CURRENT_COMPUTER_NAME);
    }
    if (defined('CURRENT_COMPUTER_ID') && (int)CURRENT_COMPUTER_ID > 0) {
        return 'Computer #' . (int)CURRENT_COMPUTER_ID;
    }
    return '';
}

function getSettingsLocalFilePath()
{
    return __DIR__ . DIRECTORY_SEPARATOR . 'settings.local.php';
}

function getDbConfig()
{
    static $dbConfig = null;

    if ($dbConfig !== null) {
        return $dbConfig;
    }

    $envConfig = array(
        'host' => getenv(KDS_ENV_DB_HOST) ?: '',
        'port' => getenv(KDS_ENV_DB_PORT) ?: '',
        'name' => getenv(KDS_ENV_DB_NAME) ?: '',
        'user' => getenv(KDS_ENV_DB_USER) ?: '',
        'pass' => getenv(KDS_ENV_DB_PASS) ?: '',
    );

    if ($envConfig['host'] !== '' && $envConfig['name'] !== '' && $envConfig['user'] !== '') {
        $dbConfig = normalizeDbConfig($envConfig);
        return $dbConfig;
    }

    $local = getLocalSettings();
    $inlineConfig = array(
        'host' => localSetting($local, 'db_host', '127.0.0.1'),
        'port' => localSetting($local, 'db_port', 3307),
        'name' => localSetting($local, 'db_name', 'ini76'),
        'user' => localSetting($local, 'db_user', 'root'),
        'pass' => localSetting($local, 'db_pass', ''),
    );

    $dbConfig = normalizeDbConfig($inlineConfig);
    return $dbConfig;
}

function normalizeDbConfig($config)
{
    $host = isset($config['host']) ? trim((string)$config['host']) : '';
    $port = isset($config['port']) ? (int)$config['port'] : 3306;
    $name = isset($config['name']) ? trim((string)$config['name']) : '';
    $user = isset($config['user']) ? trim((string)$config['user']) : '';
    $pass = isset($config['pass']) ? (string)$config['pass'] : '';

    if ($host === '' || $name === '' || $user === '') {
        throw new Exception('ค่าฐานข้อมูลไม่ครบ: host / name / user จำเป็นต้องมี');
    }

    if ($port <= 0) {
        $port = 3306;
    }

    return array(
        'host' => $host,
        'port' => $port,
        'name' => $name,
        'user' => $user,
        'pass' => $pass,
    );
}

function getDbConnection()
{
    $db = getDbConfig();

    $conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name'], (int)$db['port']);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    if (!$conn->set_charset('utf8')) {
        throw new Exception('Unable to set charset utf8');
    }

    $conn->query("SET time_zone = '+07:00'");

    return $conn;
}

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function jsonResponse($payload, $statusCode = 200)
{
    while (ob_get_level()) ob_end_clean();
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function toDecimalString($number, $precision)
{
    return number_format((float)$number, (int)$precision, '.', '');
}
