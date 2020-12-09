CREATE OR REPLACE FUNCTION adq.ft_tresoluciones_info_pre_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Adquisiciones
 FUNCION: 		adq.ft_tresoluciones_info_pre_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'adq.ttresoluciones_info_pre'
 AUTOR: 		 (maylee.perez)
 FECHA:	        07-12-2020 19:01:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				07-12-2020 19:01:00								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'adq.ttresoluciones_info_pre'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'adq.ft_tresoluciones_info_pre_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_REINPRE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		maylee.perez
 	#FECHA:		07-12-2020 19:01:00
	***********************************/

	if(p_transaccion='ADQ_REINPRE_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						reinpre.id_resoluciones_info_pre,
						reinpre.estado_reg,
						reinpre.nro_directorio,
						reinpre.nro_nota,
						reinpre.nro_nota2,
						reinpre.observaciones,
						reinpre.id_gestion,
						reinpre.gestion,
						reinpre.id_usuario_reg,
						reinpre.fecha_reg,
						reinpre.id_usuario_ai,
						reinpre.usuario_ai,
						reinpre.id_usuario_mod,
						reinpre.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        to_char(reinpre.fecha_certificacion	,''DD/MM/YYYY'')::varchar as fecha_certificacion

						from adq.ttresoluciones_info_pre reinpre
						inner join segu.tusuario usu1 on usu1.id_usuario = reinpre.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = reinpre.id_usuario_mod
				        where reinpre.estado_reg = ''activo'' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_REINPRE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		07-12-2020 19:01:00
	***********************************/

	elsif(p_transaccion='ADQ_REINPRE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_resoluciones_info_pre)
					    from adq.ttresoluciones_info_pre reinpre
					    inner join segu.tusuario usu1 on usu1.id_usuario = reinpre.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = reinpre.id_usuario_mod
					    where reinpre.estado_reg = ''activo'' and ';

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
