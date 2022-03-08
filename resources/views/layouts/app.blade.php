<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MLB Fantasy Draft 202</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/playerDetail.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">Home</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        @guest
                        <div></div>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
{{--                                    <a href="{{ route('logout') }}">Logout</a>--}}
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
          <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
            <strong>Copyright &copy; 2021 <a href="mailto:carlosamaya1@gmail.com">Carlos Amaya</a>.</strong>
            </div>
        </footer>
    </div>
    <!-- Scripts -->
    <script type="application/javascript" src="{{ asset('js/app.js') }}" defer></script>
    <script type="application/javascript" src="{{ asset('js/jquery-3.3.0.min.js') }}" ></script>
    <script type="application/javascript" src="{{ asset('js/bootstrap.min.js') }}" ></script>
    <script type="application/javascript" src="{{ asset('js/pusher.min.js') }}" ></script>
{{--    <script type="application/javascript" src="https://js.pusher.com/3.1/pusher.min.js"></script>--}}
    <script type="application/javascript">
        var playersArray = new Array();
        var idPlayerEvent = "";
        const positionsArr = ["C","CI","MI","UTY","OF","P","RP","BN"];
        var montoBase = 0;
        var totalGastado = 0;
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        // Initiate the Pusher JS library
        var pusher = new Pusher('d5900700f385247dc7ed', {
        cluster: 'mt1',
        forceTLS: true
        });
        //Eliminar una subasta ya creada
        function deleteAuction(idAuction){
            if (typeof idAuction !== 'undefined') {
                if(confirm("Está seguro que desea eliminar la subasta nro:" + idAuction+"?")) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('home.deleteauction') }}",
                        method: "POST",
                        data: {
                            'idAuction': idAuction
                        },
                        dataType: "json",
                        success: function (data) {
                            alert("La subasta #" + idAuction + " ha sido borrada!");
                            console.log(data);
                            location.reload(true);
                        }
                    });
                }
            }
        };
        // Subscribe to the channel we specified in our Laravel Event
        var channel = pusher.subscribe('auction-created-channel');
        //var channel2 = pusher.subscribe('closeBidChannel');
        //var channel3 = pusher.subscribe('fantasyTeamsChannel');
        var channel4 = pusher.subscribe('winningBidsChannel');
        //var channel5 = pusher.subscribe('auctionLoaded');
        var channel6 = pusher.subscribe('deleteBidChannel');
        //Delete Auction deleteAuction
        ///JQuery Initiator
        $(document).ready(function(){
            deleteAuction();
            $(".dropdown-toggle").dropdown();
            //Escucha de Canales y acciones
            //Channel for teams formation
            // channel3.bind('show-teams', function(data) {
            //     location.reload(true);
            // });
            channel4.bind('show-winnings', function(data) {
                console.log(data);
                alert(data.message);
                location.reload(true);
            });

            channel.bind('auction-created', function(data) {
                $("#divCurrentAuction").show();
                var currentAuctionPlayer = Object.values(data.message[0]);
                $("#enSubasta").html('<div class="alert alert-success">En Subasta: '+currentAuctionPlayer[32]+'</div>');
                //Imagen del Jusgador
                $("#imgPlayerAuction").attr('src',currentAuctionPlayer[13]);
                $("#txt_player_auction").val(currentAuctionPlayer[32]);
                $("#playerPosAuction").text(currentAuctionPlayer[5]);
                $("#playerStAuction").text(currentAuctionPlayer[1]);
                $("#playerPosRanking").text(currentAuctionPlayer[16]);
                $("#playerHltyAuction").text(currentAuctionPlayer[30]);
                $("#plJgosAuction").text(currentAuctionPlayer[15]);
                $("#txt_player_mlb_auction").val(currentAuctionPlayer[3]);
                $("#plExpAuction").text(currentAuctionPlayer[33]);
                $("#plPtosAuction").text(currentAuctionPlayer[14]);
                //Datos del Jugador
                if(currentAuctionPlayer[4] == "P"){
                    $("#trPitchers").show();
                    $("#trBatters").hide();
                    $("#pitIp").text(currentAuctionPlayer[18]);
                    $("#pitWin").text(currentAuctionPlayer[19]);
                    $("#pitLost").text(currentAuctionPlayer[20]);
                    $("#pitSv").text(currentAuctionPlayer[21]);
                    $("#pitHld").text(currentAuctionPlayer[22]);
                    $("#pitQs").text(currentAuctionPlayer[23]);
                    $("#pitBsv").text(currentAuctionPlayer[24]);
                    $("#playerRosteredPit").text(currentAuctionPlayer[17]+"%");
                }else{
                    $("#trPitchers").hide();
                    $("#trBatters").show();
                    $("#batAb").text(currentAuctionPlayer[25]);
                    $("#batHr").text(currentAuctionPlayer[26]);
                    $("#batCe").text(currentAuctionPlayer[27]);
                    $("#batSb").text(currentAuctionPlayer[28]);
                    $("#batK").text(currentAuctionPlayer[29]);
                    $("#playerRosteredBat").text(currentAuctionPlayer[17]+"%");
                }
             });

            channel6.bind('auction-delete', function(data) {
                 //Event recd : {"event":"auction-delete","data":{"message":"{'estado':'eliminada'}"},"channel":"deleteBidChannel"}
                alert(data.message);
                location.reload(true);
            });
            var $dropdown = $("#txt_valor_puja");
            for(var i=1;i<=191;i++){
                $dropdown.append(new Option(i, i));
            }

            //Muestra el Dialog
            $('#add_auction').on('click', function(){
                $('#auctionModal').modal('show');
                $('#auction_form')[0].reset();
                $('#form_output').html('');
            });
            //Inicia Subasta Manual
            $('#iniciaSubasta').on('click', function(){
               if($("#txt_player").val().trim() != ""){ 
                $('#action').removeAttr('disabled');
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('home.initmanualauction') }}",
                        method:"POST",
                        data:{
                            'idPlayer': $("#playerIDapi").val(),
                        },
                        dataType:"json",
                        success:function(data){
                            $.each(data, function() {
                                var key = Object.keys(this)[32];
                                var value = this[key];
                                console.log('Subasta Iniciada, jugador:'+value);
                            });
                            /*
                            setTimeout(function(){
                                    $('#auctionModal').modal('hide');
                                    location.reload(true);
                                }, 2000);
                            */
                        }
                    });
               }else{
                   alert("Debe seleccionar un jugador!");
                   $("#txt_player_search").focus();
               }
            });
            //Limpia el modal cuando se cierra
            $('#auctionModal').on('hidden.bs.modal', function(){
                $(this).data('modal', null);
                $("#imgPlayer").attr('src',"");
                $("#txt_player_photo").val("");
                $("#imgPlayer").attr('alt',"");
                $("#txt_player").val("");
                $("#txt_player_mlb").val("");
                $("#playerPos").text("");
                $("#playerOrig").text("");
                $("#playerSt").text("");
                $("#playerHlty").text("");
                $("#plJgos").text("0");
                $("#plPtos").text("0");
                $("#txt_valor").val("");
                $("#txt_player_search").val("");
                $("#search_result").html("");

                for(var it=0; it < 191 ; it++){
                    $("#txt_valor_puja option[value="+it+"]").removeAttr('disabled');
                    $("#txt_valor_puja option[value="+it+"]").removeAttr('selected');
                }
                $("#txt_valor_puja option[value=1]").attr('selected','selected');
                //Re-habilitamos las posiciones
                positionsArr.forEach(element => function(){
                    $("#sel_pos option[value="+element+"]").removeAttr('disabled');
                    $("#sel_pos option[value="+element+"]").removeAttr('selected');
                });
                $('#form_output').html("");
            });
            //Inicia subasta
            $('#auction_form').on('submit', function(event){
                event.preventDefault();
                var form_data = $(this).serialize();
                if(confirm('Confirme que desea cargar la subasta'))
                {
                    $.ajax({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('home.loadauction') }}",
                        method:"POST",
                        data:{
                            'idPlayer': $("#search_result option:selected").val(),
                            'idTeam': $("#sel_equipos").val(),
                            'txt_player': $("#txt_player").val(),
                            'final_prize_int': $("#txt_valor_puja option:selected").val(),
                            'position_txt': $("#sel_pos").val(),
                            'txt_player_mlb': $('#txt_player_mlb').val(),
                            'txt_player_photo': $('#txt_player_photo').val(),
                            'playerIDapi': $("#playerIDapi").val()
                        },
                        dataType:"json",
                        success:function(data){
                            if(data.error.length > 0){
                                var error_html = '';
                                for(var count = 0;count < data.error.length;count++){
                                    error_html = '<div class="alert alert-danger">'+data.error[count]+'</div>';
                                }
                                $('#form_output').html(error_html);
                            }else{
                                $('#form_output').html(data.success);
                                $('#auction_form')[0].reset();
                                setTimeout(function(){
                                    $('#auctionModal').modal('hide');
                                    location.reload(true);
                                }, 2000);
                            }
                        }
                    });
                }
            });
            //Mostrar Div de Cambio de Password
            $("#toggleUsrPwd").on('click',function(){
                $("#mostrarToggle").toggle();
            });
            //Actualiza Precio a Jugador
            $("#actualizaPrecio").on('click',function(){
                if($("#txt_valor").val().trim() != "" && (! isNaN(parseInt($("#txt_valor").val().trim())))){
                    //alert("SI LO VA A CAMBIAR:"+$("#txt_valor").val().trim());
                    if (confirm("Está Seguro de Cambiar el precio al jugador?")) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ route('home.updatePlayerPrice') }}",
                            method: "POST",
                            data: {
                                idplayer: $("#playerIDapi").val(),
                                precio: $("#txt_valor").val().trim()
                            },
                            dataType: "json",
                            success: function (data) {
                                alert(data);
                                console.log(data);
                            },

                        });
                    }


                }else{
                    alert("Por Favor Seleccione un jugador o Coloque un precio correcto");
                }
            });
            //Cambiar PWS
            $("#cambiaPwd").on('click',function() {
                if ($("#email_pwd").val().trim() != "" && $("#data_pwd").val().trim() != "") {
                    if (confirm("Está Seguro de Cambiar el password al usuario?")) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ route('home.generatepwd') }}",
                            method: "POST",
                            data: {
                                email: $("#email_pwd").val().trim(),
                                pwd: $("#data_pwd").val().trim()
                            },
                            dataType: "json",
                            success: function (data) {
                                alert(data);
                                console.log(data);
                                $("#email_pwd").val("");
                                $("#data_pwd").val("");
                            },

                        });
                    }
                }
                else{
                    alert("Error, asegúrese de que los campos email o password no estén vacíos!");
                    $("#email_pwd").focus();
                }
            });
            //Player live search
            $('#txt_player_search').keyup(function(event){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('home.searchplayer') }}",
                    method:"GET",
                    data:{
                        search_player: $('#txt_player_search').val().trim()
                    },
                    dataType:"json",
                    success:function(data){
                        $("#search_result").empty();
                        playersArray = new Array();
                        if(data.length > 0){
                            for(var i=0;i<data.length;i++)
                            {
                                playersArray[i] = data[i];
                                $("#search_result").append('<option value="'+i+'">'+data[i].YahooName+'</option>');
                            }
                        }else{
                            $("#search_result").append('<option selected="selected">No hay Coincidencias</option>');
                        }
                    }
                });
            });

            //Select a Player
            $("#search_result").on('click',function(){
                $('#action').prop("disabled", true);
                //Re-habilitamos los options
                for(var it=0; it < 191 ; it++){
                    $("#txt_valor_puja option[value="+it+"]").removeAttr('disabled');
                    $("#txt_valor_puja option[value="+it+"]").removeAttr('selected');
                }
                var valorYahoo = playersArray[$("option:selected",this).val()].YahooPrice;
                var playerIDapi = playersArray[$("option:selected",this).val()].PlayerID;
                $("#txt_valor_puja option[value=2]").attr('selected','selected');
                //Re-habilitamos las posiciones
                positionsArr.forEach(element => function(){
                    $("#sel_pos option[value="+element+"]").removeAttr('disabled');
                    $("#sel_pos option[value="+element+"]").removeAttr('selected');
                });
                $("#imgPlayer").attr('src',playersArray[$("option:selected",this).val()].PhotoUrl);
                $("#txt_player_photo").val(playersArray[$("option:selected",this).val()].PhotoUrl);
                $("#imgPlayer").attr('alt',playersArray[$("option:selected",this).val()].YahooName);
                $("#txt_player").val(playersArray[$("option:selected",this).val()].YahooName);
                $("#txt_player_mlb").val(playersArray[$("option:selected",this).val()].Team);
                $("#playerPos").text(playersArray[$("option:selected",this).val()].Position);
                $("#playerOrig").text(playersArray[$("option:selected",this).val()].BirthCity+"-"+playersArray[$("option:selected",this).val()].BirthCountry);
                $("#playerSt").text(playersArray[$("option:selected",this).val()].Status);
                $("#playerHlty").text(playersArray[$("option:selected",this).val()].InjuryStatus);
                $("#plJgos").text(playersArray[$("option:selected",this).val()].last_year_games);
                $("#plPtos").text(playersArray[$("option:selected",this).val()].last_year_points);
                $("#txt_valor").val(valorYahoo);


                $("#imgPlayer1").attr('src', playersArray[$("option:selected", this).val()].PhotoUrl);
                $("#txt_player_photo1").val(playersArray[$("option:selected", this).val()].PhotoUrl);
                $("#imgPlayer1").attr('alt', playersArray[$("option:selected", this).val()].YahooName);
                $("#txt_player1").val(playersArray[$("option:selected", this).val()].YahooName);
                $("#txt_player_mlb1").val(playersArray[$("option:selected", this).val()].Team);
                $("#playerPos1").text(playersArray[$("option:selected", this).val()].Position);
                $("#playerOrig1").text(playersArray[$("option:selected", this).val()].BirthCity + "-" + playersArray[$("option:selected", this).val()].BirthCountry);
                $("#playerSt1").text(playersArray[$("option:selected", this).val()].Status);
                $("#playerHlty1").text(playersArray[$("option:selected", this).val()].InjuryStatus);
                $("#plJgos1").text(playersArray[$("option:selected", this).val()].last_year_games);
                $("#plPtos1").text(playersArray[$("option:selected", this).val()].last_year_points);
                $("#playerIDapi").val(playerIDapi);
                $("#txt_valor1").val(valorYahoo);

                //Validamos POS
                /* opciones:
                    2B,3B,C,CF,DH,LF,OF,P,PH,PR,RF,RP,SP,SS
                */
                switch(playersArray[$("option:selected",this).val()].Position){
                    case "P": case "SP":  case "RP":
                            $("#sel_pos option[value=P]").removeAttr('disabled');
                            $("#sel_pos option[value=P]").attr('selected','selected');
                            $("#sel_pos option[value=RP]").removeAttr('disabled');
                            $("#sel_pos option[value=RP]").removeAttr('selected');

                            $("#sel_pos option[value=C]").attr('disabled','disabled');
                            $("#sel_pos option[value=CI]").attr('disabled','disabled');
                            $("#sel_pos option[value=UTY]").attr('disabled','disabled');
                            $("#sel_pos option[value=MI]").attr('disabled','disabled');
                            $("#sel_pos option[value=OF]").attr('disabled','disabled');

                            $("#sel_pos option[value=C]").removeAttr('selected');
                            $("#sel_pos option[value=CI]").removeAttr('selected');
                            $("#sel_pos option[value=UTY]").removeAttr('selected');
                            $("#sel_pos option[value=MI]").removeAttr('selected');
                            $("#sel_pos option[value=OF]").removeAttr('selected');
                        break;
                    default:
                            $("#sel_pos option[value=C]").removeAttr('disabled');
                            $("#sel_pos option[value=C]").attr('selected','selected');
                            $("#sel_pos option[value=CI]").removeAttr('disabled');
                            $("#sel_pos option[value=UTY]").removeAttr('disabled');
                            $("#sel_pos option[value=MI]").removeAttr('disabled');
                            $("#sel_pos option[value=OF]").removeAttr('disabled');
                            $("#sel_pos option[value=CI]").removeAttr('selected');
                            $("#sel_pos option[value=UTY]").removeAttr('selected');
                            $("#sel_pos option[value=MI]").removeAttr('selected');
                            $("#sel_pos option[value=OF]").removeAttr('selected');

                            $("#sel_pos option[value=P]").attr('disabled','disabled');
                            $("#sel_pos option[value=RP]").attr('disabled','disabled');
                            $("#sel_pos option[value=RP]").removeAttr('selected');
                            $("#sel_pos option[value=P]").removeAttr('selected');
                        break;
                }

                for(var t=valorYahoo-1; t>0 ; t--){
                    $("#txt_valor_puja option[value="+t+"]").attr('disabled','disabled');
                }
                //Set Value
                console.log("BASE-YAHOO:"+valorYahoo);
                $("#txt_valor_puja option[value="+(parseInt(valorYahoo))+"]").attr('selected','selected');
            });
        }); //END DOCUMENT READY
    </script>
</body>
</html>
