<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::all();
        if (count($users) > 0) return response()->json($users);
        return response()->json([]);
    }

    public function getClients(Request $request): JsonResponse
    {
        $user = $request->session()->get('user');
        if (isset($user)) {
            $clients = User::find($user->id)->clients;
            if (count($clients) > 0) return response()->json($clients);
            return response()->json([]);
        } else {
            return response()->json('Unauthorized', 401);
        }
    }

    public function myInfo(Request $request): JsonResponse
    {
        $user = $request->session()->get('user');
        if (isset($user)) {
            return response()->json($user, 200);
        }
        return response()->json('Unauthorized', 401);
    }
}
