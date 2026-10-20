<?php
/**
 * ========================================
 * الإعدادات العامة للتطبيق
 * Application Configuration
 * ========================================
 * 
 * هذا الملف يحتوي على الإعدادات الديناميكية
 * جميع الإعدادات تُحمّل من جدول settings في قاعدة البيانات
 * ويمكن تعديلها من لوحة التحكم بسهولة بدون تعديل الكود
 */

// تحديد البيئة والمسارات الأساسية
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', getenv('APP_ENV') ?? 'development');
}
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', ENVIRONMENT !== 'production');
}
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}
if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . '/src');
}
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', ROOT_PATH . '/public');
}
if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', ROOT_PATH . '/storage');
}
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', PUBLIC_PATH . '/assets/uploads');
}

// ========================================
// إعدادات التطبيق الأساسية (قيم افتراضية)
// ========================================
$app_config = [
    // الإعدادات العامة
    'site_name'                   => 'بوابة وظائف مصر | Wazaef Misr',
    'site_description'            => 'منصة وظائف إلكترونية تربط الباحثين عن عمل مع الشركات',
    'site_url'                    => 'http://localhost/works/project-root',
    'site_email'                  => 'info@jobsplatform.com',
    'site_phone'                  => '+20000000000',
    'site_address'                => 'كفر الشيخ، مصر',
    'site_logo'                   => '/assets/images/logo.png',
    'site_favicon'                => '/assets/images/favicon.ico',
    
    // إعدادات الصفحة
    'items_per_page'              => 20,
    'admin_per_page'              => 50,
    'jobs_per_page'               => 20,
    'companies_per_page'          => 12,
    'pagination_range'            => 5,
    
    // إعدادات اللغة والمنطقة الزمنية
    'language'                    => 'ar',
    'timezone'                    => 'Africa/Cairo',
    'date_format'                 => 'd/m/Y',
    'time_format'                 => 'H:i:s',
    'datetime_format'             => 'd/m/Y H:i:s',
    
    // إعدادات العملة والدفع
    'currency'                    => 'EGP',
    'currency_symbol'             => 'ج.م',
    'decimal_places'              => 2,
    'thousand_separator'          => ',',
    'decimal_separator'           => '.',
    'tax_rate'                    => 14,
    'commission_rate'             => 10,
    
    // إعدادات الوظائف
    'default_job_duration'        => 30,
    'job_status_colors' => [
        'draft'     => '#95A5A6',
        'published' => '#27AE60',
        'expired'   => '#E74C3C',
        'closed'    => '#C0392B'
    ],
    'job_type_labels' => [
        'full-time'   => 'دوام كامل',
        'part-time'   => 'دوام جزئي',
        'contract'    => 'عقد مؤقت',
        'temporary'   => 'مؤقت',
        'internship'  => 'تدريب'
    ],
    'experience_levels' => [
        'entry'     => 'بدء التشغيل',
        'junior'    => 'مبتدئ',
        'mid'       => 'متوسط',
        'senior'    => 'أقدم',
        'lead'      => 'قيادي',
        'executive' => 'تنفيذي'
    ],
    'company_sizes' => [
        'startup'    => 'شركة ناشئة',
        'small'      => 'صغيرة',
        'medium'     => 'متوسطة',
        'large'      => 'كبيرة',
        'enterprise' => 'مؤسسة'
    ],
    
    // إعدادات الإعلانات
    'featured_job_boost_days'     => 7,
    'ad_click_tracking'           => true,
    'ad_impression_tracking'      => true,
    
    // إعدادات المرفوعات (الملفات)
    'max_upload_size'             => 5242880, // 5MB بالبايت
    'allowed_file_types'          => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'],
    'allowed_image_types'         => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    'upload_directories' => [
        'resumes'   => UPLOAD_PATH . '/resumes',
        'companies' => UPLOAD_PATH . '/companies',
        'ads'       => UPLOAD_PATH . '/ads',
        'posts'     => UPLOAD_PATH . '/posts',
        'users'     => UPLOAD_PATH . '/users'
    ],
    
    // إعدادات البريد الإلكتروني
    'email_enabled'               => true,
    'email_driver'                => 'mail', // mail, smtp, sendgrid
    'email_from_name'             => 'منصة الوظائف',
    'email_from_address'          => 'info@jobsplatform.com',
    'smtp_host'                   => 'smtp.gmail.com',
    'smtp_port'                   => 587,
    'smtp_encryption'             => 'tls',
    'smtp_username'               => '',
    'smtp_password'               => '',
    
    // إعدادات الإشعارات
    'email_notifications_enabled' => true,
    'sms_notifications_enabled'   => false,
    'notification_types' => [
        'job_applied'       => 'عند التقدم على وظيفة',
        'job_rejected'      => 'عند رفض الطلب',
        'job_shortlisted'   => 'عند قبول الطلب للمرحلة الثانية',
        'job_accepted'      => 'عند قبول الوظيفة',
        'job_expired'       => 'عند انتهاء الوظيفة',
        'company_message'   => 'عند رسالة من الشركة',
        'payment'           => 'إشعارات الدفع',
        'system'            => 'رسائل النظام'
    ],
    
    // إعدادات الأمان
    'password_min_length'         => 8,
    'password_require_uppercase'  => true,
    'password_require_lowercase'  => true,
    'password_require_numbers'    => true,
    'password_require_special'    => true,
    'password_reset_expiry'       => 24, // بالساعات
    'verification_token_expiry'   => 48, // بالساعات
    'max_login_attempts'          => 5,
    'login_attempt_window'        => 15, // بالدقائق
    'session_timeout'             => 3600, // بالثواني
    'remember_me_duration'        => 604800, // 7 أيام بالثواني
    'cors_enabled'                => false,
    'cors_origins'                => ['localhost', 'localhost:3000'],
    'rate_limiting_enabled'       => true,
    'rate_limit_requests'         => 100,
    'rate_limit_window'           => 3600, // بالثواني
    
    // إعدادات Google AdSense
    'google_adsense_enabled'      => false,
    'google_adsense_client_id'    => '',
    'google_adsense_slot_id'      => '',
    'google_analytics_enabled'    => false,
    'google_analytics_id'         => '',
    
    // إعدادات وسائل التواصل
    'social_links' => [
        'facebook'  => 'https://facebook.com',
        'twitter'   => 'https://twitter.com',
        'linkedin'  => 'https://linkedin.com',
        'youtube'   => 'https://youtube.com',
        'instagram' => 'https://instagram.com',
        'whatsapp'  => 'https://wa.me/201000000000'
    ],
    
    // إعدادات الصيانة
    'maintenance_mode'            => false,
    'maintenance_message'         => 'الموقع تحت الصيانة حالياً، يرجى المحاولة لاحقاً',
    
    // الإعدادات المتقدمة
    'cache_enabled'               => true,
    'cache_driver'                => 'file', // file, redis, memcached
    'cache_ttl'                   => 3600, // بالثواني
    'log_enabled'                 => true,
    'log_level'                   => DEBUG_MODE ? 'debug' : 'error', // debug, info, warning, error
    'log_file'                    => STORAGE_PATH . '/logs/app.log',
];

