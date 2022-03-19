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
use App\Auction;
use App\Player;
use App\PlayerEvent;


class AuctionSettingsController extends Controller
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
            ->where('tbl_teams_event.status_int', 1)->where('tbl_event.status_int', 1)->where('tbl_teams.id_user', $id_user)
            ->get();

        //Traemos los favoritos del Equipo
        $data_favs_players = \DB::table('tbl_teams_event_favs_player')
            ->leftJoin('tbl_teams_event', 'tbl_teams_event_favs_player.id_teams_event', '=', 'tbl_teams_event.id_teams_event')
            ->leftJoin('tbl_teams', 'tbl_teams_event.id_team', '=', 'tbl_teams.id_team')
            ->leftJoin('tbl_event', 'tbl_teams_event_favs_player.id_event', '=', 'tbl_event.id_event')
            ->leftJoin('tbl_players_api', 'tbl_teams_event_favs_player.PlayerID', '=', 'tbl_players_api.PlayerID')
            ->select('tbl_teams_event_favs_player.PlayerID', 'tbl_teams_event_favs_player.id_event', 
            'tbl_teams_event_favs_player.id_teams_event','tbl_players_api.FirstName', 'tbl_players_api.LastName',
            'tbl_players_api.PhotoUrl','tbl_players_api.last_year_points','tbl_players_api.ranking', 'tbl_players_api.YahooPrice')
            ->distinct()
            ->where('tbl_teams_event.status_int', 1)->where('tbl_event.status_int', 1)->where('tbl_teams.id_user', $id_user)
            ->where('tbl_players_api.id_event', config('app.event'))
            ->get();

        // user role
        $user_role = \DB::table('tbl_user_role')->where('id_user', $id_user)->where('status_int', 1)->get();
        if(!$user_role)
        {
            abort(404);
        }
        return view('settings', ['id_user'=> $id_user, 'user_role' => $user_role,'data_team' => $data_team,
                    'data_favs' => $data_favs_players]);
    }

    //Agrega Jugador a Favoritos
    function addPlayerToFavs(Request $request)
    {
        $idPlayer = trim($request->get('idPlayer'));
        $idTeamsEvent = trim($request->get('idTeamsEvents'));
        //Vemos si ya no esta agregado
        $playerToAdd = \DB::table('tbl_teams_event_favs_player')
                    ->select('PlayerID')
                    ->where('PlayerID', $idPlayer)
                    ->where('id_teams_event', $idTeamsEvent)
                    ->where('id_event', config('app.event'))
                    ->first();

        if(! $playerToAdd)
        {
            \DB::table('tbl_teams_event_favs_player')
            ->insert(['id_teams_event'=> $idTeamsEvent, 'PlayerID' => $idPlayer, 'id_event' => config('app.event')]);
            $output = 'Se ha agregado el jugador a Favoritos Exitosamente';
        }
        else
        {
            $output = 'El jugador ya existe en Favoritos!';
        }
        
        echo json_encode($output);
    }

    //Elimina Jugador de Favoritos
    function deleteFavs(Request $request)
    {
        $idFav = trim($request->get('idFav'));
        $idTeamsEvent = trim($request->get('idTeamsEvents'));

        \DB::table('tbl_teams_event_favs_player')
        ->where('PlayerID', $idFav)
        ->where('id_teams_event', $idTeamsEvent)
        ->where('id_event', config('app.event'))
        ->delete();

        $output = 'Se ha eliminando el jugador de favoritos exitosamente!';
    
        echo json_encode($output);
    }    
}