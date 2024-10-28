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
$query = 'SELECT id, name, role, email FROM users WHERE id = ?';
$stmt = $pdo->prepare($query);

// Выполнение запроса
try {
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Возвращаем данные пользователя
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to retrieve user']);
}

// Закрываем соединение с базой данных
$pdo = null;