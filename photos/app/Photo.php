<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    
    public $table = 'photos';
    
    protected $fillable = [
        'name', 'url', 'id',
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'owner_id'
    ];

}
