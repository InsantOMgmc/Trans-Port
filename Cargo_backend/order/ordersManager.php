<?php
require '../cors.php';
require '../config/config.php';
require 'entityManager.php';

$orderManager = new EntityManager(pdo: $pdo, table: 'orders');
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['author'], $data['weight'], $data['company'], $data['quarry'], $data['product'], $data['auto'], $data['distance'], $data['date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        $newOrder = [
            'author' => $data['author'],
            'weight' => $data['weight'],
            'company' => $data['company'],
            'quarry' => $data['quarry'],
            'product' => $data['product'],
            'auto' => $data['auto'],
            'distance' => $data['distance'],
            'comment' => $data['comment'] ?? '',
            'date' => $data['date']
        ];

        if ($orderManager->add($newOrder)) {
            http_response_code(201);
            echo json_encode(['message' => 'Order created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create order']);
        }
        break;

    case "GET":
        if (isset($_GET['id'])) {
            $orderId = intval($_GET['id']);
            $order = $orderManager->getById($orderId);
            if ($order) {
                echo json_encode($order);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
            }
        } elseif (isset($_GET['authorId'])) {
            $authorId = intval($_GET['authorId']);
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
