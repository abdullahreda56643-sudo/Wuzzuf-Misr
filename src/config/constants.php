<?php
/**
 * ========================================
 * الثوابت والقيم الثابتة للتطبيق
 * Application Constants
 * ========================================
 * 
 * تحتوي على جميع الثوابت المستخدمة في التطبيق
 */

// ========================================
// ثوابت أنواع المستخدمين
// ========================================
define('USER_TYPE_JOB_SEEKER', 'job_seeker');
define('USER_TYPE_EMPLOYER', 'employer');
define('USER_TYPE_ADMIN', 'admin');

// قائمة أنواع المستخدمين
define('USER_TYPES', [
    USER_TYPE_JOB_SEEKER => 'باحث عن عمل',
    USER_TYPE_EMPLOYER   => 'صاحب عمل',
    USER_TYPE_ADMIN      => 'إداري'
]);

// ========================================
// ثوابت حالات المستخدمين
// ========================================
define('USER_STATUS_ACTIVE', 'active');
define('USER_STATUS_INACTIVE', 'inactive');
define('USER_STATUS_SUSPENDED', 'suspended');
define('USER_STATUS_BANNED', 'banned');

define('USER_STATUSES', [
    USER_STATUS_ACTIVE    => 'نشط',
    USER_STATUS_INACTIVE  => 'غير نشط',
    USER_STATUS_SUSPENDED => 'معلق',
    USER_STATUS_BANNED    => 'محظور'
]);

// ========================================
// ثوابت حالات الوظائف
// ========================================
define('JOB_STATUS_DRAFT', 'draft');
define('JOB_STATUS_PUBLISHED', 'published');
define('JOB_STATUS_EXPIRED', 'expired');
define('JOB_STATUS_CLOSED', 'closed');

define('JOB_STATUSES', [
    JOB_STATUS_DRAFT     => 'مسودة',
    JOB_STATUS_PUBLISHED => 'منشور',
    JOB_STATUS_EXPIRED   => 'منتهي',
    JOB_STATUS_CLOSED    => 'مغلق'
]);

// ========================================
// ثوابت أنواع الوظائف
// ========================================
define('JOB_TYPE_FULLTIME', 'full-time');
define('JOB_TYPE_PARTTIME', 'part-time');
define('JOB_TYPE_CONTRACT', 'contract');
define('JOB_TYPE_TEMPORARY', 'temporary');
define('JOB_TYPE_INTERNSHIP', 'internship');

define('JOB_TYPES', [
    JOB_TYPE_FULLTIME   => 'دوام كامل',
    JOB_TYPE_PARTTIME   => 'دوام جزئي',
    JOB_TYPE_CONTRACT   => 'عقد مؤقت',
    JOB_TYPE_TEMPORARY  => 'مؤقت',
    JOB_TYPE_INTERNSHIP => 'تدريب'
]);

// ========================================
// ثوابت مستويات الخبرة
// ========================================
define('EXPERIENCE_ENTRY', 'entry');
define('EXPERIENCE_JUNIOR', 'junior');
define('EXPERIENCE_MID', 'mid');
define('EXPERIENCE_SENIOR', 'senior');
define('EXPERIENCE_LEAD', 'lead');
define('EXPERIENCE_EXECUTIVE', 'executive');

define('EXPERIENCE_LEVELS', [
    EXPERIENCE_ENTRY     => 'بدء التشغيل',
    EXPERIENCE_JUNIOR    => 'مبتدئ',
    EXPERIENCE_MID       => 'متوسط',
    EXPERIENCE_SENIOR    => 'أقدم',
    EXPERIENCE_LEAD      => 'قيادي',
    EXPERIENCE_EXECUTIVE => 'تنفيذي'
]);

// ========================================
// ثوابت حالات تطبيقات الوظائف
// ========================================
define('APPLICATION_STATUS_PENDING', 'pending');
define('APPLICATION_STATUS_REVIEWED', 'reviewed');
define('APPLICATION_STATUS_SHORTLISTED', 'shortlisted');
define('APPLICATION_STATUS_REJECTED', 'rejected');
define('APPLICATION_STATUS_ACCEPTED', 'accepted');
define('APPLICATION_STATUS_WITHDRAWN', 'withdrawn');

