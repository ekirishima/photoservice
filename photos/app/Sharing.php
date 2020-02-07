<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sharing extends Model
{

    public $table = 'shares';
    
    protected $fillable = [
        'user_id', 'photo_id',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function photo() {
        return $this->belongsTo("App\Photo");
    }

}
