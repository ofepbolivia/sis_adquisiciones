<?php
/**
 * @package pXP
 * @file gen-ACTSolicitudModalidades.php
 * @author  (maylee.perez)
 * @date 28-09-2020 12:12:51
 * @description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */
require_once(dirname(__FILE__) . '/../../pxp/pxpReport/ReportWriter.php');
require_once(dirname(__FILE__) . '/../reportes/RSolicitudCompra.php');
//require_once(dirname(__FILE__).'/../reportes/ROrdenCompra.php');
require_once(dirname(__FILE__) . '/../reportes/RPreOrdenCompra.php');
require_once(dirname(__FILE__) . '/../reportes/DiagramadorGantt.php');
require_once(dirname(__FILE__) . '/../../pxp/pxpReport/DataSource.php');
include_once(dirname(__FILE__) . '/../../lib/PHPMailer/class.phpmailer.php');
include_once(dirname(__FILE__) . '/../../lib/PHPMailer/class.smtp.php');
include_once(dirname(__FILE__) . '/../../lib/lib_general/cls_correo_externo.php');

include_once(dirname(__FILE__) . '/../../sis_seguridad/modelo/MODSubsistema.php');
require_once(dirname(__FILE__) . '/../reportes/RCertificadoPoaPDF.php');

//Reportes para generar el qr y el memorandum de designacion CRP
require_once(dirname(__FILE__) . '/../reportes/RMemoDesigCR.php');
require_once(dirname(__FILE__) . '/../reportes/RGenerarQRCR.php');


class ACTSolicitudModalidades extends ACTbase
{

    function listarSolicitudModalidades()
    {
        $this->objParam->defecto('ordenacion', 'id_solicitud');
        $this->objParam->defecto('dir_ordenacion', 'asc');

        if ($this->objParam->getParametro('id_depto') != '') {
            $this->objParam->addFiltro("sol.id_depto = " . $this->objParam->getParametro('id_depto'));
        }

        if ($this->objParam->getParametro('id_gestion') != '') {
            $this->objParam->addFiltro("sol.id_gestion=" . $this->objParam->getParametro('id_gestion'));
        }

        if ($this->objParam->getParametro('estado') != '') {
            $this->objParam->addFiltro("sol.estado = ''" . $this->objParam->getParametro('estado') . "''");
        }

        if ($this->objParam->getParametro('pes_estado') == 'borrador') {
            $this->objParam->addFiltro("sol.estado in (''borrador'')");
        }
        if ($this->objParam->getParametro('pes_estado') == 'proceso') {
            $this->objParam->addFiltro("sol.estado not in (''borrador'',''finalizado'',''anulado'')");
        }
        if ($this->objParam->getParametro('pes_estado') == 'finalizados') {
            $this->objParam->addFiltro("sol.estado in (''finalizado'',''anulado'')");
        }

        if ($this->objParam->getParametro('filtro_aprobadas') == 1) {
            $this->objParam->addFiltro("(sol.estado = ''aprobado'' or  sol.estado = ''proceso'')");
        }

        if ($this->objParam->getParametro('filtro_solo_aprobadas') == 1) {
            $this->objParam->addFiltro("(sol.estado = ''aprobado'')");
        }

        if ($this->objParam->getParametro('filtro_campo') != '') {
            $this->objParam->addFiltro($this->objParam->getParametro('filtro_campo') . " = " . $this->objParam->getParametro('filtro_valor'));
        }

        //var_dump($_SESSION["ss_id_funcionario"]);

        if ($this->objParam->getParametro('id_cargo') != '' && $this->objParam->getParametro('id_cargo_ai') != '') {
            $this->objParam->addFiltro("(sol.id_cargo_rpc = " . $this->objParam->getParametro('id_cargo') . " or sol.id_cargo_rpc_ai =" . $this->objParam->getParametro('id_cargo_ai') . ")");
        } elseif ($this->objParam->getParametro('id_cargo') != '') {
            $this->objParam->addFiltro("sol.id_cargo_rpc = " . $this->objParam->getParametro('id_cargo'));
        }

        if ($this->objParam->getParametro('tipo_interfaz') == 'solicitudRpc') {
            $this->objParam->addFiltro("(sol.estado != ''finalizado'' and  sol.estado != ''cancelado'')");
        }

        if ($this->objParam->getParametro('moneda_base') == 'base' && ($this->objParam->getParametro('tipo_interfaz') == 'SolicitudVb' || $this->objParam->getParametro('tipo_interfaz') == 'solicitudvbpresupuestos' || $this->objParam->getParametro('tipo_interfaz') == 'solicitudvbpoa')) {
            $this->objParam->addFiltro("sol.id_moneda = 1");
        } else if ($this->objParam->getParametro('moneda_base') == 'extranjera' && ($this->objParam->getParametro('tipo_interfaz') == 'SolicitudVb' || $this->objParam->getParametro('tipo_interfaz') == 'solicitudvbpresupuestos' || $this->objParam->getParametro('tipo_interfaz') == 'solicitudvbpoa')) {
            $this->objParam->addFiltro("sol.id_moneda != 1");
        }

        $this->objParam->addParametro('id_funcionario_usu', $_SESSION["ss_id_funcionario"]);

        //filtro breydi.vasquez 07/01/2020
        $this->objParam->getParametro('tramite_sin_presupuesto_centro_c') != '' && $this->objParam->addFiltro("sol.presupuesto_aprobado = ''sin_presupuesto_cc'' ");

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODSolicitudModalidades', 'listarSolicitudModalidades');
        } else {
            $this->objFunc = $this->create('MODSolicitudModalidades');

            $this->res = $this->objFunc->listarSolicitudModalidades($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarSolicitudCompletaMenor()
    {
        $this->objFunc = $this->create('MODSolicitudModalidades');
        if ($this->objParam->insertar('id_solicitud')) {
            $this->res = $this->objFunc->insertarSolicitudCompletaMenor($this->objParam);
        } else {
            //$this->res=$this->objFunc->modificarSolicitud($this->objParam);
            //trabajar en la modificacion compelta de solicitud ....
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>
