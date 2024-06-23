<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class Accounts extends Model
{
    use HasFactory;

    protected $table = 'accounts';
    protected $fillable = ["name"];
    
    public $timestamps = true;

    public function transactions()
    {
        // return $this->hasMany(Transaction::class, 'account_id');
        return $this->hasMany(Transaction::class, );
    }
}
