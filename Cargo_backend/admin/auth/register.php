<?php
// Подключение CORS
require '../../cors.php';

// Данные для подключения к базе данных
require '../../config/config.php';


// Получение данных из тела запроса
$data = json_decode(file_get_contents("php://input"));

// Проверка на наличие необходимых полей
if (!isset($data->name, $data->email, $data->password, $data->role)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Хеширование пароля
$hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);

// Подготовка SQL-запроса
$query = 'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)';
$stmt = $pdo->prepare($query);

// Выполнение запроса
try {
    $stmt->execute([$data->name, $data->email, $hashedPassword, $data->role]);
    http_response_code(201); // Успешное создание
    echo json_encode(['message' => 'User created successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create user']);
}

// Закрываем соединение с базой данных
$pdo = null;