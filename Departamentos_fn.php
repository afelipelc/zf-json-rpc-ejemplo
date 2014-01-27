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


class Departamentos
{
	private $db;

	function __construct()
	{
		//inicializar el Adapter
		$this->db = new Adapter(array(
		'driver' => 'Mysqli',
		'host'     => '127.0.0.1',
		'username' => 'afelipe',
		'password' => 'asdf123',
		'dbname'   => 'departamentos',
		'driver_options' => array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
		));
	}

	/**
	 * Lista de todos los departamentos registrados
	 *
	 * @return array
	*/
	function Departamentos()
	{
		mysqli_set_charset('utf8');
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
	 * @return Departamento
	*/
	function RegistrarDepartamento($nombre, $responsable, $cargoResp, $fotoResp, $email, $telefono, $infoAd){
		if($nombre && $responsable && $telefono)
		{		
			$sql = "INSERT into departamentos(nombre, responsable, cargoResp, fotoResp, email, telefono, informacion) values('$nombre','$responsable', '$cargoResp', '$fotoResp','$email','$telefono', '$infoAd')";
			$this->db->query($sql, Adapter::QUERY_MODE_EXECUTE);
			$id = $this->db->getDriver()->getLastGeneratedValue();

			//devolver el objeto registrado
			return $this->DatosDepartamento($id);
		}else
			return null;
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
	 * @return Departamento
	*/
	function ActualizarDepartamento($idDepto, $nombre, $responsable, $cargoResp, $email, $telefono, $infoAd){

		if($idDepto && $idDepto > 0 && $nombre && $responsable && $telefono)
		{		
			$sql = "UPDATE departamentos set nombre = '$nombre', responsable='$responsable', cargoResp='$cargoResp', email='$email', telefono='$telefono', informacion='$infoAd' where id=$idDepto limit 1";
			$result = $this->db->query($sql, Adapter::QUERY_MODE_EXECUTE);

			//devolver el resultado de la accion
			return ($result->getAffectedRows() == 1) ? true : false;
		}else
			return null;
	}

	/**
	 * Elimina el registro de un departamento y devuelve True o False segun el resultado de la accion
	 *
	 * @param integer
	 * @param boolean
	 * @return boolean
	*/
	function EliminarDepartamento($idDepto, $confirmar){
		if($idDepto && $idDepto >0 && $confirmar == true)
		{
			
			$sql = "delete from departamentos where id=$idDepto limit 1";
			$result = $this->db->query($sql, Adapter::QUERY_MODE_EXECUTE);
			return $result->getAffectedRows() == 1 ? true : false;
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
}
?>