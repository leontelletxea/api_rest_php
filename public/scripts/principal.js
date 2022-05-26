/// <reference path="../node_modules/@types/jquery/index.d.ts" />
$(function () {
    ObtenerListadoUsuarios();
    ObtenerListadoAutos();
    $('#altaAuto').html(MostrarFormulario('alta'));
});
function ObtenerListadoUsuarios() {
    VerificarJWT();
    var token = localStorage.getItem("token");
    $.ajax({
        type: 'GET',
        url: API,
        dataType: "json",
        data: {},
        headers: { token: token },
        async: true
    })
        .done(function (resultado) {
        console.log(resultado);
        if (resultado.exito) {
            $("#usuarios").html(ArmarTablaUsuarios(resultado.dato));
        }
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        var respuesta = JSON.parse(jqXHR.responseText);
        console.log(respuesta.mensaje);
    });
}
function ArmarTablaUsuarios(usuarios) {
    var tabla = '<table class="table table-primary">';
    tabla += "<tr>\n        <th>CORREO</th>\n        <th>NOMBRE</th>\n        <th>APELLIDO</th>\n        <th>PERFIL</th>\n        <th>FOTO</th>\n    </tr>";
    usuarios.forEach(function (usuario) {
        tabla += "<tr>\n        <td>" + usuario.correo + "</td>\n        <td>" + usuario.nombre + "</td>\n        <td>" + usuario.apellido + "</td>\n        <td>" + usuario.perfil + "</td>\n        <td><img src=\"" + usuario.foto + "\" alt=\"\" width=\"50px\" height=\"50px\"></td>\n        </tr>";
    });
    tabla += "</table>";
    return tabla;
}
function ObtenerListadoAutos() {
    VerificarJWT();
    var token = localStorage.getItem("token");
    $.ajax({
        type: 'GET',
        url: API + 'autos',
        dataType: "json",
        data: {},
        headers: { token: token },
        async: true
    })
        .done(function (resultado) {
        console.log(resultado);
        if (resultado.exito) {
            if (resultado.dato === null) {
                $("#autos").html(CrearAlerta(resultado.mensaje, 'warning'));
            }
            else {
                $("#autos").html(ArmarTablaAutos(resultado.dato));
            }
        }
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        var respuesta = JSON.parse(jqXHR.responseText);
        console.log(respuesta.mensaje);
    });
}
function ArmarTablaAutos(autos) {
    var tabla = '<table class="table table-primary">';
    tabla += "<tr>\n        <th>MARCA</th>\n        <th>COLOR</th>\n        <th>MODELO</th>\n        <th>PRECIO</th>\n        <th>ELIMINAR</th>\n        <th>MODIFICAR</th>\n    </tr>";
    autos.forEach(function (auto) {
        tabla += "<tr>\n        <td>" + auto.marca + "</td>\n        <td>" + auto.color + "</td>\n        <td>" + auto.modelo + "</td>\n        <td>" + auto.precio + "</td>\n        <td><a href='#' class='btn btn-danger' data-action='eliminar' data-obj_auto='" + JSON.stringify(auto) + "' title='Eliminar'<i class='bi bi-pencil'></i>Eliminar</a></td>\n        <td><a href='#' class='btn btn-success' data-action='modificar' data-obj_auto='" + JSON.stringify(auto) + "' title='Modificar'><i class='bi bi-pencil'></i>Modificar</a></td>\n         </tr>";
    });
    tabla += "</table>";
    return tabla;
}
function MostrarFormulario(accion, obj_auto) {
    if (obj_auto === void 0) { obj_auto = null; }
    var boton = "";
    var funcion = "";
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
    var id = "";
    var color = "";
    var marca = "";
    var precio = "";
    var modelo = "";
    if (obj_auto !== null) {
        id = obj_auto.id;
        color = obj_auto.color;
        marca = obj_auto.marca;
        precio = obj_auto.precio;
        modelo = obj_auto.modelo;
    }
    var form = "<div class=\"row col-lg-6 col-md-8 col-sm-10 m-auto\">\n                            <div class=\"w-100 my-4\">\n                                <div class=\"p-3 rounded-5 \" style=\"background-color: darkcyan;\">\n                                    <form>\n                                        <div class=\"form-group d-flex\">\n                                            <input type=\"hidden\" class=\"form-control \" id=\"id\" value=\"" + id + "\" readonly >\n                                        </div>\n                                        <div class=\"form-group d-flex\">\n                                            <i class=\"fas fa-trademark p-2 mr-2 content-center rounded border controlador bg-light\"></i>\n                                            <input type=\"text\" class=\"form-control\" id=\"marca\" placeholder=\"Marca\" name=\"marca\" value=\"" + marca + "\" required>\n                                        </div>\n                                        <div class=\"form-group d-flex\">\n                                            <i class=\"fas fa-palette p-2 mr-2 content-center rounded border controlador bg-light\"></i>\n                                            <input type=\"text\" class=\"form-control\" id=\"color\" placeholder=\"Color\" name=\"color\" value=\"" + color + "\"  required>\n                                        </div>\n                                        <div class=\"form-group d-flex\">\n                                            <i class=\"fas fa-car p-2 mr-2 content-center rounded border controlador bg-light\"></i>\n                                            <input type=\"number\" class=\"form-control\" id=\"precio\" placeholder=\"Precio\" name=\"precio\" value=\"" + precio + "\" required>\n                                        </div>\n                                        <div class=\"form-group d-flex\">\n                                            <i class=\"fas fa-dollar-sign p-2 mr-2 content-center rounded border controlador bg-light\"></i>\n                                            <input type=\"text\" class=\"form-control\" id=\"modelo\" placeholder=\"Modelo\" name=\"modelo\" value=\"" + modelo + "\" required>\n                                        </div>\n                                        <div class=\"row justify-content-around mt-3 mb-5\">\n                                            <button type=\"submit\" id=\"" + boton + "\" class=\"col-4 btn btn-success\" onclick=\"" + funcion + "\">Agregar</button>\n                                            <button type=\"reset\" class=\"col-4 btn btn-warning\">Limpiar</button>\n                                        </div>\n                                    </form>\n                                </div>\n                            </div>\n                        </div>";
    return form;
}
function AgregarAuto(e) {
    VerificarJWT();
    e.preventDefault();
    var color = $("#color").val();
    var marca = $("#marca").val();
    var precio = $("#precio").val();
    var modelo = $("#modelo").val();
    // alert(`color: ${color} - marca: ${marca} - precio ${precio} - modelo ${modelo}`)
    var datos = {
        "color": color,
        "marca": marca,
        "precio": precio,
        "modelo": modelo
    };
    $.ajax({
        type: 'POST',
        url: API,
        dataType: "json",
        data: { "auto": JSON.stringify(datos) },
        async: true
    })
        .done(function (resultado) {
        if (resultado.exito)
            $("#error").html(CrearAlerta(resultado.mensaje, "success"));
        else
            $("#error").html(CrearAlerta(resultado.mensaje, "danger"));
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        var retorno = JSON.stringify(jqXHR.responseText);
        var alerta = CrearAlerta(retorno, "danger");
        $("#error").html(alerta);
    });
}
function EliminarAuto(id) {
    VerificarJWT();
    var token = localStorage.getItem("token");
    $.ajax({
        type: 'DELETE',
        url: API,
        dataType: "json",
        data: JSON.stringify({ 'id_auto': id }),
        headers: { token: token },
        async: true
    })
        .done(function (resultado) {
        if (resultado.exito)
            ObtenerListadoAutos();
        else
            $("#error").html(CrearAlerta(resultado.mensaje, "warning"));
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        var retorno = jqXHR.responseText;
        console.log(retorno);
    });
}
function ModificarAuto(e) {
    VerificarJWT();
    e.preventDefault();
    var token = localStorage.getItem("token");
    var id = $("#id").val();
    var color = $("#color").val();
    var marca = $("#marca").val();
    var precio = $("#precio").val();
    var modelo = $("#modelo").val();
    // alert(`id: ${id} - color: ${color} - marca: ${marca} - precio ${precio} - modelo ${modelo}`)
    var dato = {
        "color": color,
        "marca": marca,
        "precio": precio,
        "modelo": modelo
    };
    $.ajax({
        type: 'PUT',
        url: API,
        dataType: "json",
        data: JSON.stringify({ auto: dato, id_auto: id }),
        headers: { token: token, "content-type": "application/json" },
        async: true
    })
        .done(function (resultado) {
        if (resultado.exito)
            ObtenerListadoAutos();
        else
            $("#error").html(CrearAlerta(resultado.mensaje, "danger"));
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        var retorno = jqXHR.responseText;
        var alerta = CrearAlerta(retorno.mensaje, "danger");
        $("#error").html(alerta);
    });
}
function VerificarJWT() {
    var token = localStorage.getItem("token");
    $.ajax({
        type: 'GET',
        url: API + "login",
        dataType: "json",
        data: {},
        headers: { token: token, "content-type": "application/json" },
        async: true
    })
        .done(function (resultado) {
        console.log(resultado);
        if (!resultado.exito)
            $(location).attr("href", API + 'front-end-login');
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        var retorno = JSON.parse(jqXHR.responseText);
        console.log(retorno.mensaje);
    });
}
function CrearAlerta(mensaje, tipo) {
    if (tipo === void 0) { tipo = "success"; }
    var alerta = "<div class=\"alert alert-" + tipo + " alert-dismissible fade show\" role=\"alert\">\n                            <strong>Atencion!</strong> " + mensaje + "\n                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n                            <span aria-hidden=\"true\">&times;</span>\n                            </button>\n                        </div>";
    return alerta;
}
function Logout() {
    localStorage.removeItem("token");
    alert('Usuario deslogueado!');
    $(location).attr("href", API + 'front-end-login');
}
function FiltrarPrecioColor() {
    var token = localStorage.getItem('token');
    var listado = new Array();
    $.ajax({
        type: 'GET',
        url: API + 'autos',
        dataType: "json",
        data: {},
        headers: { token: token },
        async: true
    })
        .done(function (resultado) {
        listado = resultado.dato;
        // console.log(listado);
        // let listadoFiltrado = listado.filter(auto => auto.precio > 199999 && auto.color != 'rojo');
        var listadoFiltrado = listado.filter(function (auto) { return auto.precio < 60000; });
        // console.log(listadoFiltrado);
        $('#derecha').html(ArmarTablaAutos(listadoFiltrado));
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        var respuesta = JSON.parse(jqXHR.responseText);
        console.log(respuesta.mensaje);
    });
}
function Promedio() {
    var token = localStorage.getItem('token');
    var listado = new Array();
    $.ajax({
        type: 'GET',
        url: API + 'autos',
        dataType: "json",
        data: {},
        headers: { token: token },
        async: true
    })
        .done(function (resultado) {
        listado = resultado.dato;
        // console.log(listado);
        var listadoFiltrado = listado.filter(function (auto) { return auto.marca[0] == 'f'; });
        var total = listadoFiltrado.reduce(function (anterior, actual) { return anterior + parseInt(actual.precio); }, 0);
        console.log(listadoFiltrado);
        console.log(total);
        $('#error').html(CrearAlerta(total, 'info'));
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        var respuesta = JSON.parse(jqXHR.responseText);
        console.log(respuesta.mensaje);
    });
}
function ListadoNombreFoto() {
    var token = localStorage.getItem('token');
    var listado = new Array();
    $.ajax({
        type: 'GET',
        url: API,
        dataType: "json",
        data: {},
        headers: { token: token },
        async: true
    })
        .done(function (resultado) {
        listado = resultado.dato;
        console.log(listado);
        var listadoFiltrado = listado.filter(function (usuario) { return usuario.perfil == 'empleado' || usuario.perfil == 'supervisor'; });
        console.log(listadoFiltrado);
        var tabla = '<table class="table table-dark table-hover">';
        tabla += "<tr>\n            <th>Nombre</th>\n            <th>Foto</th>\n            </tr>";
        listadoFiltrado.forEach(function (usuario) {
            tabla += "<tr>\n                <td>" + usuario.nombre + "</td>\n                <td><img src=\"" + usuario.foto + "\" alt=\"\" width=\"50px\" height=\"50px\"></td>\n                </tr>";
        });
        tabla += "</table>";
        $('#izquierda').html(tabla);
    })
        .fail(function (jqXHR, textStatus, errorThrown) {
        var respuesta = JSON.parse(jqXHR.responseText);
        console.log(respuesta.mensaje);
    });
}
