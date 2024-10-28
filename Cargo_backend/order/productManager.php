<?php
require '../cors.php';

require '../config/config.php';
require 'EntityManager.php';

// Создание экземпляра EntityManager для продуктов
$productManager = new EntityManager($pdo, 'products');

// Обработка запросов (например, добавление, обновление, удаление, получение)
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
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