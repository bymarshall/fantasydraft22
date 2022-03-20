var playersArray = new Array();
var playersArrayFavs = new Array();
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

// Subscribe to the channel we specified in our Laravel Event
var channel = pusher.subscribe('auction-created-channel');
var channel2 = pusher.subscribe('closeBidChannel');
//var channel3 = pusher.subscribe('fantasyTeamsChannel');
var channel4 = pusher.subscribe('winningBidsChannel');
//var channel5 = pusher.subscribe('auctionLoaded');
var channel6 = pusher.subscribe('deleteBidChannel');
//Delete Auction deleteAuction
var isAuctionActive = false;

//Eliminar una subasta ya creada
function deleteAuction(idAuction){
    if (typeof idAuction !== 'undefined') {
        if(confirm("Está seguro que desea eliminar la subasta nro:" + idAuction+"?")) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: config.routes.deleteauction,
                method: "POST",
                data: {
                    'idAuction': idAuction
                },
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $.each(data, function() {
                        var key = Object.keys(this)[32];
                        var value = this[key];
                        console.log("La subasta #" + value + " ha sido borrada!");
                        alert("La subasta #" + value + " ha sido borrada!");                                            
                    });                    
                    location.reload(true);
                }
            });
        }
    }
};

//Eliminar una favorito
function deleteFavs(idFav, namePlayerFav){
    if (typeof idFav !== 'undefined') {
        if(confirm("Realmente desea eliminar al jugador "+namePlayerFav+"?")){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: config.routes.deletefavs,
                method: "POST",
                data: {
                    'idFav': idFav,
                    'idTeamsEvents' : $("#idTeamsEventFavs").val()                          
                },
                dataType: "json",
                success: function (data) {
                    console.log(data)
                    alert(data);
                    location.reload(true);
                }
            });
        }
    }
};        

