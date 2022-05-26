<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

require_once 'Usuario.php';

class MW
{
// 1.- (método de instancia) Verifique que estén “seteados” el correo y la clave.
// Si no existe alguno de los dos (o los dos) retorne un JSON con el mensaje de error
// correspondiente (y status 403).
// Si existen, pasar al siguiente Middleware que verifique que:
    public function ValidarParametrosUsuario(Request $request, RequestHandler $handler) : ResponseMW
    {
        $json = $request->getParsedBody();
        if(isset($json['user']))
        {
            $json = json_decode($json['user']);
        }
        else if(isset($json['usuario']))
        {
            $json = json_decode($json['usuario']);
        }
        $retorno = new stdClass();
        $retorno->mensaje = 'No se paso ni el correo ni la clave';
        $retorno->status = 403;

        $responseMW = new ResponseMW();

        if(isset($json->correo))
        {
            if(isset($json->clave))
            {
                $response = $handler->handle($request);
                $responseMW->withStatus($response->getStatusCode());
                $responseMW->getBody()->write((string)$response->getBody());
                $retorno->status = 200;
                return $responseMW;
            }
            else
            {
                $retorno->mensaje = 'No se paso la clave';
            }
        }
        else if(isset($json->clave))
        {
            $retorno->mensaje = 'No se paso el correo';
        }
        $responseMW->withStatus(403);
        $responseMW->getBody()->write(json_encode($retorno));
        return $responseMW;
    }

    // 2.- (método de clase) Si alguno está vacío (o los dos) retorne un JSON con el mensaje de error
    // correspondiente (y status 409).
    // Caso contrario, pasar al siguiente Middleware.
    public static function VerificarCorreoClaveVacios(Request $request, RequestHandler $handler) : ResponseMW
    {
        $json = $request->getParsedBody();
        if(isset($json['user']))
        {
            $json = json_decode($json['user']);
        }
        else if(isset($json['usuario']))
        {
            $json = json_decode($json['usuario']);
        }
        $retorno = new stdClass();
        $retorno->mensaje = 'Ambos campos vacios';
        $retorno->status = 409;

        $responseMW = new ResponseMW();

        if($json->correo != '')
        {
            if($json->clave != '')
            {
                $response = $handler->handle($request);
                $responseMW->withStatus($response->getStatusCode());
                $responseMW->getBody()->write((string)$response->getBody());
                return $responseMW;
            }
            else
            {
                $retorno->mensaje = 'Clave vacia';
            }
        }
        else if($json->clave != '')
        {
            $retorno->mensaje = 'Correo vacio';
        }
        $responseMW->withStatus(409);
        $responseMW->getBody()->write(json_encode($retorno));
        return $responseMW;
    }

    // 3.- (método de instancia) Verificar que el correo y clave existan en la base de datos. Si NO
    // existen, retornar un JSON con el mensaje de error correspondiente (y status 403).
    // Caso contrario, acceder al verbo de la API.
    public static function VerificarParametrosBD(Request $request, RequestHandler $handler) : ResponseMW
    {
        $json = $request->getParsedBody();
        if(isset($json['user']))
        {
            $json = json_decode($json['user']);
        }
        else if(isset($json['usuario']))
        {
            $json = json_decode($json['usuario']);
        }
        $retorno = new stdClass();
        $retorno->mensaje = 'Credenciales Invalidas';
        $retorno->status = 403;

        $responseMW = new ResponseMW($retorno->status);
        $usuario = Usuario::TraerUsuarioCorreoClave($json);
        if($usuario != null)
        {
            $retorno->status = 200;
            $response = $handler->handle($request);
            $responseMW->withStatus($response->getStatusCode());
            $responseMW->getBody()->write((string)$response->getBody());
            return $responseMW;
        }
        $responseMW->withStatus($retorno->status);
        $responseMW->getBody()->write(json_encode($retorno));
        return $responseMW;
    }

