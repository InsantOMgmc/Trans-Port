<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // Указываем конкретный источник
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS"); // Разрешенные методы
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Разрешенные заголовки

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // Нет содержимого
    exit;
}