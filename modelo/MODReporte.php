<?php
/**
 *@package pXP
 *@file MODReporte.php
 *@author  (fea)
 *@date 19-02-2018 12:55:30
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODReporte extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarForm400(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='adq.f_reporte_sel';
        $this->transaccion='ADQ_FORM_400_SEL';
        $this->tipo_procedimiento='SEL';

        $this->setParametro('id_usuario','id_usuario','int4');
        $this->setParametro('chequeado','chequeado','varchar');

        //Define los parametros para la funcion

        $this->captura('id_cotizacion', 'int4');
        $this->captura('id_proceso_wf', 'int4');
        $this->captura('id_estado_wf', 'int4');
        $this->captura('estado', 'varchar');

        $this->captura('num_tramite', 'varchar');
        $this->captura('fun_solicitante', 'varchar');
        $this->captura('fun_resp', 'varchar');
        $this->captura('tieneform400', 'varchar');

        $this->captura('dias_form_400', 'integer');
        $this->captura('fecha_inicio', 'date');
        $this->captura('tipo_doc', 'varchar');
        $this->captura('fecha_aprob', 'date');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarForm500(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='adq.f_reporte_sel';
        $this->transaccion='ADQ_FORM_500_SEL';
        $this->tipo_procedimiento='SEL';

        $this->setParametro('id_usuario','id_usuario','int4');
        $this->setParametro('chequeado','chequeado','varchar');

        //Define los parametros para la funcion
        $this->captura('id_cotizacion', 'int4');
        $this->captura('id_proceso_wf', 'int4');
        $this->captura('id_estado_wf', 'int4');
        $this->captura('estado', 'varchar');

        $this->captura('num_tramite', 'varchar');
        $this->captura('fun_solicitante', 'varchar');
        $this->captura('fun_resp', 'varchar');
        $this->captura('tieneform500', 'varchar');
        $this->captura('conformidad', 'varchar');
        $this->captura('nro_cuota', 'numeric');
        $this->captura('dias_form_500', 'integer');
        $this->captura('fecha_inicio', 'date');
        //$this->captura('fecha_fin', 'date');
        $this->captura('fecha_conformidad', 'date');
        $this->captura('tipo_doc', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //(f.e.a)
    function alertarFormularios_4(){
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_ALERT_FORM_4_5';
        $this->tipo_procedimiento = 'SEL';

        $this->setParametro('id_usuario','id_usuario','int4');

        $this->captura('id_usuario','int4');
        $this->captura('estado','varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('fun_solicitante', 'varchar');
        $this->captura('tieneform400', 'varchar');
        $this->captura('dias_form_400', 'integer');
        $this->captura('fecha_inicio', 'date');
        $this->captura('fecha_fin', 'date');
        $this->captura('desc_depto', 'varchar');
        $this->captura('fun_responsable', 'varchar');
        $this->captura('plazo_dias', 'varchar');

        //Ejecutar la instruccion
        $this->armarConsulta();
        //var_dump($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;

    }

    //(f.e.a)
    function alertarFormularios_5(){
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_ALERT_FORM_5';
        $this->tipo_procedimiento = 'SEL';

        $this->setParametro('id_usuario','id_usuario','int4');

        $this->captura('id_usuario','int4');
        $this->captura('estado','varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('fun_solicitante', 'varchar');
        $this->captura('fun_responsable', 'varchar');
        $this->captura('desc_depto', 'varchar');
        $this->captura('tieneform500', 'varchar');
        $this->captura('conformidad', 'varchar');
        $this->captura('dias_form_500', 'integer');
        $this->captura('fecha_inicio', 'date');
        $this->captura('fecha_fin', 'date');
        $this->captura('plazo_dias', 'varchar');

        //Ejecutar la instruccion
        $this->armarConsulta();
        //var_dump($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;

    }

    //(f.e.a)
    function reportePendientesForm400(){
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_PEN_FORM400_REP';
        $this->tipo_procedimiento = 'SEL';

        $this->setParametro('id_usuario','id_usuario','int4');
        $this->setParametro('chequeado','chequeado','varchar');

        $this->captura('id_usuario','int4');
        $this->captura('estado','varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('fun_solicitante', 'varchar');
        $this->captura('tieneform400', 'varchar');
        $this->captura('dias_form_400', 'integer');
        $this->captura('fecha_inicio', 'date');
        //$this->captura('fecha_fin', 'date');
        $this->captura('desc_depto', 'varchar');
        $this->captura('fun_responsable', 'varchar');
        $this->captura('plazo_dias', 'varchar');
        $this->captura('tipo_doc', 'varchar');


        //Ejecutar la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;

    }

    //(f.e.a)
    function reportePendientesForm500(){
        $this->procedimiento = 'adq.f_proceso_compra_sel';
        $this->transaccion = 'ADQ_PEN_FORM500_REP';
        $this->tipo_procedimiento = 'SEL';

        $this->setParametro('id_usuario','id_usuario','int4');

        $this->captura('id_usuario','int4');
        $this->captura('estado','varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('fun_solicitante', 'varchar');
        $this->captura('fun_responsable', 'varchar');
        $this->captura('desc_depto', 'varchar');
        $this->captura('tieneform500', 'varchar');
        $this->captura('conformidad', 'varchar');
        $this->captura('dias_form_500', 'integer');
        $this->captura('fecha_inicio', 'date');
        $this->captura('fecha_fin', 'date');
        $this->captura('plazo_dias', 'varchar');

        //Ejecutar la instruccion
        $this->armarConsulta();
        //var_dump($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;

    }

    function getDatosUsuario(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='adq.f_reporte_ime';
        $this->transaccion='ADQ_USUARIO_GET';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_usuario','id_usuario','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //(f.e.a)
    function reporteTiempoPresupuesto(){

        $this->procedimiento = 'adq.f_reporte_sel';
        $this->transaccion = 'ADQ_TIME_PRES_REP';
        $this->tipo_procedimiento = 'SEL';

        $this->setParametro('id_usuario','id_usuario','int4');

        $this->captura('fun_responsable', 'varchar');
        $this->captura('desc_depto', 'varchar');
        $this->captura('tieneform500', 'varchar');
        $this->captura('conformidad', 'varchar');
        $this->captura('dias_form_500', 'integer');
        $this->captura('fecha_inicio', 'date');
        $this->captura('fecha_fin', 'date');
        $this->captura('plazo_dias', 'varchar');

        //Ejecutar la instruccion
        $this->armarConsulta();
        //var_dump($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;

    }


}
?>