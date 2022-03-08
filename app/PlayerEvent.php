<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayerEvent extends Model
{
    //
    protected $table = 'tbl_player_event';
    protected $fillable = ['position_txt','mlb_team_txt','id_player','photo_txt'];
    public $timestamps = false;
}

