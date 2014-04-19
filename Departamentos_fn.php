<?php
/**
* 
*/

//incluir los espacios de nombres de las clases de ZF2 a utilizar
use Zend\Loader\StandardAutoloader;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;

//cargar el archivo encargado de hacer funcionar el ZF2
require_once dirname(__DIR__) . '/ZF2/library/Zend/Loader/StandardAutoloader.php';
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->register();

//incluir la clase Modelo
require_once('Departamento.php');
require_once('Response.php');

class Departamentos
{
	private $db;
	private $configDB;
	function __construct()
	{
		$this->configDB = array(
		'driver' => 'Mysqli',
		'host'     => 'localhost',
		'username' => 'afelipe',
		'password' => 'asdf123',
		'dbname'   => 'departamentos',
		'driver_options' => array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
		);
		//inicializar el Adapter
		$this->db = new Adapter($this->configDB);
	}



	/**
	 * Validar usuario y contraseña
	 *
	 * @param string
	 * @param string
	 * @return Usuario
	*/
	function Login($userName, $password){
		try {
			$sql = "SELECT id, usuario FROM usuarios where usuario='$userName' and pwd=Password('$password') limit 1";
			$result = $this->db->query($sql)->execute();
			//return array_map('utf8_encode', $result->current());
			$row =  $result->current();
			mysql_free_result($result);
			//$this->db->close();
			if($row['id'] > 0 && $row['usuario'] != ""){
				//genera Token
				$tokenKey = $row['usuario'].$row['id']."";
				$sql = "UPDATE usuarios set fechalogin = now(), token = SHA1('$tokenKey') where id=".$row['id']." limit 1";

				$this->db = new Adapter($this->configDB);
				$result = $this->db->query($sql, Adapter::QUERY_MODE_EXECUTE);

				//devolver el resultado de la accion
				if($result->getAffectedRows() == 1)
				{
					mysql_free_result($result);
					//$adapter->close();
					$this->db = new Adapter($this->configDB);
					$sql = "SELECT id, usuario, token FROM usuarios where usuario='$userName' and pwd=Password('$password') limit 1";
					$result = $this->db->query($sql)->execute();
					return $result->current();
				}else
				{
					return new MyResponse(true,true,"No se pudo autenticar a usuario: ".$userName);
					//return "{\"error\":true,\"message\":\"No se pudo autenticar a usuario: $userName\"";
					//return $error;
				}
			}else
			{
				//return "{\"error\":true,\"message\":\"Datos de sesion incorrectos\"";
				//return "Datos invalidos";
				return new MyResponse(true,true,"Datos de sesion incorrectos.");
				//return $error;
			}
		} catch (Exception $_e) {
    	return new MyResponse(false,true,"Error interno en el servidor: ");
				//return "{\"error\":true,\"message\":\"No se pudo autenticar a usuario: $userName\"";
		}
	}


	/**
	 * Lista de todos los departamentos registrados
	 *
	 * @return array
	*/
	function Departamentos()
	{
		//mysqli_set_charset('utf8');
		$result = $this->db->query('SELECT * FROM departamentos')->execute();
		return $this->ConvertirArray($result);

	}

	/**
	 * Obtener los datos de un departamento especificado por ID
	 * 
	 * @param int
	 * @return Departamento
	*/
	function DatosDepartamento($idDepto)
	{
		$sql = "SELECT * FROM departamentos where id=$idDepto limit 1";
		$result = $this->db->query($sql)->execute();
		//return array_map('utf8_encode', $result->current());
		return $result->current();
	}

