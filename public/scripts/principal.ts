/// <reference path="../node_modules/@types/jquery/index.d.ts" />


$(function(){
    ObtenerListadoUsuarios();
    ObtenerListadoAutos();
    $('#altaAuto').html(MostrarFormulario('alta'));
})



function ObtenerListadoUsuarios() : void
{
    VerificarJWT();
    let token = localStorage.getItem("token");
    $.ajax({
        type: 'GET',
        url: API,
        dataType: "json",
        data: {},
        headers : {token : token},
        async: true
    })
    .done(function (resultado:any) {
        console.log(resultado);
        if(resultado.exito){
            $("#usuarios").html(ArmarTablaUsuarios(resultado.dato));
        }
    })
    .fail(function (jqXHR: any, textStatus: any, errorThrown: any)
    {
        let respuesta = JSON.parse(jqXHR.responseText);
        console.log(respuesta.mensaje);
    })
}

function ArmarTablaUsuarios(usuarios : any)
{
    let tabla:string = '<table class="table table-primary">';
    tabla += `<tr>
        <th>CORREO</th>
        <th>NOMBRE</th>
        <th>APELLIDO</th>
        <th>PERFIL</th>
        <th>FOTO</th>
    </tr>`;
    usuarios.forEach(usuario => {

        tabla += `<tr>
        <td>${usuario.correo}</td>
        <td>${usuario.nombre}</td>
        <td>${usuario.apellido}</td>
        <td>${usuario.perfil}</td>
        <td><img src="${usuario.foto}" alt="" width="50px" height="50px"></td>
        </tr>`;
    });
    tabla += "</table>";
    return tabla;
}

function ObtenerListadoAutos() : void
{
    VerificarJWT()
    let token = localStorage.getItem("token");
    $.ajax({
        type: 'GET',
        url: API + 'autos',
        dataType: "json",
        data: {},
        headers : {token : token},
        async: true
    })
    .done(function (resultado:any)
    {
        console.log(resultado);
        if(resultado.exito)
        {
            if(resultado.dato === null){
                $("#autos").html(CrearAlerta(resultado.mensaje, 'warning'));
            }
            else{
                $("#autos").html(ArmarTablaAutos(resultado.dato));
            }
        }
    })
    .fail(function (jqXHR: any, textStatus: any, errorThrown: any)
    {
        let respuesta = JSON.parse(jqXHR.responseText);
        console.log(respuesta.mensaje);
    })
}

function ArmarTablaAutos(autos : any)
{
    let tabla:string = '<table class="table table-primary">';
    tabla += `<tr>
        <th>MARCA</th>
        <th>COLOR</th>
        <th>MODELO</th>
        <th>PRECIO</th>
        <th>ELIMINAR</th>
        <th>MODIFICAR</th>
    </tr>`;
    autos.forEach(auto => {

        tabla += `<tr>
        <td>${auto.marca}</td>
        <td>${auto.color}</td>
        <td>${auto.modelo}</td>
        <td>${auto.precio}</td>
        <td><a href='#' class='btn btn-danger' data-action='eliminar' data-obj_auto='${JSON.stringify(auto)}' title='Eliminar'<i class='bi bi-pencil'></i>Eliminar</a></td>
        <td><a href='#' class='btn btn-success' data-action='modificar' data-obj_auto='${JSON.stringify(auto)}' title='Modificar'><i class='bi bi-pencil'></i>Modificar</a></td>
         </tr>`;
    });
    tabla += "</table>";
    return tabla;
}

