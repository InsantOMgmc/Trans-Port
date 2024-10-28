<?php
// Подключение CORS
require '../../cors.php';

// Проверка на админа
require '../checkAdmin.php';
checkAdmin();

// Данные для подключения к базе данных
require '../../config/config.php';

try {
    // Получение данных из тела запроса
    $data = json_decode(file_get_contents("php://input"), true);
    $id = isset($data['id']) ? intval($data['id']) : null;
    $name = isset($data['name']) ? $data['name'] : null;
    $email = isset($data['email']) ? $data['email'] : null;

    // Проверка на наличие необходимых данных
    if ($id === null || $name === null || $email === null) {
        http_response_code(400);
        echo json_encode(['error' => 'ID, name, and email are required']);
        exit;
    }

    // Обновление данных пользователя
    $updateQuery = "UPDATE users SET name = ?, email = ? WHERE id = ?";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([$name, $email, $id]);

    // Проверка успешности обновления
    if ($updateStmt->rowCount() > 0) {
        echo json_encode(['message' => 'User updated successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found or no changes made']);
    }

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid token']);
}

$pdo = null;