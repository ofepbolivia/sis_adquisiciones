<?php
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDFFormulario.php';
require_once dirname(__FILE__).'/../../pxp/pxpReport/Report.php';
 class CustomReport extends ReportePDFFormulario{
    
    private $dataSource;    
    public function setDataSource(DataSource $dataSource) {
        $this->dataSource = $dataSource;        
    }
    
    public function getDataSource() {
        return $this->dataSource;
    }
    
    public function Header() {
        $height = 20;        
        $codigo_uo=$this->getDataSource()->getParameter('codigo_uo');
		
		$this->Cell(40, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->SetFontSize(16);
        $this->SetFont('','B');
		if($codigo_uo=='MM'){
			$this->Cell(105, $height, 'SOLICITUD DE COMPRA/REPARACION', 0, 0, 'C', false, '', 0, false, 'T', 'C');								
		}else{
			$this->Cell(105, $height, 'SOLICITUD DE COMPRA', 0, 0, 'C', false, '', 0, false, 'T', 'C');								
		}
		$this->firmar();
		/*jrr:cambio para firmas*/
        //$this->Image(dirname(__FILE__).'/../../pxp/lib'.$_SESSION['_DIR_LOGO'], $x, $y, 36);
        
    }  
    
}


Class RSolicitudCompra extends Report {
	var $objParam;
	function __construct(CTParametro $objParam) {
		$this->objParam = $objParam;
	}
    function write() {
    	
        $pdf = new CustomReport($this->objParam);
        $pdf->setDataSource($this->getDataSource());
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		/*jrr: Cambio para firmas*/
		$pdf->firma['datos_documento']['numero'] = $this->getDataSource()->getParameter('numero');
		$pdf->firma['datos_documento']['numero_tramite'] = $this->getDataSource()->getParameter('num_tramite');
		$pdf->firma['datos_documento']['tipo'] = $this->getDataSource()->getParameter('tipo');
		$pdf->firma['datos_documento']['justificacion'] = $this->getDataSource()->getParameter('justificacion');		
        
		// add a page
        $pdf->AddPage();
        
        $height = 5;
        $width1 = 15;
        $width2 = 20;
        $width3 = 35;
        $width4 = 75;
        
        $pdf->SetFontSize(8.5);
        $pdf->SetFont('', 'B');
        $pdf->setTextColor(0,0,0);

        $fecha_reg = substr($this->getDataSource()->getParameter('fecha_reg'),0,4);
        $gestion = $this->getDataSource()->getParameter('desc_gestion');

        if ( $this->getDataSource()->getParameter('fecha_soli_gant') >= '2019-09-01'){
            $fecha_solicitud = $this->getDataSource()->getParameter('fecha_soli_gant');
        }else{
            $fecha_solicitud = $this->getDataSource()->getParameter('fecha_soli');
        }
       
        $pdf->Cell($width3, $height, 'Número de Solicitud', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width3, $height, 'Fecha de Solicitud', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width3, $height, 'Fecha de Aprobacion', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2+8, $height, 'Nro Tramite', 0, 0, 'C', false, '', 0, false, 'T', 'C');
		$pdf->Cell($width2-3, $height, 'Tipo', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2-3, $height, 'Moneda', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2-3, $height, 'Gestion', 0, 0, 'C', false, '', 0, false, 'T', 'C');		
        $pdf->Ln();
      
        $pdf->SetFont('', '');        
        $pdf->Cell($width3, $height, $this->getDataSource()->getParameter('numero'), 0, 0, 'C', false, '', 0, false, 'T', 'C');        
        $pdf->Cell($width3, $height, $fecha_solicitud, 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width3, $height, $this->getDataSource()->getParameter('fecha_apro'), 0, 0, 'C', false, '', 0, false, 'T', 'C');        
        $pdf->Cell($width2+8, $height, $this->getDataSource()->getParameter('num_tramite'), 0, 0, 'C', false, '', 0, false, 'T', 'C');
		$pdf->Cell($width2-3, $height, $this->getDataSource()->getParameter('tipo'), 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2-3, $height, $this->getDataSource()->getParameter('desc_moneda'), 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2-3, $height, $this->getDataSource()->getParameter('desc_gestion'), 0, 0, 'C', false, '', 0, false, 'T', 'C');		
        $pdf->Ln();
        $pdf->Ln();

        $white = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));
        $black = array('T' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        //$pdf->setLineStyle($white);
        
        
        $pdf->SetFontSize(7);
        $pdf->SetFont('', 'B');
        $pdf->Cell($width3, $height, 'Proceso:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->SetFillColor(192,192,192, true);
        
        //est alinea cambia el color de la lienas
        $pdf->Cell($width3+$width2, $height, $this->getDataSource()->getParameter('desc_proceso_macro'), $white, 0, 'L', true, '', 0, false, 'T', 'C');
        
        
        $pdf->SetFont('', 'B');
        
        $pdf->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width3, $height, 'Categoria de Compra:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->SetFillColor(192,192,192, true);
        $pdf->Cell($width3+$width2, $height, $this->getDataSource()->getParameter('desc_categoria_compra'), $white, 0, 'L', true, '', 0, false, 'T', 'C');
        
        $pdf->Ln();
       
        $pdf->SetFont('', 'B');                             
        $pdf->Cell($width3, $height, 'Gerente:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->SetFillColor(192,192,192, true);
        //$pdf->MultiCell($width3+$width2, $height, $this->getDataSource()->getParameter('desc_funcionario_apro'), 0,'L', true ,0);
        $pdf->Cell($width3+$width2, $height, $this->getDataSource()->getParameter('desc_funcionario_apro'), $white, 0, 'L', true, '', 0, false, 'T', 'C');        
        
        $pdf->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', 'B');
        $pdf->Cell($width3, $height, 'RPC:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->SetFillColor(192,192,192, true);
        //$pdf->MultiCell($width3+$width2, $height, $this->getDataSource()->getParameter('desc_funcionario_rpc'), 1,'L', true ,1);
        $pdf->Cell($width3+$width2, $height, $this->getDataSource()->getParameter('desc_funcionario_rpc'), $white, 0, 'L', true, '', 0, false, 'T', 'C');        
        $pdf->Ln();
        
        
        $pdf->SetFont('', 'B');                             
        $pdf->Cell($width3, $height, 'Unidad Solicitante:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->SetFillColor(192,192,192, true);
        $pdf->MultiCell($width3+$width2, $height, $this->getDataSource()->getParameter('desc_uo'), 0,'L', true ,0);
        
        //$pdf->Cell($width3+$width2, $height, $this->getDataSource()->getParameter('desc_uo'), $white, 0, 'L', true, '', 1, false, 'T', 'C');        
        $pdf->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', 'B');
        $pdf->Cell($width3, $height, 'Funcionario:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->SetFillColor(192,192,192, true);
        $pdf->MultiCell($width3+$width2, $height, $this->getDataSource()->getParameter('desc_funcionario'), 1,'L', true ,1);
        //$pdf->Cell($width3+$width2, $height, $this->getDataSource()->getParameter('desc_funcionario'), $white, 0, 'L', true, '', 0, false, 'T', 'C');        
       
       
       if($this->getDataSource()->getParameter('nombre_usuario_ai')!= ''&&$this->getDataSource()->getParameter('nombre_usuario_ai')!= 'NULL'){
            $pdf->SetFont('', 'B');                             
            $pdf->Cell($width3, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $pdf->SetFont('', '');
            $pdf->SetFillColor(192,192,192, true);
            $pdf->MultiCell($width3+$width2, $height, '', 0,'L', true ,0);
            
            $pdf->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $pdf->SetFont('', 'B');
            $pdf->Cell($width3, $height, 'Funcionario AI:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $pdf->SetFont('', '');
            $pdf->SetFillColor(192,192,192, true);
            $pdf->MultiCell($width3+$width2, $height, $this->getDataSource()->getParameter('nombre_usuario_ai'), 1,'L', true ,1);
           
            $pdf->Ln();     
           
       }
       
        //imprime el detalle de la solicitud
        
        $this->writeDetalles($this->getDataSource()->getParameter('detalleDataSource'), $pdf);
        
        //imprime el pie del reporte
        $pdf->setTextColor(0,0,0);
        $pdf->SetFontSize(8);
        $pdf->SetFont('', 'B');
        $pdf->Cell($width3, $height, 'Justificación', 0, 0, 'L', false, '', 1, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->MultiCell($width4*2, $height, $this->getDataSource()->getParameter('justificacion'), 0,'L', false ,0);
        $pdf->Ln();
        $pdf->SetFont('', 'B');
        $pdf->Cell($width3, $height, 'Comité Calificación:', 0, 0, 'L', false, '', 1, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->MultiCell($width4*2, $height, $this->getDataSource()->getParameter('comite_calificacion'), 0,'L', false ,0);
        $pdf->Ln();
        $pdf->SetFont('', 'B');
        $pdf->Cell($width3, $height, 'Posibles Proveedores:', 0, 0, 'L', false, '', 1, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->MultiCell($width4*2, $height, $this->getDataSource()->getParameter('posibles_proveedores'), 0,'L', false ,0);$pdf->setTextColor(0,0,0);
        $pdf->Ln();
        $pdf->SetFont('', 'B');
        $pdf->Cell($width3, $height, 'Lugar de Entrega:', 0, 0, 'L', false, '', 1, false, 'T', 'C');
        $pdf->SetFont('', '');
        $pdf->Cell($width3+$width2, $height, $this->getDataSource()->getParameter('lugar_entrega'), 0, 1, 'L', false, '', 0, false, 'T', 'C');
        
        $pdf->Ln();
        $pdf->Ln();
        $firma_solicitante = $this->getDataSource()->getParameter('desc_funcionario');
        $cargo_solicitante = $this->getDataSource()->getParameter('cargo_desc_funcionario');
        $firma_gerente = $this->getDataSource()->getParameter('desc_funcionario_apro');
        $cargo_gerente = $this->getDataSource()->getParameter('cargo_desc_funcionario_apro');
        $nro_tramite_qr = $this->getDataSource()->getParameter('num_tramite');
        $prioridad = $this->getDataSource()->getParameter('prioridad');
        $firma_rpc = $this->getDataSource()->getParameter('desc_funcionario_rpc');
        $cargo_rpc = $this->getDataSource()->getParameter('cargo_desc_funcionario_rpc');      
        $dep_prioridad = $this->getDataSource()->getParameter('dep_prioridad'); 
        //$date = date('d/m/Y');        
       //var_dump($cargo_gerente);exit;
       
        if ($this->getDataSource()->getParameter('fecha_soli') >= '2019-09-01') {
            
            $pdf->GetY() >= 234 && $pdf->Ln(20);

            if($this->getDataSource()->getParameter('estado')=='borrador'){
                    $tbl = '<table>
                    <tr>
                    <td style="width: 15%"></td>
                    <td style="width: 70%">
                    <table cellspacing="0" cellpadding="1" border="1" style="font-family: Calibri; font-size: 9px;">
                        <tr>
                            <td style="font-family: Calibri; font-size: 9px;"><b> Solicitado por:</b> <br> </td>
                            <td style="font-family: Calibri; font-size: 9px;"><b> Aprobado por:</b><br> </td>
                        </tr>
                        <tr>
                            <td align="center" >
                                <br><br>
                                <img  style="width: 95px; height: 95px;" src="" alt="Logo"><br>

                            </td>
                            <td align="center" >
                                <br><br>
                                <img  style="width: 95px; height: 95px;" src="" alt="Logo"><br>

                            </td>
                        </tr>
                    </table>
                    </td>
                    <td style="width:15%;"></td>
                    </tr>
                    </table>';
            $pdf->Ln(5);
            $pdf->writeHTML($tbl, true, false, false, false, '');                                                      
            }
            else if($prioridad == 383 and $dep_prioridad != 1){                
                $estados_prioridad = array('vbpoa', 'suppresu', 'vbpresupuestos', 'vbrpc');                
                if ($this->getDataSource()->getParameter('estado') != 'borrador' and in_array($this->getDataSource()->getParameter('estado'),$estados_prioridad)){                    
                    $tbl = '<table>
                            <tr>
                            <td style="width: 15%"></td>
                            <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                <tr>
                                    <td style="font-family: Calibri; font-size: 9px;"><b> Solicitado por:</b>' .$firma_solicitante. '</td>
                                    <td style="font-family: Calibri; font-size: 9px;"><b> Aprobado por:</b><br> </td>
                                </tr>
                                <tr>
                                    <td align="center" >
                                        <br><br>
                                        <img  style="width: 110px; height: 110px;" src="' . $this->generarImagen($firma_solicitante, $cargo_solicitante, $nro_tramite_qr) . '" alt="Logo">                                        
                                    </td>
                                    <td align="center" >
                                        <br><br>
                                        <img  style="width: 95px; height: 95px;" src="" alt="Logo"><br>

                                    </td>                                
                                </tr>
                            </table>
                            </td>
                            <td style="width:15%;"></td>
                            </tr>
                            </table>
        
                        ';
                    $pdf->Ln(5);
                    $pdf->writeHTML($tbl, true, false, false, false, '');
                }else{                    
                    $tbl = '<table>
                            <tr>
                            <td style="width: 15%"></td>
                            <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1" style="font-family: Calibri; font-size: 9px;">
                                <tr>
                                    <td style="font-family: Calibri; font-size: 9px;"><b> Solicitado por:</b>' .$firma_solicitante. '</td>
                                    <td style="font-family: Calibri; font-size: 9px;"><b> Aprobado por:</b>' .$firma_gerente. '</td>
                                </tr>
                                <tr>
                                    <td align="center" >
                                        <br><br>
                                        <img  style="width: 110px; height: 110px;" src="' . $this->generarImagen($firma_solicitante, $cargo_solicitante, $nro_tramite_qr) . '" alt="Logo">    
                                    </td>
                                    <td align="center" >
                                        <br><br>
                                        <img  style="width: 110px; height: 110px;" src="' . $this->generarImagen($firma_rpc, $cargo_rpc, $nro_tramite_qr) . '" alt="Logo">
                                    </td>
                                </tr>
                            </table>
                            </td>
                            <td style="width:15%;"></td>
                            </tr>
                            </table>
                        ';
                    $pdf->Ln(5);
                    $pdf->writeHTML($tbl, true, false, false, false, '');
                }
            }  
                else if ($prioridad == 383 and $dep_prioridad == 1){
                    $estados_priori = array('vbgerencia');                    
                    if ($this->getDataSource()->getParameter('estado') != 'borrador' and in_array($this->getDataSource()->getParameter('estado'),$estados_priori)){                    
                        $tbl = '<table>
                        <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 70%">
                        <table cellspacing="0" cellpadding="1" border="1">
                            <tr>
                                <td style="font-family: Calibri; font-size: 9px;"><b> Solicitado por:</b>' .$firma_solicitante. '</td>
                                <td style="font-family: Calibri; font-size: 9px;"><b> Aprobado por:</b><br> </td>
                            </tr>
                            <tr>
                                <td align="center" >
                                    <br><br>
                                    <img  style="width: 110px; height: 110px;" src="' . $this->generarImagen($firma_solicitante, $cargo_solicitante, $nro_tramite_qr) . '" alt="Logo">                                        
                                </td>
                                <td align="center" >
                                    <br><br>
                                    <img  style="width: 95px; height: 95px;" src="" alt="Logo"><br>
    
                                </td>                                
                            </tr>
                        </table>
                        </td>
                        <td style="width:15%;"></td>
                        </tr>
                        </table>
    
                    ';
                    $pdf->Ln(5);
                    $pdf->writeHTML($tbl, true, false, false, false, '');
                }else{
                    $tbl = '<table>
                            <tr>
                            <td style="width: 15%"></td>
                            <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1" style="font-family: Calibri; font-size: 9px;">
                                <tr>
                                    <td style="font-family: Calibri; font-size: 9px;"><b> Solicitado por:</b>' .$firma_solicitante. '</td>
                                    <td style="font-family: Calibri; font-size: 9px;"><b> Aprobado por:</b>' .$firma_gerente. '</td>
                                </tr>
                                <tr>
                                    <td align="center" >
                                        <br><br>
                                        <img  style="width: 110px; height: 110px;" src="' . $this->generarImagen($firma_solicitante, $cargo_solicitante, $nro_tramite_qr) . '" alt="Logo">    
                                    </td>
                                    <td align="center" >
                                        <br><br>
                                        <img  style="width: 110px; height: 110px;" src="' . $this->generarImagen($firma_gerente, $cargo_gerente, $nro_tramite_qr) . '" alt="Logo">
                                    </td>
                                </tr>
                            </table>
                            </td>
                            <td style="width:15%;"></td>
                            </tr>
                            </table>
        
                        ';                    
            $pdf->Ln(5);
            $pdf->writeHTML($tbl, true, false, false, false, '');
                }                               
  
            }else if($prioridad != 383){
                $estados_ant_gerencia = array('vbactif', 'vbuti', 'vbgerencia'); 
                if( $this->getDataSource()->getParameter('estado') != 'borrador' and in_array($this->getDataSource()->getParameter('estado'),$estados_ant_gerencia)){
                    $tbl = '<table>
                            <tr>
                            <td style="width: 15%"></td>
                            <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1">
                                <tr>
                                    <td style="font-family: Calibri; font-size: 9px;"><b> Solicitado por:</b>' .$firma_solicitante. '</td>
                                    <td style="font-family: Calibri; font-size: 9px;"><b> Aprobado por:</b><br> </td>
                                </tr>
                                <tr>
                                    <td align="center" >
                                        <br><br>
                                        <img  style="width: 110px; height: 110px;" src="' . $this->generarImagen($firma_solicitante, $cargo_solicitante, $nro_tramite_qr) . '" alt="Logo">                                        
                                    </td>
                                    <td align="center" >
                                        <br><br>
                                        <img  style="width: 95px; height: 95px;" src="" alt="Logo"><br>

                                    </td>                                
                                </tr>
                            </table>
                            </td>
                            <td style="width:15%;"></td>
                            </tr>
                            </table>
        
                        ';
                    $pdf->Ln(5);
                    $pdf->writeHTML($tbl, true, false, false, false, '');
                }else{                                    
                    $tbl = '<table>
                            <tr>
                            <td style="width: 15%"></td>
                            <td style="width: 70%">
                            <table cellspacing="0" cellpadding="1" border="1" style="font-family: Calibri; font-size: 9px;">
                                <tr>
                                    <td style="font-family: Calibri; font-size: 9px;"><b> Solicitado por:</b>' .$firma_solicitante. '</td>
                                    <td style="font-family: Calibri; font-size: 9px;"><b> Aprobado por:</b>' .$firma_gerente. '</td>
                                </tr>
                                <tr>
                                    <td align="center" >
                                        <br><br>
                                        <img  style="width: 110px; height: 110px;" src="' . $this->generarImagen($firma_solicitante, $cargo_solicitante, $nro_tramite_qr) . '" alt="Logo">    
                                    </td>
                                    <td align="center" >
                                        <br><br>
                                        <img  style="width: 110px; height: 110px;" src="' . $this->generarImagen($firma_gerente, $cargo_gerente, $nro_tramite_qr) . '" alt="Logo">
                                    </td>
                                </tr>
                            </table>
                            </td>
                            <td style="width:15%;"></td>
                            </tr>
                            </table>
                        ';
                    $pdf->Ln(5);
                    $pdf->writeHTML($tbl, true, false, false, false, '');
                }
            }            
        }
        

        //presupuestos para la sisguiente gestion
        /*
        if($fecha_reg != $gestion){
            $pdf->setTextColor(248,0,0);
            $pdf->MultiCell(185, $height, 'EN CUMPLIMIENTO A INSTRUCCIONES EMITIDAS POR EL ORGANO RECTOR  MEDIANTE CITE MEFP/VPCF/DGPGP/UEP/N° 355/2016, BOLIVIANA DE AVIACION EN FECHA 04 DE SEPTIEMBRE DEL AÑO EN CURSO, REMITE AL MINISTERIO DE ECONOMÍA Y FINANZAS PÚBLICAS  EL ANTEPROYECTO DE PRESUPUESTO INSTITUCIONAL DE BoA, A TRAVES DE LA NOTA OB.GG.NE.639.016, PARA SU CORRESPONDIENTE INCLUSIÓN EN EL PRESUPUESTO GENERAL DEL ESTADO - GESTION 2017, DONDE SE JUSTIFICO LA ASIGNACION PRESUPUESTARIA EN LAS DIFERENTES PARTIDAS DE GASTO, QUE COMPONEN EL MISMO, DE ACUERDO A LA PROGRAMACION REALIZADA EN MEMORIAS DE CALCULO.', 0,'J', false);
        }
        */

        $pdf->Ln();
		/*jrr: Cambio para firmas*/
		$res =$pdf->firma;
		
		$pdf->Output($pdf->url_archivo, 'F');			
		return $res;
    }
   
    function writeDetalles (DataSource $dataSource, TCPDF $pdf) {
            
         $pdf->setTextColor(0,0,0);
         $pdf->setFont('','B');
         $pdf->setFont('','');         
        //cambia el color de lienas
        $pdf->SetDrawColor    (  0,-1,-1,-1,false,'');   
        
         
         //$pdf->Cell($width3+$width2, $height, $this->getDataSource()->getParameter('desc_proceso_macro'), $white, 0, 'L', true, '', 0, false, 'T', 'C');
        
                    
        //$blackAll = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));  
        //$blackSide = array('LR' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        //$blackBottom = array('B' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        //$blackTop = array('T' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        //$widthMarginLeft = 1;
        
        $width1 = 15;
        $width2 = 25;
        $width3 = 20;
        
        //$pdf->SetFontSize(7.5);
        //$pdf->SetFont('', 'B');
        $height = 5;
        $pdf->Ln();       
            
                    
                
        $conf_par_tablewidths=array($width2,$width2*2,$width2*2+15,$width1+$width2);
        $conf_par_tablealigns=array('L','L','L','R');
        $conf_par_tablenumbers=array(0,0,0,0);
        $conf_tableborders=array();
        $conf_tabletextcolor=array();
        
        $conf_par_tabletextcolor_rojo=array(array(0,0,0),array(0,0,0),array(0,0,0),array(255,0,0));
        $conf_par_tabletextcolor_verde=array(array(0,0,0),array(0,0,0),array(0,0,0),array(35,142,35));
        
        
        
        $conf_det_tablewidths=array($width2+$width1,$width2+25+$width3*2,$width1,$width3,$width3);
        $conf_det_tablealigns=array('L','L','L','R','R');
        $conf_det_tablenumbers=array(0,0,0,0,0);
        
        
        
        $conf_det2_tablewidths=array($width2+$width1,$width2+25+$width3*2,$width1,$width3,$width3);
        $conf_det2_tablealigns=array('L','L','L','R','R');
        $conf_det2_tablenumbers=array(0,0,0,2,2);
        
        
        $conf_tp_tablewidths=array($width2+$width1+$width2+25+($width3*2)+$width1+$width3,$width3);
        $conf_tp_tablealigns=array('R','R');
        $conf_tp_tablenumbers=array(0,2);
        $conf_tp_tableborders=array(0,1);
        
        $total_solicitud = 0;
        $count_partidas = 0;
        
        foreach($dataSource->getDataset() as $row) {
                    
                
            $pdf->tablewidths=$conf_par_tablewidths;
            $pdf->tablealigns=$conf_par_tablealigns;
            $pdf->tablenumbers=$conf_par_tablenumbers;
            $pdf->tableborders=$conf_tableborders;
            $pdf->tabletextcolor=$conf_tabletextcolor;
            
           
        
                    
            $RowArray = array(
                        'codigo_partida'  =>  'Código Partida',
                        'nombre_partida'  => 'Nombre Partida',
                        'desc_centro_costo'    => 'Centro de Costo',
                        //'totalRef' => '',
                        'ejecutado' => 'Presupuesto'
                    );     
                         
             $pdf-> MultiRow($RowArray,false,0); 
            
            //chequear disponibilidad
            
            $estado_sin_presupuesto = array("borrador", "pendiente", "vbgerencia", "vbpresupuestos");
	 	    if (in_array($this->getDataSource()->getParameter('estado'), $estado_sin_presupuesto)){
                //verifica la disponibilidad de presupeusto para el  agrupador     
                if($row['presu_verificado']=="true"){
                    $disponibilida = 'DISPONIBLE';
                    $pdf->tabletextcolor=$conf_tabletextcolor;
                }
                else{
                   $disponibilida ='NO DISPONIBLE';
                   $pdf->tabletextcolor=$conf_par_tabletextcolor_rojo;
                }
            }
            else{
               $disponibilida ='DISPONIBLE Y APROBADO';
               $pdf->tabletextcolor=$conf_par_tabletextcolor_verde;
            }
            if($this->getDataSource()->getParameter('sw_cat')=='si'){
                $descCentroCosto =  'Cat. Prog.: '.$row['groupeddata'][0]['codigo_categoria']."\n".$row['grup_desc_centro_costo'];
            }else{
                $descCentroCosto =  $row['grup_desc_centro_costo'];
            }

            // din chequeo disponibilidad
            $RowArray = array(
                        'codigo_partida'  => $row['groupeddata'][0]['codigo_partida'],
                        'nombre_partida'  => $row['groupeddata'][0]['nombre_partida'],
                        //'desc_centro_costo'    => $row['groupeddata'][0]['desc_centro_costo'],
                        'desc_centro_costo'    => $descCentroCosto,
                        //'desc_centro_costo'    => $row['grup_desc_centro_costo']. "\nCP: ".$row['groupeddata'][0]['codigo_categoria'],
                        //'desc_centro_costo'    => $row['groupeddata'][0]['desc_centro_costo']. "\n".$row['groupeddata'][0]['codigo_categoria'],
                        //'desc_centro_costo'    => $row['groupeddata'][0]['codigo_categoria'],
                        // 'totalRef' => $row['totalRef'],
                        'ejecutado' =>  $disponibilida
                    );     
                         
            $pdf-> MultiRow($RowArray,false,0);
            
            /////////////////////////////////      
            //agregar detalle de la solicitud
            //////////////////////////////////
            
            $pdf->tablewidths=$conf_det_tablewidths;
            $pdf->tablealigns=$conf_det_tablealigns;
            $pdf->tablenumbers=$conf_det_tablenumbers;
            $pdf->tableborders=$conf_tableborders;
            $pdf->tabletextcolor=$conf_tabletextcolor;

            $table = '<table border="1" style="font-size: 7pt; color: black;">
                        <tr>
                            <th width="25%" align="center"><b>Concepto Gasto</b></th>
                            <th width="45%" align="center"><b>Descripción</b></th>
                            <th width="8%" align="center"><b>Cantidad</b></th>
                            <th width="12%" align="center"><b>Precio Unitario</b></th>
                            <th width="10%" align="center"><b>Precio Total</b></th>
                        </tr>
                        ';
            
            /*$RowArray = array(
            			'desc_concepto_ingas'  => 'Concepto Gasto',
                        'descripcion'  => 'Descripcion' ,                        
                        'cantidad'    => 'Cantidad',
                        'precio_unitario' => 'Precio Unitario',
                        'precio_total' => 'Precio Total'
                    );     
                         
            $pdf-> MultiRow($RowArray,false,1);*/
            
            //$pdf->Ln();
            $totalRef=0;
            $totalGa=0;
            $totalSg=0;
            $xEnd=0;
            $yEnd=0;
            
            $pdf->tablewidths=$conf_det2_tablewidths;
            $pdf->tablealigns=$conf_det2_tablealigns;
            $pdf->tablenumbers=$conf_det2_tablenumbers;
            $pdf->tableborders=$conf_tableborders;
            
            foreach ($row['groupeddata'] as $solicitudDetalle) {

                $table.='<tr>
                            <td style="text-align: justify;">'.$solicitudDetalle['desc_concepto_ingas'].'</td>
                            <td>'.stripcslashes(nl2br(htmlentities($solicitudDetalle['descripcion']))).'</td>
                            <td style="text-align: center;">'.$solicitudDetalle['cantidad'].'</td>
                            <td>'.$solicitudDetalle['precio_unitario'].'</td>
                            <td>'.$solicitudDetalle['precio_total'].'</td>
                         </tr>
                        ';
                /*$RowArray = array(
                        'desc_concepto_ingas'  => $solicitudDetalle['desc_concepto_ingas'],
                        'descripcion'  =>  $solicitudDetalle['descripcion'],
                        'cantidad'    => $solicitudDetalle['cantidad'],
                        'precio_unitario' => $solicitudDetalle['precio_unitario'],
                        'precio_total' => $solicitudDetalle['precio_total']
                    );     
                         
                $pdf-> MultiRow($RowArray,false,1) ;*/
                
                $totalRef=$totalRef+$solicitudDetalle['precio_total'];
                $totalGa=$totalGa+$solicitudDetalle['precio_ga'];
                $totalSg=$totalSg+$solicitudDetalle['precio_sg'];
            
            
            }
           //coloca el total de la partida 
           $pdf->tablewidths=$conf_tp_tablewidths;
           $pdf->tablealigns=$conf_tp_tablealigns;
           $pdf->tablenumbers=$conf_tp_tablenumbers;
           $pdf->tableborders=$conf_tp_tableborders;
            $table.='<tr>
                            <td colspan="3" align="center">TOTAL</td>
                            
                            <td>('.$this->getDataSource()->getParameter('desc_moneda').')</td>
                            <td>'.number_format ($totalRef,2).'</td>
                     </tr>
                        ';
           /*$RowArray = array(
                        'precio_unitario' => '('.$this->getDataSource()->getParameter('desc_moneda').')',
                        'precio_total' => $totalRef
                    );     
                         
           $pdf-> MultiRow($RowArray,false,1);*/

            $table.='</table>';
            $pdf->writeHTML ($table);
           $total_solicitud = $total_solicitud + $totalRef;
           $count_partidas = $count_partidas + 1;
           $pdf->Ln();
           
        } 
        
        //coloca el gran total de la solicitu 
               
        if($count_partidas > 1){
           $pdf->tablewidths=$conf_tp_tablewidths;
           $pdf->tablealigns=$conf_tp_tablealigns;
           $pdf->tablenumbers=$conf_tp_tablenumbers;
           $pdf->tableborders=array(0,0);
            
           $RowArray = array(
                        'precio_unitario' => 'Total Solcitud ('.$this->getDataSource()->getParameter('desc_moneda').')',
                        'precio_total' => $total_solicitud
                    );     
                         
           $pdf-> MultiRow($RowArray,false,1); 
           $pdf->Ln();
           $pdf->Ln();  
                
        }
        
    }
    function generarImagen($nom, $car, $ntra){
        $cadena_qr = 'Nombre: '.$nom. "\n". "Cargo: ".$car. "\n"."N° Tramite: ". $ntra;
        $barcodeobj = new TCPDF2DBarcode($cadena_qr, 'QRCODE,M');
        $png = $barcodeobj->getBarcodePngData($w = 8, $h = 8, $color = array(0, 0, 0));
        $im = imagecreatefromstring($png);
        if ($im !== false) {
            header('Content-Type: image/png');
            imagepng($im, dirname(__FILE__) . "/../../reportes_generados/" . $nom . ".png");
            imagedestroy($im);

        } else {
            echo 'A ocurrido un Error.';
        }
        $url_archivo = dirname(__FILE__) . "/../../reportes_generados/" . $nom . ".png";

        return $url_archivo;
    }          
}
?>