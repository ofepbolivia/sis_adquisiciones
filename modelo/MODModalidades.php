<?php
/**
*@package pXP
*@file gen-MODModalidades.php
*@author  (maylee.perez)
*@date 15-10-2020 15:31:50
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODModalidades extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarModalidades(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='adq.ft_modalidades_sel';
		$this->transaccion='ADQ_MODALI_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_modalidad','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('codigo','varchar');
		$this->captura('nombre_modalidad','varchar');
		$this->captura('condicion_menor','numeric');
		$this->captura('condicion_mayor','numeric');
		$this->captura('observaciones','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');

		$this->captura('con_concepto','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarModalidades(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_modalidades_ime';
		$this->transaccion='ADQ_MODALI_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('nombre_modalidad','nombre_modalidad','varchar');
		$this->setParametro('condicion_menor','condicion_menor','numeric');
		$this->setParametro('condicion_mayor','condicion_mayor','numeric');
		$this->setParametro('observaciones','observaciones','varchar');

		$this->setParametro('con_concepto','con_concepto','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarModalidades(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_modalidades_ime';
		$this->transaccion='ADQ_MODALI_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_modalidad','id_modalidad','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('nombre_modalidad','nombre_modalidad','varchar');
		$this->setParametro('condicion_menor','condicion_menor','numeric');
		$this->setParametro('condicion_mayor','condicion_mayor','numeric');
		$this->setParametro('observaciones','observaciones','varchar');

		$this->setParametro('con_concepto','con_concepto','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarModalidades(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_modalidades_ime';
		$this->transaccion='ADQ_MODALI_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_modalidad','id_modalidad','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>