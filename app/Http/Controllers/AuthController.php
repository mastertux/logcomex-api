<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUser;
use App\Services\UserService;
use App\Utils\ApiResponseClass;
use App\Http\Resources\UserResource;


class AuthController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService) 
    {
        $this->userService = $userService;
    }
    
    public function register(RegisterUser $registerUser) 
    {
        $userDaata = [
            'name' => $registerUser->name,
            'email' => $registerUser->email,
            'password' => bcrypt($registerUser->password)
        ];

        try{
            $user = $this->userService->store($userDaata);
            return ApiResponseClass::sendResponse(new UserResource($user), 'User created successful', 201);
        }catch(\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        } 
    }

    public function login()
    {
        $credentials = request(['email', 'password']);
  
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
  
        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth()->logout();
  
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me() {
        return response()->json(auth()->user());
    }

    public function refresh() {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
