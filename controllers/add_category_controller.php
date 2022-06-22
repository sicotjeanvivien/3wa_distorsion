<?php

declare(strict_types=1);

require_once 'models/Category.php';
require_once 'services/database.php';

$category = new Category();
$errors = [];

if (isset($_POST['name'])) {
    $name = ucfirst(trim($_POST['name']));

    // Hydrating category
    $category->setName($name);

    // Detecting errors
    $errors = $category->validate();
    // Inserting in DB only if errors array is empty
    if (empty($errors)) {
        $pdo = getConnection();
        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
        $stmt->bindValue(':name', $name);
        $stmt->execute();

        if ($pdo->errorInfo()[0] === '00000') {
            header('Location: index.php?page=chat');
            exit;
        }
    }
}

require 'views/category/add_category.phtml';
