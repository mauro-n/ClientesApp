<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function authenticate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'pwd' => 'required'
        ], ['required' => ':attribute cannot be null']);

        if ($validator->fails()) {
            return response()->json($validator->messages()->get('*'), 400);
        }

        if (Auth::attempt(
            [
                'name' => $request->input('name'),
                'password' => $request->input('pwd')
            ]
        )) {
            $request->session()->put('user', Auth::user());
            return response()->json(Auth::user(), 200);
        }

        return response()->json('Login ou senha inválidos', 401);
    }

    public function whoami(Request $request): JsonResponse
    {
        if (config('app.env') == 'local') {
            $user = User::where('id', 1)->first();
            if (empty($user)) return response()->json(null, 404);
            return response()->json($user, 200);
        }
        
        $me = $request->session()->get('user');
        if (empty($me)) return response()->json(null, 401);
        return response()->json($me, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users',
            'email' => 'required|unique:users',
            'pwd' => 'required'
        ], ['required' => ':attribute cannot be null']);

        if ($validator->fails()) {
            return response()->json($validator->messages()->get('*'), 400);
        }

        $pwd = Hash::make($request->input('pwd'));

        $user = new User();
        $user['name'] = $request->input('name');
        $user['email'] = $request->input('email');
        $user['password'] = $pwd;

        if ($user->save()) {
            $request->session()->put('user', $user);
            return response()->json(null, 200);
        }
        return response()->json(null, 500);;
    }

    public function logout(Request $request) {
        $request->session()->flush();
        return response()->json(null, 200);
    }
}
