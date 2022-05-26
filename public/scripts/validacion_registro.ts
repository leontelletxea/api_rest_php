/// <reference path="../node_modules/@types/jquery/index.d.ts" />

function AdministradoraDeValidaciones(e : Event)
{
    e.preventDefault();
    var retorno:boolean = true;
    var alert:string = "";
    if(ValidarCamposVacios(<string>$("#txtCorreo").val()) == false){
        retorno = false;
        $('#correoError').html(CrearAlertaRegistro("Campo correo vacio","danger"));
    }
    if(ValidarCamposVacios(<string>$("#txtClave").val()) == false){
        if(ValidarCantidadCaracteres(parseInt(<string>$("#txtClave").val()),4,8) == false){
            retorno = false;
            $('#claveError').html(CrearAlertaRegistro("Campo clave vacio o  rango incorrecto","danger"));   
        }
    }
    if(ValidarCamposVacios(<string>$("#txtNombre").val()) == false){
        if(ValidarCantidadCaracteres(parseInt(<string>$("#txtNombre").val()),4,10) == false){
            retorno = false;
            $('#nombreError').html(CrearAlertaRegistro("campo nombre vacio o  rango incorrecto","danger"));
        }
    }
    if(ValidarCamposVacios(<string>$("#txtApellido").val()) == false){
        if(ValidarCantidadCaracteres(parseInt(<string>$("#txtApellido").val()),2,15) == false){
            retorno = false;
            $('#apellidoError').html(CrearAlertaRegistro("campo apellido vacio o  rango incorrecto","danger"));
        }
    }
    if(ValidarCamposVacios(<string>$("#cmbPerfil").val()) == false){
        if(ValidarCombo(<string>$("#cmbPerfil").val(),"nulo") == false){
            retorno = false;
            $('#perfilError').html(CrearAlertaRegistro("Campo perfil incorrecto o vacio","danger"));           
        }
    }
    if(ValidarCamposVacios(<string>$("#foto").val()) == false){
        retorno = false;
        $('#fotoError').html(CrearAlertaRegistro("Campo file vacio","danger"));
    }
    if(retorno){
        RegistrarUsuario();
    }
}

function ValidarCamposVacios(idCampo : string) : boolean
{
    if(idCampo.length != 0 || idCampo != '')
        return true;
    return false;
}

function ValidarCantidadCaracteres(cantidadLetras : number, minimo : number, maximo : number): boolean
{
    if(cantidadLetras >= minimo && cantidadLetras <= maximo)
        return true;
    return false;
}

function ValidarCombo(idCampo : string, valorNoDeseado : string): boolean
{
    if(idCampo == valorNoDeseado)
        return false;
    return true;
}

function CrearAlertaRegistro(mensaje : string, tipo : string = "success") : string
{
    let alerta : string = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                            <strong>Atencion!</strong> ${mensaje}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>`;
    return alerta;
}