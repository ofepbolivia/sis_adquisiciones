CREATE OR REPLACE FUNCTION adq.f_actualizar_material (
)
RETURNS boolean AS
$body$
DECLARE

	v_nombre_funcion   	text;
    v_resp    			varchar;
    v_mensaje varchar;

    v_registros record;



BEGIN

	   v_nombre_funcion = 'adq.f_actualizar_material';


       /*for v_registros in select ts.nro_tramite, ts.mel from mat.tsolicitud ts where ts.mel != 'No Aplica' and (ts.fecha_solicitud between '1/1/2018'::date and '3/7/2018'::date)   loop
       	update adq.tsolicitud  set
        	prioridad = v_registros.mel
        where num_tramite = v_registros.nro_tramite;
       end loop;*/

       for v_registros in select ts.mel, ts.nro_tramite from mat.tsolicitud ts where ts.fecha_solicitud between '1/1/2017'::date and '5/7/2018'::date loop
       	/*update adq.tproceso_compra  set
        	prioridad = v_registros.prioridad
        where id_solicitud = v_registros.id_solicitud;*/
        update adq.tsolicitud  set
        	prioridad = case when v_registros.mel = 'AOG' then 383
            				 when v_registros.mel = 'A' then 384
                             when v_registros.mel = 'B' then 385
                             when v_registros.mel = 'C' then 386
                             when v_registros.mel = 'No Aplica' then 387 else null end
        where num_tramite = v_registros.nro_tramite;
       end loop;


RETURN   TRUE;



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