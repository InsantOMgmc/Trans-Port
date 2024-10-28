<?php
require '../cors.php';

require '../config/config.php';
require 'entityManager.php';

// Инициализация EntityManager для работы с таблицей orders
$orderManager = new EntityManager(pdo: $pdo, table: 'orders');
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case "GET":
        if (isset($_GET['id'])) {
            // Получение заказа по ID
            $orderId = intval($_GET['id']);
            $order = $orderManager->getById($orderId); // Здесь должен быть вызов метода getById
            if ($order) {
                echo json_encode($order);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']); // Изменено на "Order"
            }
        } elseif (isset($_GET['authorId'])) {
            $authorId = intval($_GET['authorId']); // Получение authorId из запроса
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE author = :authorId");
            $stmt->bindParam(':authorId', $authorId);
            $stmt->execute();

            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($orders) {
                echo json_encode($orders);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'No orders found for this author']);
            }
        } else {
            // Получение всех заказов, если не был передан id
            $orders = $orderManager->getAll(); // Исправлено на getAll()
            echo json_encode($orders);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
