<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function get(Request $request)
    {
        if (config('app.env') == 'local') {
            $clientId = $request->input('client');
            if (empty($clientId)) return response()->json('Missing client id', 400);
            $transactions = Transaction
                ::where('client_id', $clientId)
                ->where('user_id', 1)->get();
            if (count($transactions) > 0) return response()->json($transactions);
            return response()->json([]);
        }

        $user = $request->session()->get('user');
        if (empty($user)) return response()->json(null, 401);
        $clientId = $request->input('client');
        if (empty($clientId)) return response()->json('Missing client id', 400);
        $transactions = Transaction
            ::where('client_id', $clientId)
            ->where('user_id', $user->id)->get();
        if (count($transactions) > 0) return response()->json($transactions);
        return response()->json([]);
    }

    public function store(Request $request): JsonResponse
    {
        if (config('app.env') == 'local') {
            $validator = Validator::make($request->all(), [
                'item' => 'required',
                'value' => 'required',
                'client_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->messages()->get('*'), 400);
            }

            $userId = '1';
            $client = Client::where('id', $request->input('client_id'))
                ->where('user_id', $userId)->first();

            $client = Client::where('id', $request->input('client_id'))
                ->where('user_id', $userId)->first();

            if (empty($client)) return response()->json('Client not found', 404);

            $paid = $request->input('paid');
            if (empty($paid)) $paid = $request->input('value');

            $transaction = new Transaction();
            $transaction['item'] = $request->input('item');
            $transaction['value'] = $request->input('value');
            $transaction['paid'] = $paid;
            $transaction['user_id'] = $userId;
            $transaction['client_id'] = $client->id;
            if ($transaction->save()) return response()->json('Salvo', 200);
            return response()->json(null, 500);
        }

        $user = $request->session()->get('user');
        if (empty($user)) return response()->json(null, 401);
        $validator = Validator::make($request->all(), [
            'item' => 'required',
            'value' => 'required',
            'client_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages()->get('*'), 400);
        }

        $client = Client::where('id', $request->input('client_id'))
            ->where('user_id', $user->id)->first();

        if (empty($client)) return response()->json('Client not found', 404);

        $paid = $request->input('paid');
        if (empty($paid)) $paid = $request->input('value');

        $transaction = new Transaction();
        $transaction['item'] = $request->input('item');
        $transaction['value'] = $request->input('value');
        $transaction['paid'] = $paid;
        $transaction['user_id'] = $user->id;
        $transaction['client_id'] = $client->id;

        if ($transaction->save()) {
            return response()->json('Salvo', 200);
        }
        return response()->json(null, 500);
    }

    public function update(Request $request, $id): JsonResponse
    {
        if (config('app.env') == 'local') {
            $transaction = Transaction::where('user_id', 1)
                ->where('id', $id)->first();

            if (empty($transaction)) return response()->json(null, 404);

            $client = Client::where('id', $transaction['client_id'])
                ->where('user_id', 1)->first();

            if (empty($client)) return response()->json(null, 404);

            $item = $request->input('item', false);
            $paid = $request->input('paid', false);
            $open = $request->input('open', false);

            if (!empty($item)) $transaction['item'] = $item;
            if (!empty($paid)) $transaction['paid'] = $paid;
            if (!empty($open)) $transaction['open'] = $open;

            if ($transaction->save()) return response()->json(null, 200);
            return response()->json(null, 500);
        }

        $user = $request->session()->get('user');
        if (empty($user)) return response()->json(null, 401);

        $transaction = Transaction::where('user_id', $user->id)
            ->where('id', $id)->first();

        if (empty($transaction)) return response()->json(null, 404);

        $client = Client::where('id', $transaction['client_id'])
            ->where('user_id', $user->id)->first();

        if (empty($client)) return response()->json(null, 404);

        $item = $request->input('item', false);
        $paid = $request->input('paid', false);
        $open = $request->input('open', false);

        if (!empty($item)) $transaction['item'] = $item;
        if (!empty($paid)) $transaction['paid'] = $paid;
        if (!empty($open)) $transaction['open'] = $open;

        if ($transaction->save()) return response()->json(null, 200);
        return response()->json(null, 500);
    }

    public function delete(Request $request, $id): JsonResponse
    {
        if (config('app.env') == 'local') {
            $transaction = Transaction::where('user_id', 1)
                ->where('id', $id)->first();
            if (empty($transaction)) return response()->json(null, 404);
            if ($transaction->delete()) return response()->json(null, 200);
            return response()->json(null, 500);
        }

        $user = $request->session()->get('user');
        if (empty($user)) return response()->json(null, 401);
        $transaction = Transaction::where('user_id', $user->id)
            ->where('id', $id)->first();
        if (empty($transaction)) return response()->json(null, 404);
        if ($transaction->delete()) return response()->json(null, 200);
    }
}
