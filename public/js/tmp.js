var playersArray = new Array();
var idPlayerEvent = "";
const positionsArr = ["C","CI","MI","UTY","OF","P","RP","BN"];
var montoBase = 0;
var totalGastado = 0;
var $dropdown = $("#txt_valor_puja");
//Máximo a ofertar por un jugador
for(var i=1;i<=191;i++){
    $dropdown.append(new Option(i, i));
}
//Muestra el Dialog
$('#add_auction').on('click', function(){
    $('#auctionModal').modal('show');
    $('#auction_form')[0].reset();
    $('#form_output').html('');
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
//Player live search
$('#txt_player_search').keyup(function(event){
$.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: "{{ route('home.searchplayer') }}",
    method:"GET",
    data:{
        search_player:$('#txt_player_search').val().trim()
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
$("#txt_valor_puja option[value="+(parseInt(valorYahoo)+1)+"]").attr('selected','selected');
});
