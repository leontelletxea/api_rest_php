/// <reference path="../node_modules/@types/jquery/index.d.ts" />
function RegistrarUsuario() {
    var nombre = $("#txtNombre").val();
    var apellido = $("#txtApellido").val();
    var correo = $("#txtCorreo").val();
    var clave = $("#txtClave").val();
    var perfil = $("#cmbPerfil").val();
    var foto = $("#foto").prop("files")[0];
    var dato = {
        "correo": correo,
        "clave": clave,
        "nombre": nombre,
        "apellido": apellido,
        "perfil": perfil
    };
    var form = new FormData();
    var usuario = JSON.stringify(dato);
    form.append("usuario", usuario);
    form.append("foto", foto);
    $.ajax({
        type: 'POST',
        url: API + "usuarios",
        dataType: "json",
        data: form,
        async: true,
        contentType: false,
        processData: false
    })
        .done(function (resultado) {
        if (resultado.exito) {
            $(location).attr("href", API + 'front-end-login');
        }
        else {
            $("#mensajeError").html(CrearAlertaRegistro(resultado.mensaje, 'danger'));
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        var datos = (jqXHR.responseText);
        $("#mensajeError").html(CrearAlertaRegistro(datos.mensaje, 'danger'));
    });
}
