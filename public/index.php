<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use App\Dispatcher\EventDispatcher;
use App\Observer\LoggingListener;
use App\Controller\ActionController;
use App\Util\ResponseUtil;

EventDispatcher::getInstance()->addListener(new LoggingListener());

// Routing minimal
$path   = $_SERVER['PATH_INFO'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];
$body   = json_decode(file_get_contents('php://input'), true) ?: [];

try {
    if ($method==='GET' && $path==='/') {
        http_response_code(302);
        header('Location: /test.html');
        exit;
    }
    if ($method==='POST' && $path==='/actions') {
        ActionController::execute($body);
    } else {
        ResponseUtil::json(['error'=>'Route not found'], 404);
    }
} catch (\Throwable $e) {
    ResponseUtil::json([
        'error'=>$e->getMessage(),
        'trace' => $e->getTrace()
    ], 400);
}
