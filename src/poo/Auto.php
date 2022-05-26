<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once 'AccesoDatos.php';

class Auto{
    public $id;
    public $color;
    public $marca;
    public $precio;
    public $modelo;

    public function __construct($id=null, $color=null, $marca=null, $precio=null, $modelo=null)
    {
        $this->id = $id;
        $this->color = $color;
        $this->marca = $marca;
        $this->precio = $precio;
        $this->modelo = $modelo;
    }

    // A nivel de aplicación:
    // (POST) Alta de autos. Se agregará un nuevo registro en la tabla autos *.
    // Se envía un JSON → auto (color, marca, precio y modelo).
    // * ID auto-incremental.
    // Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)
    public function AltaAuto(Request $request, Response $response, array $args) : Response
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "Error al agregar el auto.";
        $retorno->status = 418;

        // Obtengo los datos enviados
        $body = $request->getParsedBody();
        // Al ser un 'json' los parseo a objeto php
        $jsonAuto = json_decode($body['auto']);

        // Instancio un nuevo objeto 'Auto' con los datos recuperados
        $auto = new Auto(null, $jsonAuto->color, $jsonAuto->marca, $jsonAuto->precio, $jsonAuto->modelo);
        
        // Si lo pude agregar con exito -> modifico el retorno y muevo la foto al destino final
        if(Auto::AgregarAuto($auto))
        {
            $retorno->exito = true;
            $retorno->mensaje = 'Auto agregado correctamente';
            $retorno->status = 200;
        }

