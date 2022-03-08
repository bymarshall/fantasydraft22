<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    //
    protected $table = 'tbl_auction';
    protected $fillable = ['final_prize_int', 'position_txt', 'id_teams_event', 'id_player_event','auct_status_int'];
    public $timestamps = false;
}