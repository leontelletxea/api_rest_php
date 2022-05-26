<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once 'AccesoDatos.php';
require_once 'autentificadora.php';

class Usuario{
    public $id;
    public $correo;
    public $clave;
    public $nombre;
    public $apellido;
    public $perfil;
    public $foto;

    public function __construct($id=null, $correo=null, $clave=null, $nombre=null, $apellido=null, $perfil=null, $foto=null)
    {
        $this->id = $id;
        $this->correo=$correo;
        $this->clave=$clave;
        $this->nombre=$nombre;
        $this->apellido=$apellido;
        $this->perfil=$perfil;
        $this->foto=$foto;
    }

    // A nivel de ruta (/usuarios):
    // (POST) Alta de usuarios. Se agregará un nuevo registro en la tabla usuarios *.
    // Se envía un JSON → usuario (correo, clave, nombre, apellido, perfil**) y foto.
    // La foto se guardará en ./src/fotos, con el siguiente formato: correo_id.extension.
    // Ejemplo: ./src/fotos/juan@perez_152.jpg
    // * ID auto-incremental. ** propietario, encargado y empleado.
    // Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)
    public function AltaUsuario(Request $request, Response $response, array $args) : Response
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "Error al agregar usuario.";
        $retorno->status = 418;

        // Obtengo los datos enviados
        $body = $request->getParsedBody();
        // Al ser un 'json' los parseo a objeto php
        $jsonUsuario = json_decode($body['usuario']);

        // Obtengo la foto
        $foto = $request->getUploadedFiles()['foto'];
        
        // Obtengo el nombre de la foto para saber la extension
        $nombreFoto = $foto->getClientFileName();
        // Obtengo un array separando por '.', luego invierto el sentido del array asi tengo en la posicion 0 la extension
        $extension = explode(".", $nombreFoto);
        $extension = array_reverse($extension);
        $extension = $extension[0];

        // Obtengo el ultimo ID + 1
        foreach(self::TraerUsuarios() as $item)
            $id = $item->id;
        $id += 1;

        //Destino final de la foto
        $destino = "../src/fotos/" . $jsonUsuario->correo . '_' . $id . '.' . $extension;
        
        // Instancio un nuevo objeto 'Usuario' con los datos recuperados
        $usuario = new Usuario(null, $jsonUsuario->correo, $jsonUsuario->clave, $jsonUsuario->nombre, $jsonUsuario->apellido, $jsonUsuario->perfil, $destino);
        
        // Si lo pude agregar con exito -> modifico el retorno y muevo la foto al destino final
        if(Usuario::AgregarUsuario($usuario))
        {
            $retorno->exito = true;
            $retorno->mensaje = 'Usuario agregado correctamente';
            $retorno->status = 200;
            $foto->moveTo($destino);
        }

