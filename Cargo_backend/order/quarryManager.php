<?php
require '../cors.php';

require '../config/config.php';
require 'EntityManager.php';

$quarryManager = new EntityManager($pdo, 'quarries');

// Обработка запросов (например, добавление, обновление, удаление, получение)
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Получение карьера по ID
            $quarryId = intval($_GET['id']);
            $quarry = $quarryManager->getById($quarryId);
            if ($quarry) {
                echo json_encode($quarry);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Quarry not found']);
            }
        } else {
            // Получение всех карьеров если не был передан id
            $quarries = $quarryManager->getAll();
            echo json_encode($quarries);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}