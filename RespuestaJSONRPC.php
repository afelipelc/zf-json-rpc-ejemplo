<?php
/**
* 
*/
class RespuestaJSONRPC
{
	
	function __construct(callback, data)
	{
		$this->callback = callback;
		$this->datos = data;
	}
	
	public $callback;
	public $datos;
}
?>