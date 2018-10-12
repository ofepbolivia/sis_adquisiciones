<?php
//incluimos la libreria
//echo dirname(__FILE__);
//include_once(dirname(__FILE__).'/../PHPExcel/Classes/PHPExcel.php');
class RepProcContraDet
{
    private $docexcel;
    private $objWriter;
    private $nombre_archivo;
    private $hoja;
    private $columnas = array();
    private $fila;
    private $equivalencias = array();

    private $indice, $m_fila, $titulo;
    private $swEncabezado = 0; //variable que define si ya se imprimi� el encabezado
    private $objParam;
    public $url_archivo;
    private $resumen = array();
    private $resumen_regional = array();

    function __construct(CTParametro $objParam)
    {

        //reducido menos 23,24,26,27,29,30
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/" . $this->objParam->getParametro('nombre_archivo');
        //ini_set('memory_limit','512M');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "' . $this->objParam->getParametro('titulo_archivo') . '", generado por el framework PXP')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");

        $sheetId = 1;
        $this->docexcel->createSheet(NULL, $sheetId);
        $this->docexcel->setActiveSheetIndex($sheetId);


        $this->docexcel->setActiveSheetIndex(0);

        $this->docexcel->createSheet(NULL, 2);
        $this->docexcel->createSheet(NULL, 3);
        $this->docexcel->createSheet(NULL, 4);
        $this->docexcel->createSheet(NULL, 5);

        $this->equivalencias = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I',
            9 => 'J', 10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R',
            18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z',
            26 => 'AA', 27 => 'AB', 28 => 'AC', 29 => 'AD', 30 => 'AE', 31 => 'AF', 32 => 'AG', 33 => 'AH',
            34 => 'AI', 35 => 'AJ', 36 => 'AK', 37 => 'AL', 38 => 'AM', 39 => 'AN', 40 => 'AO', 41 => 'AP',
            42 => 'AQ', 43 => 'AR', 44 => 'AS', 45 => 'AT', 46 => 'AU', 47 => 'AV', 48 => 'AW', 49 => 'AX',
            50 => 'AY', 51 => 'AZ',
            52 => 'BA', 53 => 'BB', 54 => 'BC', 55 => 'BD', 56 => 'BE', 57 => 'BF', 58 => 'BG', 59 => 'BH',
            60 => 'BI', 61 => 'BJ', 62 => 'BK', 63 => 'BL', 64 => 'BM', 65 => 'BN', 66 => 'BO', 67 => 'BP',
            68 => 'BQ', 69 => 'BR', 70 => 'BS', 71 => 'BT', 72 => 'BU', 73 => 'BV', 74 => 'BW', 75 => 'BX',
            76 => 'BY', 77 => 'BZ');

    }


    function imprimeIniciados()
    {
        //*************************************TITULO*****************************************

        $styleTitulos1 = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos3 = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        );

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'DETALLE DE PROCESOS INICIADOS ');
        $this->docexcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:F2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Del: ' . $this->objParam->getParametro('fecha_ini') . '   Al: ' . $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A3:F3');

        //*************************************FIN TITULO*****************************************


        $this->docexcel->getActiveSheet()->setTitle('Procesos Iniciados');
        $datos = $this->objParam->getParametro('iniciados');
        $this->docexcel->setActiveSheetIndex(0);

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);


        $styleTitulos = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'c5d9f1'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));
        $this->docexcel->getActiveSheet()->getStyle('A5:F5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A6:F6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A6:F6')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************

        //$this->docexcel->getActiveSheet()->setCellValue('A6', 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 5, 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->getStyle('A5:A6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A5:A6');

        //$this->docexcel->getActiveSheet()->setCellValue('B6', 'Fecha de Inicio');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 5, 'Fecha de Inicio');
        $this->docexcel->getActiveSheet()->getStyle('B5:B6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('B5:B6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Precio Referencial en');
        $this->docexcel->getActiveSheet()->getStyle('C5:D5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('C5:D5');
        $this->docexcel->getActiveSheet()->setCellValue('C6', 'Bs.');
        $this->docexcel->getActiveSheet()->setCellValue('D6', 'US$');

        //$this->docexcel->getActiveSheet()->setCellValue('E6', 'Responsable del Inicio y/o Solicitud');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, 5, 'Responsable');
        $this->docexcel->getActiveSheet()->getStyle('E5:E6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('E5:E6');

        //$this->docexcel->getActiveSheet()->setCellValue('F6', 'Unidad Solicitante');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, 5, 'Unidad Solicitante');
        $this->docexcel->getActiveSheet()->getStyle('F5:F6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('F5:F6');

        //*************************************Detalle*****************************************
//        $columna = 0;
//        $fila = 7;
//        foreach ($datos as $value) {
//
//            foreach ($value as $key => $val) {
//
//                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($columna, $fila, $val);
//                $columna++;
//            }
//            $fila++;
//            $columna = 0;
//        }

        foreach ($datos as $indice => $value) {
            $fila = $indice + 7;

            foreach ($value as $key => $val) {

                if ($value['codigo'] == 'Bs') {
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['fecha_soli']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['precio_bs']);
//                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['precio_moneda_solicitada']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['solicitante']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['nombre_unidad']);

                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['estados_cotizacion']);
                }else{
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['fecha_soli']);
//                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['precio_bs']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['precio_moneda_solicitada']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['solicitante']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['nombre_unidad']);

                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['estados_cotizacion']);
                }
            }

        }


        //************************************************Fin Detalle***********************************************
    }

    function imprimeAdjudicados()
    {
        $this->docexcel->setActiveSheetIndex(1);
        $this->docexcel->getActiveSheet()->setTitle('Procesos Adjudicados');
        $datos = $this->objParam->getParametro('adjudicados');

        //*************************************TITULO*****************************************

        $styleTitulos1 = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos3 = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        );

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'DETALLE DE PROCESOS ADJUDICADOS');
        $this->docexcel->getActiveSheet()->getStyle('A2:L2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:L2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Del: ' . $this->objParam->getParametro('fecha_ini') . '   Al: ' . $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('A3:L3')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A3:L3');

        //*************************************FIN TITULO*****************************************


        $fila = 7;
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(45);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);


        $styleTitulos = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'c5d9f1'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));
        $this->docexcel->getActiveSheet()->getStyle('A5:L5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:L5')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A6:L6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A6:L6')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************
        //$this->docexcel->getActiveSheet()->setCellValue('A1', 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 5, 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->getStyle('A5:A6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A5:A6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 5, 'Precio Referencial en');
        $this->docexcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('B5:C5');
        $this->docexcel->getActiveSheet()->setCellValue('B6', 'Bs.');
        $this->docexcel->getActiveSheet()->setCellValue('C6', 'US$');

        //$this->docexcel->getActiveSheet()->setCellValue('D1', 'Fecha de Adjudicación');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, 5, 'Fecha de Adjudicación');
        $this->docexcel->getActiveSheet()->getStyle('D5:D6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('D5:D6');

        //$this->docexcel->getActiveSheet()->setCellValue('E1', 'Nombre del Proveedor Adjudicado');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, 5, 'Nombre del Proveedor Adjudicado');
        $this->docexcel->getActiveSheet()->getStyle('E5:E6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('E5:E6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, 5, 'Importe Adjudicado en');
        $this->docexcel->getActiveSheet()->getStyle('F5:G5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('F5:G5');
        $this->docexcel->getActiveSheet()->setCellValue('F6', 'Bs.');
        $this->docexcel->getActiveSheet()->setCellValue('G6', 'US$');

        //$this->docexcel->getActiveSheet()->setCellValue('H1', 'Responsable de la Adjudicación');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, 5, 'Responsable de la Adjudicación');
        $this->docexcel->getActiveSheet()->getStyle('H5:H6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('H5:H6');

        //$this->docexcel->getActiveSheet()->setCellValue('I1', 'Contrato');
        //$this->docexcel->getActiveSheet()->setCellValue('J1', 'Orden de Compra/Servicio');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, 5, 'Con');
        $this->docexcel->getActiveSheet()->getStyle('I5:J5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('I5:J5');
        $this->docexcel->getActiveSheet()->setCellValue('I6', 'Contrato');
        $this->docexcel->getActiveSheet()->setCellValue('J6', 'Orden de Compra/Servicio');

        //$this->docexcel->getActiveSheet()->setCellValue('K1', 'Plazo de Ejecución');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, 5, 'Plazo de Ejecución');
        $this->docexcel->getActiveSheet()->getStyle('K5:L5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('K5:L5');
        $this->docexcel->getActiveSheet()->setCellValue('K6', 'Inicio');
        $this->docexcel->getActiveSheet()->setCellValue('L6', 'Conclusión');

        //*************************************Detalle*****************************************
//        $columna = 0;
//        foreach ($datos as $value) {
//
//            foreach ($value as $key => $val) {
//
//                $this->docexcel->setActiveSheetIndex(1)->setCellValueByColumnAndRow($columna, $fila, $val);
//                $columna++;
//            }
//            $fila++;
//            $columna = 0;
//        }
        foreach ($datos as $indice => $value) {
            $fila = $indice + 7;

            foreach ($value as $key => $val) {

                if ($value['codigo'] == 'Bs') {
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['precio_bs']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, '');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['fecha_adju']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['proveedor_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['monto_total_adjudicado_mb']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['monto_total_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['solicitante']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['requiere_contrato']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['tipo']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['tiempo_entrega']);

                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['estados_cotizacion']);
                }else{
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, '');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['precio_moneda_solicitada']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['fecha_adju']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['proveedor_adjudicado']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['monto_total_adjudicado_mb']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['monto_total_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['solicitante']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['requiere_contrato']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['tipo']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['fecha_inicio']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['fecha_fin']);

                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['estados_cotizacion']);

                }


            }

        }

        //************************************************Fin Detalle***********************************************
    }

    function imprimeConContrato()
    {
        $this->docexcel->setActiveSheetIndex(2);
        $this->docexcel->getActiveSheet()->setTitle('Procesos c-Contrato');
        $datos = $this->objParam->getParametro('contrato');

        //*************************************TITULO*****************************************

        $styleTitulos1 = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos3 = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        );

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'DETALLE DE PROCESOS CON CONTRATO');
        $this->docexcel->getActiveSheet()->getStyle('A2:N2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:N2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Del: ' . $this->objParam->getParametro('fecha_ini') . '   Al: ' . $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('A3:N3')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A3:N3');

        //*************************************FIN TITULO*****************************************


        $fila = 7;
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);


        $styleTitulos = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'c5d9f1'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));
        $this->docexcel->getActiveSheet()->getStyle('A5:N5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A6:N6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A6:N6')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 5, 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->getStyle('A5:A6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A5:A6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 5, 'Precio Referencial en');
        $this->docexcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('B5:C5');
        $this->docexcel->getActiveSheet()->setCellValue('B6', 'Bs.');
        $this->docexcel->getActiveSheet()->setCellValue('C6', 'US$');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, 5, 'N° de Contrato');
        $this->docexcel->getActiveSheet()->getStyle('D5:D6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('D5:D6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, 5, 'Fecha de firma de Contrato');
        $this->docexcel->getActiveSheet()->getStyle('E5:E6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('E5:E6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, 5, 'Plazo de Ejecución');
        $this->docexcel->getActiveSheet()->getStyle('F5:G5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('F5:G5');
        $this->docexcel->getActiveSheet()->setCellValue('F6', 'Inicio');
        $this->docexcel->getActiveSheet()->setCellValue('G6', 'Conclusión');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, 5, 'Nombre del Proveedor');
        $this->docexcel->getActiveSheet()->getStyle('H5:H6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('H5:H6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, 5, 'Importe en');
        $this->docexcel->getActiveSheet()->getStyle('I5:J5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('I5:J5');
        $this->docexcel->getActiveSheet()->setCellValue('I6', 'Bs.');
        $this->docexcel->getActiveSheet()->setCellValue('J6', 'US$');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, 5, 'Fecha de emisión de Acta de Conformidad');
        $this->docexcel->getActiveSheet()->getStyle('K5:K6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('K5:K6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, 5, 'Objeto');
        $this->docexcel->getActiveSheet()->getStyle('L5:L6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('L5:L6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, 5, 'Tipo de proceso');
        $this->docexcel->getActiveSheet()->getStyle('M5:M6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('M5:M6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, 5, 'Forma de pago');
        $this->docexcel->getActiveSheet()->getStyle('N5:N6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('N5:N6');

        //*************************************Detalle*****************************************
//        $columna = 0;
//        foreach ($datos as $value) {
//
//            foreach ($value as $key => $val) {
//
//                $this->docexcel->setActiveSheetIndex(2)->setCellValueByColumnAndRow($columna, $fila, $val);
//                $columna++;
//            }
//            $fila++;
//            $columna = 0;
//        }
        foreach ($datos as $indice => $value) {
            $fila = $indice + 7;

            foreach ($value as $key => $val) {

                if ($value['codigo'] == 'Bs') {
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['precio_bs']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['precio_moneda_solicitada']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['numero']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['fecha_elaboracion']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['fecha_inicio']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['fecha_fin']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['proveedor_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['monto_total_adjudicado_mb']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['monto_total_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['conformidad_final']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['objeto']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['tipo']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $value['forma_pago']);

                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $value['estados_cotizacion']);
                }else{
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['precio_bs']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['precio_moneda_solicitada']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['numero']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['fecha_elaboracion']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['fecha_inicio']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['fecha_fin']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['proveedor_adjudicado']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['monto_total_adjudicado_mb']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['monto_total_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['conformidad_final']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['objeto']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['tipo']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $value['forma_pago']);

                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $value['estados_cotizacion']);
                }

            }

        }


        //************************************************Fin Detalle***********************************************
    }

    function imprimeConOrdenComSer()
    {
        $this->docexcel->setActiveSheetIndex(3);
        $this->docexcel->getActiveSheet()->setTitle('Procesos c-OC OS');
        $datos = $this->objParam->getParametro('compraservicio');

        //*************************************TITULO*****************************************

        $styleTitulos1 = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos3 = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        );

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'DETALLE DE PROCESOS CON ORDEN DE COMPRA/SERVICIO');
        $this->docexcel->getActiveSheet()->getStyle('A2:N2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:N2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Del: ' . $this->objParam->getParametro('fecha_ini') . '   Al: ' . $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('A3:N3')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A3:N3');

        //*************************************FIN TITULO*****************************************


        $fila = 7;
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);


        $styleTitulos = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'c5d9f1'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));
        $this->docexcel->getActiveSheet()->getStyle('A5:M5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:M5')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A6:M6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A6:M6')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 5, 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->getStyle('A5:A6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A5:A6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 5, 'Tipo de Orden');
        $this->docexcel->getActiveSheet()->getStyle('B5:B6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('B5:B6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Precio Referencial en');
        $this->docexcel->getActiveSheet()->getStyle('C5:D5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('C5:D5');
        $this->docexcel->getActiveSheet()->setCellValue('C6', 'Bs.');
        $this->docexcel->getActiveSheet()->setCellValue('D6', 'US$');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, 5, 'N° de Orden ');
        $this->docexcel->getActiveSheet()->getStyle('E5:E6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('E5:E6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, 5, 'Fecha de emisión de  Orden');
        $this->docexcel->getActiveSheet()->getStyle('F5:F6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('F5:F6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, 5, 'Plazo de Ejecución');
        $this->docexcel->getActiveSheet()->getStyle('G5:H5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('G5:H5');
        $this->docexcel->getActiveSheet()->setCellValue('G6', 'Inicio');
        $this->docexcel->getActiveSheet()->setCellValue('H6', 'Conclusión');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, 5, 'Nombre del Proveedor');
        $this->docexcel->getActiveSheet()->getStyle('I5:I6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('I5:I6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, 5, 'Importe en');
        $this->docexcel->getActiveSheet()->getStyle('J5:K5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('J5:K5');
        $this->docexcel->getActiveSheet()->setCellValue('J6', 'Bs.');
        $this->docexcel->getActiveSheet()->setCellValue('K6', 'US$');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, 5, 'Fecha de emisión de Acta de Conformidad');
        $this->docexcel->getActiveSheet()->getStyle('L5:L6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('L5:L6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, 5, 'Objeto');
        $this->docexcel->getActiveSheet()->getStyle('M5:M6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('M5:M6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, 5, 'Forma de pago');
        $this->docexcel->getActiveSheet()->getStyle('N5:N6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('N5:N6');

        //*************************************Detalle*****************************************
//        $columna = 0;
//        foreach ($datos as $value) {
//
//            foreach ($value as $key => $val) {
//
//                $this->docexcel->setActiveSheetIndex(3)->setCellValueByColumnAndRow($columna, $fila, $val);
//                $columna++;
//            }
//            $fila++;
//            $columna = 0;
//        }
        foreach ($datos as $indice => $value) {
            $fila = $indice + 7;

            foreach ($value as $key => $val) {

                if ($value['codigo'] == 'Bs') {
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['tipo']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['precio_bs']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['precio_moneda_solicitada']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['numero_oc']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['fecha_adju']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['fecha_inicio']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['fecha_fin']);

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['desc_proveedor']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['monto_total_adjudicado_mb']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['monto_total_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['conformidad_final']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['objeto']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $value['forma_pago']);

                    // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $value['estados_cotizacion']);
                }else{
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['tipo']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['precio_bs']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['precio_moneda_solicitada']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['numero_oc']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['fecha_adju']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['fecha_inicio']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['fecha_fin']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['desc_proveedor']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['monto_total_adjudicado_mb']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['monto_total_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['conformidad_final']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['objeto']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $value['forma_pago']);

                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $value['estados_cotizacion']);
                }

            }

        }


        //************************************************Fin Detalle***********************************************
    }

    function imprimeEjecutados()
    {
        $this->docexcel->setActiveSheetIndex(4);
        $this->docexcel->getActiveSheet()->setTitle('Procesos en Ejecución');
        $datos = $this->objParam->getParametro('ejecutados');

        //*************************************TITULO*****************************************

        $styleTitulos1 = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos3 = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        );

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'DETALLE DE PROCESOS EN EJECUCIÓN');
        $this->docexcel->getActiveSheet()->getStyle('A2:N2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:N2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Del: ' . $this->objParam->getParametro('fecha_ini') . '   Al: ' . $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('A3:N3')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A3:N3');

        //*************************************FIN TITULO*****************************************


        $fila = 7;
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);


        $styleTitulos = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'c5d9f1'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));
        $this->docexcel->getActiveSheet()->getStyle('A5:N5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A6:N6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A6:N6')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 5, 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->getStyle('A5:A6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A5:A6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 5, 'Fecha de Adjudicación y/o Firma de Contrato');
        $this->docexcel->getActiveSheet()->getStyle('B5:B6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('B5:B6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Plazo de Ejecución');
        $this->docexcel->getActiveSheet()->getStyle('C5:D5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('C5:D5');
        $this->docexcel->getActiveSheet()->setCellValue('C6', 'Inicio');
        $this->docexcel->getActiveSheet()->setCellValue('D6', 'Conclusión');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, 5, 'Importe Adjudicado en');
        $this->docexcel->getActiveSheet()->getStyle('E5:F5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('E5:F5');
        $this->docexcel->getActiveSheet()->setCellValue('E6', 'Bs.');
        $this->docexcel->getActiveSheet()->setCellValue('F6', 'US$');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, 5, 'Proveedor');
        $this->docexcel->getActiveSheet()->getStyle('G5:G6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('G5:G6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, 5, 'Importe Pagado a la fecha en');
        $this->docexcel->getActiveSheet()->getStyle('H5:I5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('H5:I5');
        $this->docexcel->getActiveSheet()->setCellValue('H6', 'Bs.');
        $this->docexcel->getActiveSheet()->setCellValue('I6', 'US$');


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, 5, 'Responsable del Inicio y/o Solicitud');
        $this->docexcel->getActiveSheet()->getStyle('J5:J6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('J5:J6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, 5, 'Unidad Solicitante');
        $this->docexcel->getActiveSheet()->getStyle('K5:K6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('K5:K6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, 5, 'Tipo de proceso');
        $this->docexcel->getActiveSheet()->getStyle('L5:L6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('L5:L6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, 5, 'N° de cuotas');
        $this->docexcel->getActiveSheet()->getStyle('M5:M6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('M5:M6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, 5, 'Forma de pago');
        $this->docexcel->getActiveSheet()->getStyle('N5:N6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('N5:N6');


        //*************************************Detalle*****************************************
//        $columna = 0;
//        foreach ($datos as $value) {
//
//            foreach ($value as $key => $val) {
//
//                $this->docexcel->setActiveSheetIndex(4)->setCellValueByColumnAndRow($columna, $fila, $val);
//                $columna++;
//            }
//            $fila++;
//            $columna = 0;
//        }
        foreach ($datos as $indice => $value) {
            $fila = $indice + 7;

            foreach ($value as $key => $val) {
                if ($value['codigo'] == 'Bs') {
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['fecha_elaboracion']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['fecha_inicio']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['fecha_fin']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['monto_total_adjudicado_mb']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['monto_total_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['proveedor_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['total_pagado']);
//                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['precio_moneda_solicitada']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['solicitante']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['nombre_unidad']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['tipo']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['nro_cuota_vigente']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $value['forma_pago']);

                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $value['estados_cotizacion']);
                }else{
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['fecha_elaboracion']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['fecha_inicio']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['fecha_fin']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['monto_total_adjudicado_mb']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['monto_total_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['proveedor_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['total_pagado']);
//                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['precio_moneda_solicitada']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['solicitante']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['nombre_unidad']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['tipo']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['nro_cuota_vigente']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $value['forma_pago']);

                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $value['estados_cotizacion']);
                }

            }

        }


        //************************************************Fin Detalle***********************************************
    }

    function imprimeConcluidos()
    {
        $this->docexcel->setActiveSheetIndex(5);
        $this->docexcel->getActiveSheet()->setTitle(' Procesos Concluidos');
        $datos = $this->objParam->getParametro('concluidos');

        //*************************************TITULO*****************************************

        $styleTitulos1 = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos3 = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        );

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'DETALLE DE PROCESOS CONCLUIDOS');
        $this->docexcel->getActiveSheet()->getStyle('A2:K2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:K2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Del: ' . $this->objParam->getParametro('fecha_ini') . '   Al: ' . $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A3:K3');

        //*************************************FIN TITULO*****************************************


        $fila = 7;
        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(35);


        $styleTitulos = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'c5d9f1'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));
        $this->docexcel->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A5:K5')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A6:K6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A6:K6')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 5, 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->getStyle('A5:A6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('A5:A6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 5, 'Fecha de Adjudicación y/o Firma de Contrato');
        $this->docexcel->getActiveSheet()->getStyle('B5:B6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('B5:B6');

//        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Plazo de Ejecución');
//        $this->docexcel->getActiveSheet()->getStyle('C5:C6')->applyFromArray($styleTitulos);
//        $this->docexcel->getActiveSheet()->mergeCells('C5:C6');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Plazo de Ejecución');
        $this->docexcel->getActiveSheet()->getStyle('C5:D5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('C5:D5');
        $this->docexcel->getActiveSheet()->setCellValue('C6', 'Inicio');
        $this->docexcel->getActiveSheet()->setCellValue('D6', 'Conclusión');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, 5, 'Importe Adjudicado en');
        $this->docexcel->getActiveSheet()->getStyle('E5:F5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('E5:F5');
        $this->docexcel->getActiveSheet()->setCellValue('E6', 'Bs.');
        $this->docexcel->getActiveSheet()->setCellValue('F6', 'US$');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, 5, 'Proveedor');
        $this->docexcel->getActiveSheet()->getStyle('G5:G6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('G5:G6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, 5, 'Importe Pagado en');
        $this->docexcel->getActiveSheet()->getStyle('H5:I5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('H5:I5');
        $this->docexcel->getActiveSheet()->setCellValue('H6', 'Bs.');
        $this->docexcel->getActiveSheet()->setCellValue('I6', 'US$');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, 5, 'Responsable del Inicio y/o Solicitud');
        $this->docexcel->getActiveSheet()->getStyle('J5:J6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('J5:J6');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, 5, 'Unidad Solicitante');
        $this->docexcel->getActiveSheet()->getStyle('K5:K6')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('K5:K6');

        //*************************************Detalle*****************************************
        foreach ($datos as $indice => $value) {
            $fila = $indice + 7;

            foreach ($value as $key => $val) {
                if ($value['codigo'] == 'Bs') {
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['fecha_elaboracion']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['fecha_inicio']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['fecha_fin']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['monto_total_adjudicado_mb']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['monto_total_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['proveedor_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['total_pagado']);
//                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['solicitante']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['nombre_unidad']);

                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['estados_cotizacion']);
                }else{
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['fecha_elaboracion']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['fecha_inicio']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['fecha_fin']);
                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['monto_total_adjudicado_mb']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['monto_total_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['proveedor_adjudicado']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['total_pagado']);
//
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['solicitante']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['nombre_unidad']);

                    //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['estados_cotizacion']);
                }

            }

        }


        //************************************************Fin Detalle***********************************************
    }


    function generarReporte()
    {
        //echo $this->nombre_archivo; exit;
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->docexcel->setActiveSheetIndex(0);

        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);

    }


}

?>