/// <reference path="../node_modules/@types/jquery/index.d.ts" />

const API : string = 'http://api_slim4/';

$(function(){
    $("#btnQuieroRegistrarme").on("click", function(){
        $(location).attr('href', API + 'front-end-registro');
    });
});

function Login(e:Event):void{
    e.preventDefault();
    let correo = $("#txtCorreoLogin").val();
    let clave = $("#txtClaveLogin").val();
    let dato : any = {
        correo : correo,
        clave : clave
    };    
    $.ajax({
        type: 'POST',
        url: API + "login",
        dataType: "json",
        data: {"user":JSON.stringify(dato)},
        async: true
    })
    .done(function (resultado : any)
    {
        if(resultado.exito){
            localStorage.setItem("token", resultado.jwt);
            $(location).attr("href", API + 'front-end-principal');
        }
        else{
            $('#mensajeError').html(CrearAlertaLogin(resultado.mensaje, 'danger'));
        }
    }).fail(function (jqXHR:any, textStatus:any, errorThrown:any) {
        let datos = (jqXHR.responseText);
        console.log(datos);
    });

}