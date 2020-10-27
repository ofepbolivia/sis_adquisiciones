<?php
/**
*@package pXP
*@file gen-MODMatrizModalidad.php
*@author  (maylee.perez)
*@date 22-09-2020 13:33:53
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODMatrizModalidad extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarMatrizModalidad(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='adq.ft_matriz_modalidad_sel';
		$this->transaccion='ADQ_MATRIZ_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_matriz_modalidad','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('referencia','varchar');
		$this->captura('tipo_contratacion','varchar');
		$this->captura('nacional','varchar');
		$this->captura('internacional','varchar');
		$this->captura('id_uo','int4');
		$this->captura('nivel_importancia','varchar');
		$this->captura('id_cargo','int4');
		$this->captura('contrato_global','varchar');
		$this->captura('modalidad_menor','varchar');
		$this->captura('modalidad_anpe','varchar');
		$this->captura('modalidad_licitacion','varchar');
		$this->captura('modalidad_directa','varchar');
		$this->captura('modalidad_excepcion','varchar');
		$this->captura('modalidad_desastres','varchar');
		$this->captura('punto_reorden','varchar');
		$this->captura('observaciones','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('nombre_uo','varchar');
		$this->captura('codigo_uo','varchar');
		$this->captura('nombre','varchar');
		$this->captura('list_concepto_gasto','varchar');

		$this->captura('resp_proc_contratacion_menor','varchar');
		$this->captura('resp_proc_contratacion_anpe','varchar');
		$this->captura('resp_proc_contratacion_directa','varchar');
		$this->captura('resp_proc_contratacion_licitacion','varchar');
		$this->captura('resp_proc_contratacion_excepcion','varchar');
		$this->captura('resp_proc_contratacion_desastres','varchar');
		$this->captura('flujo_mod_directa','varchar');

		$this->captura('nombre_gerencia','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarMatrizModalidad(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_matriz_modalidad_ime';
		$this->transaccion='ADQ_MATRIZ_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('referencia','referencia','varchar');
		$this->setParametro('tipo_contratacion','tipo_contratacion','varchar');
		$this->setParametro('nacional','nacional','varchar');
		$this->setParametro('internacional','internacional','varchar');
		$this->setParametro('id_uo','id_uo','int4');
		$this->setParametro('nivel_importancia','nivel_importancia','varchar');
		$this->setParametro('id_cargo','id_cargo','int4');
		$this->setParametro('contrato_global','contrato_global','varchar');
		$this->setParametro('modalidad_menor','modalidad_menor','varchar');
		$this->setParametro('modalidad_anpe','modalidad_anpe','varchar');
		$this->setParametro('modalidad_licitacion','modalidad_licitacion','varchar');
		$this->setParametro('modalidad_directa','modalidad_directa','varchar');
		$this->setParametro('modalidad_excepcion','modalidad_excepcion','varchar');
		$this->setParametro('modalidad_desastres','modalidad_desastres','varchar');
		$this->setParametro('punto_reorden','punto_reorden','varchar');
		$this->setParametro('observaciones','observaciones','varchar');

		$this->setParametro('resp_proc_contratacion_menor','resp_proc_contratacion_menor','varchar');
		$this->setParametro('resp_proc_contratacion_anpe','resp_proc_contratacion_anpe','varchar');
		$this->setParametro('resp_proc_contratacion_directa','resp_proc_contratacion_directa','varchar');
		$this->setParametro('resp_proc_contratacion_licitacion','resp_proc_contratacion_licitacion','varchar');
		$this->setParametro('resp_proc_contratacion_excepcion','resp_proc_contratacion_excepcion','varchar');
		$this->setParametro('resp_proc_contratacion_desastres','resp_proc_contratacion_desastres','varchar');
		$this->setParametro('flujo_mod_directa','flujo_mod_directa','varchar');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarMatrizModalidad(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_matriz_modalidad_ime';
		$this->transaccion='ADQ_MATRIZ_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_matriz_modalidad','id_matriz_modalidad','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('referencia','referencia','varchar');
		$this->setParametro('tipo_contratacion','tipo_contratacion','varchar');
		$this->setParametro('nacional','nacional','varchar');
		$this->setParametro('internacional','internacional','varchar');
		$this->setParametro('id_uo','id_uo','int4');
		$this->setParametro('nivel_importancia','nivel_importancia','varchar');
		$this->setParametro('id_cargo','id_cargo','int4');
		$this->setParametro('contrato_global','contrato_global','varchar');
		$this->setParametro('modalidad_menor','modalidad_menor','varchar');
		$this->setParametro('modalidad_anpe','modalidad_anpe','varchar');
		$this->setParametro('modalidad_licitacion','modalidad_licitacion','varchar');
		$this->setParametro('modalidad_directa','modalidad_directa','varchar');
		$this->setParametro('modalidad_excepcion','modalidad_excepcion','varchar');
		$this->setParametro('modalidad_desastres','modalidad_desastres','varchar');
		$this->setParametro('punto_reorden','punto_reorden','varchar');
		$this->setParametro('observaciones','observaciones','varchar');

        $this->setParametro('resp_proc_contratacion_menor','resp_proc_contratacion_menor','varchar');
        $this->setParametro('resp_proc_contratacion_anpe','resp_proc_contratacion_anpe','varchar');
        $this->setParametro('resp_proc_contratacion_directa','resp_proc_contratacion_directa','varchar');
        $this->setParametro('resp_proc_contratacion_licitacion','resp_proc_contratacion_licitacion','varchar');
        $this->setParametro('resp_proc_contratacion_excepcion','resp_proc_contratacion_excepcion','varchar');
        $this->setParametro('resp_proc_contratacion_desastres','resp_proc_contratacion_desastres','varchar');
        $this->setParametro('flujo_mod_directa','flujo_mod_directa','varchar');



        //Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarMatrizModalidad(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='adq.ft_matriz_modalidad_ime';
		$this->transaccion='ADQ_MATRIZ_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_matriz_modalidad','id_matriz_modalidad','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>