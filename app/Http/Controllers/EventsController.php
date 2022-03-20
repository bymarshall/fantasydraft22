<?php

namespace App\Http\Controllers;

use App\Events\AuctionCreated;
use App\Events\AuctionLoaded;
use App\Events\AuctionClosed;
use App\Events\AuctionDeleted;
use App\Events\FantasyTeams;
use App\Events\WinningBids;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Integer;
use Pusher\Laravel\Facades\Pusher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Auction;
use App\Player;
use App\PlayerEvent;


class EventsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $id_event = \DB::table('tbl_event')->select('id_event')->where('status_int', 1)->get();
        config(['app.event' => $id_event[0]->id_event]);        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        $id_user = Auth::user()->id;
        // Trae la informacion del equipo
        $data_team = \DB::table('tbl_teams')
            ->leftJoin('tbl_teams_event', 'tbl_teams.id_team', '=', 'tbl_teams_event.id_team')
            ->leftJoin('tbl_event', 'tbl_teams_event.id_event', '=', 'tbl_event.id_event')
            ->select('tbl_teams_event.id_teams_event', 'tbl_teams.name_txt', 'tbl_teams.avatar_txt', 'tbl_event.id_event')
            ->where('tbl_teams_event.status_int', 1)
            ->where('tbl_event.status_int', 1)
            ->where('tbl_teams.id_user', $id_user)
            ->get();
        //Trae los eventos disponibles
        $events = \DB::table('tbl_event')->select('*')->get();        
        // user role
        $user_role = \DB::table('tbl_user_role')->where('id_user', $id_user)->where('status_int', 1)->get();
        if(!$user_role)
        {
            Log::channel('stderr')->info("EVENTS INDEX EL USUARIO ".$id_user." NO TIENE ROL DEFINIDO");
            abort(404);
        }
        return view('events', ['id_user'=> $id_user, 'user_role' => $user_role,'data_team' => $data_team, 'events' => $events]);
    }

    public function searchevents(Request $request)
    {
        $responses = array();
        //Detalles del Evento
        $eventinfo = \DB::table('tbl_event')
                        ->select('*')
                        ->where('id_event', $request->get('idEvent'))
                        ->get();

        array_push($responses, $eventinfo);
        $id_team_event = \DB::table('tbl_teams')
                            ->leftJoin('tbl_teams_event','tbl_teams_event.id_team','=','tbl_teams.id_team')
                            ->select('tbl_teams_event.id_teams_event')
                            ->where('tbl_teams_event.id_event', $request->get('idEvent'))
                            ->where('tbl_teams.id_user',Auth::user()->id)
                            ->first();

        Log::channel('stderr')->info("BUSCA EVENTO: ".json_encode($eventinfo));
        Log::channel('stderr')->info("BUSCA EVENTO: ".json_encode($id_team_event));
        
        //Detalles del equipo en el evento seleccionado
        $query_teams_event ='SELECT auct.id_auction idBid, pl.name_txt Jugador, auct.final_prize_int PrecioFinal,';
        $query_teams_event .='tm.name_txt FantasyTeam, auct.position_txt posPlayer, pe.photo_txt pl_avatar, ';
        $query_teams_event .='te.budget budget, tm.avatar_txt team_avatar, pe.mlb_team_txt MLB, ';
        $query_teams_event .='pe.id_player_event IdPlayerEvent  FROM tbl_auction auct ';
        $query_teams_event .='LEFT JOIN tbl_player_event pe ON (auct.id_player_event = pe.id_player_event)  ';
        $query_teams_event .='LEFT JOIN tbl_event evt ON (pe.id_event = evt.id_event) ';
        $query_teams_event .='LEFT JOIN tbl_player pl ON (pe.id_player = pl.id_player) ';
        $query_teams_event .='LEFT JOIN tbl_teams_event te ON (auct.id_teams_event = te.id_teams_event) ';
        $query_teams_event .='LEFT JOIN tbl_teams tm ON (te.id_team = tm.id_team)  ';
        $query_teams_event .='WHERE auct.winningbid = 1 AND auct.auct_status_int = 1 AND evt.id_event = '.$request->get('idEvent');
        $query_teams_event .=' AND auct.id_teams_event = '.$id_team_event->id_teams_event.' AND auct.id_event = '.$request->get('idEvent');
        $query_teams_event .=' ORDER BY posPlayer ';
        $your_team = \DB::select($query_teams_event);

        Log::channel('stderr')->info("EVENTS BUSCA TEAM EVENTO: ".json_encode($your_team));   
        array_push($responses, $your_team);

        //Detalles del equipo en el evento seleccionado
        $query_event ='SELECT auct.id_auction idBid, pl.name_txt Jugador, auct.final_prize_int PrecioFinal,';
        $query_event .='tm.name_txt FantasyTeam, auct.position_txt posPlayer, pe.photo_txt pl_avatar, ';
        $query_event .='te.budget budget, tm.avatar_txt team_avatar, pe.mlb_team_txt MLB, ';
        $query_event .='pe.id_player_event IdPlayerEvent  FROM tbl_auction auct ';
        $query_event .='LEFT JOIN tbl_player_event pe ON (auct.id_player_event = pe.id_player_event) ';
        $query_event .='LEFT JOIN tbl_event evt ON (pe.id_event = evt.id_event) ';
        $query_event .='LEFT JOIN tbl_player pl ON (pe.id_player = pl.id_player)';
        $query_event .='LEFT JOIN tbl_teams_event te ON (auct.id_teams_event = te.id_teams_event) ';
        $query_event .='LEFT JOIN tbl_teams tm ON (te.id_team = tm.id_team) ';
        $query_event .='WHERE auct.winningbid = 1 AND auct.auct_status_int = 1 AND evt.id_event = '.$request->get('idEvent');
        $query_event .=' AND auct.id_teams_event in (SELECT `id_teams_event` FROM `tbl_teams_event` WHERE `id_event` = '.$request->get('idEvent').') ';
        $query_event .=' AND auct.id_event = '.$request->get('idEvent').' ORDER BY FantasyTeam, posPlayer';
        $all_teams = \DB::select($query_event);
        array_push($responses, $all_teams);

        echo json_encode($responses);    
    }
}