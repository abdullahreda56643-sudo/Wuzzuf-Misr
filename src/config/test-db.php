<?php
/**
 * ========================================
 * اختبار الاتصال بقاعدة البيانات
 * Database Connection Test
 * ========================================
 */

if (!defined('APP_ENV')) {
    putenv('APP_ENV=development');
}

echo "\n";
echo "========================================\n";
echo "اختبار الاتصال بقاعدة البيانات\n";
echo "Database Connection Test\n";
echo "========================================\n\n";

// ========================================
// 1. جلب إعدادات قاعدة البيانات
// ========================================
echo "[1] جلب إعدادات الاتصال...\n";

$db_config_file = __DIR__ . '/database.php';
if (!file_exists($db_config_file)) {
    echo "❌ خطأ: ملف الإعدادات غير موجود\n";
    exit(1);
}

try {
    $db_config = require $db_config_file;
    echo "✅ تم جلب الإعدادات بنجاح\n";
    
    echo "\n📋 الإعدادات الحالية:\n";
    echo "   • Host: " . $db_config['host'] . "\n";
    echo "   • Port: " . $db_config['port'] . "\n";
    echo "   • Database: " . $db_config['database'] . "\n";
    echo "   • Username: " . $db_config['username'] . "\n";
    echo "   • Charset: " . $db_config['charset'] . "\n\n";
    
} catch (Exception $e) {
    echo "❌ خطأ في جلب الإعدادات: " . $e->getMessage() . "\n";
    exit(1);
}

// ========================================
// 2. اختبار اتصال PDO
// ========================================
echo "[2] اختبار الاتصال بـ PDO...\n";

try {
    $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset={$db_config['charset']}";
    
    $pdo = new PDO(
        $dsn,
        $db_config['username'],
        $db_config['password'],
        $db_config['options'] ?? []
    );
    
    echo "✅ تم الاتصال بقاعدة البيانات بنجاح\n\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ في الاتصال:\n";
    echo "   السبب: " . $e->getMessage() . "\n";
    exit(1);
}

// ========================================
// 3. اختبار حالة الاتصال
// ========================================
echo "[3] فحص حالة الاتصال...\n";

try {
    $stmt = $pdo->query("SELECT 1");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✅ الاتصال يعمل بشكل صحيح\n\n";
    }
    
} catch (PDOException $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    exit(1);
}

// ========================================
// 4. معلومات الخادم
// ========================================
echo "[4] معلومات خادم قاعدة البيانات:\n";

try {
    $version = $pdo->query("SELECT VERSION()")->fetchColumn();
    echo "   • الإصدار: " . $version . "\n";
    
    $database_name = $pdo->query("SELECT DATABASE()")->fetchColumn();
    echo "   • اسم قاعدة البيانات: " . $database_name . "\n";
    
    $user = $pdo->query("SELECT USER()")->fetchColumn();
    echo "   • المستخدم الحالي: " . $user . "\n\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}

// ========================================
// 5. اختبار إنشاء جدول مؤقت
// ========================================
echo "[5] اختبار إنشاء جدول مؤقت...\n";

try {
    $pdo->exec("DROP TABLE IF EXISTS test_connection");
    
    $pdo->exec("
        CREATE TABLE test_connection (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET={$db_config['charset']} COLLATE {$db_config['collation']}
    ");
    
    echo "✅ تم إنشاء جدول تجريبي\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ في إنشاء الجدول: " . $e->getMessage() . "\n";
    exit(1);
}

// ========================================
// 6. اختبار الإدراج
// ========================================
echo "[6] اختبار الإدراج (INSERT)...\n";

try {
    $stmt = $pdo->prepare("INSERT INTO test_connection (name) VALUES (?)");
    $stmt->execute(['اختبار']);
    
    $last_id = $pdo->lastInsertId();
    echo "✅ تم الإدراج بنجاح (ID: $last_id)\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ في الإدراج: " . $e->getMessage() . "\n";
    exit(1);
}

// ========================================
// 7. اختبار الاستعلام
// ========================================
echo "[7] اختبار الاستعلام (SELECT)...\n";

try {
    $stmt = $pdo->query("SELECT * FROM test_connection");
    $results = $stmt->fetchAll();
    
    echo "✅ تم الاستعلام بنجاح (عدد النتائج: " . count($results) . ")\n";
    foreach ($results as $row) {
        echo "   • ID: {$row['id']}, Name: {$row['name']}, Created: {$row['created_at']}\n";
    }
    echo "\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ في الاستعلام: " . $e->getMessage() . "\n";
    exit(1);
}

// ========================================
// 8. اختبار التحديث
// ========================================
echo "[8] اختبار التحديث (UPDATE)...\n";

try {
    $stmt = $pdo->prepare("UPDATE test_connection SET name = ? WHERE id = ?");
    $stmt->execute(['اختبار محدث', 1]);
    
    $affected = $stmt->rowCount();
    echo "✅ تم التحديث بنجاح (عدد الصفوف المتأثرة: $affected)\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ في التحديث: " . $e->getMessage() . "\n";
    exit(1);
}

// ========================================
// 9. اختبار الحذف
// ========================================
echo "[9] اختبار الحذف (DELETE)...\n";

try {
    $stmt = $pdo->prepare("DELETE FROM test_connection WHERE id = ?");
    $stmt->execute([1]);
    
    $affected = $stmt->rowCount();
    echo "✅ تم الحذف بنجاح (عدد الصفوف المحذوفة: $affected)\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ في الحذف: " . $e->getMessage() . "\n";
    exit(1);
}

// ========================================
// 10. اختبار العمليات (Transactions)
// ========================================
echo "[10] اختبار العمليات (Transactions)...\n";

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("INSERT INTO test_connection (name) VALUES (?)");
    $stmt->execute(['عملية 1']);
    $stmt->execute(['عملية 2']);
    $stmt->execute(['عملية 3']);
    
    $pdo->commit();
    
    echo "✅ تم الالتزام بالعمليات بنجاح\n";
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "❌ خطأ في العمليات: " . $e->getMessage() . "\n";
    exit(1);
}

// ========================================
// 11. اختبار Prepared Statements
// ========================================
echo "[11] اختبار Prepared Statements...\n";

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM test_connection");
    $stmt->execute();
    $result = $stmt->fetch();
    
    echo "✅ تم الاستعلام باستخدام Prepared Statement بنجاح\n";
    echo "   • العدد الكلي: " . $result['total'] . " سجل\n\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    exit(1);
}

// ========================================
// 12. التحقق من الجداول الموجودة
// ========================================
echo "[12] التحقق من الجداول الموجودة في قاعدة البيانات:\n";

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "   ⚠️  لا توجد جداول في قاعدة البيانات (قد تحتاج إلى تشغيل install.sql)\n";
    } else {
        echo "   • عدد الجداول: " . count($tables) . "\n";
        foreach ($tables as $table) {
            echo "      - " . $table . "\n";
        }
    }
    echo "\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}

// ========================================
// 13. حذف الجدول التجريبي
// ========================================
echo "[13] تنظيف الجداول التجريبية...\n";

try {
    $pdo->exec("DROP TABLE IF EXISTS test_connection");
    echo "✅ تم حذف الجدول التجريبي\n\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}

// ========================================
// النتيجة النهائية
// ========================================
echo "========================================\n";
echo "✅ تم اختبار الاتصال بنجاح!\n";
echo "جميع الاختبارات نجحت بدون مشاكل\n";
echo "========================================\n\n";

// إغلاق الاتصال
$pdo = null;

echo "🔌 تم إغلاق الاتصال\n\n";

exit(0);
