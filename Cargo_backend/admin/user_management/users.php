<?php
// Подключение CORS
require '../../cors.php';

// Проверка на админа
require '../checkAdmin.php';
checkAdmin();

// Данные для подключения к базе данных
require '../../config/config.php';

// Запрос на получение списка пользователей, исключая администраторов
$stmt = $pdo->prepare("SELECT id, name, role, email FROM users WHERE role != 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Возвращаем список пользователей
echo json_encode($users);

// Закрываем соединение с базой данных
$pdo = null;
?>
    