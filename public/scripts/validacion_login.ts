/// <reference path="../node_modules/@types/jquery/index.d.ts" />

function ValidarCampos(e : Event)
{
    e.preventDefault();
    let correo :any = $('#txtCorreoLogin').val();
    let clave :any = $('#txtClaveLogin').val();
    let mensaje = 'Ambos cambos estan vacios';
    let retorno : boolean = false;
    // alert(`${correo}  ${clave}`);
    if(correo.length != 0 || correo != '')
    {
        if(clave.length != 0 || clave != '')
        {
            retorno = true;
        }
        mensaje = 'La clave esta vacia';
    }
    if(clave.length != 0 || clave != '')
    {
        mensaje = 'El correo esta vacio';
    }

    if(!retorno)
    {
        $('#mensajeError').html(CrearAlertaLogin(mensaje, 'danger'));
    }
    else
    {
        Login(e);
    }
}

function CrearAlertaLogin(mensaje : string, tipo : string = "success") : string
{
    let alerta : string = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                            <strong>Atencion!</strong> ${mensaje}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>`;
    return alerta;
}