define('APPLICATION_STATUSES', [
    APPLICATION_STATUS_PENDING      => 'قيد الانتظار',
    APPLICATION_STATUS_REVIEWED     => 'تمت مراجعته',
    APPLICATION_STATUS_SHORTLISTED  => 'اختياري',
    APPLICATION_STATUS_REJECTED     => 'مرفوض',
    APPLICATION_STATUS_ACCEPTED     => 'مقبول',
    APPLICATION_STATUS_WITHDRAWN    => 'منسحب'
]);

// ========================================
// ثوابت حالات الإعلانات
// ========================================
define('AD_STATUS_DRAFT', 'draft');
define('AD_STATUS_PENDING', 'pending');
define('AD_STATUS_ACTIVE', 'active');
define('AD_STATUS_EXPIRED', 'expired');
define('AD_STATUS_REJECTED', 'rejected');

define('AD_STATUSES', [
    AD_STATUS_DRAFT    => 'مسودة',
    AD_STATUS_PENDING  => 'قيد المراجعة',
    AD_STATUS_ACTIVE   => 'نشط',
    AD_STATUS_EXPIRED  => 'منتهي',
    AD_STATUS_REJECTED => 'مرفوض'
]);

// ========================================
// ثوابت أنواع الإعلانات
// ========================================
define('AD_TYPE_BANNER', 'banner');
define('AD_TYPE_FEATURED_JOB', 'featured_job');
define('AD_TYPE_COMPANY_PROMO', 'company_promo');
define('AD_TYPE_OTHER', 'other');

define('AD_TYPES', [
    AD_TYPE_BANNER         => 'إعلان بانر',
    AD_TYPE_FEATURED_JOB   => 'وظيفة مميزة',
    AD_TYPE_COMPANY_PROMO  => 'ترويج شركة',
    AD_TYPE_OTHER          => 'آخر'
]);

// ========================================
// ثوابت طرق التقديم على الوظائف
// ========================================
define('APPLICATION_METHOD_INTERNAL', 'internal');
define('APPLICATION_METHOD_WHATSAPP', 'whatsapp');
define('APPLICATION_METHOD_PHONE', 'phone');
define('APPLICATION_METHOD_EMAIL', 'email');
define('APPLICATION_METHOD_EXTERNAL', 'external_link');

define('APPLICATION_METHODS', [
    APPLICATION_METHOD_INTERNAL  => 'التقديم عبر المنصة',
    APPLICATION_METHOD_WHATSAPP  => 'واتساب',
    APPLICATION_METHOD_PHONE     => 'هاتف',
    APPLICATION_METHOD_EMAIL     => 'بريد إلكتروني',
    APPLICATION_METHOD_EXTERNAL  => 'رابط خارجي'
]);

// ========================================
// ثوابت حالات الدفع
// ========================================
define('PAYMENT_STATUS_PENDING', 'pending');
define('PAYMENT_STATUS_PROCESSING', 'processing');
define('PAYMENT_STATUS_COMPLETED', 'completed');
define('PAYMENT_STATUS_FAILED', 'failed');
define('PAYMENT_STATUS_REFUNDED', 'refunded');

define('PAYMENT_STATUSES', [
    PAYMENT_STATUS_PENDING    => 'قيد الانتظار',
    PAYMENT_STATUS_PROCESSING => 'قيد المعالجة',
    PAYMENT_STATUS_COMPLETED  => 'مكتمل',
    PAYMENT_STATUS_FAILED     => 'فشل',
    PAYMENT_STATUS_REFUNDED   => 'مسترد'
]);

// ========================================
// ثوابت طرق الدفع
// ========================================
define('PAYMENT_METHOD_CREDIT_CARD', 'credit_card');
define('PAYMENT_METHOD_DEBIT_CARD', 'debit_card');
define('PAYMENT_METHOD_BANK_TRANSFER', 'bank_transfer');
define('PAYMENT_METHOD_MOBILE_WALLET', 'mobile_wallet');
define('PAYMENT_METHOD_CASH', 'cash');

define('PAYMENT_METHODS', [
    PAYMENT_METHOD_CREDIT_CARD    => 'بطاقة ائتمان',
    PAYMENT_METHOD_DEBIT_CARD     => 'بطاقة خصم',
    PAYMENT_METHOD_BANK_TRANSFER  => 'تحويل بنكي',
    PAYMENT_METHOD_MOBILE_WALLET  => 'محفظة رقمية',
    PAYMENT_METHOD_CASH            => 'نقد'
]);