        $newResponse = $response->withStatus($retorno->status);
        $newResponse->getBody()->write(json_encode($retorno));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    private static function AgregarAuto($usuario)
    {
        try{
            $pdo = AccesoDatos::DameUnObjetoAcceso();
            $cursor = $pdo->RetornarConsulta("INSERT INTO autos (color, marca, precio, modelo) 
                                            VALUES (:color,:marca,:precio,:modelo)");
            $cursor->bindParam(':color', $usuario->color, PDO::PARAM_STR);
            $cursor->bindParam(':marca', $usuario->marca, PDO::PARAM_STR);
            $cursor->bindParam(':precio', $usuario->precio, PDO::PARAM_INT);
            $cursor->bindParam(':modelo', $usuario->modelo, PDO::PARAM_STR);
            $cursor->execute();
            if($cursor->rowCount())
            {
                return true;
            }
            return false;
        }
        catch(PDOException $e)
        {
            return false;
        }
    }

    // A nivel de ruta (/autos):
    // (GET) Listado de autos. Mostrará el listado completo de los autos (array JSON).
    // Retorna un JSON (éxito: true/false; mensaje: string; dato: stringJSON; status: 200/424)
    public function ListadoAutos(Request $request, Response $response, array $args) : Response
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "Error al obtener el listado de autos.";
        $retorno->dato = null;
        $retorno->status = 424;

        $listado = Auto::TraerAutos();
        if(count($listado))
        {
            $retorno->exito = true;
            $retorno->mensaje = "Exito al traer el listado.";
            $retorno->dato = $listado;
            $retorno->status = 200;
        }
        else{
            $retorno->exito = true;
            $retorno->mensaje = "Listado vacio";
            $retorno->status = 200;
        }

        $newResponse = $response->withStatus($retorno->status);
        $newResponse->getBody()->write(json_encode($retorno));
        
        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    private static function TraerAutos()
    {
        try{
            $pdo = AccesoDatos::DameUnObjetoAcceso();
            $cursor = $pdo->RetornarConsulta("SELECT * FROM autos");
            $cursor->execute();
            $listado = array();
            if($cursor->rowCount())
            {
                $array = $cursor->fetchAll(PDO::FETCH_OBJ);
                foreach($array as $item)
                    array_push($listado, new Auto($item->id, $item->color, $item->marca, $item->precio, $item->modelo));
            }
            return $listado;
        }
        catch(PDOException $e)
        {
            return 'Error: ' . $e->getMessage();
        }
    }

    // (DELETE) Borrado de autos por ID.
    // Recibe el ID del auto a ser borrado (id_auto) más el JWT → token (en el header).
    // Si el perfil es ‘propietario’ se borrará de la base de datos. Caso contrario, se mostrará el
    // mensaje correspondiente (indicando que usuario intentó realizar la acción).
    // Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)
    public static function BorrarAuto(Request $request, Response $response, array $args) : Response
    {
        // Token por header - id por raw
        $token = $request->getHeader('token')[0];
        $id = json_decode($request->getBody())->id_auto;
        
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "Error al eliminar el auto.";
        $retorno->status = 418;

        $retornoBorrado = Auto::EliminarAutoBD($id);
        if($retornoBorrado->exito)
        {
            $retorno->exito = $retornoBorrado->exito;
            $retorno->mensaje = $retornoBorrado->mensaje;
            $retorno->status = 200;
        }
        else
        {
            $retorno->mensaje = $retornoBorrado->mensaje;
        }

        $newResponse = $response->withStatus($retorno->status);
        $newResponse->getBody()->write(json_encode($retorno));
        
        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    private static function EliminarAutoBD($id)
    {
        $retorno = new stdClass();
        $retorno->mensaje = 'No se pudo eliminar';
        $retorno->exito = false;
        try{
            $pdo = AccesoDatos::DameUnObjetoAcceso();
            $cursor = $pdo->RetornarConsulta("DELETE FROM autos WHERE id = :id");
            $cursor->bindParam(':id', $id, PDO::PARAM_INT);
            $cursor->execute();
            if($cursor->rowCount())
            {
                $retorno->mensaje = 'Se elimino con exito';
                $retorno->exito = true;
            }
            else
            {
                $retorno->mensaje = 'No existe auto con el id indicado';
            }
        }
        catch(PDOException $e)
        {
            $retorno->mensaje = 'Error: ' . $e->getMessage();
        }
        return $retorno;
    }

    // (PUT) Modificar los autos por ID.
    // Recibe el JSON del auto a ser modificado (auto), el ID (id_auto) y el JWT → token (en el header).
    // Si el perfil es ‘encargado’ se modificará de la base de datos. Caso contrario, se mostrará
    // el mensaje correspondiente (indicando que usuario intentó realizar la acción).
    // Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)
    public static function ModificarAuto(Request $request, Response $response, array $args) : Response
    {
        $json = json_decode($request->getBody())->auto;
        $id = json_decode($request->getBody())->id_auto;
        
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "Error al modificar el auto.";
        $retorno->status = 418;

        $retornoBorrado = Auto::ModificarAutoBD($json, $id);
        if($retornoBorrado->exito)
        {
            $retorno->exito = true;
            $retorno->mensaje = $retornoBorrado->mensaje;
            $retorno->status = 200;
        }
        else
        {
            $retorno->mensaje = $retornoBorrado->mensaje;
        }

        $newResponse = $response->withStatus($retorno->status);
        $newResponse->getBody()->write(json_encode($retorno));
        
        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    public static function ModificarAutoBD($json, $id)
    {
        $retorno = new stdClass();
        $retorno->mensaje = 'No se pudo modificar';
        $retorno->exito = false;
        try{
            $pdo = AccesoDatos::DameUnObjetoAcceso();
            
            $cursor = $pdo->RetornarConsulta("UPDATE autos SET color=:color, marca=:marca, precio=:precio, modelo=:modelo WHERE id = :id");
            $cursor->bindParam(':color', $json->color, PDO::PARAM_STR);
            $cursor->bindParam(':marca', $json->marca, PDO::PARAM_STR);
            $cursor->bindParam(':precio', $json->precio, PDO::PARAM_INT);
            $cursor->bindParam(':modelo', $json->modelo, PDO::PARAM_STR);
            $cursor->bindParam(':id', $id, PDO::PARAM_INT);
            $cursor->execute();
            if($cursor->rowCount())
            {
                $retorno->mensaje = 'Se modifico con exito';
                $retorno->exito = true;
            }
            else
            {
                $retorno->mensaje = 'No existe auto con el id indicado';
            }
        }
        catch(PDOException $e)
        {
            $retorno->mensaje = 'Error: ' . $e->getMessage();
        }
        return $retorno;
    }

    
}

?>