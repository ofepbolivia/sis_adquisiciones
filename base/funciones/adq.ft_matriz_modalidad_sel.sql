CREATE OR REPLACE FUNCTION adq.ft_matriz_modalidad_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Adquisiciones
 FUNCION: 		adq.ft_matriz_modalidad_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'adq.tmatriz_modalidad'
 AUTOR: 		 (maylee.perez)
 FECHA:	        22-09-2020 13:33:53
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				22-09-2020 13:33:53								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'adq.tmatriz_modalidad'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'adq.ft_matriz_modalidad_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_MATRIZ_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		maylee.perez
 	#FECHA:		22-09-2020 13:33:53
	***********************************/

	if(p_transaccion='ADQ_MATRIZ_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						matriz.id_matriz_modalidad,
						matriz.estado_reg,
						matriz.referencia,
						matriz.tipo_contratacion,
						matriz.nacional,
						matriz.internacional,
						matriz.id_uo,
						matriz.nivel_importancia,
						matriz.id_cargo,
						matriz.contrato_global,
						matriz.modalidad_menor,
						matriz.modalidad_anpe,
						matriz.modalidad_licitacion,
						matriz.modalidad_directa,
						matriz.modalidad_excepcion,
						matriz.modalidad_desastres,
						matriz.punto_reorden,
						matriz.observaciones,
						matriz.id_usuario_reg,
						matriz.fecha_reg,
						matriz.id_usuario_ai,
						matriz.usuario_ai,
						matriz.id_usuario_mod,
						matriz.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        uo.nombre_unidad as nombre_uo,
                        uo.codigo as codigo_uo,
                        car.nombre,
                        (matriz.list_concepto_gasto)::varchar,

                        matriz.resp_proc_contratacion_menor,
                        matriz.resp_proc_contratacion_anpe,
                        matriz.resp_proc_contratacion_directa,
                        matriz.resp_proc_contratacion_licitacion,
                        matriz.resp_proc_contratacion_excepcion,
                        matriz.resp_proc_contratacion_desastres,
                        matriz.flujo_mod_directa,

                        uogeren.nombre_unidad as nombre_gerencia,
                        matriz.flujo_sistema

						from adq.tmatriz_modalidad matriz
						inner join segu.tusuario usu1 on usu1.id_usuario = matriz.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = matriz.id_usuario_mod
                        left join orga.tuo uo on uo.id_uo = matriz.id_uo
                        left join orga.tcargo car on car.id_cargo = matriz.id_cargo

                        left join orga.tuo uogeren on uogeren.id_uo = matriz.id_uo_gerencia

				        where matriz.estado_reg =''activo'' and  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' group by matriz.id_matriz_modalidad,
						matriz.estado_reg,
						matriz.referencia,
						matriz.tipo_contratacion,
						matriz.nacional,
						matriz.internacional,
						matriz.id_uo,
						matriz.nivel_importancia,
						matriz.id_cargo,
						matriz.contrato_global,
						matriz.modalidad_menor,
						matriz.modalidad_anpe,
						matriz.modalidad_licitacion,
						matriz.modalidad_directa,
						matriz.modalidad_excepcion,
						matriz.modalidad_desastres,
						matriz.punto_reorden,
						matriz.observaciones,
						matriz.id_usuario_reg,
						matriz.fecha_reg,
						matriz.id_usuario_ai,
						matriz.usuario_ai,
						matriz.id_usuario_mod,
						matriz.fecha_mod,
						usu1.cuenta ,
						usu2.cuenta,
                        uo.nombre_unidad,
                        uo.codigo,
                        car.nombre,
                        matriz.resp_proc_contratacion_menor,
                        matriz.resp_proc_contratacion_anpe,
                        matriz.resp_proc_contratacion_directa,
                        matriz.resp_proc_contratacion_licitacion,
                        matriz.resp_proc_contratacion_excepcion,
                        matriz.resp_proc_contratacion_desastres,
                        matriz.flujo_mod_directa,
                        uogeren.nombre_unidad,
                        matriz.flujo_sistema  '||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_MATRIZ_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		22-09-2020 13:33:53
	***********************************/

	elsif(p_transaccion='ADQ_MATRIZ_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(matriz.id_matriz_modalidad)
					    from adq.tmatriz_modalidad matriz
					    inner join segu.tusuario usu1 on usu1.id_usuario = matriz.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = matriz.id_usuario_mod
                        left join orga.tuo uo on uo.id_uo = matriz.id_uo
                        left join orga.tcargo car on car.id_cargo = matriz.id_cargo

                        left join orga.tuo uogeren on uogeren.id_uo = matriz.id_uo_gerencia


					    where matriz.estado_reg =''activo'' and  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

	else

		raise exception 'Transaccion inexistente';

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
