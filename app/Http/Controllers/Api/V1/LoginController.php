<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\LoginResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request) {
        $data = $request->all();
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password']
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return new LoginResource(auth()->user());
        }else {
            return response()->json([
                'Token' => [],
                'message'=> "User Not Authorize",
            ], 401);
        }
    }
}
