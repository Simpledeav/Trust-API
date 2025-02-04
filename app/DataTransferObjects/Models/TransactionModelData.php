<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Models;

use App\Contracts\Auth\HasTransactionPin;

class TransactionModelData
{
    private string $userId;
    private float $amount;
    private string $transactableId;
    private string $transactableType;
    private string $type;
    private string $status;
    private ?string $comment;

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function setTransactableId(string $transactableId): self
    {
        $this->transactableId = $transactableId;
        return $this;
    }

    public function setTransactableType(string $transactableType): self
    {
        $this->transactableType = $transactableType;
        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'transactable_id' => $this->transactableId,
            'transactable_type' => $this->transactableType,
            'type' => $this->type,
            'status' => $this->status,
            'comment' => $this->comment,
        ];
    }
}
