CREATE OR REPLACE FUNCTION adq.ft_matriz_concepto_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Adquisiciones
 FUNCION: 		adq.ft_matriz_concepto_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.tmatriz_concepto'
 AUTOR: 		 (maylee.perez)
 FECHA:	        22-09-2020 17:47:40
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				22-09-2020 17:47:40								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.tmatriz_concepto'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_matriz_concepto	integer;

BEGIN

    v_nombre_funcion = 'adq.ft_matriz_concepto_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_MACONCEP_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		22-09-2020 17:47:40
	***********************************/

	if(p_transaccion='ADQ_MACONCEP_INS')then

        begin
        	--Sentencia de la insercion
        	insert into adq.tmatriz_concepto(
			estado_reg,
			id_matriz_modalidad,
			id_concepto_ingas,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.id_matriz_modalidad,
			v_parametros.id_concepto_ingas,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_matriz_concepto into v_id_matriz_concepto;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Matriz - conceptos de gasto almacenado(a) con exito (id_matriz_concepto'||v_id_matriz_concepto||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_matriz_concepto',v_id_matriz_concepto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_MACONCEP_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		22-09-2020 17:47:40
	***********************************/

	elsif(p_transaccion='ADQ_MACONCEP_MOD')then

		begin
			--Sentencia de la modificacion
			update adq.tmatriz_concepto set
			id_matriz_modalidad = v_parametros.id_matriz_modalidad,
			id_concepto_ingas = v_parametros.id_concepto_ingas,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_matriz_concepto=v_parametros.id_matriz_concepto;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Matriz - conceptos de gasto modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_matriz_concepto',v_parametros.id_matriz_concepto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_MACONCEP_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		22-09-2020 17:47:40
	***********************************/

	elsif(p_transaccion='ADQ_MACONCEP_ELI')then

		begin
			--Sentencia de la eliminacion
			/*delete from adq.tmatriz_concepto
            where id_matriz_concepto=v_parametros.id_matriz_concepto;*/

            UPDATE adq.tmatriz_concepto SET
            estado_reg = 'inactivo'
            WHERE v_parametros.id_matriz_concepto;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Matriz - conceptos de gasto eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_matriz_concepto',v_parametros.id_matriz_concepto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

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
