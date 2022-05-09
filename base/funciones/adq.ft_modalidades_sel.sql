CREATE OR REPLACE FUNCTION adq.ft_modalidades_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Adquisiciones
 FUNCION: 		adq.ft_modalidades_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'adq.tmodalidades'
 AUTOR: 		 (maylee.perez)
 FECHA:	        15-10-2020 15:31:50
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				15-10-2020 15:31:50								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'adq.tmodalidades'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'adq.ft_modalidades_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'ADQ_MODALI_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		maylee.perez
 	#FECHA:		15-10-2020 15:31:50
	***********************************/

	if(p_transaccion='ADQ_MODALI_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						modali.id_modalidad,
						modali.estado_reg,
						modali.codigo,
						modali.nombre_modalidad,
						modali.condicion_menor,
						modali.condicion_mayor,
						modali.observaciones,
						modali.id_usuario_reg,
						modali.fecha_reg,
						modali.id_usuario_ai,
						modali.usuario_ai,
						modali.id_usuario_mod,
						modali.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        modali.con_concepto

						from adq.tmodalidades modali
						inner join segu.tusuario usu1 on usu1.id_usuario = modali.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = modali.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'ADQ_MODALI_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		maylee.perez
 	#FECHA:		15-10-2020 15:31:50
	***********************************/

	elsif(p_transaccion='ADQ_MODALI_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_modalidad)
					    from adq.tmodalidades modali
					    inner join segu.tusuario usu1 on usu1.id_usuario = modali.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = modali.id_usuario_mod
					    where ';

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
