<?php
/**
*@package pXP
*@file gen-MODTresolucionesInfoPre.php
*@author  (maylee.perez)
*@date 07-12-2020 19:01:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODTresolucionesInfoPre extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarTresolucionesInfoPre(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='adq.ft_tresoluciones_info_pre_sel';
		$this->transaccion='ADQ_REINPRE_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_resoluciones_info_pre','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('nro_directorio','varchar');
		$this->captura('nro_nota','varchar');
		$this->captura('nro_nota2','varchar');
		$this->captura('observaciones','varchar');
		$this->captura('id_gestion','int4');
		$this->captura('gestion','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('fecha_certificacion','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarTresolucionesInfoPre(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_tresoluciones_info_pre_ime';
		$this->transaccion='ADQ_REINPRE_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_directorio','nro_directorio','varchar');
		$this->setParametro('nro_nota','nro_nota','varchar');
		$this->setParametro('nro_nota2','nro_nota2','varchar');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_gestion','id_gestion','int4');
		$this->setParametro('gestion','gestion','int4');
		$this->setParametro('fecha_certificacion','fecha_certificacion','date');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarTresolucionesInfoPre(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_tresoluciones_info_pre_ime';
		$this->transaccion='ADQ_REINPRE_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_resoluciones_info_pre','id_resoluciones_info_pre','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_directorio','nro_directorio','varchar');
		$this->setParametro('nro_nota','nro_nota','varchar');
		$this->setParametro('nro_nota2','nro_nota2','varchar');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_gestion','id_gestion','int4');
		$this->setParametro('gestion','gestion','int4');
		$this->setParametro('fecha_certificacion','fecha_certificacion','date');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarTresolucionesInfoPre(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_tresoluciones_info_pre_ime';
		$this->transaccion='ADQ_REINPRE_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_resoluciones_info_pre','id_resoluciones_info_pre','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>