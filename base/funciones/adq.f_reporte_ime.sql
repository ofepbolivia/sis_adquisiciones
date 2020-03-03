CREATE OR REPLACE FUNCTION adq.f_reporte_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
  /**************************************************************************
   SISTEMA:		Adquisiciones
   FUNCION: 		adq.f_reporte_ime
   DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'adq.f_reporte_ime'
   AUTOR: 		 f.e.a
   FECHA:	        19-02-2018 12:55:30
   COMENTARIOS:
  ***************************************************************************
   HISTORIAL DE MODIFICACIONES:

   DESCRIPCION:
   AUTOR:
   FECHA:
  ***************************************************************************/

  DECLARE

    v_parametros           	record;
    v_id_requerimiento     	integer;
    v_resp		            varchar;
    v_nombre_funcion        text;
    v_registros				record;


  BEGIN

    v_nombre_funcion = 'adq.f_proceso_compra_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    if(p_transaccion='ADQ_USUARIO_GET')then

      begin
		SELECT tu.id_usuario, vf.desc_funcionario1
        INTO v_registros
        FROM segu.tusuario tu
        INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
        INNER JOIN orga.vfuncionario vf on vf.id_funcionario = tf.id_funcionario
        WHERE tu.id_usuario = p_id_usuario ;

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Datos devueltos Exitosamente');
        v_resp = pxp.f_agrega_clave(v_resp,'id_usuario',v_registros.id_usuario::varchar);
         v_resp = pxp.f_agrega_clave(v_resp,'desc_usuario',v_registros.desc_funcionario1::varchar);

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