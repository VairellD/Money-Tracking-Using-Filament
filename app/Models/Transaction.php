<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'transactions';
    protected $casts = [
        'amount' => MoneyCast::class,
    ];
    protected $fillable = ["user_id","account_id","category_id","name","transaction_date","is_income","amount","notes"];

    public $timestamps = true;

    public function account()
    {
        // return $this->belongsTo(Accounts::class, 'account_id');
        return $this->belongsTo(Accounts::class);
    }

    public function category()
    {
        // return $this->belongsTo(Category::class, 'category_id');
        return $this->belongsTo(Category::class, );
    }

    public function user()
    {
        // return $this->belongsTo(User::class, 'user_id');
        return $this->belongsTo(User::class);
    }

}
