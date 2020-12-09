CREATE OR REPLACE FUNCTION adq.ft_tresoluciones_info_pre_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Adquisiciones
 FUNCION: 		adq.ft_tresoluciones_info_pre_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.ttresoluciones_info_pre'
 AUTOR: 		 (maylee.perez)
 FECHA:	        07-12-2020 19:01:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				07-12-2020 19:01:00								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.ttresoluciones_info_pre'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_resoluciones_info_pre	integer;
    v_nom_gestion			integer;

BEGIN

    v_nombre_funcion = 'adq.ft_tresoluciones_info_pre_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_REINPRE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		07-12-2020 19:01:00
	***********************************/

	if(p_transaccion='ADQ_REINPRE_INS')then

        begin
        	SELECT ges.gestion
            INTO v_nom_gestion
            FROM param.tgestion ges
            WHERE ges.id_gestion = v_parametros.id_gestion;

        	--Sentencia de la insercion
        	insert into adq.ttresoluciones_info_pre(
			estado_reg,
			nro_directorio,
			nro_nota,
			nro_nota2,
			observaciones,
			id_gestion,
			gestion,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,
            fecha_certificacion

          	) values(
			'activo',
			v_parametros.nro_directorio,
			v_parametros.nro_nota,
			v_parametros.nro_nota2,
			v_parametros.observaciones,
			v_parametros.id_gestion,
			v_nom_gestion,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,
            v_parametros.fecha_certificacion

			)RETURNING id_resoluciones_info_pre into v_id_resoluciones_info_pre;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Resolucion  información presupuestaria almacenado(a) con exito (id_resoluciones_info_pre'||v_id_resoluciones_info_pre||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_resoluciones_info_pre',v_id_resoluciones_info_pre::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_REINPRE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		07-12-2020 19:01:00
	***********************************/

	elsif(p_transaccion='ADQ_REINPRE_MOD')then

		begin

        	SELECT ges.gestion
            INTO v_nom_gestion
            FROM param.tgestion ges
            WHERE ges.id_gestion = v_parametros.id_gestion;

			--Sentencia de la modificacion
			update adq.ttresoluciones_info_pre set
			nro_directorio = v_parametros.nro_directorio,
			nro_nota = v_parametros.nro_nota,
			nro_nota2 = v_parametros.nro_nota2,
			observaciones = v_parametros.observaciones,
			id_gestion = v_parametros.id_gestion,
			gestion = v_nom_gestion,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            fecha_certificacion = v_parametros.fecha_certificacion
			where id_resoluciones_info_pre=v_parametros.id_resoluciones_info_pre;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Resolucion  información presupuestaria modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_resoluciones_info_pre',v_parametros.id_resoluciones_info_pre::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_REINPRE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		07-12-2020 19:01:00
	***********************************/

	elsif(p_transaccion='ADQ_REINPRE_ELI')then

		begin
			--Sentencia de la eliminacion
			/*delete from adq.ttresoluciones_info_pre
            where id_resoluciones_info_pre=v_parametros.id_resoluciones_info_pre;*/

            UPDATE adq.ttresoluciones_info_pre SET
            id_usuario_mod = p_id_usuario,
            fecha_mod = now(),
            estado_reg = 'inactivo'
        	WHERE id_resoluciones_info_pre = v_parametros.id_resoluciones_info_pre;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Resolucion  información presupuestaria eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_resoluciones_info_pre',v_parametros.id_resoluciones_info_pre::varchar);

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
