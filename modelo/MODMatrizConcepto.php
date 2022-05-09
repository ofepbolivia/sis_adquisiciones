<?php
/**
*@package pXP
*@file gen-MODMatrizConcepto.php
*@author  (maylee.perez)
*@date 22-09-2020 17:47:40
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODMatrizConcepto extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarMatrizConcepto(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='adq.ft_matriz_concepto_sel';
		$this->transaccion='ADQ_MACONCEP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query

        $this->setParametro('id_matriz_modalidad','id_matriz_modalidad','integer');

        $this->captura('id_matriz_concepto','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_matriz_modalidad','int4');
		$this->captura('id_concepto_ingas','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');

		$this->captura('desc_ingas','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarMatrizConcepto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_matriz_concepto_ime';
		$this->transaccion='ADQ_MACONCEP_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_matriz_modalidad','id_matriz_modalidad','int4');
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarMatrizConcepto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_matriz_concepto_ime';
		$this->transaccion='ADQ_MACONCEP_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_matriz_concepto','id_matriz_concepto','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_matriz_modalidad','id_matriz_modalidad','int4');
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarMatrizConcepto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_matriz_concepto_ime';
		$this->transaccion='ADQ_MACONCEP_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_matriz_concepto','id_matriz_concepto','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>