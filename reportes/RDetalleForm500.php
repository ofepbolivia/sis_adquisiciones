<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';

class RDetalleForm500 extends ReportePDF
{
    var $datos;

    function setDatos($datos) {
        $this->datos = $datos;
    }

    function Header() {
        $this->Ln(3);
        //formato de fecha

        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 16,5,40,20);
        $this->ln(5);


        $this->SetFont('','B',12);
        $this->Cell(0,5,"DOCUMENTOS PENDIENTES DE FORM. 500",0,1,'C');

        $this->Ln(2);



    }


    function generarReporte(){

        $this->AddPage();
        $this->SetMargins(20, 35, 15);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $tbl = '<table border="0" style="font-size: 8pt;"> 
                <tr>
                <td width="15%"><b>FUNCIONARIO RESP.: </b></td>
                <td width="35%"> '.$this->datos[0]['fun_responsable'].'</td>
                <td width="10%"><b>UNIDAD RESP.: </b></td>
                <td width="40%">'.$this->datos[0]['desc_depto'].'</td></tr>
                </table>';

        $this->Ln(5);
        $this->writeHTML ($tbl);

        $dias_form_400 = -1;
        $tbl = '<table border="1" style="font-size: 8pt;">';
        $tbl.=' <tr style="font-size: 10pt;" >
                    <td colspan="6" align="center"><b>PROCESOS QUE ESTAN EN PLAZO</b></td>
                </tr>
                <tr style="font-size: 8pt;">
                    <td width="15%" align="center"><b>NO. TRAMITE</b></td>
                    <td width="15%" align="center"><b>ESTADO.</b></td>
                    <td width="35%" align="center"><b>FUN. SOLICITANTE</b></td>
                    <td width="12%" align="center"><b>FECHA INICIO</b></td>
                    <td width="11%" align="center"><b>FECHA FIN</b></td>
                    <td width="12%" align="center"><b>CONFORMIDAD</b></td>
                </tr>';

        $tbl_fuera_plazo = '<table border="1" style="font-size: 8pt;">
                <tr style="font-size: 10pt;" >
                    <td colspan="7" align="center"><b>PROCESOS QUE ESTAN FUERA DE PLAZO</b></td>
                </tr>
                <tr style="font-size: 8pt;">
                    <td width="15%" align="center"><b>NO. TRAMITE</b></td>
                    <td width="14%" align="center"><b>ESTADO.</b></td>
                    <td width="25%" align="center"><b>FUN. SOLICITANTE</b></td>
                    <td width="11%" align="center"><b>FECHA INICIO</b></td>
                    <td width="10%" align="center"><b>FECHA FIN</b></td>
                    <td width="10%" align="center"><b>CONFORMIDAD</b></td>
                    <td width="15%" align="center"><b>DIAS VENCIMIENTO</b></td>
                </tr>';
        foreach ($this->datos as $record){
            $cad_conformidad = $record["conformidad"] == 'TIENE CONFORMIDAD'? '<span style="color: green;">'.$record["conformidad"].'</span>': '<span style="color: red;">'.$record["conformidad"].'</span>';

            if($record["dias_form_500"]>=0 && $record["dias_form_500"]<=$record["plazo_dias"] && $record["dias_form_500"]!= null){
                if($record["dias_form_500"] != $dias_form_400) {
                    if($record["dias_form_500"] == 0)
                        $tbl .= '<tr style="color: red;font-size: 10pt;"><td colspan="5" align="center"><b>PROCESOS CON PLAZO DE (' . $record["dias_form_500"] . ')' . ' DIAS</b></td></tr>';
                    if($record["dias_form_500"] >=1 && $record["dias_form_500"]<=5)
                        $tbl .= '<tr style="color: orange;font-size: 10pt;"><td colspan="5" align="center"><b>PROCESOS CON PLAZO DE (' . $record["dias_form_500"] . ')' . ' DIAS</b></td></tr>';

                    $dias_form_400 = $record["dias_form_500"];
                }
                $tbl .= '<tr>
                            <td width="15%" align="center">' . $record["num_tramite"] . '</td>
                            <td width="15%" align="center">' . $record["estado"] . '</td>
                            <td width="35%" align="left">' . $record["fun_solicitante"] . '</td>
                            <td width="12%" align="center">' . date_format(date_create($record["fecha_inicio"]),'d/m/Y') . '</td>
                            <td width="12%" align="center">' . date_format(date_create($record["fecha_fin"]),'d/m/Y') . '</td>
                            <td width="11%" align="center">'.$cad_conformidad.'</td>
                        </tr>';
            }else{
                $tbl_fuera_plazo .= '<tr>
                            <td width="15%" align="center">' . $record["num_tramite"] . '</td>
                            <td width="14%" align="center">' . $record["estado"] . '</td>
                            <td width="25%" align="left">' . $record["fun_solicitante"] . '</td>
                            <td width="11%" align="center">' . date_format(date_create($record["fecha_inicio"]),'d/m/Y') . '</td>
                            <td width="10%" align="center">' . date_format(date_create($record["fecha_fin"]),'d/m/Y') . '</td>
                            <td width="10%" align="center">'.$cad_conformidad.'</td>
                            <td width="15%" align="center">' . $record["dias_form_400"] . '</td>  
                        </tr>';
            }

        }
        $tbl.='</table>';
        $tbl_fuera_plazo.='</table>';
        $this->writeHTML ($tbl);
        $this->ln(5);
        $this->writeHTML ($tbl_fuera_plazo);


    }



}
?>