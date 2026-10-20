<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/core/Helpers.php';

spl_autoload_register(function (string $class): void {
    $paths = [
        ROOT_PATH . '/src/core/' . $class . '.php',
        ROOT_PATH . '/src/models/' . $class . '.php',
        ROOT_PATH . '/src/middleware/' . $class . '.php',
        ROOT_PATH . '/src/traits/' . $class . '.php',
        ROOT_PATH . '/src/controllers/' . $class . '.php',
        ROOT_PATH . '/src/controllers/admin/' . $class . '.php',
        ROOT_PATH . '/src/controllers/frontend/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . '/src');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';

$basePrefixes = [];

$siteBasePath = parse_url(config('site_url', 'http://localhost/works/project-root'), PHP_URL_PATH) ?: '/';
$siteBasePath = rtrim($siteBasePath, '/');
if ($siteBasePath !== '' && $siteBasePath !== '/') {
    $basePrefixes[] = $siteBasePath;
}

$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
$scriptDir = rtrim($scriptDir, '/');
if ($scriptDir !== '' && $scriptDir !== '.' && $scriptDir !== '/') {
    $basePrefixes[] = $scriptDir;
}

$basePrefixes[] = '/admin';
$basePrefixes[] = '/public';
$basePrefixes[] = '/project-root';
$basePrefixes[] = '/works/project-root';
$basePrefixes = array_values(array_unique(array_filter($basePrefixes, static fn ($value) => $value !== '')));

foreach ($basePrefixes as $base) {
    if ($path === $base) {
        $path = '/';
        break;
    }

    if (strpos($path, $base) === 0) {
        $path = substr($path, strlen($base));
        break;
    }
}

if (strpos($path, '/admin') === 0) {
    $path = substr($path, strlen('/admin'));
    if ($path === '') {
        $path = '/';
    }
}

$projectBase = rtrim(config('site_url', 'http://localhost/works/project-root'), '/');

if ($path !== '/login' && !Auth::check()) {
    Session::set('flash', [
        'type' => 'error',
        'message' => 'يجب تسجيل الدخول أولاً',
    ]);
    header('Location: ' . $projectBase . '/admin/login');
    exit;
}

if ($path !== '/login' && Auth::check() && !Auth::isAdmin()) {
    Session::set('flash', [
        'type' => 'error',
        'message' => 'ليس لديك صلاحية الوصول إلى لوحة الإدارة',
    ]);

    $redirectPath = '/';
    if (Auth::isEmployer()) {
        $redirectPath = '/employer/dashboard';
    } elseif (Auth::isJobSeeker()) {
        $redirectPath = '/dashboard';
    }

    header('Location: ' . $projectBase . $redirectPath);
    exit;
}

$router = new Router();

$router->get('/login', [AdminAuthController::class, 'login']);
$router->post('/login', [AdminAuthController::class, 'login']);
$router->get('/logout', [AdminAuthController::class, 'logout']);

$router->get('/', [AdminController::class, 'index']);
$router->get('/dashboard', [AdminController::class, 'index']);

$router->get('/jobs', [JobController::class, 'index']);
$router->get('/jobs/create', [JobController::class, 'create']);
$router->post('/jobs/create', [JobController::class, 'create']);
$router->get('/jobs/{id}', [JobController::class, 'details']);
$router->post('/jobs/{id}/approve', [JobController::class, 'approve']);
$router->post('/jobs/{id}/reject', [JobController::class, 'reject']);
$router->get('/jobs/{id}/edit', [JobController::class, 'edit']);
$router->post('/jobs/{id}/edit', [JobController::class, 'edit']);
$router->get('/jobs/{id}/delete', [JobController::class, 'delete']);

$router->get('/companies', [CompanyController::class, 'index']);
$router->get('/companies/create', [CompanyController::class, 'create']);
$router->post('/companies/create', [CompanyController::class, 'create']);
$router->post('/companies/{id}/verify', [CompanyController::class, 'verify']);
$router->post('/companies/{id}/reject-verification', [CompanyController::class, 'rejectVerification']);
$router->get('/companies/{id}/edit', [CompanyController::class, 'edit']);
$router->post('/companies/{id}/edit', [CompanyController::class, 'edit']);
$router->get('/companies/{id}/delete', [CompanyController::class, 'delete']);

$router->get('/categories', [CategoryController::class, 'index']);
$router->get('/categories/create', [CategoryController::class, 'create']);
$router->post('/categories/create', [CategoryController::class, 'create']);
$router->get('/categories/{id}/edit', [CategoryController::class, 'edit']);
$router->post('/categories/{id}/edit', [CategoryController::class, 'edit']);
$router->get('/categories/{id}/delete', [CategoryController::class, 'delete']);

$router->get('/locations', [LocationController::class, 'index']);
$router->get('/locations/create', [LocationController::class, 'create']);
$router->post('/locations/create', [LocationController::class, 'create']);

$router->get('/users', [UserController::class, 'index']);
$router->get('/users/{id}/edit', [UserController::class, 'edit']);
$router->get('/users/{id}/delete', [UserController::class, 'delete']);
$router->get('/employment-requests', [EmploymentRequestController::class, 'index']);
$router->post('/employment-requests/{id}/status/{status}', [EmploymentRequestController::class, 'updateStatus']);

$router->get('/posts', [PostController::class, 'index']);
$router->get('/posts/create', [PostController::class, 'create']);
$router->post('/posts/create', [PostController::class, 'create']);
$router->get('/posts/{id}/edit', [PostController::class, 'edit']);
$router->post('/posts/{id}/edit', [PostController::class, 'edit']);
$router->get('/posts/{id}/delete', [PostController::class, 'delete']);

$router->get('/pages', [PageController::class, 'index']);
$router->get('/pages/create', [PageController::class, 'create']);
$router->post('/pages/create', [PageController::class, 'create']);

$router->get('/ads', [AdController::class, 'index']);
$router->get('/ads/create', [AdController::class, 'create']);
$router->post('/ads/create', [AdController::class, 'create']);
$router->get('/ads/{id}/edit', [AdController::class, 'edit']);
$router->post('/ads/{id}/edit', [AdController::class, 'edit']);
$router->get('/ads/{id}/delete', [AdController::class, 'delete']);

$router->get('/settings', [SettingController::class, 'index']);
$router->post('/settings', [SettingController::class, 'save']);

$router->get('/reports', [ReportController::class, 'index']);
$router->post('/reports/{id}/status/{status}', [ReportController::class, 'updateStatus']);
$router->post('/reports/{id}/delete', [ReportController::class, 'delete']);
$router->get('/payments', [PaymentController::class, 'index']);

$router->dispatch($path);

