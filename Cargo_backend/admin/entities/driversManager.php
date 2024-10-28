<?php
require '../../cors.php';
// Проверка на админа
require '../checkAdmin.php';
checkAdmin();

require '../../config/config.php';
require 'EntityManager.php';

// Инициализация EntityManager для работы с таблицей drivers
$driverManager = new EntityManager(pdo: $pdo, table: 'drivers');
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case "GET":
        if (isset($_GET['id'])) {
            // Получение водителя по ID
            $driverId = intval($_GET['id']);
            $driver = $driverManager->getById($driverId);
            if ($driver) {
                echo json_encode($driver);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Driver not found']);
            }
        } else {
            // Получение всех водителей, если не был передан id
            $drivers = $driverManager->getAll();
            echo json_encode($drivers);
        }
        break;

    case "POST":
        // Добавление нового водителя
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['name'])) {
            if ($driverManager->add(['name' => $data['name']])) {
                http_response_code(201);
                echo json_encode(['message' => 'Driver added successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to add driver']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Name is required']);
        }
        break;

    case "PATCH":
        // Обновление водителя
        $driverId = isset($_GET['id']) ? intval($_GET['id']) : null;
        $data = json_decode(file_get_contents("php://input"), true);
        if ($driverId && isset($data['name'])) {
            if ($driverManager->update($driverId, ['name' => $data['name']])) {
                http_response_code(200);
                echo json_encode(['message' => 'Driver updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Driver not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID and Name are required']);
        }
        break;

    case "DELETE":
        // Удаление водителя
        $driverId = isset($_GET['id']) ? intval($_GET['id']) : null;
        if ($driverId) {
            if ($driverManager->delete($driverId)) {
                http_response_code(200);
                echo json_encode(['message' => 'Driver deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Driver not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID is required']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
