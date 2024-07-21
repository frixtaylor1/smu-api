<?php

namespace SMU\Services;

use Error;
use SMU\Core\ValidatorResult;
use SMU\Models\User as UserModel;

class User
{
    /**
     * Get user/s 
     *
     * @param int $id
     * @param int $nbOfRows
     * @param int $offset
     * 
     * @return null|UserModel|array<UserModel>
     */
    public function getUsers(int $id = null, int $nbOfRows = null, int $offset = null): null|UserModel|array
    {
        $userModel = new UserModel();
        $result = $id ? $userModel->find($id) : $userModel->all();

        $data   = [];
        if (is_array($result)) {
            foreach ($result as $user) {
                $data[] = [
                    "id"     => $user->getId(),
                    "nombre" => $user->getName(),
                    "email"  => $user->getEmail()
                ];
            }
        }

        if (!is_array($result) && $result) {
            $data = [
                "id"     => $result->getId(),
                "nombre" => $result->getName(),
                "email"  => $result->getEmail(),
            ];
        }
        
        return $data;
    }

    /**
     * Creates an user
     * 
     * @param \SMU\Core\ValidatorResult $validatedData
     *
     * @return array
     *
     */
    public function createUser(ValidatorResult $validatedData): array
    {
        try {
            $user = new UserModel();
            
            if ($user->findByEmail($validatedData->getParam('email'))) {
                return [
                    'status'  => false,
                    'message' => "user with email {$validatedData->getParam('email')} exist!"
                ];
            }

            $user
                ->setEmail($validatedData->getParam('email'))
                ->setPassword($validatedData->getParam('password'))
                ->setName($validatedData->getParam('name'))
                ->setAddress($validatedData->getParam('address'))
                ->save();

            $savedUser = $user->save();

            $data = [
                "email"=>$validatedData->getParam('email'),
                "password"=>$validatedData->getParam('password'),
                "name"=>$validatedData->getParam('name'),
                "address"=>$validatedData->getParam('address'),
            ];
            var_dump($data);
            return [
                'status'  => true,
                'id_user' => $savedUser->getId(),
            ];
        } catch (Error $err) {
            throw $err;
        }
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
     * @params array $newData
     *
     */
     public function updateUser(array $newData): array
     {
         return [];
     }
}
