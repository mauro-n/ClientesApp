<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /* Make sure always return array */
        $user = $request->session()->get('user');
        if (empty($user)) return response()->json(null, 401);
        $clients = Client::where('user_id', $user->id)->get();
        if (count($clients) > 0) return response()->json($clients);
        return response()->json([]);
    }
    
    public function getClient(Request $request, $id): JsonResponse
    {
        $user = $request->session()->get('user');
        if (empty($user)) return response()->json(null, 401);

        $client = Client::where('id', $id)->where('user_id', $user->id)->first();
        //$client = Client::find($id);
        if (empty($client)) return response()->json(null, 404);

        $transactions = $client->transactions;
        $totalBought = $transactions->sum('value');
        $totalPaid = $transactions->sum('paid');
        $totalDebt = $totalBought - $totalPaid;
        $client['transactions'] = $transactions;
        $client['totalBought'] = $totalBought;
        $client['totalDebt'] = $totalDebt;
        return response()->json($client);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->session()->get('user');
        if (empty($user)) return response()->json(null, 401);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'max:100'],
                'address' => ['max:250'],
                'age' => ['max:3'],
                'sex' => ['max:15'],
            ],
            ['required' => ':attribute não pode ser nulo'],
            [
                'name' => 'Nome',
                'address', 'Endereço',
                'age', 'Idade',
                'sex', 'Genêro'
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->messages()->get('*'), 400);
        }

        $client = new Client();
        $client['name'] = $request->input('name');
        $client['age'] = $request->input('age');
        $client['sex'] = $request->input('sex');
        $client['address'] = $request->input('address');
        $client['user_id'] = $user->id;

        if ($client->save()) {
            return response()->json('Registro criado com sucesso', 200);
        }

        return response()->json(null, 500);
    }


    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->session()->get('user');
        if (empty($user)) return response()->json(null, 401);

        $client = Client::find($id);
        if (empty($client)) return response()->json(null, 404);
        if ($client['user_id'] !== $user->id) return response()->json(null, 403);
        if ($client->delete()) return response()->json(null, 200);
    }
}
