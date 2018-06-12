CREATE OR REPLACE FUNCTION adq.f_correo_habilitar_pago (
  p_id_cotizacion integer,
  p_id_usuario integer
)
RETURNS boolean AS
$body$
/**************************************************************************
 SISTEMA ENDESIS - SISTEMA DE ...
***************************************************************************
 SCRIPT: 		adq.f_correo_habilitar_pago
 DESCRIPCIÓN: 	Envia correo al solicitante cuando se habilita pago
 AUTOR: 		Franklin Espinoza Alvarez
 FECHA:			22/2/2018
 COMENTARIOS:
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCIÓN:
 AUTOR:
 FECHA:

***************************************************************************/

-- PARÁMETROS FIJOS


DECLARE
	v_resp            	varchar;
    v_nombre_funcion  	varchar;
    v_id_alarma   		integer;
    v_desc_persona		record;
    v_descripcion 		varchar;
    v_registros_sol		record;

BEGIN
	v_nombre_funcion ='adq.f_correo_habilitar_pago';

  	--Preparamos la alarma para enviar al funcionario Solicitante
    select
     fun.desc_funcionario1,
     ts.num_tramite,
     fun.email_empresa,
     ts.id_funcionario
    into
      v_registros_sol
    from  adq.tcotizacion tc
    inner join adq.tproceso_compra tpc on tpc.id_proceso_compra = tc.id_proceso_compra
    inner join adq.tsolicitud ts  on ts.id_solicitud = tpc.id_solicitud
    INNER JOIN orga.vfuncionario_persona fun ON fun.id_funcionario = ts.id_funcionario
    where tc.id_cotizacion = p_id_cotizacion;

    v_descripcion =  'Estimad@, '|| v_registros_sol.desc_funcionario1||'<br>'||
    'su Solicitud al tramite #'||v_registros_sol.num_tramite||'<br>ha sido habilitado para pago.<br>'||
    'Puede dar seguimiento en la ventana (Obligaciones de pago ADQ).';


    --preparamos el correo en bandeja para ser enviado.
    v_id_alarma :=  param.f_inserta_alarma(
                                          v_registros_sol.id_funcionario,
                                          v_descripcion,
                                          '../../../sis_tesoreria/vista/obligacion_pago/ObligacionPagoAdq.php',
                                          now()::date,
                                          'notificacion',
                                          'Ninguna',
                                          p_id_usuario,
                                          'ObligacionPagoAdq',
                                          v_registros_sol.desc_funcionario1,--titulo
                                          '{filtro_directo:{campo:"id_cotizacion",valor:"'||p_id_cotizacion::varchar||'"}}',
                                          NULL::integer,
                                          ('Pago Habilitado - '||v_registros_sol.num_tramite)::varchar,
                                          v_registros_sol.email_empresa::text
                                          );


    return true;


EXCEPTION

	WHEN OTHERS THEN
			v_resp='';
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
			v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
			raise exception '%',v_resp;


END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;