CREATE OR REPLACE FUNCTION adq.ft_solicitud_det_lista (
  lista varchar
)
RETURNS varchar AS
$body$
DECLARE
  v_activos 		    varchar[];
  cont 					integer;
  v_valores 			varchar = '';
  v_valor				varchar;
  v_tam					integer;
  v_lista_activos       varchar;
BEGIN

			v_activos = string_to_array(lista,',')::varchar[];
            v_tam = array_length(v_activos,1);
        	if (v_tam>0)then
            	for cont in 1..v_tam loop
                    select af.denominacion
                    into  v_valor
                    from kaf.tactivo_fijo af
                    where af.id_activo_fijo = v_activos[cont]::integer;
                    if (cont < v_tam) then
                    	v_valores = v_valores ||cont||'. '|| v_valor || ',';
                    else
                    	v_valores = v_valores ||cont||'. '|| v_valor;
                    end if;
                end loop;
			end if;

            return v_valores;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;