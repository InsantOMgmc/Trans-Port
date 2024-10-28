<?php
// Подключение библиотеки JWT
require '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Функция для проверки роли администратора
function checkAdmin()
{
    // Получение JWT токена из заголовков
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authorization header not found']);
        exit;
    }

    $jwt = str_replace('Bearer ', '', $headers['Authorization']);
    $secretKey = 'talgat';

    try {
        // Декодирование JWT
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

        // Проверка роли
        if ($decoded->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
}