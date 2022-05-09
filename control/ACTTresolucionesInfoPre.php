<?php
/**
*@package pXP
*@file gen-ACTTresolucionesInfoPre.php
*@author  (maylee.perez)
*@date 07-12-2020 19:01:00
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTTresolucionesInfoPre extends ACTbase{    
			
	function listarTresolucionesInfoPre(){
		$this->objParam->defecto('ordenacion','id_resoluciones_info_pre');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODTresolucionesInfoPre','listarTresolucionesInfoPre');
		} else{
			$this->objFunc=$this->create('MODTresolucionesInfoPre');
			
			$this->res=$this->objFunc->listarTresolucionesInfoPre($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarTresolucionesInfoPre(){
		$this->objFunc=$this->create('MODTresolucionesInfoPre');	
		if($this->objParam->insertar('id_resoluciones_info_pre')){
			$this->res=$this->objFunc->insertarTresolucionesInfoPre($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarTresolucionesInfoPre($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarTresolucionesInfoPre(){
			$this->objFunc=$this->create('MODTresolucionesInfoPre');	
		$this->res=$this->objFunc->eliminarTresolucionesInfoPre($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>