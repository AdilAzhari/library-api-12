<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\UserRoles;

final class UserCreateDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly UserRoles $role = UserRoles::MEMBER,
        public readonly ?string $phone = null,
        public readonly ?string $address = null,
    ) {}

    public function toModel(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role' => $this->role->value,
            'phone' => $this->phone,
            'address' => $this->address,
        ];
    }
}
