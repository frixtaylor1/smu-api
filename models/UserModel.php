<?php

namespace SMU\Models;

use SMU\Core\Constants\DBTypes;
use SMU\Core\ORM\Model;

class User extends Model
{
    protected $id;
    protected $email;
    protected $password;
    protected $name;
    protected $address;

    protected function getTableConfig(): array
    {
        return [
            'tableName' => 'users',
            'fillable'  => [
                'email',
                'password',
                'name',
                'address'
            ]
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail($email): self
    {
        $this->email = (string)$email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword($password): self
    {
        $this->password = (string)$password;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = (string)$name;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress($address): self
    {
        $this->address = (string)$address;

        return $this;
    }

    public function findByEmail($email): ?self
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE email = ?", [[DBTypes::VARCHAR => $email]]);
        return !empty($result) ? false : $this->mapResult($result);
    }
}