///JQuery Initiator
$(document).ready(function(){
    deleteAuction();
    deleteFavs();
    $(".dropdown-toggle").dropdown();
    // $('#auction_form')[0].reset();
    // $('#form_output').html('');    
    //$( "#auctionModal" ).dialog({autoOpen: false, closeOnEscape: false});
    //$( ".selector" ).dialog({ closeOnEscape: false });
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

    channel2.bind('auction-close', function(data) {
        console.log(data);
        isAuctionActive = false;
        cleanAuction();
    });
    
    function cleanAuction()
    {
        $('#action').prop("disabled", true);
        $('#cancelarSubasta').prop("disabled", true);
        $("#favPlayer").html("");
        $("#enSubasta").html("");
        $("#imgPlayerAuction").attr('src',"");
        $("#txt_player_auction").val("");
        $("#playerPosAuction").text("");
        $("#playerStAuction").text("");
        $("#playerPosRanking").text("");
        $("#playerHltyAuction").text("");
        $("#plJgosAuction").text("");
        $("#txt_player_mlb_auction").val("");
        $("#plExpAuction").text("");
        $("#plPtosAuction").text("");
        $("#pitIp").text("");
        $("#pitWin").text("");
        $("#pitLost").text("");
        $("#pitSv").text("");
        $("#pitHld").text("");
        $("#pitQs").text("");
        $("#pitBsv").text("");
        $("#playerRosteredPit").text("");
        $("#batAb").text("");
        $("#batHr").text("");
        $("#batCe").text("");
        $("#batSb").text("");
        $("#batK").text("");
        $("#playerRosteredBat").text("");
        $("#divCurrentAuction").hide();
    }

    channel.bind('auction-created', function(data) {
        isAuctionActive = true;
        $("#favPlayer").html("");
        $("#enSubasta").html("");
        $("#divCurrentAuction").show();
        console.log(data.message[0]);
        var currentAuctionPlayer = Object.values(data.message[0]);
        //console.log("Auction: "+$("#idTeamFavs").val()[0]);
        $("#enSubasta").html('<div class="alert alert-success">En Subasta: '+currentAuctionPlayer[33]+'</div>');

        $("#idTeamFavs > option").each(function() {
            if (this.value == currentAuctionPlayer[0])
                $("#favPlayer").html('<div class="alert alert-danger blinker">Este Jugador esta en tus Favoritos</div>');
        });

        //Imagen del Jusgador
        $("#imgPlayerAuction").attr('src',currentAuctionPlayer[14]);
        $("#txt_player_auction").val(currentAuctionPlayer[33]);
        $("#playerPosAuction").text(currentAuctionPlayer[6]);
        $("#playerStAuction").text(currentAuctionPlayer[2]);
        $("#playerPosRanking").text(currentAuctionPlayer[17]);
        $("#playerHltyAuction").text(currentAuctionPlayer[31]);
        $("#plJgosAuction").text(currentAuctionPlayer[16]);
        $("#txt_player_mlb_auction").val(currentAuctionPlayer[4]);
        $("#plExpAuction").text(currentAuctionPlayer[34]);
        $("#plPtosAuction").text(currentAuctionPlayer[15]);
        //Datos del Jugador
        if(currentAuctionPlayer[5] == "P"){
            $("#trPitchers").show();
            $("#trBatters").hide();
            $("#pitIp").text(currentAuctionPlayer[19]);
            $("#pitWin").text(currentAuctionPlayer[20]);
            $("#pitLost").text(currentAuctionPlayer[21]);
            $("#pitSv").text(currentAuctionPlayer[22]);
            $("#pitHld").text(currentAuctionPlayer[23]);
            $("#pitQs").text(currentAuctionPlayer[24]);
            $("#pitBsv").text(currentAuctionPlayer[25]);
            $("#playerRosteredPit").text(currentAuctionPlayer[18]);
        }else{
            $("#trPitchers").hide();
            $("#trBatters").show();
            $("#batAb").text(currentAuctionPlayer[26]);
            $("#batHr").text(currentAuctionPlayer[27]);
            $("#batCe").text(currentAuctionPlayer[28]);
            $("#batSb").text(currentAuctionPlayer[29]);
            $("#batK").text(currentAuctionPlayer[30]);
            $("#playerRosteredBat").text(currentAuctionPlayer[18]);
        }
     });

    channel6.bind('auction-delete', function(data) {
        alert(data.message);
        location.reload(true);
    });

    var $dropdown = $("#txt_valor_puja");
    for(var i=1;i<=191;i++){
        $dropdown.append(new Option(i, i));
    }

    //Muestra el Dialog
    // $('#add_auction').on('click', function(){
    //     $('#auctionModal').modal('show');
    //     $('#auction_form')[0].reset();
    //     $('#form_output').html('');
    // });

    //Inicia Subasta Manual
    $('#iniciaSubasta').on('click', function(){
       if($("#txt_player").val().trim() != ""){ 

        //$('#iniciaSubasta').prop("disabled", true);
        $('#action').removeAttr('disabled');                
        $('#cancelarSubasta').removeAttr('disabled');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: config.routes.initmanualauction,
                method:"POST",
                data:{
                    'idPlayer': $("#playerIDapi").val(),
                    'namePlayer': $("#txt_player").val()
                },
                dataType:"json",
                success:function(data){
                    console.log(data);
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

    //Cancel Auction cancelarSubasta
    $('#cancelarSubasta').on('click', function()
    {
        if($("#txt_player").val().trim() != ""){ 
           $('#iniciaSubasta').removeAttr('disabled'); 
           $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: config.routes.cancelauction,
                method:"POST",
                data:{},
                dataType:"json",
                success:function(data){}
            });                     
        }else{
           alert("Debe seleccionar un jugador!");
           $("#txt_player_search").focus();
       }
    });


    /* INICIO CODIGO PARA AGREGAR PLAYERS A FAVORITOS*/
    //Agrega Player a los favoritos del Equipo
    $('#agregarFavs').on('click', function()
    {
        if($("#txt_player_favs").val().trim() != ""){ 
           $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: config.routes.addfavs,
                method:"POST",
                data:{
                    'idPlayer': $("#playerIDapi_favs").val().trim(),
                    'idTeamsEvents' : $("#idTeamsEventFavs").val(),
                    'idEvent': $("#idEventFavs").val()
                },
                dataType:"json",
                success:function(data)
                {
                    alert(data+" "+$("#txt_player_favs").val().trim());
                    console.log(data+" "+$("#txt_player_favs").val().trim());
                    window.location.reload(true);
                }
            });                     
        }else{
           alert("Debe seleccionar un jugador!");
           $("#txt_player_search_favs").focus();
       }
    });

    //Player live search FAVS
    $('#txt_player_search_favs').keyup(function(event)
    {
        $("#imgPlayer_favs").attr('src', '');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: config.routes.search,
            method:"GET",
            data:{
                search_player: $('#txt_player_search_favs').val().trim()
            },
            dataType:"json",
            success:function(data){
                $("#search_result_favs").empty();
                playersArrayFavs = new Array();
                if(data.length > 0){
                    for(var i=0;i<data.length;i++)
                    {
                        playersArrayFavs[i] = data[i];
                        $("#search_result_favs").append('<option value="'+i+'">'+data[i].YahooName+'</option>');
                    }
                }else{
                    $("#search_result_favs").append('<option selected="selected">No hay Coincidencias</option>');
                }
            }
        });
    });

    //Select a Player in favs
    $("#search_result_favs").on('click',function(){
        $("#imgPlayer_favs").attr('src', '');
        //Re-habilitamos los options
        var valorYahooFavs = playersArrayFavs[$("option:selected",this).val()].YahooPrice;
        var playerIDapiFavs = playersArrayFavs[$("option:selected",this).val()].PlayerID;
        //$("#txt_valor_puja option[value=2]").attr('selected','selected');
        //Re-habilitamos las posiciones
        positionsArr.forEach(element => function(){
            $("#sel_pos_favs option[value="+element+"]").removeAttr('disabled');
            $("#sel_pos_favs option[value="+element+"]").removeAttr('selected');
        });

        $("#imgPlayer_favs").attr('src',playersArrayFavs[$("option:selected",this).val()].PhotoUrl);
        $("#txt_player_photo_favs").val(playersArrayFavs[$("option:selected",this).val()].PhotoUrl);
        $("#imgPlayer_favs").attr('alt',playersArrayFavs[$("option:selected",this).val()].YahooName);
        $("#txt_player_favs").val(playersArrayFavs[$("option:selected",this).val()].YahooName);
        $("#txt_player_mlb_favs").val(playersArrayFavs[$("option:selected",this).val()].Team);
        $("#playerPos_favs").text(playersArrayFavs[$("option:selected",this).val()].Position);
        $("#playerOrig_favs").text(playersArrayFavs[$("option:selected",this).val()].BirthCity+"-"+playersArrayFavs[$("option:selected",this).val()].BirthCountry);
        $("#playerSt_favs").text(playersArrayFavs[$("option:selected",this).val()].Status);
        $("#playerHlty_favs").text(playersArrayFavs[$("option:selected",this).val()].InjuryStatus);
        $("#plJgos_favs").text(playersArrayFavs[$("option:selected",this).val()].last_year_games);
        $("#plPtos_favs").text(playersArrayFavs[$("option:selected",this).val()].last_year_points);
        $("#txt_valor_favs").val(valorYahooFavs);
        $("#playerIDapi_favs").val(playerIDapiFavs);

        //Validamos POS
        /* opciones:
            2B,3B,C,CF,DH,LF,OF,P,PH,PR,RF,RP,SP,SS
        */
        switch(playersArrayFavs[$("option:selected",this).val()].Position){
            case "P": case "SP":  case "RP":
                    $("#sel_pos_favs option[value=P]").removeAttr('disabled');
                    $("#sel_pos_favs option[value=P]").attr('selected','selected');
                    $("#sel_pos_favs option[value=RP]").removeAttr('disabled');
                    $("#sel_pos_favs option[value=RP]").removeAttr('selected');

                    $("#sel_pos_favs option[value=C]").attr('disabled','disabled');
                    $("#sel_pos_favs option[value=CI]").attr('disabled','disabled');
                    $("#sel_pos_favs option[value=UTY]").attr('disabled','disabled');
                    $("#sel_pos_favs option[value=MI]").attr('disabled','disabled');
                    $("#sel_pos_favs option[value=OF]").attr('disabled','disabled');

                    $("#sel_pos_favs option[value=C]").removeAttr('selected');
                    $("#sel_pos_favs option[value=CI]").removeAttr('selected');
                    $("#sel_pos_favs option[value=UTY]").removeAttr('selected');
                    $("#sel_pos_favs option[value=MI]").removeAttr('selected');
                    $("#sel_pos_favs option[value=OF]").removeAttr('selected');
                break;
            default:
                    $("#sel_pos_favs option[value=C]").removeAttr('disabled');
                    $("#sel_pos_favs option[value=C]").attr('selected','selected');
                    $("#sel_pos_favs option[value=CI]").removeAttr('disabled');
                    $("#sel_pos_favs option[value=UTY]").removeAttr('disabled');
                    $("#sel_pos_favs option[value=MI]").removeAttr('disabled');
                    $("#sel_pos_favs option[value=OF]").removeAttr('disabled');
                    $("#sel_pos_favs option[value=CI]").removeAttr('selected');
                    $("#sel_pos_favs option[value=UTY]").removeAttr('selected');
                    $("#sel_pos_favs option[value=MI]").removeAttr('selected');
                    $("#sel_pos_favs option[value=OF]").removeAttr('selected');

                    $("#sel_pos_favs option[value=P]").attr('disabled','disabled');
                    $("#sel_pos_favs option[value=RP]").attr('disabled','disabled');
                    $("#sel_pos_favs option[value=RP]").removeAttr('selected');
                    $("#sel_pos_favs option[value=P]").removeAttr('selected');
                break;
        }

        // for(var t=valorYahoo-1; t>0 ; t--){
        //     $("#txt_valor_puja option[value="+t+"]").attr('disabled','disabled');
        // }
        // //Set Value
        // console.log("BASE-YAHOO:"+valorYahoo);
        // $("#txt_valor_puja option[value="+(parseInt(valorYahoo))+"]").attr('selected','selected');
    });
    /* FIN CODIGO PARA AGREGAR PLAYERS A FAVORITOS*/

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

    //Guarda la subasta
    $('#auction_form').on('submit', function(event){
        event.preventDefault();
        $("#favPlayer").html("");
        $("#enSubasta").html("");                
        var form_data = $(this).serialize();
        if(confirm('Confirme que desea cargar la subasta'))
        {
            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: config.routes.loadauction,
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
                    url: config.routes.updateprice,
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
                    url: config.routes.changepassword,
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
        $("#imgPlayer").attr('src', '');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: config.routes.search,
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
        if(isAuctionActive){
            //disparamos el ajax para cancelar la subasta
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: config.routes.cancelauction,
                method:"POST",
                data:{},
                dataType:"json",
                success:function(data){ }
            })
        }

        $("#imgPlayer").attr('src', '');
        $('#action').prop("disabled", true);
        $('#cancelarSubasta').prop("disabled", true);
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