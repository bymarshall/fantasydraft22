<?php

namespace App\Http\Controllers;

use App\Events\AuctionCreated;
use App\Events\AuctionLoaded;
use App\Events\AuctionClosed;
use App\Events\AuctionDeleted;
use App\Exceptions\Handler;
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


class HomeController extends Controller
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

    private function getAllBids(){
        $query_bids_finished ='SELECT DISTINCT auction.idBid as idBid, tm.name_txt as fantasy_team,';
        $query_bids_finished .='auction.maxBid as maxBid, pl.name_txt as Player ';
        $query_bids_finished .='FROM tbl_auction act LEFT JOIN tbl_player_event pe on ';
        $query_bids_finished .='act.id_player_event = pe.id_player_event ';
        $query_bids_finished .='LEFT JOIN tbl_event evt on pe.id_event = evt.id_event ';
        $query_bids_finished .='LEFT JOIN tbl_player pl on pe.id_player = pl.id_player ';
        $query_bids_finished .='LEFT JOIN tbl_teams_event te on act.id_teams_event = te.id_teams_event ';
        $query_bids_finished .='LEFT JOIN tbl_teams tm on te.id_team = tm.id_team, ';
        $query_bids_finished .='    (	SELECT Bid.id_auction as idBid, Bid.id_player_event as idPlayer, ';
        $query_bids_finished .='max_bids.player_max as maxBid, Bid.id_teams_event as tmBid ';
        $query_bids_finished .='        FROM `tbl_auction` as Bid, ';
        $query_bids_finished .='            (	SELECT id_player_event, MAX(final_prize_int) as player_max ';
        $query_bids_finished .='                 FROM `tbl_auction` ';
        $query_bids_finished .='                 WHERE auct_status_int = 1 ';
        $query_bids_finished .='                GROUP BY id_player_event	) as max_bids ';
        $query_bids_finished .='        WHERE Bid.id_player_event = max_bids.id_player_event ';
        $query_bids_finished .='        AND Bid.final_prize_int = max_bids.player_max	) as auction ';
        $query_bids_finished .=' WHERE auction.idPlayer = act.id_player_event AND ';
        $query_bids_finished .='      auction.tmBid = act.id_teams_event AND evt.status_int = 1 ';
        $query_bids_finished .='ORDER BY idBid DESC;';

        return \DB::select($query_bids_finished);
    }

    private function getBidsByTeam(){
        $query_bids_byteams ='SELECT te.id_teams_event, tm.name_txt, count(auct.winningbid) as completados ';
        $query_bids_byteams .='FROM tbl_auction auct ';
        $query_bids_byteams .='LEFT JOIN tbl_teams_event te ON (auct.id_teams_event = te.id_teams_event) ';
        $query_bids_byteams .='LEFT JOIN tbl_teams tm ON (te.id_team = tm.id_team) ';
        $query_bids_byteams .='LEFT JOIN tbl_event evt ON (auct.id_event = evt.id_event) ';
        $query_bids_byteams .='WHERE auct.winningbid = 1 AND auct.auct_status_int = 1 AND evt.status_int = 1 ';
        $query_bids_byteams .='GROUP BY tm.name_txt,te.id_teams_event HAVING completados = 10;';

        return \DB::select($query_bids_byteams);
    }

    private function getFormedTeamsPerTeam($team, $isATeam){
        $completeQuery = "";
        switch((integer)$isATeam){
            case 0:
                $completeQuery = "WHERE auct.winningbid = 1 AND auct.auct_status_int = 1 AND evt.status_int = 1 ";
                $completeQuery .="ORDER BY FantasyTeam, posPlayer, idBid DESC ";
                break;
            case 1:
                $completeQuery = "WHERE auct.winningbid = 1 AND auct.auct_status_int = 1 AND evt.status_int = 1 ";
                $completeQuery .= " AND auct.id_teams_event = ".$team;
                $completeQuery .=" ORDER BY posPlayer ";
                break;
            case 2:
                $completeQuery = "WHERE auct.id_auction = ".$team." AND evt.status_int = 1 ";
                break;
        }
        // Team Bids
        $query_teams ='SELECT auct.id_auction idBid, pl.name_txt Jugador, auct.final_prize_int PrecioFinal,';
        $query_teams .='tm.name_txt FantasyTeam, auct.position_txt posPlayer, pe.photo_txt pl_avatar, ';
        $query_teams .='te.budget budget, tm.avatar_txt team_avatar, pe.mlb_team_txt MLB, ';
        $query_teams .='pe.id_player_event IdPlayerEvent  FROM tbl_auction auct ';
        $query_teams .='LEFT JOIN tbl_player_event pe ON (auct.id_player_event = pe.id_player_event) ';
        $query_teams .='LEFT JOIN tbl_event evt ON (pe.id_event = evt.id_event) ';
        $query_teams .='LEFT JOIN tbl_player pl ON (pe.id_player = pl.id_player) ';
        $query_teams .='LEFT JOIN tbl_teams_event te ON (auct.id_teams_event = te.id_teams_event) ';
        $query_teams .='LEFT JOIN tbl_teams tm ON (te.id_team = tm.id_team) ';
        $query_teams .= $completeQuery;
        $result = \DB::select($query_teams);
        return $result;
    }

    public function index()
    {
        $id_user = Auth::user()->id;
        $teams = $this->getFormedTeamsPerTeam("",0);       
        $team_bids_finished = null;

        $data_team = \DB::table('tbl_teams')
            ->leftJoin('tbl_teams_event', 'tbl_teams.id_team', '=', 'tbl_teams_event.id_team')
            ->leftJoin('tbl_event', 'tbl_teams_event.id_event', '=', 'tbl_event.id_event')
            ->select('tbl_teams_event.id_teams_event', 'tbl_teams.name_txt', 'tbl_teams.avatar_txt', 'tbl_event.id_event')
            ->where('tbl_teams_event.status_int', 1)->where('tbl_event.status_int', 1)->where('tbl_teams.id_user', $id_user)
            ->get();
        
        if(count($data_team) == 0)
        {
            $data_team = array("item" => array("uno" => array("id_teams_event" => "0","name_txt" => "ERROR",
            "avatar_txt" => "ERROR")));
            Log::channel('stderr')->info("INDEX: EQUIPO NO REGISTRADO PARA ESTE EVENTO");
        }
        else
        {
            $team_bids_finished = $this->getFormedTeamsPerTeam((string)$data_team[0]->id_teams_event, 1);
        }

        $all_teams = \DB::table('tbl_teams')
            ->leftJoin('tbl_teams_event', 'tbl_teams.id_team', '=', 'tbl_teams_event.id_team')
            ->leftJoin('tbl_event', 'tbl_teams_event.id_event', '=', 'tbl_event.id_event')
            ->select('tbl_teams_event.id_teams_event', 'tbl_teams.name_txt', 'tbl_teams.avatar_txt','tbl_teams_event.budget')
            ->where('tbl_teams_event.status_int', 1)->where('tbl_event.status_int', 1)
            ->get();
        
        if(count($all_teams) == 0)
        {
            $all_teams = array("item" => array("uno" => array("id_teams_event" => "0","name_txt" => "ERROR",
            "avatar_txt" => "ERROR", "budget" => "0")));
            Log::channel('stderr')->info("INDEX: NO HAY NINGUN EQUIPO REGISTRADO PARA ESTE EVENTO");
        }
        $baseEquipoItem = array();

        foreach ($all_teams as $todosEquipos){
            array_push($baseEquipoItem, array("team" => $todosEquipos->name_txt,"budget" => $todosEquipos->budget,
            "team_avatar" => $todosEquipos->avatar_txt,"players" => array( )) );
        }

        $fantasyTeamsArr = json_encode($baseEquipoItem);
        $ftsyTeams = json_decode($fantasyTeamsArr);

        foreach($teams as $data){
            foreach($ftsyTeams as $dataArray){
                if(trim($data->FantasyTeam) == trim($dataArray->team)){
                    $dataArray->budget = $data->budget;
                    $dataArray->team_avatar = $data->team_avatar;
                    array_push($dataArray->players, ["jugador" => $data->Jugador, "position" => $data->posPlayer, 
                    "price" => $data->PrecioFinal, "avatar" => $data->pl_avatar, "idBid"=> $data->idBid ]);
                }
            }
        }
        //Llenamos con Free los spots vacios
        foreach ($ftsyTeams as &$item){
            for ($it=count($item->players); $it < 10; $it++){
                array_push($item->players, array( "jugador" => "", "position" => "FREE", "price" => "0", "avatar" =>"", 
                "idBid"=>"0"));
            }
        }

        // Finished Bids
        $bids_finished = $this->getAllBids();

        //bidsByTeam
        $bidsByTeam = $this->getBidsByTeam();

        //tomamos los favoritos
        $team_favs = \DB::table('tbl_teams_event_favs_player')
            ->select('PlayerID')
            ->where('id_teams_event', $data_team[0]->id_teams_event)->where('id_event', config('app.event'))
            ->get();        

        // user role
        $user_role = \DB::table('tbl_user_role')->where('id_user', $id_user)->where('status_int', 1)->get();
        if((!$user_role) || (!$data_team) || (!$all_teams))
        {
            Log::channel('stderr')->info("INDEX: NO HAY NINGUN ROL REGISTRADO PARA ESTE USUARO");
            abort(404);
        }
        return view('home', ['table_teams'=> $ftsyTeams, 'all_teams' => $all_teams, 'team_data' => $data_team[0], 
                'user_role' => $user_role, 'bids_finished' => $bids_finished, 'bidsByTeams' => $bidsByTeam,
                'team_bids_finished' => $team_bids_finished, 'team_favs' => $team_favs]);
    }

    //Inicia Subasta Manual
    function initmanualauction(Request $request){
        $player_in_auction = \DB::table('tbl_players_api')
            ->leftJoin('tbl_event', 'tbl_event.id_event', '=', 'tbl_players_api.id_event')
            ->select('tbl_players_api.*')
            ->where('tbl_players_api.PlayerID', $request->idPlayer)
            ->where('tbl_event.status_int', 1)
            ->where('tbl_players_api.id_event', config('app.event'))
            ->get();
        event(new AuctionCreated($player_in_auction));
        return $player_in_auction;
    }
    
    //Elimina una subasta
    function deleteauction(Request $request){
        $error = false;
        //Buscamos el id del Player
        $idPlyEvt = \DB::select("SELECT id_player_event from tbl_auction WHERE id_auction=".trim($request->idAuction)
                                ." AND id_event = ".config('app.event'));

        if(count($idPlyEvt) > 0) 
        {
            $idPly = \DB::select("SELECT id_player from tbl_player_event WHERE id_player_event = ".(string)$idPlyEvt[0]->id_player_event
                                ." AND id_event = ".config('app.event'));
            if(count($idPly) > 0) 
            {
                $idPly_api = \DB::select("SELECT id_player_id_api from tbl_player WHERE id_player = ".(string)$idPly[0]->id_player
                                ." AND id_event = ".config('app.event'));
            }
            else
            {
                $error = true;
            }
        }
        else
        {
            $error = true;
        }

        if(! $error) {
            //delete in Auction
            $sqlDelete = "DELETE from tbl_auction WHERE id_auction=".trim($request->idAuction)
                        ." AND id_event = ".config('app.event');
            $resp = \DB::select($sqlDelete);
            //Delete in player event
            $sqlDeletePlyrEvt = "DELETE from tbl_player_event WHERE id_player_event = ".(string)$idPlyEvt[0]->id_player_event
                        ." AND id_event = ".config('app.event');
            $resp2 = \DB::select($sqlDeletePlyrEvt);
            //Delete in player
            $sqlDeletePlyr = "DELETE from tbl_player WHERE id_player = ".(string)$idPly[0]->id_player
                            ." AND id_event = ".config('app.event');
            $resp3 = \DB::select($sqlDeletePlyr);
            //Update taken in player api
            $resp4 = \DB::table('tbl_players_api')
                ->where('PlayerID', (string)$idPly_api[0]->id_player_id_api)
                ->where('id_event', config('app.event'))
                ->update(['taken' => 0]);
            if (!$resp) {
                $finSubasta = "{'subasta':'" . $request->idAuction . "','estado':'eliminada'}";
                $output = '<div class="alert alert-success">Se ha eliminado la subasta nro.'.trim($request->idAuction).'</div>';
            } else {
                Log::channel('stderr')->info("DELETE: NO SE PUDO BORRAR LA SUBASTA");
                $finSubasta = "{'estado':'ERROR'}";
                $output = '<div class="alert alert-danger">Error eliminando la Subasta!</div>';
            }
        }else{
            Log::channel('stderr')->info("DELETE: NO SE PUDO BORRAR LA SUBASTA");
            $finSubasta = "{'estado':'ERROR'}";
            $output = '<div class="alert alert-danger">Error eliminando la Subasta!</div>';
        }
        event(new AuctionDeleted($finSubasta));
        echo json_encode($output);
    }

    //Cierra Subasta Abierta
    function closeauction(Request $request){
        //Cerramos la subasta
        $sqlUpdate = "UPDATE tbl_auction SET auct_status_int = 1 WHERE id_player_event=".$request->idPlayer
                    ." AND id_event = ".config('app.event');
        $resp = \DB::select ($sqlUpdate);
        if(! $resp){
            $finSubasta = "{'estado':'cerrada'}";
            $output ='<div class="alert alert-success">Subasta Finalizada!</div>';
        }else{
            Log::channel('stderr')->info("CERRAR: NO SE PUDO CERRAR LA SUBASTA");
            $finSubasta = "{'estado':'ERROR'}";
            $output ='<div class="alert alert-danger">Error Cerrando la Subasta!</div>';
        }
        //Marcamos la Subasta ganadora
        $winsql = 'SELECT MAX(final_prize_int) as maxBid, id_auction, id_player_event ';
        $winsql .= 'FROM tbl_auction ';
        $winsql .= 'WHERE auct_status_int = 1 and id_player_event = '.$request->idPlayer;
        $winsql .= ' AND id_event = '.config('app.event');
        $winsql .= ' GROUP BY id_auction , id_player_event';
        $winsql .= ' ORDER BY maxBid DESC, id_auction DESC';
        $winnerBid = \DB::select($winsql);
        $sqlUupdate2 = "UPDATE tbl_auction SET winningbid = 1 WHERE id_auction=".$winnerBid[0]->id_auction
                        ." AND id_event = ".config('app.event');
        \DB::select ($sqlUupdate2);

        event(new AuctionClosed($finSubasta));
        $bids_finished =  $this->getAllBids();
        event(new WinningBids($bids_finished));
        $teams = $this->getFormedTeamsPerTeam("",0);
        event(new FantasyTeams($teams));

        echo json_encode($output);
    }
    
    //Cancelar una subasta activa
    function cancelauction()
    {
        $finSubasta = "{'estado':'subasta cancelada por el administrador!'}";
        $output ='<div class="alert alert-warning">Subasta Cancelada por el administrador!</div>';
        event(new AuctionClosed($finSubasta));
        echo json_encode($output);
    }

    //Generar un pwd
    function generatepwd(Request $request){
        $email = trim($request->get('email'));
        $pwd = trim($request->get('pwd'));
        $email_valid = \DB::select("SELECT count(email) as existe FROM users WHERE email = '".$email."';");
        if((int)$email_valid[0]->existe > 0){
            $password = Hash::make($pwd);
            // Actualizamos el PWD
            \DB::table('users')
                ->where('email', $email)
                ->update(['password' => $password]);
            $response ="Success: Password de usuario ".$email." actualizado!";
        }else{
            Log::channel('stderr')->info("Password: NO EXISTE EL USUARIO ".$email." EN LA BD");
            $response ="Error: Usuario no existe en BD!";
        }
        echo json_encode($response);
    }

    //Update un pwd
    function updatepwd(Request $request){
        $pwd = trim($request->get('pwd'));
        $email_valid = \DB::select("SELECT email FROM users WHERE id = ".Auth::user()->id);
        if($email_valid[0]->email != ""){
            $password = Hash::make($pwd);
            // Actualizamos el PWD
            \DB::table('users')
                ->where('id', Auth::user()->id)
                ->update(['password' => $password]);
            $response ="Success: Password de usuario ".$email_valid[0]->email." actualizado!";
        }else{
            Log::channel('stderr')->info("Password: NO EXISTE EL USUARIO EN LA BD");
            $response ="Error: Usuario no existe en BD!";
        }
        echo json_encode($response);
    }

    //Actualiza Precio a Jugador
    function updatePlayerPrice(Request $request){
        $idPlayer = trim($request->get('idplayer'));
        $precioPlayer = trim($request->get('precio'));
        \DB::table('tbl_players_api')
        ->where('PlayerID', $idPlayer)
        ->where('id_event', config('app.event'))
        ->update(['YahooPrice' => $precioPlayer]);        
        $response ='<div class="alert alert-success">Precio de Jugador '.$idPlayer.' actualizado!</div>';    
        echo json_encode($response);
    }

    //Buscar jugador para subastar
    function searchplayer(Request $request)
    {
        $playerSearch = \DB::table('tbl_players_api')
            ->leftJoin('tbl_event', 'tbl_players_api.id_event', '=', 'tbl_event.id_event')
            ->select('tbl_players_api.PlayerID', 'tbl_players_api.Status', 'tbl_players_api.Team', 'tbl_players_api.PositionCategory', 
            'tbl_players_api.Position', 'tbl_players_api.BirthDate', 'tbl_players_api.BirthCity', 'tbl_players_api.BirthCountry', 
            'tbl_players_api.PhotoUrl', 'tbl_players_api.YahooPlayerID', 'tbl_players_api.YahooName', 'tbl_players_api.Experience',
            'tbl_players_api.last_year_points', 'tbl_players_api.last_year_games', 'tbl_players_api.YahooPrice',
            'tbl_players_api.InjuryStatus')
            ->where('tbl_players_api.YahooName', 'like', "%" . $request->get('search_player') . "%")
            ->where('tbl_players_api.taken', '=', 0)
            ->where('tbl_event.status_int', '=', 1)
            ->get();
        echo json_encode($playerSearch);
    }

    //Carga la Subasta
    function loadauction(Request $request)
    {
        $error_array = array();
        $success_output = '';
        $error_output = '';
        $error_flag = false;
        //Validamos que la posicion elejida en la subasta este disponible        
        $qry_validate_pos =\DB::select("SELECT count(position_txt) as counter FROM tbl_auction WHERE 
                        position_txt = '".$request->get('position_txt')."' AND id_teams_event = ".$request->get('idTeam')
                        ." AND id_event = ".config('app.event'));
      
        switch($request->get('position_txt')){
            case "P":
                if((int)$qry_validate_pos[0]->counter > 2){
                    $error_output = "El equipo ya tiene completos los Pitchers Abridores ";
                    $error_flag = true;
                }
                break;
            case "OF":
                if((int)$qry_validate_pos[0]->counter > 1){
                    $error_output = "El equipo ya tiene completos los OutFielders ";
                    $error_flag = true;
                }
                break;
            default:
                if((int)$qry_validate_pos[0]->counter > 0){
                    $error_output = "El equipo ya tiene completos los jugadores en la posición: ".$request->get('position_txt');
                    $error_flag = true;
                }
        }
        // Ahora validamos la cantidad de Dinero Gastado
        $qry_validate_pos =\DB::select("SELECT sum(`final_prize_int`) as gastado FROM `tbl_auction` WHERE `id_teams_event` = "
                                .$request->get('idTeam')." AND id_event = ".config('app.event'));

        if(((int)$qry_validate_pos[0]->gastado + (int)$request->get('final_prize_int')) > 200 ){
            Log::channel('stderr')->info("GUARDA: EL USUARIO ".$request->get('idTeam')." HIZO OFERTA SIN PRESUPUESTO");
            $error_output = "La OFERTA de este equipo SUPERA su TOTAL de Presupuesto";
            $error_flag = true;
        }
       
        if(! $error_flag){
            //Vemos si el jugador Actualmente existe en la Base de Datos
            $id_player = \DB::table('tbl_player')
                        ->where('id_player_id_api',$request->get('playerIDapi'))
                        ->where('id_event',config('app.event'))
                        ->first();

            if(!$id_player){
                //Creamos el Jugador en la BD
                $player_data =new Player();
                $player_data->name_txt = $request->get('txt_player');
                $player_data->id_player_id_api = $request->get('playerIDapi');
                $player_data->id_event = config('app.event');
                $player_data->save();
                $player_data->id;

                //Player Event
                $player_event_data = new PlayerEvent();
                $player_event_data->position_txt = $request->get('position_txt');
                $player_event_data->mlb_team_txt = $request->get('txt_player_mlb');
                $player_event_data->photo_txt =  $request->get('txt_player_photo');
                $player_event_data->initial_prize_int = $request->get('final_prize_int');
                $player_event_data->id_event = config('app.event');
                $player_event_data->id_player = $player_data->id;
                $player_event_data->save();
                $id_player = $player_event_data->id;       
            }else{
                $tmp = \DB::table('tbl_player_event')
                            ->where('id_player', $id_player->id_player)
                            ->where('id_event', config('app.event'))
                            ->first();
                $id_player = $tmp->id_player_event;  
            }

            $player_taken = \DB::table('tbl_auction')
                                ->where('id_player_event',$id_player)
                                ->where('id_event', config('app.event'))
                                ->first();

            if(! $player_taken){
                $auction_open = \DB::table('tbl_auction')
                                ->where('auct_status_int',0)
                                ->where('id_event', config('app.event'))
                                ->first();
                if(! $auction_open){

                    $values = array('auct_status_int' => 1,'winningbid' => 1,'final_prize_int' => $request->get('final_prize_int'),
                                'position_txt' => $request->get('position_txt'),'id_teams_event' => $request->get('idTeam'),
                                'id_player_event' => $id_player, 'id_event' => config('app.event'));

                    $subasta = \DB::table('tbl_auction')->insert($values);
                    //Actualizamos el player api
                    \DB::table('tbl_players_api')
                        ->where('PlayerID', $request->get('playerIDapi'))
                        ->where('id_event', config('app.event'))
                        ->update(['taken' => 1]);
                    //event(new AuctionLoaded($subasta));
                    //event(new FantasyTeams("Jugador"));
                    $success_output ='<div class="alert alert-success">Subasta Finalizada!</div>';
                    event(new WinningBids("Subasta Finalizada!"));
                }else{
                    $error_output = 'Ya hay una subasta abierta. Debe cerrarla!';
                }
            }else{
                $error_output = 'El Jugador ya fué tomado!';
            }
        }

        if((! sizeof($error_array)) && ($success_output == '')){
            $error_array[] = $error_output;
        }
        $output = array(
            'error' => $error_array,
            'success' => $success_output
        );
        echo json_encode($output);
    }

    //Crear la Subasta
    function postdata(Request $request){
        Log::channel('stderr')->info("Entra en POST Data ".json_encode($request));      
        $validation = Validator::make($request->all(),[
            'txt_player' => 'required',
            'txt_player_mlb'  => 'required',
            'txt_player_photo' => 'required',
            'sel_pos'    => 'required',
            'txt_valor'  => 'required',
            'sel_equipos'=> 'required',
        ]);
        $error_array = array();
        $success_output = '';
        $error_output = '';
        if(! $validation->fails()){
            //Vemos si el jugador Actualmente existe en la Base de Datos
            $id_player = \DB::table('tbl_player')
                        ->where('name_txt', $request->get('txt_player'))
                        ->where('id_event', config('app.event'))
                        ->first();
             if(!$id_player)
             {
                //Creamos el Jugador en la BD
                $player_data =new Player();
                $player_data->name_txt = $request->get('txt_player');
                $player_data->id_event = config('app.event');
                $player_data->save();
                $player_data->id;
                //Player Event
                $player_event_data = new PlayerEvent();
                $player_event_data->position_txt = $request->get('sel_pos');
                $player_event_data->mlb_team_txt = $request->get('txt_player_mlb');
                $player_event_data->photo_txt =  $request->get('txt_player_photo');
                $player_event_data->initial_prize_int = $request->get('txt_valor');
                $player_event_data->id_event = config('app.event');
                $player_event_data->id_player = $player_data->id;
                $player_event_data->save();
                $id_player =$player_event_data->id;
            }
            else
            {
                $tmp = \DB::table('tbl_player_event')
                        ->where('id_player', $id_player->id_player)
                        ->where('id_event', config('app.event'))
                        ->first();

                $id_player = $tmp->id_player_event;
            }

            $player_taken = \DB::table('tbl_auction')
                            ->where('id_player_event', $id_player)
                            ->where('id_event', config('app.event'))
                            ->first();

             if(! $player_taken){
                $auction_open = \DB::table('tbl_auction')
                            ->where('auct_status_int',0)
                            ->where('id_event', config('app.event'))
                            ->first();

                if(! $auction_open){
                    $auctionId = Auction::insertGetId([
                        'final_prize_int' => $request->get('txt_valor'),
                        'position_txt' => $request->get('sel_pos'),
                        'id_teams_event' => $request->get('sel_equipos'),
                        'id_player_event' => $id_player,
                        'id_event' => config('app.event')
                        ]
                    );
                    //Traemos la info a mostrar en la subasta
                    $subasta = $this->getFormedTeamsPerTeam((string)$auctionId,2);
                    event(new AuctionCreated($subasta));
                    $success_output ='<div class="alert alert-success">Subasta Iniciada!</div>';
                }else{
                    $error_output = 'Ya hay una subasta abierta!';
                }
            }else{
                $error_output = 'El Jugador ya fué tomado!';
            }
        } else {
             foreach($validation->messages()->getMessages() as $field_name => $messages){
                $error_array[] = $messages;
              }
        }
        if((! sizeof($error_array)) && ($success_output == '')){
            $error_array[] = $error_output;
        }
        $output = array(
            'error' => $error_array,
            'success' => $success_output
        );
        echo json_encode($output);
    }
}

