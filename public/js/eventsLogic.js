$(document).ready(function()
{
    $(".dropdown-toggle").dropdown();
    // Cambia seleccion del Evento
    $("#idEventsSelect").on('change',function() {
       $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: config.routes.searchevents,
            method: "GET",
            data: {
                idEvent: $("#idEventsSelect option:selected").val().trim()
            },
            dataType: "json",
            success: function (data) {
                //var code = "<tr><th>Posicion</th><th>Imagen</th><th>Jugador</th><th>Precio</th><th>MLB</th></tr>";
                //console.log(data[0]);
                if(data.length > 0)
                {
                    //Array del Evento
                    data[0].forEach(element => {
                        if(element !== undefined)
                        {
                            $("#idEventTitle").html(element.name_txt);
                            $("#idEventDetails").html("Pais: "+element.country_txt+" Year: "+element.year_int+" Comisionado: "+element.commissioner_txt);
                        }
                    });
                    //Array del equipo
                    var index = 0;
                    var row1="<div class='row'>";
                    var row2="<div class='row'>";
                    data[1].forEach(item => {
                        if(item !== undefined){
                            if(index < 5)
                            {
                                row1 +="<div class='col-md-2'><div class='card' align='center'><table><tr><td style='text-align:center;'>";
                                row1 +="<img style='height:75px; weigth:75px;' src='"+item.pl_avatar+"' /><BR /> "+item.Jugador+"<BR /> ";
                                row1 +=" Posición: "+item.posPlayer+"<BR /> Precio: "+item.PrecioFinal+"</td></tr></table></div></div>";
                            }
                            else
                            {
                                row2 +="<div class='col-md-2'><div class='card' align='center'><table><tr><td style='text-align:center;'>";
                                row2 +="<img style='height:75px; weigth:75px;' src='"+item.pl_avatar+"' /><BR /> "+item.Jugador+"<BR /> ";
                                row2 +=" Posición: "+item.posPlayer+"<BR /> Precio: "+item.PrecioFinal+"</td></tr></table></div></div>";
                            }
                            //alert(JSON.stringify(x));
                            //console.log("row="+JSON.stringify(element));
                            //code +="<tr><td>"+element.posPlayer+"</td><td><img src='"+element.pl_avatar+"' height='75px' weight='75px'/></td><td>"+element.Jugador+"</td><td>"+element.PrecioFinal+"</td><td>"+element.MLB+"</td></tr>";
                            index++;
                        }
                    });
                    row1 +="</div>";
                    row2 +="</div>";
                    $("#equipoEvent").html(row1+row2);
                }
                else
                {
                    $("#equipoEvent").html("NO HAY DATA ASOCIADA AL EVENTO");
                }
            },
        });       
    });
});