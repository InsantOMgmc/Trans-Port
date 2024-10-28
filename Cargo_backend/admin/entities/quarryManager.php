<?php
require '../../cors.php';
// Проверка на админа
require '../checkAdmin.php';
checkAdmin();

require '../../config/config.php';
require 'EntityManager.php';

$quarryManager = new EntityManager($pdo, 'quarries');

// Обработка запросов (например, добавление, обновление, удаление, получение)
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'POST':
        // Добавление карьера
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['name'])) {
            if ($quarryManager->add(['name' => $data['name']])) {
                http_response_code(201);
                echo json_encode(['message' => 'Карьера успешно добавлен']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Не удалось добавить карьер']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Название обязательно']);
        }
        break;

    case 'PATCH':
        // Обновление карьера
        $quarryId = isset($_GET['id']) ? intval($_GET['id']) : null;
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['name']) && isset($quarryId)) {
            if ($quarryManager->update($quarryId, ['name' => $data['name']])) {
                http_response_code(200);
                echo json_encode(['message' => 'Карьера успешно обновлен']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Карьер не найден']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID и Название обязательны']);
        }
        break;

    case 'DELETE':
        // Удаление карьера
        $quarryId = isset($_GET['id']) ? intval($_GET['id']) : null;
        if ($quarryId) {
            if ($quarryManager->delete($quarryId)) {
                http_response_code(200);
                echo json_encode(['message' => 'Карьера успешно удален']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Карьер не найден']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID обязательно']);
        }
        break;

    case 'GET':
        if (isset($_GET['id'])) {
            // Получение карьера по ID
            $quarryId = intval($_GET['id']);
            $quarry = $quarryManager->getById($quarryId);
            if ($quarry) {
                echo json_encode($quarry);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Карьер не найден']);
            }
        } else {
            // Получение всех карьеров если не был передан id
            $quarries = $quarryManager->getAll();
            echo json_encode($quarries);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Метод не разрешен']);
        break;
}