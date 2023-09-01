<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaction';

    protected $attributes = [
        'paid' => '0',
        'open' => true
    ];

    protected $fillable = [
        'value', 'item', 'user_id', 'client_id', 'paid', 'open'
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}
