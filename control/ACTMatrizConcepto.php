<?php
/**
*@package pXP
*@file gen-ACTMatrizConcepto.php
*@author  (maylee.perez)
*@date 22-09-2020 17:47:40
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTMatrizConcepto extends ACTbase{    
			
	function listarMatrizConcepto(){
		$this->objParam->defecto('ordenacion','id_matriz_concepto');

        /*if($this->objParam->getParametro('id_matriz_modalidad')!=''){
            $this->objParam->addFiltro("id_matriz_modalidad = ".$this->objParam->getParametro('id_matriz_modalidad'));
        }*/

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODMatrizConcepto','listarMatrizConcepto');
		} else{
			$this->objFunc=$this->create('MODMatrizConcepto');
			
			$this->res=$this->objFunc->listarMatrizConcepto($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarMatrizConcepto(){
		$this->objFunc=$this->create('MODMatrizConcepto');	
		if($this->objParam->insertar('id_matriz_concepto')){
			$this->res=$this->objFunc->insertarMatrizConcepto($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarMatrizConcepto($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarMatrizConcepto(){
			$this->objFunc=$this->create('MODMatrizConcepto');	
		$this->res=$this->objFunc->eliminarMatrizConcepto($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>