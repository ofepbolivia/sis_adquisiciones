<?php
/**
*@package pXP
*@file gen-ACTMatrizModalidad.php
*@author  (maylee.perez)
*@date 22-09-2020 13:33:53
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTMatrizModalidad extends ACTbase{    
			
	function listarMatrizModalidad(){
		$this->objParam->defecto('ordenacion','id_matriz_modalidad');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODMatrizModalidad','listarMatrizModalidad');
		} else{
			$this->objFunc=$this->create('MODMatrizModalidad');
			
			$this->res=$this->objFunc->listarMatrizModalidad($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarMatrizModalidad(){
		$this->objFunc=$this->create('MODMatrizModalidad');	
		if($this->objParam->insertar('id_matriz_modalidad')){
			$this->res=$this->objFunc->insertarMatrizModalidad($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarMatrizModalidad($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarMatrizModalidad(){
			$this->objFunc=$this->create('MODMatrizModalidad');	
		$this->res=$this->objFunc->eliminarMatrizModalidad($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>