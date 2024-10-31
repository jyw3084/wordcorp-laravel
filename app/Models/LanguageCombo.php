<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class LanguageCombo extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'language_combo';
    
    public function from()
    {
        return $this->belongsTo(Language::class, 'language_from', 'code');
    }

    public function to()
    {
        return $this->belongsTo(Language::class, 'language_to', 'code');
    }
}
