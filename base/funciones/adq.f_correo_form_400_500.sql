CREATE OR REPLACE FUNCTION adq.f_correo_form_400_500 (
  p_id_cotizacion integer,
  p_id_usuario integer,
  p_formulario integer
)
RETURNS boolean AS
$body$
/**************************************************************************
 SISTEMA ADQUISICIONES
***************************************************************************
 SCRIPT: 		adq.f_correo_form_400_500
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
	v_resp            		varchar;
    v_nombre_funcion  		varchar;
    v_id_alarma   			integer;
    v_desc_persona			record;
    v_descripcion 			varchar;
    v_registros_auxiliar	record;
    v_cont					integer;

BEGIN
	v_nombre_funcion ='adq.f_correo_form_400_500';

  	--Preparamos la alarma para enviar al funcionario auxiliar de adquisiciones

    select
    	tpc.num_tramite,
     	tpc.id_usuario_auxiliar,
     	vf.desc_funcionario1,
     	vf.email_empresa,
        tc.fecha_adju,
        vf.id_funcionario,
        tc.id_proceso_wf
  	into
      	v_registros_auxiliar
    from  adq.tcotizacion tc
    inner join adq.tproceso_compra tpc on tpc.id_proceso_compra = tc.id_proceso_compra
    inner join segu.tusuario tu on tu.id_usuario = tpc.id_usuario_auxiliar
    inner join orga.tfuncionario tfun on tfun.id_persona = tu.id_persona
    INNER JOIN orga.vfuncionario_persona vf ON vf.id_funcionario = tfun.id_funcionario
    where tc.id_cotizacion = p_id_cotizacion;

    if(p_formulario = 400)then

      select count(ta.id_alarma)
      into v_cont
      from param.talarma ta
      where ta.id_proceso_wf = v_registros_auxiliar.id_proceso_wf and ta.fecha = current_date;
      if v_cont = 0 then

        v_descripcion =  'Estimad@ '|| v_registros_auxiliar.desc_funcionario1||':<br>'||
        'el tramite # '||v_registros_auxiliar.num_tramite||'<br>esta pendiente de adjuntar su respectivo formulario 400,<br>'||
        'recordarte que en fecha '||v_registros_auxiliar||' se aprobo la orden de compra.';

        --preparamos el correo en bandeja para ser enviado.
        v_id_alarma :=  param.f_inserta_alarma(
                                              v_registros_auxiliar.id_funcionario,
                                              v_descripcion,
                                              '../../../sis_adquisiciones/vista/cotizacion/CotizacionAdq.php',
                                              now()::date,
                                              'notificacion',
                                              'Ninguna',
                                              v_registros_auxiliar.id_usuario_auxiliar,
                                              'CotizacionAdq',
                                              v_registros_auxiliar.desc_funcionario1,--titulo
                                              '{filtro_directo:{campo:"id_cotizacion",valor:"'||p_id_cotizacion::varchar||'"}}',
                                              NULL::integer,
                                              'Adjuntar FORM. 400 - '||v_registros_auxiliar.num_tramite,
                                              v_registros_auxiliar.email_empresa,
                                              null,
                                              v_registros_auxiliar.id_proceso_wf
                                              );
        end if;
    elsif (p_formulario = 500)then

      select count(ta.id_alarma)
      into v_cont
      from param.talarma ta
      where ta.id_proceso_wf = v_registros_auxiliar.id_proceso_wf and ta.fecha = current_date;
      if v_cont = 0 then
        v_descripcion =  'Estimad@ '|| v_registros_auxiliar.desc_funcionario1||':<br>'||
        'el tramite # '||v_registros_auxiliar.num_tramite||'<br>esta pendiente de adjuntar su respectivo formulario 500,<br>'||
        'recordarte que en fecha '||v_registros_auxiliar||' se aprobo la orden de compra.';

        --preparamos el correo en bandeja para ser enviado.
        v_id_alarma :=  param.f_inserta_alarma(
                                              v_registros_auxiliar.id_funcionario,
                                              v_descripcion,
                                              '../../../sis_adquisiciones/vista/cotizacion/CotizacionAdq.php',
                                              now()::date,
                                              'notificacion',
                                              'Ninguna',
                                              v_registros_auxiliar.id_usuario_auxiliar,
                                              'CotizacionAdq',
                                              v_registros_auxiliar.desc_funcionario1,--titulo
                                              '{filtro_directo:{campo:"id_cotizacion",valor:"'||p_id_cotizacion::varchar||'"}}',
                                              NULL::integer,
                                              'Adjuntar FORM. 500 - '||v_registros_auxiliar.num_tramite,
                                              v_registros_auxiliar.email_empresa,
                                              null,
                                              v_registros_auxiliar.id_proceso_wf
                                              );
        end if;
    end if;

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