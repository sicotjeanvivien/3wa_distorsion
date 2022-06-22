<?php

declare(strict_types=1);

require_once 'services/database.php';
require_once 'models/AbstractModel.php';

class Category extends AbstractModel
{
    public function __construct(
        private ?string $name = null,
        ?int $id = null
    ) {
        parent::__construct($id);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function validate(): array
    {
        $errors = [];

        // Checking name unicity
        $pdo = getConnection();
        $stmt = $pdo->prepare('SELECT categories.id FROM categories WHERE categories.name = :name');
        $stmt->bindValue(':name', $this->name);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $errors[] = "La catégorie « {$this->name} » existe déjà.";
        }

        // Checking name length
        if (strlen($this->name) > 60) {
            $errors[] = 'Le nom de la catégorie ne doit pas excéder 60 caractères.';
        }
        if (strlen($this->name) < 1) {
            $errors[] = 'Le nom de la catégorie ne doit pas être vide.';
        }

        return $errors;
    }
}