    // 4.- (método de clase) Verificar que el correo NO exista en la base de datos. Si EXISTE,
    // retornar un JSON con el mensaje de error correspondiente (y status 403).
    // Caso contrario, acceder al verbo de la API.
    public static function VerificarCorreoBD(Request $request, RequestHandler $handler) : ResponseMW
    {
        $json = $request->getParsedBody();
        if(isset($json['user']))
        {
            $verificarCorreo = Usuario::VerificarExisteCorreoBD(json_decode($json['usuario']));
        }
        else if(isset($json['usuario']))
        {
            $verificarCorreo = Usuario::VerificarExisteCorreoBD(json_decode($json['usuario']));
        }
        $retorno = new stdClass();
        $retorno->mensaje = 'El correo existe en la base de datos';
        $retorno->status = 403;
        $responseMW = new ResponseMW();

        if(!$verificarCorreo->exito)
        {
            $response = $handler->handle($request);
            $responseMW->withStatus(200, 'OK');
            $responseMW->getBody()->write((string)$response->getBody());
            return $responseMW;
        }
        $responseMW->withStatus($retorno->status, 'Error');
        $responseMW->getBody()->write(json_encode($retorno));
        return $responseMW;
    }

    // 5.- (método de instancia) Verificar que el precio posea un rango de entre 50.000 y 600.000 y
    // que el color NO sea ‘amarillo’. Si no pasa la validación de alguno de los dos (o los dos) retorne un
    // JSON con el mensaje de error correspondiente (y status 409).
    // Caso contrario, acceder al verbo de la API.
    public static function VerificarPrecioColor(Request $request, RequestHandler $handler) : ResponseMW
    {
        $json = $request->getParsedBody()['auto'];
        $json = json_decode($json);

        $retorno = new stdClass();
        $retorno->mensaje = 'No cumple ambas validaciones (precio y color)';
        $retorno->status = 409;
        $responseMW = new ResponseMW();

        if($json->color != 'amarillo')
        {            
            if($json->precio >= 50000 && $json->precio <= 600000)
            {
                $response = $handler->handle($request);
                $responseMW->withStatus($response->getStatusCode(), 'OK');
                $responseMW->getBody()->write((string)$response->getBody());
                return $responseMW;
            }
            else 
            {
                $retorno->mensaje = 'El precio no esta en el rango permitido';
            }
        }
        else if($json->precio >= 50000 && $json->precio <= 600000)
        {
            $retorno->mensaje = "El color amarillo no esta permitido.";
        }

        $responseMW->withStatus($retorno->status, 'Error');
        $responseMW->getBody()->write(json_encode($retorno));
        return $responseMW;
    }

    // 1.- (método de instancia) verifique que el token sea válido.
    // Recibe el JWT → token (en el header) a ser verificado.
    // Retorna un JSON con el mensaje de error correspondiente (y status 403), 
    // en caso de no ser válido.
    // Caso contrario, pasar al siguiente callable.
    public function VerificarToken(Request $request, RequestHandler $handler) : ResponseMW
    {
        $token = $request->getHeader('token')[0];
        $retorno = new stdClass();
        $retorno->mensaje = 'JWT invalido';
        $retorno->status = 403;

        $responseMW = new ResponseMW();
        $respuestaVerificado = Autentificadora::VerificarJWT($token);
        if($respuestaVerificado->verificado)
        {
            $response = $handler->handle($request);
            $responseMW->withStatus($response->getStatusCode(), 'OK');
            $responseMW->getBody()->write((string)$response->getBody());
            return $responseMW;
        }

        $retorno->mensaje = $respuestaVerificado->mensaje;
        $responseMW->withStatus($retorno->status, 'Error');
        $responseMW->getBody()->write(json_encode($retorno));
        return $responseMW;
    }

    // 2.- (método de clase) verifique si es un ‘propietario’ o no.
    // Recibe el JWT → token (en el header) a ser verificado.
    // Retorna un JSON con propietario: true/false; mensaje: string (mensaje correspondiente);
    // status: 200/409.
    public static function VerificarPropietario(Request $request, RequestHandler $handler) : ResponseMW
    {
        $token = $request->getHeader('token')[0];

        $retorno = new stdClass();
        $retorno->mensaje = 'No es propietario';
        $retorno->propietario = false;
        $retorno->usuario = null;
        $retorno->status = 409;

        $responseMW = new ResponseMW();
       
        $retornoPayload = Autentificadora::ObtenerPayLoad($token);
        $perfil = $retornoPayload->payload->data->perfil;
        if($perfil == 'propietario')
        {
            $retorno->propietario = true;
            $response = $handler->handle($request);
            $responseMW->withStatus($response->getStatusCode(), 'OK');
            $responseMW->getBody()->write((string)$response->getBody());
            return $responseMW;
        }
        else
        {
            $retorno->usuario = $retornoPayload->payload->data;
        }

        $responseMW->withStatus($retorno->status, 'Error');
        $responseMW->getBody()->write(json_encode($retorno));
        return $responseMW;
    }