// ========================================
// ثوابت أنواع البلاغات
// ========================================
define('REPORT_TYPE_JOB', 'job');
define('REPORT_TYPE_COMPANY', 'company');
define('REPORT_TYPE_USER', 'user');
define('REPORT_TYPE_AD', 'ad');

define('REPORT_TYPES', [
    REPORT_TYPE_JOB     => 'وظيفة',
    REPORT_TYPE_COMPANY => 'شركة',
    REPORT_TYPE_USER    => 'مستخدم',
    REPORT_TYPE_AD      => 'إعلان'
]);

// ========================================
// ثوابت أسباب البلاغات
// ========================================
define('REPORT_REASON_SPAM', 'spam');
define('REPORT_REASON_INAPPROPRIATE', 'inappropriate');
define('REPORT_REASON_FRAUD', 'fraud');
define('REPORT_REASON_HARASSMENT', 'harassment');
define('REPORT_REASON_COPYRIGHT', 'copyright');
define('REPORT_REASON_OTHER', 'other');

define('REPORT_REASONS', [
    REPORT_REASON_SPAM          => 'بريد عشوائي',
    REPORT_REASON_INAPPROPRIATE => 'محتوى غير لائق',
    REPORT_REASON_FRAUD         => 'احتيال',
    REPORT_REASON_HARASSMENT    => 'تحرش',
    REPORT_REASON_COPYRIGHT     => 'انتهاك حقوق النشر',
    REPORT_REASON_OTHER         => 'آخر'
]);

// ========================================
// ثوابت حالات البلاغات
// ========================================
define('REPORT_STATUS_PENDING', 'pending');
define('REPORT_STATUS_REVIEWING', 'reviewing');
define('REPORT_STATUS_RESOLVED', 'resolved');
define('REPORT_STATUS_REJECTED', 'rejected');

define('REPORT_STATUSES', [
    REPORT_STATUS_PENDING   => 'قيد الانتظار',
    REPORT_STATUS_REVIEWING => 'قيد المراجعة',
    REPORT_STATUS_RESOLVED  => 'تم الحل',
    REPORT_STATUS_REJECTED  => 'مرفوض'
]);

// ========================================
// ثوابت أنواع الإشعارات
// ========================================
define('NOTIFICATION_TYPE_JOB_APPLIED', 'job_applied');
define('NOTIFICATION_TYPE_JOB_REJECTED', 'job_rejected');
define('NOTIFICATION_TYPE_JOB_SHORTLISTED', 'job_shortlisted');
define('NOTIFICATION_TYPE_JOB_ACCEPTED', 'job_accepted');
define('NOTIFICATION_TYPE_JOB_EXPIRED', 'job_expired');
define('NOTIFICATION_TYPE_COMPANY_MESSAGE', 'company_message');
define('NOTIFICATION_TYPE_SYSTEM', 'system');
define('NOTIFICATION_TYPE_PAYMENT', 'payment');

define('NOTIFICATION_TYPES', [
    NOTIFICATION_TYPE_JOB_APPLIED       => 'تقديم على وظيفة',
    NOTIFICATION_TYPE_JOB_REJECTED      => 'رفض طلب',
    NOTIFICATION_TYPE_JOB_SHORTLISTED  => 'قبول للمرحلة الثانية',
    NOTIFICATION_TYPE_JOB_ACCEPTED      => 'قبول الوظيفة',
    NOTIFICATION_TYPE_JOB_EXPIRED       => 'انتهاء الوظيفة',
    NOTIFICATION_TYPE_COMPANY_MESSAGE   => 'رسالة من الشركة',
    NOTIFICATION_TYPE_SYSTEM            => 'رسالة نظام',
    NOTIFICATION_TYPE_PAYMENT           => 'إشعار دفع'
]);

// ========================================
// ثوابت حالات التحقق من الشركات
// ========================================
define('VERIFICATION_STATUS_PENDING', 'pending');
define('VERIFICATION_STATUS_VERIFIED', 'verified');
define('VERIFICATION_STATUS_REJECTED', 'rejected');

