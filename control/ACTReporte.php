<?php
/**
 *@package pXP
 *@file ACTReporte.php
 *@author  (fea)
 *@date 19-02-2018 12:55:30
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

require_once(dirname(__FILE__).'/../reportes/RDetalleForm400.php');
require_once(dirname(__FILE__).'/../reportes/RDetalleForm500.php');

class ACTReporte extends ACTbase{

    function listarForm400(){

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODReporte','listarForm400');
        }else {

            $this->objFunc = $this->create('MODReporte');
            $this->res = $this->objFunc->listarForm400($this->objParam);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function listarForm500(){

        /*switch($this->objParam->getParametro('pes_estado')) {
            case 'con_form':
                $this->objParam->addFiltro("tdw.chequeado = " . "''si''");
                break;
            case 'sin_form':
                $this->objParam->addFiltro("tdw.chequeado = " . "''no''");
                break;
        }*/

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODReporte','listarForm500');
        }else {

            $this->objFunc = $this->create('MODReporte');
            $this->res = $this->objFunc->listarForm500($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    /*(F.E.A)Permite controlar si hay procesos con formulario 400 y 500 que son igual o menores 5 dias
    para su vencimiento y se empieza a alertar a los auxiliares de adquisiciones.*/
    function alertarFormularios_4_5(){

        $this->objFunc=$this->create('MODReporte');
        //formulario 400
        $dataSource=$this->objFunc->alertarFormularios_4($this->objParam);
        $this->res = $dataSource;
        $this->dataSource=$dataSource->getDatos();


        $nombreArchivo = uniqid(md5(session_id()).'[Detalle-Form400]').'.pdf';
        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->reporte = new RDetalleForm400($this->objParam);
        $this->reporte->setDatos($this->dataSource);
        $this->reporte->generarReporte();
        $this->reporte->output($this->reporte->url_archivo,'F');

        $evento = "enviarMensajeUsuario";
        //datos para el websocket
        $data = array(
            "mensaje" => 'Por favor adjuntar formulario 400 para los siguientes procesos.',
            "tipo_mensaje" => 'notificacion',//documento_generado
            "titulo" => 'Control Documentos',
            "id_usuario" => $this->dataSource[0]['id_usuario'],
            "destino" => 'Unico',
            "evento" => $evento,
            "url" => $nombreArchivo
        );
        $send = array(
            "tipo" => "enviarMensajeUsuario",
            "data" => $data
        );
        $usuarios_socket = $this->dispararEventoWS($send);
        $usuarios_socket =json_decode($usuarios_socket, true);
        //end 400


        //formulario 500
        $this->objFunc=$this->create('MODReporte');
        $dataSource=$this->objFunc->reportePendientesForm500($this->objParam);
        $this->res = $dataSource;
        $this->dataSource=$dataSource->getDatos();


        $nombreArchivo = uniqid(md5(session_id()).'[Detalle-Form500]').'.pdf';
        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->reporte = new RDetalleForm500($this->objParam);
        $this->reporte->setDatos($this->dataSource);
        $this->reporte->generarReporte();
        $this->reporte->output($this->reporte->url_archivo,'F');

        $evento = "enviarMensajeUsuario";
        //datos para el websocket
        $data = array(
            "mensaje" => 'Por favor adjuntar formulario 500 para los siguientes procesos.',
            "tipo_mensaje" => 'notificacion',//documento_generado
            "titulo" => 'Control Documentos Form. 500',
            "id_usuario" => $this->dataSource[0]['id_usuario'],
            "destino" => 'Unico',
            "evento" => $evento,
            "url" => $nombreArchivo
        );
        $send = array(
            "tipo" => "enviarMensajeUsuario",
            "data" => $data
        );
        $usuarios_socket = $this->dispararEventoWS($send);
        $usuarios_socket =json_decode($usuarios_socket, true);

        //end 500

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
        //$this->res->imprimirRespuesta($this->res->generarJson());

    }

    /*(F.E.A)Permite generara reporte pdf con el detalle de los procesos pendientes
    del formulario 400.*/
    function reportePendientesForm400(){

        $this->objFunc=$this->create('MODReporte');
        $dataSource=$this->objFunc->reportePendientesForm400($this->objParam);
        $this->dataSource=$dataSource->getDatos();


        $nombreArchivo = uniqid(md5(session_id()).'[Pendientes-Form400]').'.pdf';
        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->reporte = new RDetalleForm400($this->objParam);
        $this->reporte->setDatos($this->dataSource);
        $this->reporte->generarReporte();
        $this->reporte->output($this->reporte->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    /*(F.E.A)Permite generara reporte pdf con el detalle de los procesos pendientes
    del formulario 500.*/
    function reportePendientesForm500(){

        $this->objFunc=$this->create('MODReporte');
        $dataSource=$this->objFunc->reportePendientesForm500($this->objParam);
        $this->dataSource=$dataSource->getDatos();


        $nombreArchivo = uniqid(md5(session_id()).'[Pendientes-Form500]').'.pdf';
        $this->objParam->addParametro('orientacion','L');
        $this->objParam->addParametro('tamano','LETTER');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        $this->reporte = new RDetalleForm500($this->objParam);
        $this->reporte->setDatos($this->dataSource);
        $this->reporte->generarReporte();
        $this->reporte->output($this->reporte->url_archivo,'F');


        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    /*(f.e.a) Recupera el id_usuario y descripcion del usuario para la primera carga de la interfaz ConsultaForm400*/
    function getDatosUsuario () {
        $this->objFunc=$this->create('MODReporte');
        $this->res=$this->objFunc->getDatosUsuario($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>