CREATE OR REPLACE FUNCTION adq.ft_solicitud_modalidad_eli (
  v_id_solicitud integer,
  v_id_usuario integer,
  v_id_solicitud_det integer
)
RETURNS SETOF record AS
$body$
/**************************************************************************
 SISTEMA:		Adquisiciones
 FUNCION: 		adq.ft_solicitud_modalidad
 DESCRIPCION:   verifica solicitud modalidad
 AUTOR: 		maylee.perez
 FECHA:	        14-10-2020
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE
  	v_resp		           	varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
    v_consulta				text;

    v_solicitud_det			record;
    v_id_matriz_modalidad	integer;
    v_modalidades_matriz	record;
    v_id_modalidad_solicitud integer;

    v_desc_ingas			varchar;
    v_total_det				numeric;
    v_codigo_modalidad		varchar;

     --v_id_funcionario_sol	integer;
     v_id_uo  				integer;
     v_solicitud			record;
     va_id_funcionario_gerente  	INTEGER[];
     v_idfun_modalidad		integer;
     v_desc_funcionario		varchar;
     v_nombre_cargo			varchar;
     v_count_concepto_ingas	numeric;
     v_id_matriz_mod		record;
     v_modalidad_solicitud	record;
     v_modalidades_solicitud record;
	 v_modalidad			varchar;
     v_count_modalidad		numeric;
     --v_id_funcionario_aprobador	integer;
     v_solicitud_modalidad	varchar;
     v_solu_modalidades		record;
     v_respuesta_modalidad	varchar;
     v_depto_prioridad		integer;
     v_nom_tipo_contratacion	varchar;
     v_nombre_modalidad		varchar;

BEGIN




  	v_nombre_funcion = 'adq.f_solicitud_modalidad';


      ---------------
      --- obtener el precio_total de la solicitud
      SELECT sum(sd.precio_total)
      into v_total_det
      FROM adq.tsolicitud_det sd
      WHERE sd.id_solicitud = v_id_solicitud
      and sd.estado_reg = 'activo' ;

      v_total_det = v_total_det - v_id_solicitud_det;

      --COMPARACION CON LA TABLA DE MODALIDAD
      SELECT mod.codigo
      into v_codigo_modalidad
      FROM adq.tmodalidades mod
      WHERE mod.condicion_menor <= v_total_det
      and mod.condicion_mayor >= v_total_det;


      FOR v_modalidad_solicitud in(SELECT ms.id_modalidad_solicitud
                                    FROM adq.tmodalidad_solicitud ms
                                    WHERE ms.id_solicitud =  v_id_solicitud
                                    and ms.estado_reg = 'activo'
                                      )LOOP

                    SELECT ms.modalidad_menor,
                    	   ms.modalidad_anpe,
                           ms.modalidad_directa,
                            ms.modalidad_licitacion,
                            ms.modalidad_desastres,
                            ms.modalidad_excepcion,
                            ms.id_concepto_ingas,
                            ms.id_matriz_modalidad

                    INTO v_modalidades_solicitud
                    FROM adq.tmodalidad_solicitud ms
                    WHERE ms.id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud
                    and ms.estado_reg = 'activo';

                    --nombre del concepto de gasto
                    SELECT cin.desc_ingas
                    into v_desc_ingas
                    FROM param.tconcepto_ingas cin
                    WHERE cin.id_concepto_ingas = v_modalidades_solicitud.id_concepto_ingas;

                    --nombre de la matriz modalidad
                    SELECT mm.referencia ||'-'||mm.tipo_contratacion
                    into v_nom_tipo_contratacion
                    FROM adq.tmatriz_modalidad mm
                    WHERE mm.id_matriz_modalidad = v_modalidades_solicitud.id_matriz_modalidad;

                    --nombre de la matriz modalidad
                    SELECT mod.nombre_modalidad
                    into v_nombre_modalidad
                    FROM adq.tmodalidades mod
                    WHERE mod.codigo = v_codigo_modalidad;

                    	IF (v_codigo_modalidad ='mod_menor') THEN
                        	v_modalidad = v_modalidades_solicitud.modalidad_menor;

                            IF v_modalidad = 'si' THEN
                                UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'SI'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;
                            ELSE
                            	UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'NO'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;

                            	raise exception 'No se encuentra parametrizado para la modalidad %, el Concepto de Gasto < % > en el Tipo Contratación < % > de la Matriz Tipo Contratación-Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre). ',upper(v_nombre_modalidad), v_desc_ingas, v_nom_tipo_contratacion;

                            END IF;

                        ELSIF (v_codigo_modalidad ='mod_anpe') THEN
                        	v_modalidad = v_modalidades_solicitud.modalidad_anpe;

                            IF v_modalidad = 'si' THEN
                                UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'SI'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;
                            ELSE
                            	UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'NO'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;

                            	raise exception 'No se encuentra parametrizado para la modalidad %, el Concepto de Gasto < % > en el Tipo Contratación < % > de la Matriz Tipo Contratación-Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre). ',upper(v_nombre_modalidad), v_desc_ingas, v_nom_tipo_contratacion;

                            END IF;

                        ELSIF (v_codigo_modalidad ='mod_licitacion') THEN
                        	v_modalidad = v_modalidades_solicitud.modalidad_directa;

                            IF v_modalidad = 'si' THEN
                                UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'SI'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;
                            ELSE
                            	UPDATE adq.tmodalidad_solicitud set
                                calificacion = 'NO'
                                WHERE id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud;

                            	raise exception 'No se encuentra parametrizado para la modalidad %, el Concepto de Gasto < % > en el Tipo Contratación < % > de la Matriz Tipo Contratación-Aprobador. Comunicarse con el Departamento de Adquisiciones (Marcelo Vidaurre). ',upper(v_nombre_modalidad), v_desc_ingas, v_nom_tipo_contratacion;

                            END IF;

                        /*ELSIF (v_modalidades_solicitud.modalidad_licitacion ='si') THEN
                        	v_modalidad = 'mod_';

                        ELSIF (v_modalidades_solicitud.modalidad_desastres ='si') THEN
                        	v_modalidad = 'mod_';

                        ELSIF (v_modalidades_solicitud.modalidad_excepcion ='si') THEN
                        	v_modalidad = 'mod_';*/

                        END IF;


                    SELECT count(ms.id_modalidad_solicitud)
                    INTO v_count_modalidad
                    FROM adq.tmodalidad_solicitud ms
                    WHERE ms.id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud
                    and ms.id_solicitud = v_id_solicitud
                    and ms.calificacion = 'SI';

                    IF (v_count_modalidad >=2) THEN

                    	raise EXCEPTION 'son mas de dos calificados como si';

                    ELSE

                    	SELECT ms.id_funcionario_aprobador,
                        		ms.modalidad_menor,
                                ms.modalidad_anpe,
                                ms.modalidad_directa,
                                ms.modalidad_licitacion,
                                ms.modalidad_desastres,
                                ms.modalidad_excepcion

                        INTO v_solu_modalidades
                        FROM adq.tmodalidad_solicitud ms
                        WHERE ms.id_modalidad_solicitud = v_modalidad_solicitud.id_modalidad_solicitud
                        and ms.id_solicitud = v_id_solicitud
                        and ms.calificacion = 'SI';

                        IF (v_solu_modalidades.modalidad_menor = 'si' and v_codigo_modalidad = 'mod_menor') THEN
                        	v_respuesta_modalidad = 'mod_menor';
                        ELSIF (v_solu_modalidades.modalidad_anpe = 'si' and v_codigo_modalidad = 'mod_anpe') THEN
                        	v_respuesta_modalidad = 'mod_anpe';
                        ELSIF (v_solu_modalidades.modalidad_licitacion = 'si' and v_codigo_modalidad = 'mod_licitacion') THEN
                        	v_respuesta_modalidad = 'mod_licitacion';
                        END IF;

                    	UPDATE adq.tsolicitud SET
                        tipo_modalidad = v_respuesta_modalidad
                        WHERE id_solicitud = v_id_solicitud;

                    END IF;




      END LOOP;


      --raise exception 'llegan %',v_resp;

          --Devuelve la respuesta
          return ;

        raise notice '%',v_resp;

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
COST 100 ROWS 1000;