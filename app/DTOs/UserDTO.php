<?php

namespace App\DTOs;

readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $phoneNumber = null,
        public ?bool $twoFaEnabled = false,
        public ?bool $isActive = true,
    ) {}

    /**
     * Create a UserDTO from an array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            phoneNumber: $data['phone_number'] ?? null,
            twoFaEnabled: $data['two_fa_enabled'] ?? false,
            isActive: $data['is_active'] ?? true,
        );
    }

    /**
     * Convert DTO to array for database operations.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'phone_number' => $this->phoneNumber,
            'two_fa_enabled' => $this->twoFaEnabled,
            'is_active' => $this->isActive,
        ];
    }
}
