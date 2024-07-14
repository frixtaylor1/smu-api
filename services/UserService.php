<?php

namespace SMU\Services;

use SMU\Models\User as UserModel;

class User
{
    /**
     * Get user/s 
     *
     * @params int $id
     *
     * @return UserModel|array<UserModel>
     */
    public function getUsers(int $id = null): null|UserModel|array
    {
        $userModel = new UserModel();
        $result = $id ? $userModel->find($id) : $userModel->all();
        return $result;
    }

    /**
     * Removes an user
     * 
     * @params int $id
     *
     * @return array
     *
     */
    public function deletUser(int $id): array
    {
        return [];
    }

    /**
     * Updates an user
     *
     * @params int $id
     * @params array $userdata
     *
     */
}
