<?php
/**
 * @package pXP
 * @file gen-MODSolicitudModalidades.php
 * @author  (maylee.perez)
 * @date 28-09-2020 12:12:51
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODSolicitudModalidades extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function listarSolicitudModalidades()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'adq.f_solicitud_modalidades_sel';
        $this->transaccion = 'ADQ_SOLMODAL_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion


        $this->setParametro('id_funcionario_usu', 'id_funcionario_usu', 'int4');
        $this->setParametro('tipo_interfaz', 'tipo_interfaz', 'varchar');
        $this->setParametro('historico', 'historico', 'varchar');


        //Definicion de la lista del resultado del query
        $this->captura('id_solicitud', 'int4');
        $this->captura('estado_reg', 'varchar');
        $this->captura('id_solicitud_ext', 'int4');
        $this->captura('presu_revertido', 'varchar');
        $this->captura('fecha_apro', 'date');
        $this->captura('estado', 'varchar');
        $this->captura('id_funcionario_aprobador', 'int4');
        $this->captura('id_moneda', 'int4');
        $this->captura('id_gestion', 'int4');
        $this->captura('tipo', 'varchar');
        $this->captura('num_tramite', 'varchar');
        $this->captura('justificacion', 'text');
        $this->captura('id_depto', 'int4');
        $this->captura('lugar_entrega', 'varchar');
        $this->captura('extendida', 'varchar');

        $this->captura('posibles_proveedores', 'text');
        $this->captura('id_proceso_wf', 'int4');
        $this->captura('comite_calificacion', 'text');
        $this->captura('id_categoria_compra', 'int4');
        $this->captura('id_funcionario', 'int4');
        $this->captura('id_estado_wf', 'int4');
        $this->captura('fecha_soli', 'date');
        $this->captura('fecha_reg', 'timestamp');
        $this->captura('id_usuario_reg', 'int4');
        $this->captura('fecha_mod', 'timestamp');
        $this->captura('id_usuario_mod', 'int4');
        $this->captura('usr_reg', 'varchar');
        $this->captura('usr_mod', 'varchar');

        $this->captura('id_uo', 'integer');

        $this->captura('desc_funcionario', 'text');
        $this->captura('desc_funcionario_apro', 'text');
        $this->captura('desc_uo', 'text');
        $this->captura('desc_gestion', 'integer');
        $this->captura('desc_moneda', 'varchar');
        $this->captura('desc_depto', 'varchar');
        $this->captura('desc_proceso_macro', 'varchar');
        $this->captura('desc_categoria_compra', 'varchar');
        $this->captura('id_proceso_macro', 'integer');
        $this->captura('numero', 'varchar');
        $this->captura('desc_funcionario_rpc', 'text');
        $this->captura('obs', 'text');
        $this->captura('instruc_rpc', 'varchar');
        $this->captura('desc_proveedor', 'varchar');
        $this->captura('id_proveedor', 'integer');
        $this->captura('id_funcionario_supervisor', 'integer');
        $this->captura('desc_funcionario_supervisor', 'text');
        $this->captura('ai_habilitado', 'varchar');
        $this->captura('id_cargo_rpc', 'integer');
        $this->captura('id_cargo_rpc_ai', 'integer');
        $this->captura('tipo_concepto', 'varchar');
        $this->captura('revisado_asistente', 'varchar');
        $this->captura('fecha_inicio', 'date');
        $this->captura('dias_plazo_entrega', 'integer');
        $this->captura('obs_presupuestos', 'varchar');
        $this->captura('precontrato', 'varchar');
        $this->captura('update_enable', 'varchar');
        $this->captura('codigo_poa', 'varchar');
        $this->captura('obs_poa', 'varchar');
        $this->captura('contador_estados', 'bigint');

        $this->captura('nro_po', 'varchar');
        $this->captura('fecha_po', 'date');

        $this->captura('importe_total', 'numeric');
        $this->captura('prioridad', 'varchar');
        $this->captura('id_prioridad', 'integer');
        $this->captura('list_proceso', 'integer[]');

        $this->captura('cuce', 'varchar');
        $this->captura('fecha_conclusion', 'date');
        $this->captura('presupuesto_aprobado', 'varchar');

        $this->captura('tipo_modalidad', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarSolicitudCompletaMenor()
    {

        //Abre conexion con PDO
        $cone = new conexion();
        $link = $cone->conectarpdo();
        $copiado = false;
        try {
            $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $link->beginTransaction();

            /////////////////////////
            //  inserta cabecera de la solicitud de compra
            ///////////////////////

            //Definicion de variables para ejecucion del procedimiento
            $this->procedimiento = 'adq.f_solicitud_modalidades_ime';
            $this->transaccion = 'ADQ_SOLMODAL_INS';
            $this->tipo_procedimiento = 'IME';

            //Define los parametros para la funcion
            $this->setParametro('estado_reg', 'estado_reg', 'varchar');
            $this->setParametro('id_solicitud_ext', 'id_solicitud_ext', 'int4');
            $this->setParametro('presu_revertido', 'presu_revertido', 'varchar');
            $this->setParametro('fecha_apro', 'fecha_apro', 'date');
            $this->setParametro('estado', 'estado', 'varchar');
            $this->setParametro('id_moneda', 'id_moneda', 'int4');
            $this->setParametro('id_gestion', 'id_gestion', 'int4');
            $this->setParametro('tipo', 'tipo', 'varchar');
            $this->setParametro('num_tramite', 'num_tramite', 'varchar');
            $this->setParametro('justificacion', 'justificacion', 'text');
            $this->setParametro('id_depto', 'id_depto', 'int4');
            $this->setParametro('lugar_entrega', 'lugar_entrega', 'varchar');
            $this->setParametro('extendida', 'extendida', 'varchar');
            $this->setParametro('numero', 'numero', 'varchar');
            $this->setParametro('posibles_proveedores', 'posibles_proveedores', 'text');
            $this->setParametro('id_proceso_wf', 'id_proceso_wf', 'int4');
            $this->setParametro('comite_calificacion', 'comite_calificacion', 'text');
            $this->setParametro('id_categoria_compra', 'id_categoria_compra', 'int4');
            $this->setParametro('id_funcionario', 'id_funcionario', 'int4');
            $this->setParametro('id_estado_wf', 'id_estado_wf', 'int4');
            $this->setParametro('fecha_soli', 'fecha_soli', 'date');
            $this->setParametro('id_proceso_macro', 'id_proceso_macro', 'int4');
            $this->setParametro('id_proveedor', 'id_proveedor', 'int4');
            $this->setParametro('tipo_concepto', 'tipo_concepto', 'varchar');
            $this->setParametro('fecha_inicio', 'fecha_inicio', 'date');
            $this->setParametro('dias_plazo_entrega', 'dias_plazo_entrega', 'integer');
            $this->setParametro('precontrato', 'precontrato', 'varchar');
            $this->setParametro('correo_proveedor', 'correo_proveedor', 'varchar');

            $this->setParametro('nro_po', 'nro_po', 'varchar');
            $this->setParametro('fecha_po', 'fecha_po', 'date');

            $this->setParametro('prioridad', 'prioridad', 'integer');

            $this->setParametro('tipo_modalidad', 'tipo_modalidad', 'varchar');

            //Ejecuta la instruccion
            $this->armarConsulta();
            $stmt = $link->prepare($this->consulta);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            //recupera parametros devuelto depues de insertar ... (id_solicitud)
            $resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
            if ($resp_procedimiento['tipo_respuesta'] == 'ERROR') {
                throw new Exception("Error al ejecutar en la bd", 3);
            }

            $respuesta = $resp_procedimiento['datos'];

            $id_solicitud = $respuesta['id_solicitud'];

            //////////////////////////////////////////////
            //inserta detalle de la solicitud de compra
            /////////////////////////////////////////////


            //decodifica JSON  de detalles
            $json_detalle = $this->aParam->_json_decode($this->aParam->getParametro('json_new_records'));

            //var_dump($json_detalle)	;
            foreach ($json_detalle as $f) {

                $this->resetParametros();
                //Definicion de variables para ejecucion del procedimiento
                $this->procedimiento = 'adq.f_solicitud_det_ime';
                $this->transaccion = 'ADQ_SOLD_INS';
                $this->tipo_procedimiento = 'IME';
                //modifica los valores de las variables que mandaremos
                $this->arreglo['id_centro_costo'] = $f['id_centro_costo'];
                $this->arreglo['descripcion'] = $f['descripcion'];
                $this->arreglo['precio_unitario'] = $f['precio_unitario'];
                $this->arreglo['id_solicitud'] = $id_solicitud;
                $this->arreglo['id_orden_trabajo'] = $f['id_orden_trabajo'];
                $this->arreglo['id_concepto_ingas'] = $f['id_concepto_ingas'];
                $this->arreglo['precio_total'] = $f['precio_total'];
                $this->arreglo['cantidad_sol'] = $f['cantidad_sol'];
                $this->arreglo['precio_ga'] = $f['precio_ga'];
                $this->arreglo['precio_sg'] = $f['precio_sg'];

                $this->arreglo['id_activo_fijo'] = $f['id_activo_fijo'];
                $this->arreglo['codigo_act'] = $f['codigo_act'];
                $this->arreglo['fecha_ini_act'] = $f['fecha_ini_act'];
                $this->arreglo['fecha_fin_act'] = $f['fecha_fin_act'];


                //Define los parametros para la funcion
                $this->setParametro('id_centro_costo', 'id_centro_costo', 'int4');
                $this->setParametro('descripcion', 'descripcion', 'text');
                $this->setParametro('precio_unitario', 'precio_unitario', 'numeric');
                $this->setParametro('id_solicitud', 'id_solicitud', 'int4');
                $this->setParametro('id_orden_trabajo', 'id_orden_trabajo', 'int4');
                $this->setParametro('id_concepto_ingas', 'id_concepto_ingas', 'int4');
                $this->setParametro('precio_total', 'precio_total', 'numeric');
                $this->setParametro('cantidad_sol', 'cantidad_sol', 'int4');
                $this->setParametro('precio_ga', 'precio_ga', 'numeric');
                $this->setParametro('precio_sg', 'precio_sg', 'numeric');

                $this->setParametro('id_activo_fijo', 'id_activo_fijo', 'varchar');
                $this->setParametro('codigo_act', 'codigo_act', 'varchar');
                $this->setParametro('fecha_ini_act', 'fecha_ini_act', 'date');
                $this->setParametro('fecha_fin_act', 'fecha_fin_act', 'date');


                //Ejecuta la instruccion
                $this->armarConsulta();
                $stmt = $link->prepare($this->consulta);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                //recupera parametros devuelto depues de insertar ... (id_solicitud)
                $resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
                if ($resp_procedimiento['tipo_respuesta'] == 'ERROR') {
                    throw new Exception("Error al insertar detalle  en la bd", 3);
                }


            }


            //si todo va bien confirmamos y regresamos el resultado
            $link->commit();
            $this->respuesta = new Mensaje();
            $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'], $this->nombre_archivo, $resp_procedimiento['mensaje'], $resp_procedimiento['mensaje_tec'], 'base', $this->procedimiento, $this->transaccion, $this->tipo_procedimiento, $this->consulta);
            $this->respuesta->setDatos($respuesta);
        } catch (Exception $e) {
            $link->rollBack();
            $this->respuesta = new Mensaje();
            if ($e->getCode() == 3) {//es un error de un procedimiento almacenado de pxp
                $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'], $this->nombre_archivo, $resp_procedimiento['mensaje'], $resp_procedimiento['mensaje_tec'], 'base', $this->procedimiento, $this->transaccion, $this->tipo_procedimiento, $this->consulta);
            } else if ($e->getCode() == 2) {//es un error en bd de una consulta
                $this->respuesta->setMensaje('ERROR', $this->nombre_archivo, $e->getMessage(), $e->getMessage(), 'modelo', '', '', '', '');
            } else {//es un error lanzado con throw exception
                throw new Exception($e->getMessage(), 2);
            }

        }

        return $this->respuesta;
    }


}

?>