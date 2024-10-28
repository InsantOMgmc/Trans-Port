<?php
require '../../cors.php';
// Проверка на админа
require '../checkAdmin.php';
checkAdmin();

require '../../config/config.php';
require 'EntityManager.php';

// Создание экземпляра EntityManager для продуктов
$productManager = new EntityManager($pdo, 'products');

// Обработка запросов (например, добавление, обновление, удаление, получение)
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'POST':
        // Добавление продукта
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['name']) && isset($data['quarry'])) {
            if ($productManager->add(['name' => $data['name'], 'quarry_id' => $data['quarry']])) {
                http_response_code(201);
                echo json_encode(['message' => 'Product added successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to add product']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Name and Quarry ID are required']);
        }
        break;

    case 'PATCH':
        // Обновление продукта
        $productId = isset($_GET['id']) ? intval($_GET['id']) : null;
        $data = json_decode(file_get_contents("php://input"), true);
        if ($productId && isset($data['name']) && isset($data['quarry'])) {
            if ($productManager->update($productId, ['name' => $data['name'], 'quarry_id' => $data['quarry']])) {
                http_response_code(200);
                echo json_encode(['message' => 'Product updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID, Name, and Quarry ID are required']);
        }
        break;

    case 'DELETE':
        // Удаление продукта
        $productId = isset($_GET['id']) ? intval($_GET['id']) : null;
        if ($productId) {
            if ($productManager->delete($productId)) {
                http_response_code(200);
                echo json_encode(['message' => 'Product deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID is required']);
        }
        break;

    case 'GET':
        if (isset($_GET['id'])) {
            // Получение продукта по ID
            $productId = intval($_GET['id']);
            $product = $productManager->getById($productId);
            if ($product) {
                echo json_encode($product);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found']);
            }
        } else {
            // Получение всех продуктов если не был передан id
            $products = $productManager->getAll();
            echo json_encode($products);
        }
        break;


    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}