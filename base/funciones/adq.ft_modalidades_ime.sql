CREATE OR REPLACE FUNCTION adq.ft_modalidades_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Adquisiciones
 FUNCION: 		adq.ft_modalidades_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.tmodalidades'
 AUTOR: 		 (maylee.perez)
 FECHA:	        15-10-2020 15:31:50
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				15-10-2020 15:31:50								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.tmodalidades'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_modalidad	integer;

BEGIN

    v_nombre_funcion = 'adq.ft_modalidades_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_MODALI_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		15-10-2020 15:31:50
	***********************************/

	if(p_transaccion='ADQ_MODALI_INS')then

        begin
        	--Sentencia de la insercion
        	insert into adq.tmodalidades(
			estado_reg,
			codigo,
			nombre_modalidad,
			condicion_menor,
			condicion_mayor,
			observaciones,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.codigo,
			v_parametros.nombre_modalidad,
			v_parametros.condicion_menor,
			v_parametros.condicion_mayor,
			v_parametros.observaciones,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null



			)RETURNING id_modalidad into v_id_modalidad;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Modalidades almacenado(a) con exito (id_modalidad'||v_id_modalidad||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_modalidad',v_id_modalidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_MODALI_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		15-10-2020 15:31:50
	***********************************/

	elsif(p_transaccion='ADQ_MODALI_MOD')then

		begin
			--Sentencia de la modificacion
			update adq.tmodalidades set
			codigo = v_parametros.codigo,
			nombre_modalidad = v_parametros.nombre_modalidad,
			condicion_menor = v_parametros.condicion_menor,
			condicion_mayor = v_parametros.condicion_mayor,
			observaciones = v_parametros.observaciones,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_modalidad=v_parametros.id_modalidad;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Modalidades modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_modalidad',v_parametros.id_modalidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_MODALI_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		15-10-2020 15:31:50
	***********************************/

	elsif(p_transaccion='ADQ_MODALI_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from adq.tmodalidades
            where id_modalidad=v_parametros.id_modalidad;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Modalidades eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_modalidad',v_parametros.id_modalidad::varchar);

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