        $newResponse = $response->withStatus($retorno->status);
        $newResponse->getBody()->write(json_encode($retorno));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    private static function AgregarUsuario($usuario)
    {
        try{
            $pdo = AccesoDatos::DameUnObjetoAcceso();
            $cursor = $pdo->RetornarConsulta("INSERT INTO usuarios (correo, clave, nombre, apellido, perfil, foto) 
                                            VALUES (:correo,:clave,:nombre,:apellido,:perfil,:foto)");
            $cursor->bindParam(':correo', $usuario->correo, PDO::PARAM_STR);
            $cursor->bindParam(':clave', $usuario->clave, PDO::PARAM_STR);
            $cursor->bindParam(':nombre', $usuario->nombre, PDO::PARAM_STR);
            $cursor->bindParam(':apellido', $usuario->apellido, PDO::PARAM_STR);
            $cursor->bindParam(':perfil', $usuario->perfil, PDO::PARAM_STR);
            $cursor->bindParam(':foto', $usuario->foto, PDO::PARAM_STR);
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

    // A nivel de aplicación:
    // (GET) Listado de usuarios. Mostrará el listado completo de los usuarios (array JSON).
    // Retorna un JSON (éxito: true/false; mensaje: string; dato: stringJSON; status: 200/424)
    public function ListadoUsuarios(Request $request, Response $response, array $args) : Response
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "Error al obtener el listado de usuarios.";
        $retorno->dato = null;
        $retorno->status = 424;

        $listado = Usuario::TraerUsuarios();
        if(count($listado))
        {
            $retorno->exito = true;
            $retorno->mensaje = "Exito al traer el listado.";
            $retorno->dato = $listado;
            $retorno->status = 200;
        }

        $newResponse = $response->withStatus($retorno->status);
        $newResponse->getBody()->write(json_encode($retorno));
        
        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    private static function TraerUsuarios()
    {
        $pdo = AccesoDatos::DameUnObjetoAcceso();
        $cursor = $pdo->RetornarConsulta("SELECT * FROM usuarios");
        $cursor->execute();
        $listado = array();
        if($cursor->rowCount())
        {
            $array = $cursor->fetchAll(PDO::FETCH_OBJ);
            foreach($array as $item)
                array_push($listado, new Usuario($item->id, $item->correo, $item->clave, $item->nombre, $item->apellido, $item->perfil, $item->foto));
        }
        return $listado;
    }

    // A nivel de ruta (/login):
    // (POST) Se envía un JSON → user (correo y clave) y retorna un JSON (éxito: true/false; jwt: JWT
    // (con todos los datos del usuario, a excepción de la clave) / null; status: 200/403)
    public function Login(Request $request, Response $response, array $args) : Response
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->jwt = null;
        $retorno->status = 403;

        $json = $request->getParsedBody();
        $json = json_decode($json['user']);

        $auxiliar = Usuario::TraerUsuarioCorreoClave($json)[0];
        if($auxiliar != null)
        {
            $retorno->jwt = Autentificadora::CrearJWT(new Usuario($auxiliar->id, $auxiliar->correo, null, $auxiliar->nombre, $auxiliar->apellido, $auxiliar->perfil, $auxiliar->foto));
            $retorno->exito = true;
            $retorno->status = 200;
        }

        $newResponse = $response->withStatus($retorno->status);
        $newResponse->getBody()->write(json_encode($retorno));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    public static function TraerUsuarioCorreoClave($json)
    {
        try
        {
            $pdo = AccesoDatos::DameUnObjetoAcceso();
            $cursor = $pdo->RetornarConsulta('SELECT * FROM usuarios WHERE correo = :correo AND clave = :clave');
            $cursor->bindParam(':correo', $json->correo, PDO::PARAM_STR);
            $cursor->bindParam(':clave', $json->clave, PDO::PARAM_STR);
            $cursor->execute();
            if($cursor->rowCount())
            {
                return $cursor->fetchAll(PDO::FETCH_OBJ);
            }
            return false;
        }
        catch(PDOException)
        {
            return null;
        }

    }

    // (GET) Se envía el JWT → token (en el header) y se verifica. En caso exitoso, retorna un JSON
    // con mensaje y status 200. Caso contrario, retorna un JSON con mensaje y status 403.
    public function VerificarJWT(Request $request, Response $response, array $args) : Response
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = 'Error al verificar el JWT';
        $encabezado = $request->getHeader('token')[0];
        $status = 403;

        $verificar = Autentificadora::VerificarJWT($encabezado);
        if($verificar->verificado){
            $status = 200;
            $retorno->mensaje = "Exito al verificar el token.";
            $retorno->exito = true;
        }
        else{
            $status = 200;
        }
       
        $newResponse = $response->withStatus($status);
        $newResponse->getBody()->write(json_encode($retorno));
        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    public static function VerificarExisteCorreoBD($json)
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = 'No existe el correo';
        try
        {
            $pdo = AccesoDatos::DameUnObjetoAcceso();
            $cursor = $pdo->RetornarConsulta('SELECT * FROM usuarios WHERE correo = :correo');
            $cursor->bindParam(':correo', $json->correo, PDO::PARAM_STR);
            $cursor->execute();
            if($cursor->rowCount())
            {
                $retorno->exito = true;
                $retorno->mensaje = 'Existe el correo';
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