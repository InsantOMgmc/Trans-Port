<?php
require '../cors.php';
require '../config/config.php';
require 'entityManager.php';

// Инициализация EntityManager для работы с таблицей orders
$driverManager = new EntityManager(pdo: $pdo, table: 'orders');
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'PATCH':
        $orderId = isset($_GET['id']) ? intval($_GET['id']) : null;
        $data = json_decode(file_get_contents("php://input"), true);

        if ($orderId && isset($data['driver'])) {

            // Обновление поля driver_note по ID заказа
            if ($driverManager->update($orderId, ['driver' => $data['driver']])) {
                http_response_code(200);
                echo json_encode(['message' => 'Driver information added to order successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Order ID and driver information are required']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
