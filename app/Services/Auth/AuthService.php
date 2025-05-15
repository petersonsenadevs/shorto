<?php 
namespace App\Services\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use App\Services\Token\TokenService;
use Illuminate\Support\Facades\Hash;

class AuthService{

    public function __construct(private TokenService $tokenService){}


    public function login($email, $password){
        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException();
        }
        return $this->tokenService->generateToken($user);
    }
}