	/**
	 * Registra un nuevo departamento (todos los datos son necesarios)
	 *
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return Departamento
	*/
	function RegistrarDepartamento($nombre, $responsable, $cargoResp, $email, $telefono, $infoAd, $usuario, $token){
		//antes de registrar, validar token
		if(!$this->validaToken($usuario, $token))
			return new MyResponse(false,true,"Sesión no válida");

		if($nombre && $responsable && $telefono)
		{		
			$sql = "INSERT into departamentos(nombre, responsable, cargoResp, fotoResp, email, telefono, informacion) values('$nombre','$responsable', '$cargoResp', '','$email','$telefono', '$infoAd')";
			$this->db->query($sql, Adapter::QUERY_MODE_EXECUTE);
			$id = $this->db->getDriver()->getLastGeneratedValue();

			//devolver el objeto registrado
			return $this->DatosDepartamento($id);
		}else
			return new MyResponse(false,true,"No se recibieron datos a procesar");
	}

	/**
	 * Actualiza un departamento por ID (todos los datos son necesarios)
	 *
	 * @param int
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return Object
	*/
	function ActualizarDepartamento($idDepto, $nombre, $responsable, $cargoResp, $email, $telefono, $infoAd, $usuario, $token){

		if(!$this->validaToken($usuario, $token))
			return new MyResponse(false,true,"Sesión no válida");

		if($idDepto && $idDepto > 0 && $nombre && $responsable && $telefono)
		{		
			$sql = "UPDATE departamentos set nombre = '$nombre', responsable='$responsable', cargoResp='$cargoResp', email='$email', telefono='$telefono', informacion='$infoAd' where id=$idDepto limit 1";
			$result = $this->db->query($sql, Adapter::QUERY_MODE_EXECUTE);

			//devolver el resultado de la accion
			$actualizado = $result->getAffectedRows() == 1 ? true : false;

			if($actualizado)
				return new MyResponse(true,false,"Datos actualizados");
			else
				return new MyResponse(false,false,"Los datos no se actualizaron, esto sucede si los datos son los mismos que los alamacenados");
		}else
			new MyResponse(false,true,"Sesión no válida");
	}

	/**
	 * Elimina el registro de un departamento y devuelve True o False segun el resultado de la accion
	 *
	 * @param integer
	 * @param boolean
	 * @param string
	 * @param string
	 * @return ErrorResponse
	*/
	function EliminarDepartamento($idDepto, $confirmar, $usuario, $token){
		if(!$this->validaToken($usuario, $token))
			return new MyResponse(false,true,"Sesión no válida");

		if($idDepto && $idDepto > 0 && $confirmar == true)
		{
			$sql = "delete from departamentos where id=$idDepto limit 1";
			$result = $this->db->query($sql, Adapter::QUERY_MODE_EXECUTE);
			//return $result->getAffectedRows() == 1 ? true : false;
			return new MyResponse(true, false,"Se ha eliminado el departemento");
		}else{
			return new MyResponse(false, true,"No se recibieron datos.");
		}
	}

	/**
	 * Devuelve la lista de todos los departamentos que coincidan con la busqueda
	 *
	 * @param string
	 * @return array
	*/
	function Buscar($q)
	{
		$sql = "SELECT * FROM departamentos where nombre like '%$q%' or responsable like '%$q%' or cargoResp like '%$q%'";
		$result = $this->db->query($sql)->execute();
		
		return $this->ConvertirArray($result);
	}

	//funcion para convertir a array un resultado
	private function ConvertirArray($result)
	{
		 //convertir el resultado a arreglo
		  $arreglo = array();
		  foreach ($result as $row)
		  {
			//$arreglo[] = array_map('utf8_encode', $row);
			$arreglo[] = $row;
		  }
		  return $arreglo;
	}

	//funcion para validar la clave proporcionada
	private function validaToken($userName, $token){
		//tomar el token y verificar que sea el que tiene
		//el usuario en la tabla
		$adapter = new Adapter($this->configDB);
		$sql = "SELECT id, usuario from usuarios where usuario='$userName' and token='$token'";

		$result = $adapter->query($sql)->execute();
		$row = $result->current();
		mysql_free_result($result);
		if($row["id"] > 0 && $row["usuario"] != "")
			return true;
		else
			return false;
	}
}
?>