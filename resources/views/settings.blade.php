@extends('layouts.app')

@section('content')
<div class="wrapper">
    <!-- Content Wrapper. Contains page content  class="m-0 text-dark" -->
    <div class="content-wrapper">
        <!-- Content Header (Page header)[0]->id_rol  col-md-offset-1 -->
        <div class="container">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <table>
                            <thead>
                                <tr>
                                    <th>TU EQUIPO FANTASY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <img style="height:100px; weigth:100px;" src="{{ $data_team[0]->avatar_txt }}"/>
                                        <span id="nameFantasyTeam" style="font-size:26px;font-weigth:bolder">{{$data_team[0]->name_txt }} </span>
                                        {{-- Rol: { { trim( $ user_role[0] -> id_rol) } } --}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>                        
                    </div><!-- /.col -->
                    <div class="col-md-6">
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="container-fluid">
        <?php
        if($user_role[0]->id_rol == '1') {
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        ACCIONES
                    </div>
                    <div class="card-body">
                        <input type="button" id="toggleUsrPwd" value="Cambiar password a usuario">
                        <div id="mostrarToggle" style="display: none">
                        <table>
                            <tr>
                                <td>
                                    <input type="text" style="width:250px;" id="email_pwd" placeholder="coloque el email del usuario" /><br />
                                    <input type="text" style="width:250px;" id="data_pwd" placeholder="coloque el nuevo password del usuario" /><br />
                                    <input type="button" class="btn btn-primary" id="cambiaPwd" class="btn btn-default" value="Cambiar password" />
                                </td>
                            </tr>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }
        ?>
                <!-- FIRST ROW -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Selecciona tus jugadores favoritos para el MLB Fantasy draft de <b>2022</b>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <label>Buscar Jugador <i class="fa fa-search" aria-hidden="true"></i>:</label>
                                                <input type="text" placeholder="Nombre del Jugador" id="txt_player_search_favs" name="txt_player_search_favs"  style="width: 250px;"/>
                                                <input type="hidden" id="idTeamsEventFavs" name="idTeamsEventFavs" value="{{ $data_team[0]->id_teams_event }}" />                                                
                                            </div>
                                            <div class="panel-body">
                                            <select class="custom-select" id="search_result_favs" multiple style="height:320px;">
                                            </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <!-- AQUI VA LA INFO DEL JUGADOR SELECCIONADO -->
                                        <div class="panel panel-default">
                                            <div class="panel-heading" aling="right">
                                                <h2><img src="{{ asset('storage/img/mlb.png') }}" width="8%" height="8%"/> Detalles del Jugador</h2>
                                            </div>
                                            <div class="panel-body">
                                                <form id="favs_form" action="subasta" method="POST">
                                                    <table class="playerDetail_favs">
                                                        <tr>
                                                            <td rowspan="6">
                                                                <div style="margin: 0 auto; width: 150px">
                                                                    <img id="imgPlayer_favs" alt="" src="" href="#" style="width: 150px" />
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <label>Jugador:</label>
                                                                <input type="text" readonly placeholder="Nombre del Jugador" id="txt_player_favs" name="txt_player_favs" />
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="hidden" id="txt_player_photo_favs" name="txt_player_photo_favs" />
                                                                <input type="hidden" id="playerIDapi_favs" name="playerIDapi_favs" />
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
                                                                <span id="playerSt_favs" style="white-space: nowrap; overflow: hidden;"></span>
                                                            </td>
                                                            <td>
                                                                <label>Health:</label>
                                                                <span id="playerHlty_favs" style="white-space: nowrap; overflow: hidden;"></span>
                                                            </td>
                                                            <td>
                                                                <label>Juegos 2021:</label>
                                                                <span id="plJgos_favs" style="font-size: 24px;font-weight: bolder;"></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="form-group">
                                                                    <label>MLB:</label>
                                                                    <input type="text" style="width: 60px;" readonly id="txt_player_mlb_favs" name="txt_player_mlb_favs" />
                                                                </div>
                                                            </td>
                                                            <td align = "left">
                                                                <div class="form-group">
                                                                    <label>Roster<BR />Fantasy:</label>
                                                                    <select id="sel_pos_favs" name="sel_pos_favs">
                                                                        <option value="C"> C</option>
                                                                        <option value="CI">CI</option>
                                                                        <option value="MI">MI</option>
                                                                        <option value="UTY">UTY</option>
                                                                        <option value="BN">BN</option>
                                                                        <option value="OF">OF</option>
                                                                        <option value="P">P</option>
                                                                        <option value="RP">RP</option>
                                                                    </select>
                                                                </div><!-- FORM GROUP-->
                                                            </td>
                                                            <td>
                                                                <label>Puntos 2021:</label>
                                                                <span id="plPtos_favs" style="font-size: 24px;font-weight: bolder;"></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">
                                                                <label>Origen:</label>
                                                                <span id="playerOrig_favs"></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="form-group">
                                                                    <label>Precio Yahoo:</label>
                                                                    <input type="text" placeholder="Valor" id="txt_valor_favs" name="txt_valor_favs" style="width: 60px;"/>
                                                                </div><!-- FORM GROUP-->
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <label>Precio Puja:</label>
                                                                    <select id="txt_valor_puja_favs" name="txt_valor_puja_favs" style="width: 60px;">
                                                                </div><!-- FORM GROUP-->
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <div class="form-group">
                                                        <div class="modal-footer">
                                                            <input type="button" id="agregarFavs" value="Agregar a Favoritos" class="btn btn-primary"/>
                                                        </div><!-- MODAL FOOTER-->
                                                    </div><!-- FORM GROUP-->
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END FIRST ROW -->
                <!-- SECOND ROW -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                JUGADORES FAVORITOS DE &nbsp; <b>{{ $data_team[0]->name_txt }}</b>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class='col-md-12'>
                                        <div class='card' align='left'>
                                            <table class='table table-sm' style='background-color:transparent !important;'>
                                                <?php
                                                    $row1 ="";
                                                    foreach($data_favs as $fav){
                                                        $row1 .= "<tr><td style='text-align:left;'>";
                                                        $row1 .= "<img style='height:50px; weigth:50px;' src='".$fav->PhotoUrl;
                                                        $row1 .= "'/><td>Jugador: ".$fav->FirstName." ".$fav->LastName;
                                                        $row1 .=" </td><td>Puntos: ".$fav->last_year_points;
                                                        $row1 .="</td><td> Precio: ".$fav->YahooPrice."</td><td> Ranking: ".$fav->ranking;
                                                        $row1 .="</td><td><input class='btn btn-outline-danger' ";
                                                        $row1 .="type='button' onCLick='deleteFavs(".$fav->PlayerID.",`".$fav->FirstName."-".$fav->LastName."`)' ";
                                                        $row1 .="value='Eliminar' /></td></tr>";
                                                    }
                                                    echo $row1;
                                                ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>                   
                </div>
                <!-- END SECOND ROW -->
            </div>
        </div>
    </div>
</div>
@endsection