function MostrarFormulario(accion:string, obj_auto:any=null):string 
{
    let boton = "";
    let funcion = "";

    switch (accion) {
        case "alta":
            boton = 'btnAgregar';
            funcion = 'AgregarAuto(event)';
            break;

        case "modificar":
            boton = 'btnModificar';
            funcion = 'ModificarAuto(event)';
            break;
    }

    let id = "";
    let color = "";
    let marca = "";
    let precio = "";
    let modelo = "";

    if (obj_auto !== null) 
    {
        id = obj_auto.id;
        color = obj_auto.color;
        marca = obj_auto.marca;
        precio = obj_auto.precio;
        modelo = obj_auto.modelo;       
    }

    let form:string = `<div class="row col-lg-6 col-md-8 col-sm-10 m-auto">
                            <div class="w-100 my-4">
                                <div class="p-3 rounded-5 " style="background-color: darkcyan;">
                                    <form>
                                        <div class="form-group d-flex">
                                            <input type="hidden" class="form-control " id="id" value="${id}" readonly >
                                        </div>
                                        <div class="form-group d-flex">
                                            <i class="fas fa-trademark p-2 mr-2 content-center rounded border controlador bg-light"></i>
                                            <input type="text" class="form-control" id="marca" placeholder="Marca" name="marca" value="${marca}" required>
                                        </div>
                                        <div class="form-group d-flex">
                                            <i class="fas fa-palette p-2 mr-2 content-center rounded border controlador bg-light"></i>
                                            <input type="text" class="form-control" id="color" placeholder="Color" name="color" value="${color}"  required>
                                        </div>
                                        <div class="form-group d-flex">
                                            <i class="fas fa-car p-2 mr-2 content-center rounded border controlador bg-light"></i>
                                            <input type="number" class="form-control" id="precio" placeholder="Precio" name="precio" value="${precio}" required>
                                        </div>
                                        <div class="form-group d-flex">
                                            <i class="fas fa-dollar-sign p-2 mr-2 content-center rounded border controlador bg-light"></i>
                                            <input type="text" class="form-control" id="modelo" placeholder="Modelo" name="modelo" value="${modelo}" required>
                                        </div>
                                        <div class="row justify-content-around mt-3 mb-5">
                                            <button type="submit" id="${boton}" class="col-4 btn btn-success" onclick="${funcion}">Agregar</button>
                                            <button type="reset" class="col-4 btn btn-warning">Limpiar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>`;
        return form;
}

function AgregarAuto(e : Event) : void 
{  
    VerificarJWT();
    e.preventDefault();

    let color = $("#color").val();
    let marca = $("#marca").val();
    let precio = $("#precio").val();
    let modelo = $("#modelo").val();
    
    // alert(`color: ${color} - marca: ${marca} - precio ${precio} - modelo ${modelo}`)

    let datos : any = {
        "color":color,
        "marca":marca,
        "precio":precio,
        "modelo":modelo
    };

    $.ajax({
        type: 'POST',
        url: API,
        dataType: "json",
        data: {"auto":JSON.stringify(datos)},
        async: true
    })
    .done(function (resultado:any) {
        if(resultado.exito)
            $("#error").html(CrearAlerta(resultado.mensaje, "success"));
        else
            $("#error").html(CrearAlerta(resultado.mensaje, "danger"));
    })
    .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {
        let retorno = JSON.stringify(jqXHR.responseText);
        let alerta:string = CrearAlerta(retorno, "danger");
        $("#error").html(alerta);
    });    
}

function EliminarAuto(id : string)
{
    VerificarJWT();
    let token = localStorage.getItem("token");

    $.ajax({
        type: 'DELETE',
        url: API,
        dataType: "json",
        data: JSON.stringify({'id_auto' : id}),
        headers : {token : token,},
        async: true
    })
    .done(function (resultado:any) {
        if(resultado.exito)
            ObtenerListadoAutos();
        else
            $("#error").html(CrearAlerta(resultado.mensaje, "warning"));
    })
    .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {
        let retorno = jqXHR.responseText;
        console.log(retorno);
    });       
}

function ModificarAuto(e : Event) : void 
{  
    VerificarJWT();
    e.preventDefault();
    let token = localStorage.getItem("token");
    let id = $("#id").val();
    let color = $("#color").val();
    let marca = $("#marca").val();
    let precio = $("#precio").val();
    let modelo = $("#modelo").val();
    
    // alert(`id: ${id} - color: ${color} - marca: ${marca} - precio ${precio} - modelo ${modelo}`)

    let dato : any = {
        "color":color,
        "marca":marca,
        "precio":precio,
        "modelo":modelo
    };
    $.ajax({
        type: 'PUT',
        url: API,
        dataType: "json",
        data: JSON.stringify({auto : dato, id_auto : id}),
        headers : {token : token, "content-type":"application/json"},
        async: true
    })
    .done(function (resultado:any) {
        if(resultado.exito)
            ObtenerListadoAutos();
        else
            $("#error").html(CrearAlerta(resultado.mensaje, "danger"));
    })
    .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {
        let retorno = jqXHR.responseText;
        let alerta:string = CrearAlerta(retorno.mensaje, "danger");
        $("#error").html(alerta);
    });    
}

