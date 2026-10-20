<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/core/Helpers.php';

spl_autoload_register(function (string $class): void {
    $paths = [
        ROOT_PATH . '/src/core/' . $class . '.php',
        ROOT_PATH . '/src/models/' . $class . '.php',
        ROOT_PATH . '/src/middleware/' . $class . '.php',
        ROOT_PATH . '/src/traits/' . $class . '.php',
        ROOT_PATH . '/src/services/' . $class . '.php',
        ROOT_PATH . '/src/controllers/' . $class . '.php',
        ROOT_PATH . '/src/controllers/frontend/' . $class . '.php',
        ROOT_PATH . '/src/controllers/admin/' . $class . '.php',
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

$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/home', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/contact', [HomeController::class, 'contact']);
$router->post('/contact', [HomeController::class, 'contact']);
$router->post('/report', [ReportController::class, 'store']);

$router->get('/jobs', [JobController::class, 'index']);
$router->get('/jobs/create', [EmployerController::class, 'createJob']);
$router->post('/jobs/create', [EmployerController::class, 'createJob']);
$router->get('/employer', [EmployerController::class, 'dashboard']);
$router->get('/employer/dashboard', [EmployerController::class, 'dashboard']);
$router->get('/employer/jobs', [EmployerController::class, 'jobs']);
$router->get('/employer/jobs/create', [EmployerController::class, 'createJob']);
$router->post('/employer/jobs/create', [EmployerController::class, 'createJob']);
$router->get('/employer/jobs/{id}/edit', [EmployerController::class, 'editJob']);
$router->post('/employer/jobs/{id}/edit', [EmployerController::class, 'editJob']);
$router->get('/employer/jobs/{id}/renew', [EmployerController::class, 'renewJob']);
$router->post('/employer/jobs/{id}/renew', [EmployerController::class, 'renewJob']);
$router->get('/employer/applications', [EmployerController::class, 'applications']);
$router->post('/employer/update-application-status', [EmployerController::class, 'updateApplicationStatus']);
$router->get('/employer/settings', [EmployerController::class, 'settings']);
$router->post('/employer/settings', [EmployerController::class, 'settings']);
$router->get('/jobs/{id}', [JobController::class, 'show']);
$router->post('/jobs/{id}/apply', [JobController::class, 'apply']);
$router->get('/jobs/{id}/save', [JobController::class, 'save']);
$router->post('/jobs/{id}/save', [JobController::class, 'save']);

$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/employer/register', [AuthController::class, 'employerRegister']);
$router->post('/employer/register', [AuthController::class, 'employerRegister']);
$router->get('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/profile', [ProfileController::class, 'index']);
$router->post('/profile', [ProfileController::class, 'update']);
$router->get('/cv/preview/{id}', [ProfileController::class, 'previewResume']);
$router->get('/profile/saved-jobs', [ProfileController::class, 'savedJobs']);
$router->get('/profile/applications', [ProfileController::class, 'applications']);
$router->get('/profile/notifications', [ProfileController::class, 'notifications']);
$router->get('/employer', [EmployerController::class, 'index']);
$router->get('/companies', [CompanyController::class, 'index']);
$router->get('/companies/{id}', [CompanyController::class, 'show']);
$router->post('/companies/{id}/rate', [CompanyController::class, 'rate']);
$router->get('/job-seekers', [JobSeekerController::class, 'index']);
$router->post('/job-seekers/{id}/request', [JobSeekerController::class, 'request']);
$router->get('/posts', [PageController::class, 'posts']);

$router->dispatch();

