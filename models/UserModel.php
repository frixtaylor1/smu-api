<?php

namespace SMU\Models;

use SMU\Constants\User as ConstantsUser;
use SMU\Core\ORM\Model;

class User extends Model 
{
    protected $id;
    protected $name;
    protected $email;
    protected $password;

    protected $table    = ConstantsUser::TABLE_NAME;
    protected $fillable = ['name', 'email', 'password'];

    protected function setId($id)
    {
        $this->id = (int)$id;
    }

    protected function setName($value)
    {
        $this->name = (string)$value;
    }

    protected function setEmail($value)
    {
        $this->email = (string)$value;
    }

    protected function setPassword($value)
    {
        $this->password = (string)$value;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
