/// <reference path="../node_modules/@types/jquery/index.d.ts" />
function ValidarCampos(e) {
    e.preventDefault();
    var correo = $('#txtCorreoLogin').val();
    var clave = $('#txtClaveLogin').val();
    var mensaje = 'Ambos cambos estan vacios';
    var retorno = false;
    // alert(`${correo}  ${clave}`);
    if (correo.length != 0 || correo != '') {
        if (clave.length != 0 || clave != '') {
            retorno = true;
        }
        mensaje = 'La clave esta vacia';
    }
    if (clave.length != 0 || clave != '') {
        mensaje = 'El correo esta vacio';
    }
    if (!retorno) {
        $('#mensajeError').html(CrearAlertaLogin(mensaje, 'danger'));
    }
    else {
        Login(e);
    }
}
function CrearAlertaLogin(mensaje, tipo) {
    if (tipo === void 0) { tipo = "success"; }
    var alerta = "<div class=\"alert alert-" + tipo + " alert-dismissible fade show\" role=\"alert\">\n                            <strong>Atencion!</strong> " + mensaje + "\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                            <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                        </div>";
    return alerta;
}
