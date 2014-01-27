<?php
    header('Access-Control-Allow-Origin: *');  
    header('Access-Control-Allow-Methods: GET, POST');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json charset=utf-8');

//incluir los espacios de nombres de las clases de ZF2 a utilizar
use Zend\Loader\StandardAutoloader;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;

//cargar el archivo encargado de hacer funcionar el ZF2
require_once dirname(__DIR__) . '/ZF2/library/Zend/Loader/StandardAutoloader.php';
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->register();


//Si se recibe el ID de imagen, se procesa el archivo
// se responde con  JSON
if(isset($_GET['idDepartamento'])){
	$procesarFotos = new ProcesarFotos();
	echo $procesarFotos->GuardarFoto($_GET['idDepartamento'],$_FILES);
	return;
	
}else
{
	echo "{\"status\":\"error\",\"done\":false}";
}

/**
* Clase para procesar las imagenes, se almacena en archivo y se actualiza la BD
*/
class ProcesarFotos
{
	private $db;
	private $uploaddir = '../departamentos/images/depto/';

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
	 * @param int
	 * @param object
	 * @return string
	*/
	
	function GuardarFoto($idDepto,$file)
	{
		
		$allowed_types=array(
		    'image/gif',
		    'image/jpeg',
		    'image/png',
		);

		if (in_array($file['foto']['type'], $allowed_types)) {
			
			$nombreimg= $idDepto."_".date("ymdHis");

			$dotIndex = strrpos($file['foto']['name'], ".");
			$nombreimg = $nombreimg.substr($file['foto']['name'], $dotIndex);
			
			//$this->EliminarFotos($idDepto);

			//if(move_uploaded_file($file['foto']['tmp_name'], $uploaddir.$file['foto']['name']))
			if(move_uploaded_file($file['foto']['tmp_name'], $this->uploaddir.$nombreimg))
			{
				//guardar en la BD
				$sql = "UPDATE departamentos set fotoResp = '$nombreimg' where id=$idDepto limit 1";
				$result = $this->db->query($sql, Adapter::QUERY_MODE_EXECUTE);

				//devolver el resultado de la accion
				if ($result->getAffectedRows() == 1){
					return "{\"status\":\"ok\",\"done\":true,\"imagen\":\"".$nombreimg."\"}";
				}else
				{
					return "{\"status\":\"error\",\"done\":false,\"imagen\":\"".$nombreimg."\",\"message\":\"No se actualizo la BD\"}";
				}
			}else
			{
				return "{\"status\":\"ok\",\"done\":false,\"message\":\"No se pudo guardar la imagen.\"}";
			}
		}else
		{
			return "{\"status\":\"error\",\"done\":false,\"message\":\"Formato de imagen no aceptada.\"}";
		}
	}

	function EliminarFotos($idDepto){
			//unlink($uploaddir.$imagen);
		foreach(glob($this->uploaddir.$idDepto_.'*.*') as $file){
     		unlink($file);
		}
	}

}
?>