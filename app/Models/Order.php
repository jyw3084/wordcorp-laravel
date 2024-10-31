<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	use HasDateTimeFormatter;

    public function client()
    {
        return $this->belongsTo(User::class, 'email', 'email')->where('user_type', 2);
    }
}
