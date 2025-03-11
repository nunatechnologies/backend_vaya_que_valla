<?php

namespace App\Repositories\User;


interface UserRepositoryInterface
{
    public function all();
    public function allquery();

    public function create(array $data);

    public function update($id, array $data);

    public function find($id);

    public function findByEmail($email);

    public function existePhone($phone, $user_id);

    public function createToken($userId);

    public function findByToken($token, $email);

    public function updatePassword($authProvider, $password);

    public function getAllRoles();
    public function getAsesorId();
    public function getRandomAsesorId();


}