define('VERIFICATION_STATUSES', [
    VERIFICATION_STATUS_PENDING  => 'قيد الانتظار',
    VERIFICATION_STATUS_VERIFIED => 'تم التحقق',
    VERIFICATION_STATUS_REJECTED => 'مرفوض'
]);

// ========================================
// ثوابت أحجام الشركات
// ========================================
define('COMPANY_SIZE_STARTUP', 'startup');
define('COMPANY_SIZE_SMALL', 'small');
define('COMPANY_SIZE_MEDIUM', 'medium');
define('COMPANY_SIZE_LARGE', 'large');
define('COMPANY_SIZE_ENTERPRISE', 'enterprise');

define('COMPANY_SIZES', [
    COMPANY_SIZE_STARTUP    => 'شركة ناشئة',
    COMPANY_SIZE_SMALL      => 'صغيرة',
    COMPANY_SIZE_MEDIUM     => 'متوسطة',
    COMPANY_SIZE_LARGE      => 'كبيرة',
    COMPANY_SIZE_ENTERPRISE => 'مؤسسة'
]);

// ========================================
// ثوابت حالات الصفحات والمقالات
// ========================================
define('CONTENT_STATUS_DRAFT', 'draft');
define('CONTENT_STATUS_PUBLISHED', 'published');
define('CONTENT_STATUS_ARCHIVED', 'archived');

define('CONTENT_STATUSES', [
    CONTENT_STATUS_DRAFT     => 'مسودة',
    CONTENT_STATUS_PUBLISHED => 'منشور',
    CONTENT_STATUS_ARCHIVED  => 'مؤرشف'
]);

// ========================================
// ثوابت الأخطاء والرسائل
// ========================================
define('ERROR_UNAUTHORIZED', 'غير مصرح لك بهذا الإجراء');
define('ERROR_FORBIDDEN', 'الوصول مرفوض');
define('ERROR_NOT_FOUND', 'لم يتم العثور على الملف المطلوب');
define('ERROR_VALIDATION', 'البيانات المدخلة غير صحيحة');
define('ERROR_DATABASE', 'خطأ في قاعدة البيانات');
define('ERROR_SERVER', 'خطأ في الخادم');

// ========================================
// ثوابت الصحة والنجاح
// ========================================
define('SUCCESS_CREATED', 'تم الإنشاء بنجاح');
define('SUCCESS_UPDATED', 'تم التحديث بنجاح');
define('SUCCESS_DELETED', 'تم الحذف بنجاح');
define('SUCCESS_LOGIN', 'تم تسجيل الدخول بنجاح');
define('SUCCESS_LOGOUT', 'تم تسجيل الخروج بنجاح');

// ========================================
// ثوابت الأدوار والصلاحيات
// ========================================
define('ROLE_ADMIN', 'admin');
define('ROLE_EMPLOYER', 'employer');
define('ROLE_JOB_SEEKER', 'job_seeker');
define('ROLE_MODERATOR', 'moderator');

define('ROLES', [
    ROLE_ADMIN       => 'إداري',
    ROLE_EMPLOYER    => 'صاحب عمل',
    ROLE_JOB_SEEKER  => 'باحث عن عمل',
    ROLE_MODERATOR   => 'مشرف'
]);

// ========================================
// ثوابت الأكوار والحدود الزمنية
// ========================================
define('PASSWORD_MIN_LENGTH', 8);
define('USERNAME_MIN_LENGTH', 3);
define('USERNAME_MAX_LENGTH', 50);
define('EMAIL_MAX_LENGTH', 255);
define('PHONE_MAX_LENGTH', 20);

// ========================================
// ثوابت التخزين المؤقت
// ========================================
define('CACHE_DURATION_SHORT', 300);      // 5 دقائق
define('CACHE_DURATION_MEDIUM', 3600);    // ساعة واحدة
define('CACHE_DURATION_LONG', 86400);     // يوم واحد
define('CACHE_DURATION_WEEK', 604800);    // أسبوع واحد

// ========================================
// ثوابت الترقيم
// ========================================
define('PAGINATION_PER_PAGE', 20);
define('PAGINATION_RANGE', 5);

// ========================================
// ثوابت الملفات المسموحة
// ========================================
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'txt']);
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('MAX_FILE_SIZE', 5242880); // 5MB

return true; 