function VerificarJWT()
{
    let token = localStorage.getItem("token");
    $.ajax({
        type: 'GET',
        url: API + "login",
        dataType: "json",
        data: {},
        headers : {token : token, "content-type":"application/json"},
        async: true
    })
    .done(function (resultado:any) {
        console.log(resultado);
        if(!resultado.exito)
            $(location).attr("href", API + 'front-end-login');
    })
    .fail(function (jqXHR:any, textStatus:any, errorThrown:any) {
        let retorno = JSON.parse(jqXHR.responseText);
        console.log(retorno.mensaje);
    });
}

function CrearAlerta(mensaje : string, tipo : string = "success") : string
{
    let alerta : string = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                            <strong>Atencion!</strong> ${mensaje}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>`;
    return alerta;
}
function Logout():void 
{   
    localStorage.removeItem("token");
    alert('Usuario deslogueado!');
    $(location).attr("href", API + 'front-end-login');
}

function FiltrarPrecioColor()
{
    let token = localStorage.getItem('token');
    let listado = new Array();
    $.ajax({
        type: 'GET',
        url: API + 'autos',
        dataType: "json",
        data: {},
        headers : {token : token},
        async: true
    })
    .done(function (resultado:any)
    {
        listado = resultado.dato;
        // console.log(listado);
        // let listadoFiltrado = listado.filter(auto => auto.precio > 199999 && auto.color != 'rojo');
        let listadoFiltrado = listado.filter(auto => auto.precio < 60000);
        // console.log(listadoFiltrado);
        $('#derecha').html(ArmarTablaAutos(listadoFiltrado));
    })
    .fail(function (jqXHR: any, textStatus: any, errorThrown: any)
    {
        let respuesta = JSON.parse(jqXHR.responseText);
        console.log(respuesta.mensaje);
    })
}

function Promedio()
{
    let token = localStorage.getItem('token');
    let listado = new Array();
    $.ajax({
        type: 'GET',
        url: API + 'autos',
        dataType: "json",
        data: {},
        headers : {token : token},
        async: true
    })
    .done(function (resultado:any)
    {
        listado = resultado.dato;
        // console.log(listado);
        let listadoFiltrado = listado.filter(auto => auto.marca[0] == 'f')
        let total = listadoFiltrado.reduce((anterior, actual) => anterior + parseInt(actual.precio), 0);
        console.log(listadoFiltrado);
        console.log(total)
        $('#error').html(CrearAlerta(total, 'info'));
    })
    .fail(function (jqXHR: any, textStatus: any, errorThrown: any)
    {
        let respuesta = JSON.parse(jqXHR.responseText);
        console.log(respuesta.mensaje);
    })
}

function ListadoNombreFoto()
{
    let token = localStorage.getItem('token');
    let listado = new Array();
    $.ajax({
        type: 'GET',
        url: API ,
        dataType: "json",
        data: {},
        headers : {token : token},
        async: true
    })
    .done(function (resultado:any)
    {
        listado = resultado.dato;
        console.log(listado);
        let listadoFiltrado : any = listado.filter(usuario => usuario.perfil == 'empleado' || usuario.perfil == 'supervisor')
        console.log(listadoFiltrado);
        let tabla:string = '<table class="table table-dark table-hover">';
        tabla += `<tr>
            <th>Nombre</th>
            <th>Foto</th>
            </tr>`;
            listadoFiltrado.forEach(usuario => {
                tabla += `<tr>
                <td>${usuario.nombre}</td>
                <td><img src="${usuario.foto}" alt="" width="50px" height="50px"></td>
                </tr>`;
            });
            tabla += "</table>";
            $('#izquierda').html(tabla);
    })
    .fail(function (jqXHR: any, textStatus: any, errorThrown: any)
    {
        let respuesta = JSON.parse(jqXHR.responseText);
        console.log(respuesta.mensaje);
    })
}