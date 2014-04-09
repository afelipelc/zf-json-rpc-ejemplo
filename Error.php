<?php
/**
* Clase base para Departamento
*/
class ErrorResponse
{
	
	function __construct($err, $msg)
	{
		$this->error = $err;
		$this->message = $msg;
	}

	/** @var boolean */
	public $error;
	/** @var string */
	public $message;
}
?>