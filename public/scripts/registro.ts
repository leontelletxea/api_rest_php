/// <reference path="../node_modules/@types/jquery/index.d.ts" />

function RegistrarUsuario(){

    let nombre = $("#txtNombre").val();
    let apellido = $("#txtApellido").val();
    let correo = $("#txtCorreo").val();
    let clave = $("#txtClave").val();
    let perfil = $("#cmbPerfil").val();
    let foto = $("#foto").prop("files")[0];

    let dato : any = {
        "correo":correo,
        "clave":clave,
        "nombre":nombre,
        "apellido":apellido,
        "perfil":perfil,
    };

    let form = new FormData();
    let usuario = JSON.stringify(dato);

    form.append("usuario",usuario);
    form.append("foto",foto);

    $.ajax({
        type: 'POST',
        url: API + "usuarios",
        dataType: "json",
        data: form,
        async: true,
        contentType: false,
        processData: false
    })
    .done(function (resultado : any){
        if(resultado.exito){
            $(location).attr("href", API + 'front-end-login');
        }
        else{
            $("#mensajeError").html(CrearAlertaRegistro(resultado.mensaje, 'danger'));
        }
    }).fail(function (jqXHR:any, textStatus:any, errorThrown:any) {
        let datos = (jqXHR.responseText);
        $("#mensajeError").html(CrearAlertaRegistro(datos.mensaje, 'danger'));
    });
}