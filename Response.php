<?php
/**
* Clase base para Departamento
*/
class MyResponse
{
	
	function __construct($sucess ,$err, $msg)
	{
		$this->sucess = $sucess;
		$this->error = $err;
		$this->message = $msg;
	}

	/** @var boolean */
	public $sucess;
	/** @var boolean */
	public $error;
	/** @var string */
	public $message;
}
?>