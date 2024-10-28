<?php
require '../cors.php';
require '../vendor/autoload.php'; // Подключение библиотеки JWT

use \Firebase\JWT\JWT;

require '../config/config.php';
// Получение данных из тела запроса
$data = json_decode(file_get_contents("php://input"));

// Проверка на наличие имени и пароля
if (!isset($data->name, $data->password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Подготовка запроса на поиск пользователя
$stmt = $pdo->prepare("SELECT id, name, role, password FROM users WHERE name = ?");
$stmt->execute([$data->name]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// if (!$user || !password_verify($data->password, $user['password'])) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Invalid credentials']);
//     exit;
// }

// Генерация JWT токена
$secretKey = 'talgat'; // Замените на ваш секретный ключ
$issuedAt = time();
$expirationTime = $issuedAt + 1800; // Токен действителен 30 мин

$payload = [
    'iat' => $issuedAt,
    'exp' => $expirationTime,
    'id' => $user['id'],
    'name' => $user['name'],
    'role' => $user['role']
];

// Кодируем JWT
$jwt = JWT::encode($payload, $secretKey, 'HS256');

// Возвращаем токен и информацию о пользователе
echo json_encode([
    'token' => $jwt,
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'role' => $user['role']
    ]
]);

// Закрываем соединение с базой данных
$conn = null;
?>