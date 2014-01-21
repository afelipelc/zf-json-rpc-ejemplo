<?php
/**
* Clase base para Departamento
*/
class Departamento
{
	
	function __construct()
	{
		# code...
	}

	/** @var integer */
	public $id;
	/** @var string */
	public $nombre;
	/** @var string */
	public $responsable;
	/** @var string */
	public $cargoResp;
	/** @var string */
	public $fotoResp;
	/** @var string */
	public $email;
	/** @var string */
	public $telefono;
	/** @var string */
	public $informacion;
}
?>