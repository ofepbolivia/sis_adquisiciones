CREATE OR REPLACE FUNCTION adq.ft_matriz_concepto_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Adquisiciones
 FUNCION: 		adq.ft_matriz_concepto_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'adq.tmatriz_concepto'
 AUTOR: 		 (maylee.perez)
 FECHA:	        22-09-2020 17:47:40
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				22-09-2020 17:47:40								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'adq.tmatriz_concepto'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'adq.ft_matriz_concepto_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_MACONCEP_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		maylee.perez
 	#FECHA:		22-09-2020 17:47:40
	***********************************/

	if(p_transaccion='ADQ_MACONCEP_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='select
						maconcep.id_matriz_concepto,
						maconcep.estado_reg,
						maconcep.id_matriz_modalidad,
						maconcep.id_concepto_ingas,
						maconcep.id_usuario_reg,
						maconcep.fecha_reg,
						maconcep.id_usuario_ai,
						maconcep.usuario_ai,
						maconcep.id_usuario_mod,
						maconcep.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        ci.desc_ingas

						from adq.tmatriz_concepto maconcep
						inner join segu.tusuario usu1 on usu1.id_usuario = maconcep.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = maconcep.id_usuario_mod
                        inner join param.tconcepto_ingas ci on ci.id_concepto_ingas = maconcep.id_concepto_ingas
				        where maconcep.estado_reg =''activo'' and  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

             if (pxp.f_existe_parametro(p_tabla,'id_matriz_modalidad')) then
                v_consulta:= v_consulta || ' and maconcep.id_matriz_modalidad='||v_parametros.id_matriz_modalidad;
            end if;

			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_MACONCEP_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		22-09-2020 17:47:40
	***********************************/

	elsif(p_transaccion='ADQ_MACONCEP_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_matriz_concepto)
					    from adq.tmatriz_concepto maconcep
					    inner join segu.tusuario usu1 on usu1.id_usuario = maconcep.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = maconcep.id_usuario_mod
						inner join param.tconcepto_ingas ci on ci.id_concepto_ingas = maconcep.id_concepto_ingas
					    where maconcep.estado_reg =''activo'' and  ';

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
