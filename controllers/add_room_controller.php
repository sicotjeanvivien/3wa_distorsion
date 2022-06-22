<?php

declare(strict_types=1);

require_once 'models/Category.php';
require_once 'models/Room.php';
require_once 'services/database.php';

if (!isset($_GET['category_id'])) {
    header('Location: index.php?page=chat');
    exit;
}


$pdo = getConnection();
$stmt = $pdo->prepare('SELECT * FROM categories WHERE categories.id = :category_id');
$stmt->bindValue(':category_id', $_GET['category_id']);
$stmt->execute();
if ($stmt->rowCount() === 0) {
    header('Location: index.php?page=chat');
    exit;
}
$result = $stmt->fetch(\PDO::FETCH_ASSOC);

$category = new Category();
$category->setId((int) $result['id']);
$category->setName($result['name']);

$room = new Room();

if (isset($_POST['name'])) {
    $name = ucfirst(trim($_POST['name']));
    $categoryId = (int) $_GET['category_id'];

    // Hydrating room
    $room->setName($name);
    $room->setCategoryId($categoryId);

    // Detecting errors
    $errors = $room->validate();

    if (empty($errors)) {
        $pdo = getConnection();
        $stmt = $pdo->prepare('
            INSERT INTO rooms (name, category_id)
            VALUES (:name, :category_id)
        ');
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':category_id', $categoryId);
        $stmt->execute();

        if ($pdo->errorInfo()[0] === '00000') {
            header('Location: index.php?page=chat');
            exit;
        }
    }
}

require 'views/room/add_room.phtml';
