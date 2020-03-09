<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';

    public $timestamps = false;

    protected $casts = [
        'customer_id' => 'integer',
        'invoice_id' => 'integer',
        'amount' => 'double',
    ];
}
