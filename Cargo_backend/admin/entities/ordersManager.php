<?php
require '../../cors.php';
// Проверка на админа
require '../checkAdmin.php';
checkAdmin();

require '../../config/config.php';
require 'EntityManager.php';

// Инициализация EntityManager для работы с таблицей orders
$orderManager = new EntityManager(pdo: $pdo, table: 'orders');
$requestMethod = $_SERVER['REQUEST_METHOD'];
switch ($requestMethod) {
    case "GET":
        if (isset($_GET['id'])) {
            // Получение продукта по ID
            $orderId = intval($_GET['id']);
            $product = $orderManager->getById($orderId);
            if ($product) {
                echo json_encode($product);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found']);
            }
        } else {
            // Получение всех продуктов если не был передан id
            $products = $orderManager->getAll();
            echo json_encode($products);
        }
        break;
    case "DELETE":
        if (isset($_GET['id'])) {
            $orderId = intval($_GET['id']);
            $order = $orderManager->getById($orderId);

            if ($order) {
                if ($orderManager->delete($orderId)) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Order deleted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to delete order']);
                }
            } else {
                http_response_code(403);
                echo json_encode(['error' => 'You are not authorized to delete this order']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Order ID and Author ID are required']);
        }
        break;

    case "PATCH":
        if (isset($_GET['id'])) {
            $orderId = intval($_GET['id']);
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['weight'], $data['company'], $data['quarry'], $data['product'], $data['auto'], $data['distance'])) {
                http_response_code(response_code: 400);
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }

            $order = $orderManager->getById($orderId);

            if ($order) {
                $updatedOrder = [
                    'weight' => $data['weight'],
                    'company' => $data['company'],
                    'quarry' => $data['quarry'],
                    'product' => $data['product'],
                    'auto' => $data['auto'],
                    'distance' => $data['distance'],
                    'comment' => $data['comment'] ?? '',
                ];

                if ($orderManager->update($orderId, $updatedOrder)) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Order updated successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to update order']);
                }
            } else {
                http_response_code(403);
                echo json_encode(['error' => 'You are not authorized to update this order']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Order ID and Author ID are required']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
