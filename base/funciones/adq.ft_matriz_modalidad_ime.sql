CREATE OR REPLACE FUNCTION adq.ft_matriz_modalidad_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Adquisiciones
 FUNCION: 		adq.ft_matriz_modalidad_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.tmatriz_modalidad'
 AUTOR: 		 (maylee.perez)
 FECHA:	        22-09-2020 13:33:53
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				22-09-2020 13:33:53								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.tmatriz_modalidad'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_matriz_modalidad	integer;

    v_id_uo_gerencia		integer;
    v_funcionario			integer;

BEGIN

    v_nombre_funcion = 'adq.ft_matriz_modalidad_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_MATRIZ_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		22-09-2020 13:33:53
	***********************************/

	if(p_transaccion='ADQ_MATRIZ_INS')then

        begin

        	IF v_parametros.id_uo = 10445 THEN

            	v_id_uo_gerencia = v_parametros.id_uo;

            ELSE

            	SELECT fc.id_funcionario
                into v_funcionario
                FROM orga.vfuncionario_cargo fc
                WHERE fc.id_uo = v_parametros.id_uo
                and fc.fecha_asignacion  <=  now()
                and (fc.fecha_finalizacion is null or fc.fecha_finalizacion >= now() );

                -- recupera la uo gerencia del funcionario
                v_id_uo_gerencia =   orga.f_get_uo_gerencia_area_ope(NULL, v_funcionario, now()::Date);

                IF v_id_uo_gerencia = -1 THEN
                	raise exception 'No se encuentra su Gerencia correspondiente de la Unidad Responsable';
                END IF;


            END IF;

        	--Sentencia de la insercion
        	insert into adq.tmatriz_modalidad(
			estado_reg,
			referencia,
			tipo_contratacion,
			nacional,
			internacional,
			id_uo,
			nivel_importancia,
			id_cargo,
			contrato_global,
			modalidad_menor,
			modalidad_anpe,
			modalidad_licitacion,
			modalidad_directa,
			modalidad_excepcion,
			modalidad_desastres,
			punto_reorden,
			observaciones,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod,

            resp_proc_contratacion_menor,
            resp_proc_contratacion_anpe,
            resp_proc_contratacion_directa,
            resp_proc_contratacion_licitacion,
            resp_proc_contratacion_excepcion,
            resp_proc_contratacion_desastres,

            flujo_mod_directa,

            id_uo_gerencia,
            flujo_sistema

          	) values(
			'activo',
			'',
			v_parametros.tipo_contratacion,
			v_parametros.nacional,
			v_parametros.internacional,
			v_parametros.id_uo,
			v_parametros.nivel_importancia,
			v_parametros.id_cargo,
			v_parametros.contrato_global,
			v_parametros.modalidad_menor,
			v_parametros.modalidad_anpe,
			v_parametros.modalidad_licitacion,
			v_parametros.modalidad_directa,
			v_parametros.modalidad_excepcion,
			v_parametros.modalidad_desastres,
			v_parametros.punto_reorden,
			v_parametros.observaciones,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null,

			v_parametros.resp_proc_contratacion_menor,
            v_parametros.resp_proc_contratacion_anpe,
            v_parametros.resp_proc_contratacion_directa,
            v_parametros.resp_proc_contratacion_licitacion,
            v_parametros.resp_proc_contratacion_excepcion,
            v_parametros.resp_proc_contratacion_desastres,

            v_parametros.flujo_mod_directa,

            v_id_uo_gerencia,
            v_parametros.flujo_sistema

			)RETURNING id_matriz_modalidad into v_id_matriz_modalidad;


            UPDATE adq.tmatriz_modalidad SET
            referencia = v_id_matriz_modalidad::varchar
            WHERE id_matriz_modalidad = v_id_matriz_modalidad;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Matriz almacenado(a) con exito (id_matriz_modalidad'||v_id_matriz_modalidad||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_matriz_modalidad',v_id_matriz_modalidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_MATRIZ_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		22-09-2020 13:33:53
	***********************************/

	elsif(p_transaccion='ADQ_MATRIZ_MOD')then

		begin

        	IF v_parametros.id_uo = 10445 THEN

            	v_id_uo_gerencia = v_parametros.id_uo;

            ELSE

            	SELECT fc.id_funcionario
                into v_funcionario
                FROM orga.vfuncionario_cargo fc
                WHERE fc.id_uo = v_parametros.id_uo
                and fc.fecha_asignacion  <=  now()
                and (fc.fecha_finalizacion is null or fc.fecha_finalizacion >= now() );

                -- recupera la uo gerencia del funcionario
                v_id_uo_gerencia =   orga.f_get_uo_gerencia_area_ope(NULL, v_funcionario, now()::Date);

                IF v_id_uo_gerencia = -1 THEN
                	raise exception 'No se excuentra su Gerencia correspondiente de la Unidad';
                END IF;


            END IF;


			--Sentencia de la modificacion
			update adq.tmatriz_modalidad set
			referencia = (v_parametros.id_matriz_modalidad)::varchar,
			tipo_contratacion = v_parametros.tipo_contratacion,
			nacional = v_parametros.nacional,
			internacional = v_parametros.internacional,
			id_uo = v_parametros.id_uo,
			nivel_importancia = v_parametros.nivel_importancia,
			id_cargo = v_parametros.id_cargo,
			contrato_global = v_parametros.contrato_global,
			modalidad_menor = v_parametros.modalidad_menor,
			modalidad_anpe = v_parametros.modalidad_anpe,
			modalidad_licitacion = v_parametros.modalidad_licitacion,
			modalidad_directa = v_parametros.modalidad_directa,
			modalidad_excepcion = v_parametros.modalidad_excepcion,
			modalidad_desastres = v_parametros.modalidad_desastres,
			punto_reorden = v_parametros.punto_reorden,
			observaciones = v_parametros.observaciones,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,

            resp_proc_contratacion_menor = v_parametros.resp_proc_contratacion_menor,
            resp_proc_contratacion_anpe = v_parametros.resp_proc_contratacion_anpe,
            resp_proc_contratacion_directa = v_parametros.resp_proc_contratacion_directa,
            resp_proc_contratacion_licitacion = v_parametros.resp_proc_contratacion_licitacion,
            resp_proc_contratacion_excepcion = v_parametros.resp_proc_contratacion_excepcion,
            resp_proc_contratacion_desastres = v_parametros.resp_proc_contratacion_desastres,

            flujo_mod_directa = v_parametros.flujo_mod_directa,

            id_uo_gerencia = v_id_uo_gerencia,
            flujo_sistema = v_parametros.flujo_sistema

			where id_matriz_modalidad=v_parametros.id_matriz_modalidad;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Matriz modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_matriz_modalidad',v_parametros.id_matriz_modalidad::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_MATRIZ_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		22-09-2020 13:33:53
	***********************************/

	elsif(p_transaccion='ADQ_MATRIZ_ELI')then

		begin
			--Sentencia de la eliminacion
			/*delete from adq.tmatriz_modalidad
            where id_matriz_modalidad=v_parametros.id_matriz_modalidad;*/

            UPDATE adq.tmatriz_modalidad SET
            estado_reg = 'inactivo',
            fecha_mod = now(),
            id_usuario_mod = p_id_usuario
            WHERE id_matriz_modalidad = v_parametros.id_matriz_modalidad;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Matriz eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_matriz_modalidad',v_parametros.id_matriz_modalidad::varchar);

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
