<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\UserRoles;

final class UserUpdateDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?UserRoles $role = null,
        public readonly ?string $phone = null,
        public readonly ?string $address = null,
    ) {}

    public function toModel(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->email !== null) {
            $data['email'] = $this->email;
        }

        if ($this->password !== null) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->role !== null) {
            $data['role'] = $this->role->value;
        }

        if ($this->phone !== null) {
            $data['phone'] = $this->phone;
        }

        if ($this->address !== null) {
            $data['address'] = $this->address;
        }

        return $data;
    }
}
