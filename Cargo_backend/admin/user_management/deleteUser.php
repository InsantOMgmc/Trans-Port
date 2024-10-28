<?php
// Подключение CORS
require '../../cors.php';

// Проверка на админа
require '../checkAdmin.php';
checkAdmin();

// Данные для подключения к базе данных
require '../../config/config.php';

// Получение ID пользователя из запроса
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($id === null) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID is required']);
    exit;
}

// Подготовка SQL-запроса
$query = 'DELETE FROM users WHERE id = ?';
$stmt = $pdo->prepare($query);

// Выполнение запроса
try {
    $stmt->execute([$id]);
    $rowsAffected = $stmt->rowCount();

    if ($rowsAffected > 0) {
        echo json_encode(['message' => 'User deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Нельзя удалить пользователя у которого есть активные заказы']);
}

// Закрываем соединение с базой данных
$pdo = null;