    // 3.- (método de instancia) verifique si es un ‘encargado’ o no.
    // Recibe el JWT → token (en el header) a ser verificado.
    // Retorna un JSON con encargado: true/false; mensaje: string (mensaje correspondiente);
    // status: 200/409.
    public function VerificarEncargado(Request $request, RequestHandler $handler) : ResponseMW
    {
        $token = $request->getHeader('token')[0];

        $retorno = new stdClass();
        $retorno->mensaje = 'No es encargado';
        $retorno->encargado = false;
        $retorno->usuario = null;
        $retorno->status = 409;

        $responseMW = new ResponseMW();
       
        $retornoPayload = Autentificadora::ObtenerPayLoad($token);
        $perfil = $retornoPayload->payload->data->perfil;
        if($perfil == 'encargado')
        {
            $retorno->propietario = true;
            $response = $handler->handle($request);
            $responseMW->withStatus($response->getStatusCode(), 'OK');
            $responseMW->getBody()->write((string)$response->getBody());
            return $responseMW;
        }
        else
        {
            $retorno->usuario = $retornoPayload->payload->data;
        }
        $responseMW->withStatus($retorno->status, 'Error');
        $responseMW->getBody()->write(json_encode($retorno));
        return $responseMW;
    }

    // 1.- Si el que accede al listado de autos es un ‘encargado’, retorne todos los datos, menos el ID.
    // (clase MW - método de instancia).
    public function AccedeEncargadoAuto(Request $request, RequestHandler $handler) : ResponseMW
    {
        $token = $request->getHeader('token')[0];
        $retornoPayload = Autentificadora::ObtenerPayLoad($token);
        if(isset($retornoPayload->payload))
        {
            $usuario = $retornoPayload->payload->data;
            $perfil = $usuario->perfil;
            $responseMW = new ResponseMW();
            
            $response = $handler->handle($request);
            $responseMW->withStatus($response->getStatusCode());
            if($perfil == 'encargado')
            {
                $listadoAutos = json_decode($response->getBody());
                $arrayAutos = $listadoAutos->dato;
                foreach ($arrayAutos as $item)
                {
                    unset($item->id);
                }
                $listadoAutos->dato = $arrayAutos;
                $responseMW->getBody()->write(json_encode($listadoAutos));
            }
            else
            {
                $responseMW->getBody()->write((string)$response->getBody());
            }
            return $responseMW;
        }
    }

    // 2.- Si es un ‘empleado’, muestre la cantidad de colores (distintos) que se tiene. (clase MW -
    // método de instancia).
    public static function AccedeEmpleadoAuto(Request $request, RequestHandler $handler) : ResponseMW
    {
        $token = $request->getHeader('token')[0];
        $retornoPayload = Autentificadora::ObtenerPayLoad($token);
        if(isset($retornoPayload->payload))
        {
            $usuario = $retornoPayload->payload->data;
            $perfil = $usuario->perfil;
            $responseMW = new ResponseMW();
            
            $response = $handler->handle($request);
            $responseMW->withStatus($response->getStatusCode());

            if($perfil == 'empleado')
            {
                $listadoAutos = json_decode($response->getBody());
                $arrayAutos = $listadoAutos->dato;
                $colores = array();
                foreach ($arrayAutos as $item)
                {
                    array_push($colores, $item->color);
                }
                $cantidadColores = array_count_values($colores);

                $retorno = new stdClass();
                $retorno->mensaje = "Hay " . count($cantidadColores) . " colores distintos";
                $retorno->cantidadColores = $cantidadColores;

                $responseMW->getBody()->write(json_encode($retorno));
            }
            else
            {
                $responseMW->getBody()->write((string)$response->getBody());
            }
        }
        return $responseMW;
    }

    // 3.- Si es un ‘propietario’, muestre todos los datos de los autos (si el ID está vacío o indefinido) o
    // el auto (cuyo ID fue pasado como parámetro). (clase MW - método de clase).
    public static function AccedePropietarioAuto(Request $request, RequestHandler $handler) : ResponseMW
    {
        $token = $request->getHeader('token')[0];
        $id = isset(json_decode($request->getBody())->id) ? json_decode($request->getBody())->id : null;
        $retornoPayload = Autentificadora::ObtenerPayLoad($token);
        if(isset($retornoPayload->payload))
        {
            $perfil = $retornoPayload->payload->data->perfil;
            $responseMW = new ResponseMW();
            
            $response = $handler->handle($request);
            $responseMW->withStatus($response->getStatusCode());

            if($perfil == 'propietario')
            {
                $listadoAutos = json_decode($response->getBody());
                $autos = $listadoAutos->dato;
                if($id == null || $id == '')
                {
                    $responseMW->getBody()->write((string)$response->getBody());
                }
                else
                {
                    foreach($autos as $item)
                    {
                        if($item->id == $id)
                            $autos = $item;
                    }
                    $responseMW->getBody()->write(json_encode($autos));
                }
            }
        }
        return $responseMW;
    }

