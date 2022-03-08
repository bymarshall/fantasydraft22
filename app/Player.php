<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    //
    protected $table = 'tbl_player';
    protected $fillable = ['name_txt'];
    public $timestamps = false;
}