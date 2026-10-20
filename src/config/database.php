<?php
/**
 * ========================================
 * إعدادات قاعدة البيانات
 * Database Configuration
 * ========================================
 * 
 * هذا الملف يحتوي على إعدادات الاتصال بقاعدة البيانات
 * يدعم بيئات مختلفة (Development, Testing, Production)
 */

// تحديد البيئة الحالية
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', getenv('APP_ENV') ?? 'development');
}

// إعدادات قاعدة البيانات حسب البيئة
$db_config = [
    'development' => [
        'host'      => getenv('DB_HOST') ?: 'localhost',
        'port'      => getenv('DB_PORT') ?: 3306,
        'username'  => getenv('DB_USER') ?: 'root',
        'password'  => getenv('DB_PASSWORD') ?: '',
        'database'  => getenv('DB_NAME') ?: 'jobs_platform',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ],
    'testing' => [
        'host'      => getenv('DB_HOST') ?: 'localhost',
        'port'      => getenv('DB_PORT') ?: 3306,
        'username'  => getenv('DB_USER') ?: 'root',
        'password'  => getenv('DB_PASSWORD') ?: '',
        'database'  => getenv('DB_NAME') ?: 'jobs_platform_test',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ],
    'production' => [
        'host'      => getenv('DB_HOST') ?? 'localhost',
        'port'      => getenv('DB_PORT') ?? 3306,
        'username'  => getenv('DB_USER') ?: 'root',
        'password'  => getenv('DB_PASSWORD') ?: '',
        'database'  => getenv('DB_NAME') ?? 'jobs_platform',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ]
];

// الحصول على إعدادات البيئة الحالية
$current_env = ENVIRONMENT;
$config = $db_config[$current_env] ?? $db_config['development'];

// ========================================
// إعدادات الاتصال بـ PDO
// ========================================
return [
    'driver'        => 'mysql',
    'host'          => $config['host'],
    'port'          => $config['port'],
    'database'      => $config['database'],
    'username'      => $config['username'],
    'password'      => $config['password'],
    'charset'       => $config['charset'],
    'collation'     => $config['collation'],
    'prefix'        => '',
    'strict'        => true,
    'engine'        => 'InnoDB',
    
    // خيارات PDO إضافية
    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']} COLLATE {$config['collation']}"
    ],
    
    // إعدادات الاتصال
    'connection_timeout' => 30,
    'read_timeout'       => 60,
    'write_timeout'      => 60,
    
    // إعدادات الـ Pool (اختياري)
    'pool' => [
        'min'      => 2,
        'max'      => 10,
        'timeout'  => 300
    ]
]; 