    // 1.- Si el que accede al listado de autos es un ‘encargado’, retorne todos los datos, menos la
    // clave y el ID. (clase MW - método de instancia).
    public function AccedeEncargadoUsuario(Request $request, RequestHandler $handler) : ResponseMW
    {
        $token = $request->getHeader('token')[0];
        $retornoPayload = Autentificadora::ObtenerPayLoad($token);
        if(isset($retornoPayload->payload))
        {
            $usuario = $retornoPayload->payload->data;
            $perfil = $usuario->perfil;
            $responseMW = new ResponseMW();
            
            $response = $handler->handle($request);
            $responseMW->withStatus($response->getStatusCode());
            if($perfil == 'encargado')
            {
                $listado = json_decode($response->getBody());
                $array = $listado->dato;
                foreach ($array as $item)
                {
                    unset($item->id);
                    unset($item->clave);
                }
                $listado->dato = $array;
                $responseMW->getBody()->write(json_encode($listado));
            }
            else
            {
                $responseMW->getBody()->write((string)$response->getBody());
            }
            return $responseMW;
        }
    }

    // 2.- Si es un ‘empleado’, muestre solo el nombre, apellido y foto de los usuarios. (clase MW -
    // método de instancia).
    public function AccedeEmpleadoUsuario(Request $request, RequestHandler $handler) : ResponseMW
    {
        $token = $request->getHeader('token')[0];
        $retornoPayload = Autentificadora::ObtenerPayLoad($token);
        if(isset($retornoPayload->payload))
        {
            $usuario = $retornoPayload->payload->data;
            $perfil = $usuario->perfil;
            $responseMW = new ResponseMW();
            
            $response = $handler->handle($request);
            $responseMW->withStatus($response->getStatusCode());
            if($perfil == 'empleado')
            {
                $listado = json_decode($response->getBody());
                $array = $listado->dato;
                foreach ($array as $item)
                {
                    unset($item->id);
                    unset($item->correo);
                    unset($item->clave);
                    unset($item->perfil);
                }
                $listado->dato = $array;
                $responseMW->getBody()->write(json_encode($listado));
            }
            else
            {
                $responseMW->getBody()->write((string)$response->getBody());
            }
            return $responseMW;
        }
    }

    // 3.- Si es un ‘propietario’, muestre la cantidad de usuarios cuyo apellido coincida con el pasado por parámetro 
    // o los apellidos (y sus cantidades) si es que el parámetro pasado está vacío o indefinido. (clase MW - método de clase).
    public static function AccedePropietarioUsuario(Request $request, RequestHandler $handler) : ResponseMW
    {
        $token = $request->getHeader('token')[0];
        $apellido = isset(json_decode($request->getBody())->apellido) ? json_decode($request->getBody())->apellido : null;
        $retornoPayload = Autentificadora::ObtenerPayLoad($token);
        if(isset($retornoPayload->payload))
        {
            $usuario = $retornoPayload->payload->data;
            $perfil = $usuario->perfil;
            $responseMW = new ResponseMW();
            
            $response = $handler->handle($request);
            $responseMW->withStatus($response->getStatusCode());
            if($perfil == 'propietario')
            {
                $listado = json_decode($response->getBody());
                $array = $listado->dato;
                $apellidos = array();
                foreach ($array as $item)
                {
                    array_push($apellidos, $item->apellido);
                }
                $cantidadApellidos = array_count_values($apellidos);

                $retorno = new stdClass();
                if($apellido == null || $apellido == '')
                {
                    $retorno->mensaje = "Hay " . count($cantidadApellidos) . " apellidos distintos";
                    $retorno->cantidadApellidos = $cantidadApellidos;
                }
                else
                {
                    foreach ($cantidadApellidos as $key => $value) {
                        if($apellido == $key)
                            $retorno->cantidadApellidosIguales = $value;
                    }
                }
                $responseMW->getBody()->write(json_encode($retorno));
            }
            else
            {
                $responseMW->getBody()->write((string)$response->getBody());
            }
            return $responseMW;
        }
    }
}

?>