/**
 * ========================================
 * دالة الحصول على الإعدادات الديناميكية
 * ========================================
 */
function get_settings_from_db() {
    static $settings = null;
    
    if ($settings !== null) {
        return $settings;
    }
    
    $settings = [];
    
    try {
        // محاولة الاتصال بقاعدة البيانات
        $db_config = require __DIR__ . '/database.php';
        $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset={$db_config['charset']}";
        
        $pdo = new PDO(
            $dsn,
            $db_config['username'],
            $db_config['password'],
            $db_config['options'] ?? []
        );
        
        // جلب جميع الإعدادات من قاعدة البيانات
        $stmt = $pdo->prepare("SELECT `key`, `value`, `type` FROM settings WHERE is_editable = 1");
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        foreach ($results as $row) {
            // تحويل القيم حسب نوعها
            switch ($row['type']) {
                case 'boolean':
                    $settings[$row['key']] = filter_var($row['value'], FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'number':
                    $settings[$row['key']] = is_numeric($row['value']) ? (int)$row['value'] : $row['value'];
                    break;
                case 'json':
                    $settings[$row['key']] = json_decode($row['value'], true) ?? [];
                    break;
                default:
                    $settings[$row['key']] = $row['value'];
            }
        }
    } catch (Exception $e) {
        // في حالة الخطأ، نستخدم الإعدادات الافتراضية فقط
        if (DEBUG_MODE) {
            error_log('خطأ في جلب الإعدادات من قاعدة البيانات: ' . $e->getMessage());
        }
    }
    
    return $settings;
}

/**
 * ========================================
 * دالة للحصول على قيمة إعداد معين
 * ========================================
 */
function config($key, $default = null) {
    global $app_config;
    
    // أولاً: محاولة الحصول من الإعدادات الديناميكية
    $db_settings = get_settings_from_db();

    if ($key === 'social_links') {
        $defaultLinks = $app_config['social_links'] ?? [];
        $storedLinks = [];
        foreach (['facebook', 'twitter', 'linkedin', 'instagram', 'youtube', 'whatsapp'] as $network) {
            $storedKey = 'social_' . $network;
            if (array_key_exists($storedKey, $db_settings) && trim((string) $db_settings[$storedKey]) !== '') {
                $storedLinks[$network] = $db_settings[$storedKey];
            }
        }
        return array_merge($defaultLinks, $storedLinks);
    }

    if (isset($db_settings[$key])) {
        return $db_settings[$key];
    }
    
    // ثانياً: الحصول من الإعدادات الافتراضية
    if (isset($app_config[$key])) {
        return $app_config[$key];
    }
    
    // ثالثاً: إرجاع القيمة الافتراضية المعطاة
    return $default;
}

/**
 * ========================================
 * دالة لتعيين إعداد معين
 * ========================================
 */
function set_config($key, $value) {
    global $app_config;
    $app_config[$key] = $value;
    return $value;
}

/**
 * ========================================
 * دالة لجلب جميع الإعدادات
 * ========================================
 */
function all_config() {
    global $app_config;
    $db_settings = get_settings_from_db();
    return array_merge($app_config, $db_settings);
}

// إرجاع إعدادات التطبيق
return $app_config; 
