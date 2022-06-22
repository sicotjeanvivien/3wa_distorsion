<?php

declare(strict_types=1);

/**
 * This is quite a complex controller. (:
 *
 * The main chat page has multiple roles:
 * – Displaying all categories and their respectives rooms;
 * – Navigating through rooms
 * – Displaying messages in the selected room
 * – Handling message creation
 */

require_once 'services/database.php';
require_once 'models/Category.php';
require_once 'models/Room.php';
require_once 'models/Message.php';

$pdo = getConnection();

$message = new Message();

// Handling message creation
if (isset($_POST['message']) && isset($_GET['room_id'])) {
    $roomId = (int) $_GET['room_id'];
    $content = trim($_POST['message']);

    // Hydrating message
    $message->setRoomId($roomId);
    $message->setContent($content);

    // Detecting errors
    $errors = $message->validate();

    if (empty($errors)) {
        $pdo = getConnection();
        $query = $pdo->prepare('
            INSERT INTO messages (content, room_id)
            VALUES (:content, :room_id)
        ');
        $query->bindValue(':content', $content);
        $query->bindValue(':room_id', $roomId);
        $query->execute();

        if ($pdo->errorInfo()[0] === '00000') {
            header("Location: index.php?page=chat&room_id=$roomId");
            exit;
        }
    }
}

// Selecting all categories with their respective rooms
$query = $pdo->prepare('
    SELECT
        rooms.id AS room_id, rooms.name AS room_name,
        categories.id AS category_id, categories.name AS category_name
    FROM categories
    LEFT JOIN rooms ON rooms.category_id = categories.id
    ORDER BY LOWER(categories.name) ASC, LOWER(rooms.name) ASC
');
$query->execute();

// Building multidimensional array from MySQL's flat categories results
$categories = [];
while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
    if (!isset($categories[$row['category_id']])) {
        $categories[$row['category_id']]['category'] = new Category(
            $row['category_name'],
            (int) $row['category_id']
        );
    }
    if ($row['room_id'] !== null) {
        $categories[$row['category_id']]['rooms'][] = new Room(
            (int) $row['category_id'],
            $row['room_name'],
            (int) $row['room_id']
        );
    }
}

// Getting messages for selected room, if any
$messages = [];
if (isset($_GET['room_id'])) {
    $query = $pdo->prepare('
        SELECT * FROM messages
        WHERE messages.room_id = :room_id
        ORDER BY created_at DESC, id DESC 
        LIMIT 200
    ');
    $query->bindValue(':room_id', $_GET['room_id']);
    $query->execute();
    while($row = $query->fetch(\PDO::FETCH_ASSOC)) {
        $messages[] = new Message(
            (int) $row['room_id'],
            $row['content'],
            (bool) $row['pinned'],
            new \DateTime($row['created_at']),
            (int) $row['id']
        );
    }
}

require 'views/chat/chat.phtml';
