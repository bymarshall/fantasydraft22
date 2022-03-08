<?php
//Home View
$row = "";
foreach($table_teams as $data){
    $row .= "<tr><td><img style='height:63px; weigth:63px;' src='".$data->team_avatar."'><BR />".$data->team."</td><td>$ ".$data->budget."</td>";
    $it = 0;
    $gastado = 0;
    foreach($data->players as $data2){
        $it +=1;
        if ($it < 11){
            $gastado += $data2['price'];
            $row .="<td><b>".$data2['jugador']."</b>, POS:".$data2['position'].", VALUE:".$data2['price']."</td>";
        }
    }
    for($tmp = $it; $tmp < 10; $tmp++){
        $row .="<td>FREE</td>";
    }
    $row .="<td>$".$gastado."</td></tr>";
}
echo $row;

//Home Controller
//Update Bid

function makeoffer(Request $request){
    //Buscamos el id team
    Auth::user()->id;
    $data_team = \DB::table('tbl_teams')
        ->leftJoin('tbl_teams_event', 'tbl_teams.id_team', '=', 'tbl_teams_event.id_team')
        ->select('tbl_teams_event.id_teams_event')
        ->where('tbl_teams_event.status_int', 1)->where('tbl_teams.id_user', $id_user)
        ->get();

        $auctionId = Auction::insertGetId([
            'final_prize_int' => $request->get('offerBid'),
            'position_txt' => $request->get('posPlayer'),
            'id_teams_event' => $data_team[0]->id_teams_event,
            'id_player_event' => $request->get('idPlayer'),
            ]
        );
        //Traemos la info a mostrar en la subasta
        $query_subasta = 'SELECT pl.name_txt Jugador,tm.name_txt FantasyTeam, auct.final_prize_int PrecioSubasta, auct.position_txt Posicion, pl_vnt.photo_txt Photo, pl_vnt.initial_prize_int PrecioInicial, pl_vnt.mlb_team_txt MLB, pl_vnt.id_player_event IdPlayerEvent
        FROM tbl_auction auct
        LEFT JOIN tbl_player_event pl_vnt ON (auct.id_player_event=pl_vnt.id_player_event)
        LEFT JOIN tbl_player pl ON (pl_vnt.id_player=pl.id_player)
        LEFT JOIN tbl_teams_event tm_vnt ON (auct.id_teams_event = tm_vnt.id_teams_event)
        LEFT JOIN tbl_teams tm ON (tm_vnt.id_team=tm.id_team)
        WHERE auct.id_auction = '.$auctionId;
        $subasta = \DB::select($query_subasta);
        $resp = event(new AuctionCreated($subasta));
        if($resp){
            $output ='<div class="alert alert-success">Oferta Realizada!</div>';
        }else{
            $output ='<div class="alert alert-danger">Error en Oferta!</div>';
        }
    echo json_encode($output);
}

        //Ahora Armamos los equipos como van quedando
    $query_teams ='SELECT auct.id_auction idBid,pl.name_txt Jugador,auct.final_prize_int PrecioFinal,
tm.name_txt FantasyTeam,';
    $query_teams .='auct.position_txt posPlayer, pe.photo_txt avatar FROM tbl_auction auct ';
    $query_teams .='LEFT JOIN tbl_player_event pe ON (auct.id_player_event = pe.id_player_event) ';
    $query_teams .='LEFT JOIN tbl_player pl ON (pe.id_player = pl.id_player) ';
    $query_teams .='LEFT JOIN tbl_teams_event te ON (auct.id_teams_event = te.id_teams_event) ';
    $query_teams .='LEFT JOIN tbl_teams tm ON (te.id_team = tm.id_team) ';
    $query_teams .='WHERE auct.winningbid = 1 ';
    $query_teams .='ORDER BY FantasyTeam, posPlayer, idBid DESC';
    $teams = \DB::select($query_teams);

//Ahora Armamos los equipos como van quedando

$query_teams ='SELECT auct.id_auction idBid,pl.name_txt Jugador,auct.final_prize_int PrecioFinal,tm.name_txt FantasyTeam,';
$query_teams .='auct.position_txt posPlayer, te.budget budget, tm.avatar_txt avatar FROM tbl_auction auct ';
$query_teams .='LEFT JOIN tbl_player_event pe ON (auct.id_player_event = pe.id_player_event) ';
$query_teams .='LEFT JOIN tbl_event evento ON (pe.id_event = evento.id_event) ';
$query_teams .='LEFT JOIN tbl_player pl ON (pe.id_player = pl.id_player) ';
$query_teams .='LEFT JOIN tbl_teams_event te ON (auct.id_teams_event = te.id_teams_event) ';
$query_teams .='LEFT JOIN tbl_teams tm ON (te.id_team = tm.id_team) ';
$query_teams .='WHERE auct.winningbid = 1  AND auct.auct_status_int = 1 AND evento.status_int = 1 ';
$query_teams .='ORDER BY FantasyTeam, posPlayer, idBid DESC';
$teams = \DB::select($query_teams);
