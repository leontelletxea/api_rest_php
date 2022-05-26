/// <reference path="../node_modules/@types/jquery/index.d.ts" />
var API = 'http://api_slim4/';
$(function () {
    $("#btnQuieroRegistrarme").on("click", function () {
        $(location).attr('href', API + 'front-end-registro');
    });
});
function Login(e) {
    e.preventDefault();
    var correo = $("#txtCorreoLogin").val();
    var clave = $("#txtClaveLogin").val();
    var dato = {
        correo: correo,
        clave: clave
    };
    $.ajax({
        type: 'POST',
        url: API + "login",
        dataType: "json",
        data: { "user": JSON.stringify(dato) },
        async: true
    })
        .done(function (resultado) {
        if (resultado.exito) {
            localStorage.setItem("token", resultado.jwt);
            $(location).attr("href", API + 'front-end-principal');
        }
        else {
            $('#mensajeError').html(CrearAlertaLogin(resultado.mensaje, 'danger'));
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        var datos = (jqXHR.responseText);
        console.log(datos);
    });
}
