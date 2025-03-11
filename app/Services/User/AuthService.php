<?php

namespace App\Services\User;

use App\Http\Messages\ErrorMessages;
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
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            throw new \Exception(ErrorMessages::USER_NOT_FOUND, 401);
        }
        if (!Hash::check($password, $user->password)) {
            throw new \Exception(ErrorMessages::INVALID_CREDENTIALS, 401);
        }
        return $user;
    }
}
