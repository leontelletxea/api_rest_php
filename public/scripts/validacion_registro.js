/// <reference path="../node_modules/@types/jquery/index.d.ts" />
function AdministradoraDeValidaciones(e) {
    e.preventDefault();
    var retorno = true;
    var alert = "";
    if (ValidarCamposVacios($("#txtCorreo").val()) == false) {
        retorno = false;
        $('#correoError').html(CrearAlertaRegistro("Campo correo vacio", "danger"));
    }
    if (ValidarCamposVacios($("#txtClave").val()) == false) {
        if (ValidarCantidadCaracteres(parseInt($("#txtClave").val()), 4, 8) == false) {
            retorno = false;
            $('#claveError').html(CrearAlertaRegistro("Campo clave vacio o  rango incorrecto", "danger"));
        }
    }
    if (ValidarCamposVacios($("#txtNombre").val()) == false) {
        if (ValidarCantidadCaracteres(parseInt($("#txtNombre").val()), 4, 10) == false) {
            retorno = false;
            $('#nombreError').html(CrearAlertaRegistro("campo nombre vacio o  rango incorrecto", "danger"));
        }
    }
    if (ValidarCamposVacios($("#txtApellido").val()) == false) {
        if (ValidarCantidadCaracteres(parseInt($("#txtApellido").val()), 2, 15) == false) {
            retorno = false;
            $('#apellidoError').html(CrearAlertaRegistro("campo apellido vacio o  rango incorrecto", "danger"));
        }
    }
    if (ValidarCamposVacios($("#cmbPerfil").val()) == false) {
        if (ValidarCombo($("#cmbPerfil").val(), "nulo") == false) {
            retorno = false;
            $('#perfilError').html(CrearAlertaRegistro("Campo perfil incorrecto o vacio", "danger"));
        }
    }
    if (ValidarCamposVacios($("#foto").val()) == false) {
        retorno = false;
        $('#fotoError').html(CrearAlertaRegistro("Campo file vacio", "danger"));
    }
    if (retorno) {
        RegistrarUsuario();
    }
}
function ValidarCamposVacios(idCampo) {
    if (idCampo.length != 0 || idCampo != '')
        return true;
    return false;
}
function ValidarCantidadCaracteres(cantidadLetras, minimo, maximo) {
    if (cantidadLetras >= minimo && cantidadLetras <= maximo)
        return true;
    return false;
}
function ValidarCombo(idCampo, valorNoDeseado) {
    if (idCampo == valorNoDeseado)
        return false;
    return true;
}
function CrearAlertaRegistro(mensaje, tipo) {
    if (tipo === void 0) { tipo = "success"; }
    var alerta = "<div class=\"alert alert-" + tipo + " alert-dismissible fade show\" role=\"alert\">\n                            <strong>Atencion!</strong> " + mensaje + "\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                            <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                        </div>";
    return alerta;
}
