<?php

namespace App\Http\Controllers;

use App\User;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    // Регистрация
    public function signup(Request $r) {

        $validate = Validator::make($r->all(), [
            'first_name' => 'required|max:255',
            'surname' => 'required|max:255',
            'phone' => 'required|min:11|max:11|unique:users',
            'password' => 'required|max:255'
        ]);

        if($validate->fails()) return response()->json($validate->errors(), 422);

        $user = new User();
        $user->first_name = $r->get('first_name');
        $user->surname = $r->get('surname');
        $user->phone = $r->get('phone');
        $user->password = $r->get('password');
        $user->api_token = Str::random(80);
        $user->save();

        return response()->json(['id' => $user->id], 201);

    }

    // Авторизация
    public function login(Request $r) {

        $validate = Validator::make($r->all(), [
            'phone' => 'required|min:11|max:11',
            'password' => 'required|max:255'
        ]);

        if($validate->fails()) return response()->json($validate->errors(), 422);

        $user = User::where('phone', $r->get('phone'))->where('password', $r->get('password'))->first();
        if(!$user) return response()->json(["login" => "Incorrect login or password"], 404);
		
		if(!$user->api_token) {
			$user->api_token = Str::random(80);
			$user->save();
		}

        return response()->json(["token" => $user->api_token], 200);

    }

    // Выход
    public function logout(Request $r) {

        $this->user->api_token = Str::random(80);
        $this->user->save();

        return response()->json("", 200);
    }

}
