<?php
/**
*@package pXP
*@file gen-ACTModalidades.php
*@author  (maylee.perez)
*@date 15-10-2020 15:31:50
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTModalidades extends ACTbase{    
			
	function listarModalidades(){
		$this->objParam->defecto('ordenacion','id_modalidad');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODModalidades','listarModalidades');
		} else{
			$this->objFunc=$this->create('MODModalidades');
			
			$this->res=$this->objFunc->listarModalidades($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarModalidades(){
		$this->objFunc=$this->create('MODModalidades');	
		if($this->objParam->insertar('id_modalidad')){
			$this->res=$this->objFunc->insertarModalidades($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarModalidades($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarModalidades(){
			$this->objFunc=$this->create('MODModalidades');	
		$this->res=$this->objFunc->eliminarModalidades($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>