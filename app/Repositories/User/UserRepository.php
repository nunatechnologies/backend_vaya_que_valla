<?php

namespace App\Repositories\User;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\RolSpatie;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function all()
    {
        return User::all();
    }

    public function allquery()
    {
        return User::query();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->find($id);
        $user->update($data);
        return $user;
    }

    public function find($id)
    {
        return User::find($id);
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function existePhone($phone, $user_id)
    {
        return User::where('phone', $phone)->first();
    }

    public function createToken($userId)
    {
        $token = rand(100000, 999999);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $this->find($userId)->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );
        return $token;
    }

    public function findByToken($token, $email)
    {
        $tokenRecord = DB::table('password_reset_tokens')->where('email', $email)->first();
        return $tokenRecord && Hash::check($token, $tokenRecord->token);
    }

    protected function encryptToken($token)
    {
        return Hash::make($token);
    }

    public function updatePassword($authProvider, $password)
    {
        return $authProvider->update(['password' => Hash::make($password)]);
    }

    //roles
    public function getAllRoles()
    {
        return Role::all();
    }
    public function getAsesorId()
    {
        return Role::where('name', 'Asesor')->first()->id;
    }
    public function getRandomAsesorId()
    {
        return User::whereHas('roles', fn($query) => $query->where('name', RolSpatie::ASESOR->name))
        ->inRandomOrder()
        ->value('id');
    }
}
