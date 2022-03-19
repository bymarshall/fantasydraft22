@extends('layouts.app')

@section('content')
<div class="wrapper">
    <!-- Content Wrapper. Contains page content  class="m-0 text-dark" -->
    <div class="content-wrapper">
        <!-- Content Header (Page header)[0]->id_rol  col-md-offset-1 -->
        <div class="container">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Aqui podras ver los resultados de Eventos anterires al MLB Fantasy draft de <b>2022</b>
                            </div> 
                        </div>  
                    </div>                 
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">                    
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
                                                <span id="nameFantasyTeam" style="font-size:26px;font-weigth:bolder">{{ $data_team[0]->name_txt }} </span>
                                                {{-- Rol: { { trim( $ user_role[0] -> id_rol) } } --}}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>                      
                    </div><!-- /.col -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                Selecciona el evento que quieres ver
                            </div>
                            <div class="card-body">
                                <select id="idEventsSelect" name="idEventsSelect">
                                    <?php
                                        $row = "";
                                        foreach($events as $data)
                                            $row .="<option value='".$data->id_event."'>".$data->name_txt."</option>";

                                        echo $row;
                                    ?>                                    
                                </select>
                            </div>
                        </div>                     
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <span id="idEventTitle"></span>
                            </div>
                            <div class="card-body">
                                <div id="idEventDetails"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="container-fluid">
                <!-- FIRST ROW -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                TU EQUIPO EN ESE EVENTO
                            </div>
                            <div class="card-body" id="equipoEvent">
                                 {{-- <table id="equipoEvent" style="width: 700px;"></table> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END FIRST ROW -->
            </div>
        </div>
    </div>
</div>
@endsection