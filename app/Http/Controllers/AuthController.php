<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|unique:users|max:255',
            'password' => 'required|min:8',
            'address'=>'required',
            'phone' => 'required',
            'city' => 'required',
            'gender' => 'required'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
            'city' => $request->city,
            'gender' => $request->gender,
            'role_id' => $request->role_id,
            'avatar' => asset('images/blank-profile-picture-973460_960_720.webp')
        ]);
        $token = $user->createToken('auth_token')->accessToken;
        $success['name'] = $user->name;
        $success['email'] = $user->email;
        $success['token'] = $token;
        return $this->sendResponse($success, "User Registered", 200);
    }

    public function login(Request $request)
    {
        // $request->validate();
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->accessToken;
            $success['token'] =  $token;
            $success['name'] =  $user->name;
            return $this->sendResponse($success, 'User login successfully.', 200);
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], 401);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->sendResponse([], 'User logout successfully.', 200);
    }
}
