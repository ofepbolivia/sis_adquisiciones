<?php
/**
 * @package pXP
 * @file gen-MODProcesoCompra.php
 * @author  (admin)
 * @date 19-03-2013 12:55:30
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODProcesoCompra extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function listarProcesoCompra()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_PROC_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_proceso_compra', 'int4');
        $this->captura('id_depto', 'int4');
        $this->captura('num_convocatoria', 'varchar');
        $this->captura('id_solicitud', 'int4');
        $this->captura('id_estado_wf', 'int4');
        $this->captura('fecha_ini_proc', 'date');
        $this->captura('obs_proceso', 'varchar');
        $this->captura('id_proceso_wf', 'int4');
        $this->captura('num_tramite', 'varchar');
        $this->captura('codigo_proceso', 'varchar');
        $this->captura('estado_reg', 'varchar');
        $this->captura('estado', 'varchar');
        $this->captura('num_cotizacion', 'varchar');
        $this->captura('id_usuario_reg', 'int4');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('fecha_mod', 'timestamp');
        $this->captura('id_usuario_mod', 'int4');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');

        $this->captura('desc_depto', 'varchar');
        $this->captura('desc_funcionario', 'text');
        $this->captura('desc_solicitud', 'varchar');
        $this->captura('desc_moneda', 'varchar');
        $this->captura('instruc_rpc', 'varchar');
        $this->captura('id_categoria_compra', 'int4');
        $this->captura('usr_aux', 'varchar');
        $this->captura('id_moneda', 'integer');
        $this->captura('id_funcionario', 'integer');
        $this->captura('id_usuario_auxiliar', 'integer');
        $this->captura('objeto', 'varchar');

        $this->captura('estados_cotizacion', 'text');
        $this->captura('numeros_oc', 'text');
        $this->captura('proveedores_cot', 'text');
        $this->captura('prioridad', 'varchar');

        $this->captura('id_gestion', 'int4');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarProcesoCompraSolicitud()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_PROCSOL_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_solicitud', 'id_solicitud', 'int4');
        //Definicion de la lista del resultado del query
        $this->captura('id_proceso_compra', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarProcesoCompraPedido()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_PROCPED_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_proceso_compra', 'id_proceso_compra', 'int4');
        //Definicion de la lista del resultado del query
        $this->captura('id_proceso_compra', 'int4');
        $this->captura('id_depto', 'int4');
        $this->captura('num_convocatoria', 'varchar');
        $this->captura('id_solicitud', 'int4');
        $this->captura('id_estado_wf', 'int4');
        $this->captura('fecha_ini_proc', 'date');
        $this->captura('obs_proceso', 'varchar');
        $this->captura('id_proceso_wf', 'int4');
        $this->captura('num_tramite', 'varchar');
        $this->captura('codigo_proceso', 'varchar');
        $this->captura('estado_reg', 'varchar');
        $this->captura('estado', 'varchar');
        $this->captura('num_cotizacion', 'varchar');
        $this->captura('id_usuario_reg', 'int4');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('fecha_mod', 'timestamp');
        $this->captura('id_usuario_mod', 'int4');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');

        $this->captura('desc_depto', 'varchar');
        $this->captura('desc_funcionario', 'text');
        $this->captura('desc_solicitud', 'varchar');
        $this->captura('desc_moneda', 'varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    /*
    Asignacion de usuarios al proceso de compra

    */
    function asignarUsuarioProceso()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'adq.f_proceso_compra_ime';
        $this->transaccion = 'ADQ_ASIGPROC_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_depto_usuario', 'id_depto_usuario', 'int4');
        $this->setParametro('id_solicitud', 'id_solicitud', 'int4');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarProcesoCompra()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'adq.f_proceso_compra_ime';
        $this->transaccion = 'ADQ_PROC_INS';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_depto', 'id_depto', 'int4');
        $this->setParametro('num_convocatoria', 'num_convocatoria', 'varchar');
        $this->setParametro('id_solicitud', 'id_solicitud', 'int4');
        $this->setParametro('id_estado_wf', 'id_estado_wf', 'int4');
        $this->setParametro('fecha_ini_proc', 'fecha_ini_proc', 'date');
        $this->setParametro('obs_proceso', 'obs_proceso', 'varchar');
        $this->setParametro('id_proceso_wf', 'id_proceso_wf', 'int4');
        $this->setParametro('num_tramite', 'num_tramite', 'varchar');
        $this->setParametro('codigo_proceso', 'codigo_proceso', 'varchar');
        $this->setParametro('estado_reg', 'estado_reg', 'varchar');
        $this->setParametro('estado', 'estado', 'varchar');
        $this->setParametro('num_cotizacion', 'num_cotizacion', 'varchar');
        $this->setParametro('id_depto_usuario', 'id_depto_usuario', 'integer');
        $this->setParametro('objeto', 'objeto', 'varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarProcesoCompra()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'adq.f_proceso_compra_ime';
        $this->transaccion = 'ADQ_PROC_MOD';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_proceso_compra', 'id_proceso_compra', 'int4');
        $this->setParametro('id_depto', 'id_depto', 'int4');
        $this->setParametro('num_convocatoria', 'num_convocatoria', 'varchar');
        $this->setParametro('id_solicitud', 'id_solicitud', 'int4');
        $this->setParametro('id_estado_wf', 'id_estado_wf', 'int4');
        $this->setParametro('fecha_ini_proc', 'fecha_ini_proc', 'date');
        $this->setParametro('obs_proceso', 'obs_proceso', 'varchar');
        $this->setParametro('id_proceso_wf', 'id_proceso_wf', 'int4');
        $this->setParametro('num_tramite', 'num_tramite', 'varchar');
        $this->setParametro('codigo_proceso', 'codigo_proceso', 'varchar');
        $this->setParametro('estado_reg', 'estado_reg', 'varchar');
        $this->setParametro('estado', 'estado', 'varchar');
        $this->setParametro('num_cotizacion', 'num_cotizacion', 'varchar');


        $this->setParametro('objeto', 'objeto', 'varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarProcesoCompra()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'adq.f_proceso_compra_ime';
        $this->transaccion = 'ADQ_PROC_ELI';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_proceso_compra', 'id_proceso_compra', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function revertirPresupuesto()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'adq.f_proceso_compra_ime';
        $this->transaccion = 'ADQ_REVPRE_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_proceso_compra', 'id_proceso_compra', 'int4');
        $this->setParametro('id_solicitud', 'id_solicitud', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function finalizarProceso()
    {
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'adq.f_proceso_compra_ime';
        $this->transaccion = 'ADQ_FINPRO_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_proceso_compra', 'id_proceso_compra', 'int4');
        $this->setParametro('id_solicitud', 'id_solicitud', 'int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarReporteTiemposProcesoCompra()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_PROCESTIE_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('id_depto', 'id_depto', 'integer');
        $this->setParametro('tipo', 'tipo', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('num_tramite', 'varchar');
        $this->captura('justificacion', 'varchar');
        $this->captura('proveedor', 'varchar');
        $this->captura('empleado', 'varchar');
        $this->captura('fecha_inicio', 'date');
        $this->captura('mayor_20', 'varchar');
        $this->captura('tiempo_asignacion', 'numeric');
        $this->captura('tiempo_atencion', 'numeric');
        $this->captura('tiempo_legal', 'integer');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarReporteTiemposProcesoCompraResumen()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_PROCESTIE_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('id_depto', 'id_depto', 'integer');
        $this->setParametro('tipo', 'tipo', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('empleado', 'varchar');
        $this->captura('mayor_20', 'varchar');
        $this->captura('num_atendidos', 'integer');
        $this->captura('tiempo_asignacion', 'numeric');
        $this->captura('tiempo_atencion', 'numeric');
        $this->captura('tiempo_legal', 'numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function procesosIniciadosAdjudicadosEjecutados()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_PROINIADEJE_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('id_depto', 'id_depto', 'integer');
        $this->setParametro('tipo', 'tipo', 'varchar');
        $this->setParametro('monto_mayor', 'monto_mayor', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('num_tramite', 'varchar');
        $this->captura('justificacion', 'text');
        $this->captura('solicitante', 'text');
        $this->captura('tecnico_adquisiciones', 'text');
        $this->captura('proveedor_recomendado', 'varchar');
        $this->captura('proveedor_adjudicado', 'text');
        $this->captura('fecha_ini_proc', 'date');
        $this->captura('precio_bs', 'numeric');
        $this->captura('precio_moneda_solicitada', 'numeric');
        $this->captura('moneda_solicitada', 'varchar');
        $this->captura('requiere_contrato', 'text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function procesosIniAdjuEjecResumen()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_INADEJRES_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('id_depto', 'id_depto', 'integer');
        $this->setParametro('monto_mayor', 'monto_mayor', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('estado', 'text');
        $this->captura('nombre', 'varchar');
        $this->captura('total', 'bigint');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reporteProcesosContratacion()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_REPROCONTRA_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion
        //$this->setCount(false);

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('monto_mayor', 'monto_mayor', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('nombre_depto', 'varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('justificacion', 'text');
        $this->captura('solicitante', 'text');
        $this->captura('proveedor_adjudicado', 'text');
        //$this->captura('fecha_ini_proc','date');
        $this->captura('fecha_soli', 'date');
        $this->captura('precio_bs', 'varchar');
        $this->captura('precio_moneda_solicitada', 'varchar');
        $this->captura('moneda_solicitada', 'varchar');
        $this->captura('requiere_contrato', 'text');
        $this->captura('estado_solicitud', 'text');
        $this->captura('estado_compra', 'text');


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reporteProcesosContratacionResumido()
    {

        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_REPROCONRES_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('tipo', 'tipo', 'varchar');
        $this->setParametro('monto_mayor', 'monto_mayor', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('num_tramite', 'varchar');
        $this->captura('importe', 'varchar');
        $this->captura('estados_cotizacion', 'varchar');
        $this->captura('estado', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function procesosIniContratacion()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_INADEJRES_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('id_depto', 'id_depto', 'integer');
        $this->setParametro('monto_mayor', 'monto_mayor', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('estado', 'text');
        $this->captura('nombre', 'varchar');
        $this->captura('total', 'bigint');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reporteProcesosContraDet()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_REPRODET_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        //$this->setParametro('id_depto', 'id_depto', 'integer');
        $this->setParametro('tipo', 'tipo', 'varchar');
        $this->setParametro('monto_mayor', 'monto_mayor', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('num_tramite', 'varchar');
        $this->captura('solicitante', 'text');
        $this->captura('proveedor_adjudicado', 'text');
        $this->captura('fecha_ini_proc', 'date');
        $this->captura('fecha_soli', 'date');
        $this->captura('fecha_adju', 'date');
        $this->captura('precio_bs', 'varchar');
        $this->captura('precio_moneda_solicitada', 'varchar');
        $this->captura('requiere_contrato', 'text');
        $this->captura('tipo', 'text');
        $this->captura('nro_contrato', 'varchar');
        $this->captura('objeto', 'varchar');
        $this->captura('forma_pago', 'varchar');
        $this->captura('tiempo_entrega', 'varchar');
        $this->captura('monto_total_adjudicado_mb', 'varchar');
        $this->captura('monto_total_adjudicado', 'varchar');
        $this->captura('numero_oc', 'varchar');
        $this->captura('desc_proveedor', 'varchar');
        $this->captura('fecha_entrega', 'date');
        $this->captura('desc_depto', 'varchar');
        $this->captura('estados_cotizacion', 'varchar');
        $this->captura('nombre_unidad', 'varchar');
        $this->captura('numero', 'varchar');
        $this->captura('fecha_elaboracion', 'date');
        $this->captura('fecha_inicio', 'varchar');
        $this->captura('fecha_fin', 'varchar');
        $this->captura('nro_cuota_vigente', 'numeric');
        $this->captura('total_pagado', 'varchar');
        $this->captura('codigo', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function reporteProcesosContraAdj()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_REPROADJU_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion
        //$this->setCount(false);

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('monto_mayor', 'monto_mayor', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('cuce', 'varchar');
        $this->captura('fecha_conclusion', 'date');
        $this->captura('num_tramite', 'varchar');
        $this->captura('tipo_concepto', 'varchar');
        $this->captura('modalidad', 'varchar');
        $this->captura('requiere_contrato', 'varchar');
        $this->captura('nro_po', 'varchar');
        $this->captura('numero', 'varchar');
        $this->captura('fecha_elaboracion', 'date');
        $this->captura('objeto', 'varchar');
        $this->captura('desc_proveedor', 'varchar');
        $this->captura('monto_total_adjudicado_mb', 'varchar');
        $this->captura('justificacion', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

}

?>