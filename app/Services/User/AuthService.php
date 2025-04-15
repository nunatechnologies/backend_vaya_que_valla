<?php

namespace App\Services\User;

use App\Enums\RolSpatie;
use App\Enums\UserType;
use App\Http\Messages\ErrorMessages;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAuthenticatedUser()
    {
        return JWTAuth::user();
    }

    public function changePassword($user, $currentPassword, $newPassword)
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception(ErrorMessages::INVALID_PASSWORD);
        }
        $user->password = Hash::make($newPassword);
        $user->save();
    }

    public function sendResetLink($data)
    {
        $user = $this->userRepository->findByEmail($data['email']) ?: throw new \Exception(ErrorMessages::USER_NOT_FOUND, 404);
        $token = $this->userRepository->createToken($user->id);
        $this->sendResetLinkEmail($user->email, $token);
        return true;
    }

    public function sendResetLinkEmail($email, $token)
    {
        // Mail::to($email)->send(new ResetPasswordMail($token));
    }

    public function authUser($email, $password)
    {
        $user = $this->userRepository->findByEmail($email)->with(['organization', 'person'])->first();
        if (!$user) {
            throw new \Exception(ErrorMessages::USER_NOT_FOUND, 401);
        }
        if (!Hash::check($password, $user->password)) {
            throw new \Exception(ErrorMessages::INVALID_CREDENTIALS, 401);
        }
        return $user;
    }

    public function registerUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'cod_phone' => $data['cod_phone'] ?? '',
            'phone' => $data['phone'] ?? '',
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'user_type' => $data['user_type'],
        ]);
    
        // Crear relación según el tipo de usuario
        if ($data['user_type'] === UserType::PERSON->name) {
            $user->person()->create([
                'user_id' => $user->id,
                'ci' => $data['ci'],
            ]);
            $user->assignRole(RolSpatie::ANUNCIANTE->name);
        }
    
        if ($data['user_type'] === UserType::ORGANIZATION->name) {
            $user->organization()->create([
                'user_id' => $user->id,
                'social_reason' => $data['social_reason'],
                'name_contact' => $data['name_contact'],
                'phone_contact' => $data['phone_contact'],
                'commision_percentage' => $data['commision_percentage'],
            ]);
            $user->assignRole($data['role']);
        }
    
        $user->sendEmailVerificationNotification();
    
        return $user;
    }
}
