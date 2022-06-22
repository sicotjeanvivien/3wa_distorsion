<?php

declare(strict_types=1);

require_once 'models/AbstractModel.php';

class Message extends AbstractModel
{
    public function __construct(
        private ?int $roomId = null,
        private ?string $content = null,
        private bool $pinned = false,
        private ?\DateTime $createdAt = null,
        ?int $id = null
    ) {
        parent::__construct($id);
    }

    public function getRoomId(): ?int
    {
        return $this->roomId;
    }

    public function setRoomId(?int $roomId): void
    {
        $this->roomId = $roomId;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function isPinned(): bool
    {
        return $this->pinned;
    }

    public function setPinned(bool $pinned): void
    {
        $this->pinned = $pinned;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function validate(): array
    {
        $errors = [];

        if (strlen($this->content) > 500) {
            $errors[] = 'Le message ne doit pas excéder 500 caractères.';
        }
        if (strlen($this->content) < 1) {
            $errors[] = 'Le message ne doit pas être vide';
        }

        return $errors;
    }
}
