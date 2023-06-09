@extends('layouts.app')

@section('content')
<div class="wrapper">
    <!-- Content Wrapper. Contains page content  class="m-0 text-dark" -->
    <div class="content-wrapper">
<!-- Content Header (Page header)[0]->id_rol  col-md-offset-1 -->
<!-- Primero sacamos el gastado y las posiciones ocupadas -->
<?php
    $teams_base = array();
    $teams_completed = array();
    $gastado = 0;
    $posiciones = array();
    foreach($team_bids_finished as $data)
    {
        $gastado += $data->PrecioFinal;
        array_push($posiciones, $data->posPlayer);
    }

    $posiciones_fijas = array_diff(array("C","CI","MI","UTY","OF1","OF2","P1","P2","RP","BN"), $posiciones);

    //sacamos los equipos completos
    foreach ($all_teams as $value) {
        array_push($teams_base, $value->id_teams_event);
    }

    foreach ($bidsByTeams as $value) {
        array_push($teams_completed, $value->id_teams_event);
    }
    
    $teams_pendientes = array_diff($teams_base, $teams_completed);
?>
<div class="container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        TU EQUIPO FANTASY
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img style="height:100px; weigth:100px;" src="{{ $team_data->avatar_txt }}"/>
                                <span id="nameFantasyTeam" style="font-size:26px;font-weigth:bolder">{{ trim($team_data->name_txt) }} </span>
                                <select style="visibility:hidden;display:none" id="idTeamFavs" name="idTeamFavs">
                                    <?php
                                        foreach($team_favs as $fav){
                                            echo "<option value ='".$fav->PlayerID."'>".$fav->PlayerID."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6"><span class="teaminfo">SALDO:</span>
                                        <span class="teaminfo"> ${{ (int)200-(int)$gastado }}</span>
                                    </div>
                                    <div class="col-md-6"><span class="teaminfo">GASTADO:</span>
                                        <span class="teaminfo"> ${{ $gastado }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 noTomado">POSICIONES PENDIENTES</div>
                                </div>
                                <?php
                                    $index = 0;
                                    $row1 = "<div class='row'>";
                                    $row2 = "<div class='row'>";

                                    foreach($posiciones_fijas as $item2)
                                    {
                                        if($index < 5)
                                        {
                                            $row1 .="<div class='col-md-2'><span class='noTomado'>".$item2."</span></div>";
                                        }else
                                        {
                                            $row2 .="<div class='col-md-2'><span class='noTomado'>".$item2."</span></div>";
                                        }
                                        $index++;
                                    }

                                    foreach($posiciones as $item)
                                    {
                                        if($index < 5)
                                        {
                                            $row1 .="<div class='col-md-2'><span class='tomado'>".$item."</span></div>";
                                        }else
                                        {
                                            $row2 .="<div class='col-md-2'><span class='tomado'>".$item."</span></div>";
                                        }
                                        $index++;
                                    }

                                    $row1 .= "</div>";
                                    $row2 .= "</div>";

                                    echo $row1.$row2;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.col -->
        </div>
    </div>
</div>
<div class="container">
    <div class="container-fluid">
        <!-- THIRD ROW -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Así va tu Equipo: <b>{{ $team_data->name_txt}}</b> <img style="height:33px; weigth:33px;" src="{{ $team_data->avatar_txt }}" />
                    </div>
                    <div class="card-body">
                        <?php
                            $index = 0;
                            $row1 ="<div class='row'>";
                            $row2 ="<div class='row'>";
                            foreach($team_bids_finished as $data1){
                                if($index < 6){
                                    $row1 .= "<div class='col-md-2'><div class='card' align='center'><table><tr><td style='text-align:center;'>";
                                    $row1 .= "<img style='height:75px; weigth:75px;' src='";
                                    $row1 .= $data1->pl_avatar;
                                    $row1 .= "'/><BR /> ".$data1->Jugador."<BR /> Posición: ".$data1->posPlayer;
                                    $row1 .="<BR /> Precio: ".$data1->PrecioFinal."</td></tr></table></div></div>";
                                }else{
                                    $row2 .= "<div class='col-md-2'><div class='card' align='center'><table><tr><td style='text-align:center;'>";
                                    $row2 .= "<img style='height:75px; weigth:75px;' src='";
                                    $row2 .= $data1->pl_avatar."'/><BR /> ".$data1->Jugador."<BR /> Posición: ".$data1->posPlayer;
                                    $row2 .="<BR /> Precio: ".$data1->PrecioFinal."</td></tr></table></div></div>";
                                }
                                $index++;
                            }
                            $row1 .="</div>";
                            $row2 .="</div>";
                            $completado = "<div class='row'><div class='col-md-12'><div class='alert alert-success'>Tu Equipo ya esta completo. Suerte!</div></div></div>";
                            
                            if($index > 8){
                                echo $completado.$row1.$row2;
                            }else{
                                echo $row1.$row2;
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- END THIRD ROW -->
        <?php
        if($user_role[0]->id_rol == '1') {
        ?>
        <!-- FIRST ROW : SOLO PARA ADMINS QUE PUEDEN INICIAR UNA SUBASTA -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Aqui puedes iniciar la subasta de jugadores
                    </div>
                    <div class="card-body">
                        {{ csrf_field() }}
                        <span id="form_output"></span>
                        <div class="container box">
                            <div class="row">
                            <div class="col-sm-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <label>Buscar Jugador <i class="fa fa-search" aria-hidden="true"></i>:</label>
                                        <input type="text" placeholder="Nombre del Jugador" id="txt_player_search" name="txt_player_search"  style="width: 180px;"/>
                                    </div>
                                    <div class="panel-body">
                                    <select class="custom-select" id="search_result" multiple style="height:320px;">
                                    </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="panel panel-default">
                                    <div class="panel-heading" aling="center"><h2><img src="{{ asset('storage/img/mlb.png') }}" width="10%" height="10%"/> Detalles del Jugador</h2>
                                    </div>
                                    <div class="panel-body">
                                      <form id="auction_form" action="subasta" method="POST">
                                        <table class="playerDetail">
                                            <tr>
                                                <td rowspan="6">
                                                    <div style="margin: 0 auto; width: 150px">
                                                        <img id="imgPlayer" alt="" src="" href="#" style="width: 150px" />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                            <td colspan="2">
                                                <div class="form-group">
                                                    <label>Jugador:</label>
                                                    <input type="text" readonly placeholder="Nombre del Jugador" id="txt_player" name="txt_player" />
                                                </div>
                                                <div class="form-group">
                                                    <input type="hidden" id="txt_player_photo" name="txt_player_photo" />
                                                    <input type="hidden" id="playerIDapi" name="playerIDapi" />
                                                </div>
                                            </td>
                                            <td>
                                                <label>Posicion:</label>
                                                <span id="playerPos"></span>
                                            </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label>Roster MLB:</label>
                                                    <span id="playerSt" style="white-space: nowrap; overflow: hidden;"></span>
                                                </td>
                                                <td>
                                                    <label>Health:</label>
                                                    <span id="playerHlty" style="white-space: nowrap; overflow: hidden;"></span>
                                                </td>
                                                <td>
                                                    <label>Juegos 2021:</label>
                                                    <span id="plJgos" style="font-size: 24px;font-weight: bolder;"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <label>MLB:</label>
                                                        <input type="text" style="width: 60px;" readonly id="txt_player_mlb" name="txt_player_mlb" />
                                                    </div>
                                                </td>
                                                <td align = "left">
                                                    <div class="form-group">
                                                        <label>Roster<BR />Fantasy:</label>
                                                        <select id="sel_pos" name="sel_pos">
                                                            <option value="C"> C</option>
                                                            <option value="CI">CI</option>
                                                            <option value="MI">MI</option>
                                                            <option value="UTY">UTY</option>
                                                            <option value="OF1">OF1</option>
                                                            <option value="OF2">OF2</option>
                                                            <option value="P1">P1</option>
                                                            <option value="P2">P2</option>
                                                            <option value="RP">RP</option>
                                                            <option value="BN">BN</option>
                                                        </select>
                                                    </div><!-- FORM GROUP-->
                                                </td>
                                                <td>
                                                    <label>Puntos 2021:</label>
                                                    <span id="plPtos" style="font-size: 24px;font-weight: bolder;"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <label>Origen:</label>
                                                    <span id="playerOrig"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Precio Yahoo:</label>
                                                        <input type="text" placeholder="Valor" id="txt_valor" name="txt_valor" style="width: 60px;"/>
                                                        <input type="button" value="Actualizar Precio" id="actualizaPrecio"/>
                                                    </div><!-- FORM GROUP-->
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Precio Puja:</label>
                                                        <select id="txt_valor_puja" name="txt_valor_puja" style="width: 60px;">
                                                        <!-- <input type="text" placeholder="Valor" id="txt_valor_puja" name="txt_valor_puja" style="width: 60px;"/> -->
                                                    </div><!-- FORM GROUP-->
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Ganador:</label>
                                                        <select id="sel_equipos" name="sel_equipos">
                                                            <?php
                                                            foreach($all_teams as $team){
                                                                foreach ($teams_pendientes as $value) {
                                                                    if( $value == $team->id_teams_event){
                                                                        echo "<option value=".$team->id_teams_event.">".$team->name_txt."</option>";
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div><!-- FORM GROUP-->
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="form-group">
                                                <div class="modal-footer">
                                                    <input type="button" id="iniciaSubasta" value="Iniciar Subasta" class="btn btn-info"/>
                                                    <input type="submit" name="submit" id="action" value="Cargar Subasta" class="btn btn-info" disabled/>
                                                    <input type="button" id="cancelarSubasta" value="Cancelar Subasta" class="btn btn-danger" disabled/>
                                                    {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button> --}}
                                                </div><!-- MODAL FOOTER-->
                                            </div><!-- FORM GROUP-->
                                    </div><!-- PANEL BODY -->
                                </div>
                            </div>
                            </div><!-- row-->
                        </div><!-- container box -->                        
                    </div>
                </div>
            </div>
        </div>
        <!-- END FIRST ROW -->
        <?php
            }
        ?>
        <!-- SECOND ROW -->
        <div class="row" id="divCurrentAuction" style="display:none;">
            <div class="col-md-12">
                <!-- lista de Subastas terminadas -->
                <div class="card">
                    <div class="card-header">
                        Jugador Actualmente en Proceso de Subasta
                    </div>
                    <div class="card-body">
                        <div id="enSubasta"></div>
                        <div id="favPlayer"></div>
                        <table class="table table-borderless" id="currentAuction">
                            <tr>
                                <td rowspan="6">
                                    <div style="margin: 0 auto; width: 150px">
                                        <img id="imgPlayerAuction" alt="" src="" href="#" style="width: 150px" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label>Jugador:</label>
                                        <input type="text" readonly placeholder="Nombre del Jugador" id="txt_player_auction" name="txt_player_auction" />
                                    </div>
                                </td>
                                <td>
                                    <label>Ranking:</label>
                                    <span id="playerPosRanking" style="font-size: 24px;font-weight: bolder;"></span>
                                </td>
                                <td>
                                    <label>Posicion:</label>
                                    <span id="playerPosAuction" style="font-size: 24px;font-weight: bolder;"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Roster MLB:</label>
                                    <span id="playerStAuction" style="white-space: nowrap; overflow: hidden;"></span>
                                </td>
                                <td>
                                    <label>Health:</label>
                                    <span id="playerHltyAuction" style="white-space: nowrap; overflow: hidden;"></span>
                                </td>
                                <td>
                                    <label>Juegos 2021:</label>
                                    <span id="plJgosAuction" style="font-size: 24px;font-weight: bolder;"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>MLB:</label>
                                    <input type="text" style="width: 60px;" readonly id="txt_player_mlb_auction" name="txt_player_mlb_auction" />
                                </td>
                                <td>
                                    <label>Precio Yahoo:</label>
                                    <span id="plExpAuction" style="font-size: 24px;font-weight: bolder;"></span>
                                </td>
                                <td>
                                    <label>Puntos 2021:</label>
                                    <span id="plPtosAuction" style="font-size: 24px;font-weight: bolder;"></span>
                                </td>
                            </tr>

                            <tr id="trPitchers" style="display: none">
                                <td colspan="3">
                                    <table>
                                        <tr>
                                            <td>
                                                <label>IP:</label>
                                                <span id="pitIp" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>Wins:</label>
                                                <span id="pitWin" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>Lost:</label>
                                                <span id="pitLost" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>SV:</label>
                                                <span id="pitSv" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>HLD:</label>
                                                <span id="pitHld" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>QS:</label>
                                                <span id="pitQs" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>BSV:</label>
                                                <span id="pitBsv" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>% Rostered:</label>
                                                <span id="playerRosteredPit" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr id="trBatters" style="display: none">
                                <td colspan="3">
                                    <table>
                                        <tr>
                                            <td>
                                                <label>AB:</label>
                                                <span id="batAb" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>HR:</label>
                                                <span id="batHr" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>CE:</label>
                                                <span id="batCe" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>SB:</label>
                                                <span id="batSb" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>K:</label>
                                                <span id="batK" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                            <td>
                                                <label>% Rostered:</label>
                                                <span id="playerRosteredBat" style="font-size: 24px;font-weight: bolder;"></span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- END SECOND ROW -->        
        <!-- FOURTH ROW -->
        <div class="row">
            <div class="col-md-12">
                <!-- lista de Subastas terminadas -->
                <div class="card">
                <div class="card-header">
                    Recientes Subastas
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div style="position: relative;height:270px;overflow: auto;display: block;">
                        <table class="table table-borderless" id="slBids" style="height:270px;overflow-scrolling: auto;">
                            <?php
                                foreach($bids_finished as $bids){
                                    echo "<tr style='height: 10px;'><td>".$bids->idBid."</td><td>".$bids->fantasy_team."</td><td>".$bids->Player."</td><td>".$bids->maxBid."</td></tr>";
                                }
                            ?>
                        </table>
                    </div>
                </div>
                </div>
              </div>
        </div>
        <!-- END FOURTH ROW -->
        <!-- FIFTH ROW -->
        <div class="row">
            <!-- Result Table -->
          <div class="col-lg-12">
              <table id="teamsTable" class="table table-striped" style="font-family: 'Courier New', Courier, monospace; font-size: 12px;">
                  <thead class="thead-dark" >
                  <tr>
                      <th class="text-center">Team</th>
                      <th class="text-center">Total Budget</th>
                      <th class="text-center"> Fantasy Rosters </th>
                      <th class="text-center">Total Spent</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  $row = "";
                  foreach($table_teams as $data){
                      if($data->team == $team_data->name_txt){
                            $row .= "<tr><td><img style='height:80px; weigth:80px;' src='".$data->team_avatar."'><BR /><h5><b>".$data->team."</b></h5></td><td><h4><b>$ ".$data->budget."</b></h4></td>";
                            $it = 0;
                            $gastado = 0;
                            $completo = true;
                            $row .="<td><table class='table table-sm' style='background-color:transparent !important;'><tr>";
                            foreach($data->players as $item){
                                if($item['position'] == "FREE"){
                                    $completo = false;
                                    break;
                                }
                            }
                            if($completo){
                                $row .="<tr><td colspan='5'><div class='alert alert-success'>Tu equipo ya esta completo. Suerte!</div></td></tr><tr>";
                            }
                            foreach($data->players as $data2){
                                $gastado += $data2['price'];
                                $row .="<td style='text-align:center;'>";
                                if($data2['avatar'] != "") $row .="<img style='height:40px; weigth:40px;' src='".$data2['avatar']."'/><br />";
                                $row .="<b>".$data2['jugador']."</b><br /> POS:".$data2['position']."<br /> VALUE:";
                                $row .=$data2['price']."<br />";
                                if($user_role[0]->id_rol == '1') {
                                    if($data2['jugador'] != ""){
                                        $row .="<input class='btn btn-outline-danger' type='button' onCLick='deleteAuction(".$data2['idBid'].")' value='Eliminar' />";
                                    }
                                }
                                $row .="</td>";
                                if($it==4) $row .="</tr><tr>";
                                $it++;
                            }
                            $row .="</tr></table></td>";
                            $row .="<td><h4>Gastado: <b>$".$gastado."</b></h4><br /><h4>Disp: <b>$".((int)($data->budget)-(int)$gastado)."</b></h4></td></tr>";
                            break;
                        }
                  }

                  foreach($table_teams as $data){
                      if($data->team != $team_data->name_txt){
                            $row .= "<tr><td><img style='height:80px; weigth:80px;' src='".$data->team_avatar."'><BR /><h5><b>".$data->team."</b></h5></td><td><h4><b>$ ".$data->budget."</b></h4></td>";
                            $it = 0;
                            $gastado = 0;
                            $completo = true;
                            $row .="<td><table class='table table-sm' style='background-color:transparent !important;'><tr>";
                            foreach($data->players as $item){
                                if($item['position'] == "FREE"){
                                    $completo = false;
                                    break;
                                }
                            }
                            if($completo){
                                $row .="<tr><td colspan='5'><div class='alert alert-success'>Este equipo ya esta completo!</div></td></tr><tr>";
                            }
                            foreach($data->players as $data2){
                                $gastado += $data2['price'];
                                $row .="<td style='text-align:center;'>";
                                if($data2['avatar'] != "") $row .="<img style='height:40px; weigth:40px;' src='".$data2['avatar']."'/><br />";
                                $row .="<b>".$data2['jugador']."</b><br /> POS:".$data2['position']."<br /> VALUE:";
                                $row .=$data2['price']."<br />";
                                if($user_role[0]->id_rol == '1') {
                                    if($data2['jugador'] != ""){
                                        $row .="<input class='btn btn-outline-danger' type='button' onCLick='deleteAuction(".$data2['idBid'].")' value='Eliminar' />";
                                    }
                                }
                                $row .="</td>";
                                if($it==4) $row .="</tr><tr>";
                                $it++;
                            }
                            $row .="</tr></table></td>";
                            $row .="<td><h4>Gastado: <b>$".$gastado."</b></h4><br /><h4>Disp: <b>$".((int)($data->budget)-(int)$gastado)."</b></h4></td></tr>";
                        }
                  }                  
                  echo $row;
                  ?>
                  </tbody>
              </table>
          </div>
          </div>
          <!-- END SECOND ROW -->
    </div>
</div>
</div>
</div>
@endsection
