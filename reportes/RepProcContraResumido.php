<?php

class RepProcContraResumido
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

    private $NroTra = array();

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

    function imprimeCabecera()
    {
        $this->docexcel->createSheet();
        $this->docexcel->getActiveSheet()->setTitle('Procesos Contratación');
        $this->docexcel->setActiveSheetIndex(0);

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

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 2, 'REPORTE GERENCIAL DE PROCESOS DE CONTRATACIÓN');
        $this->docexcel->getActiveSheet()->getStyle('B2:I2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('B2:I2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 3, 'Del: ' . $this->objParam->getParametro('fecha_ini') . '   Al: ' . $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('B3:I3')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('B3:I3');

    }

    function imprimeIniciados()
    {
        $this->docexcel->getActiveSheet()->setTitle('Reporte');
        $datos = $this->objParam->getParametro('iniciados');
        $this->docexcel->setActiveSheetIndex(0);

        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);


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
        $this->docexcel->getActiveSheet()->getStyle('B6:C6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('B6:C6')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, 5, 'Procesos Iniciados');
        $this->docexcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('B5:C5');

        $this->docexcel->getActiveSheet()->setCellValue('B6', 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->setCellValue('C6', 'Importe (Bs y/o US$)');


        //*************************************Detalle*****************************************
        foreach ($datos as $indice => $value) {
            $fila = $indice + 7;

            foreach ($value as $key => $val) {

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['num_tramite']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['importe']);
            }

        }


        //************************************************Fin Detalle***********************************************
    }

    function imprimeAdjudicados()
    {
        $datos = $this->objParam->getParametro('adjudicados');
        //$fila = 7;
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);


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
        $this->docexcel->getActiveSheet()->getStyle('D6:E6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('D6:E6')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, 5, 'Procesos Adjudicados');
        $this->docexcel->getActiveSheet()->getStyle('D5:E5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('D5:E5');


        $this->docexcel->getActiveSheet()->setCellValue('D6', 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->setCellValue('E6', 'Importe (Bs y/o US$)');


        //*************************************Detalle*****************************************

        foreach ($datos as $indice => $value) {
            $fila = $indice + 7;
            if ($value['estados_cotizacion'] == 'adjudicado' ||
                $value['estados_cotizacion'] == 'contrato_pendiente' ||
                $value['estados_cotizacion'] == 'contrato_elaborado' ||
                $value['estados_cotizacion'] == 'pago_habilitado' ||
                $value['estados_cotizacion'] == 'finalizada' ||
                $value['estado'] == 'finalizado') {
                //unset($val[$key + 1]);

                foreach ($value as $key => $val) {

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['importe']);

                }


            }
        }

        //************************************************Fin Detalle***********************************************
    }

    function imprimeEjecutados()
    {
        $datos = $this->objParam->getParametro('ejecutados');

        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);


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
        $this->docexcel->getActiveSheet()->getStyle('F6:G6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('F6:G6')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, 5, 'Procesos en Ejecución');
        $this->docexcel->getActiveSheet()->getStyle('F5:G5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('F5:G5');


        $this->docexcel->getActiveSheet()->setCellValue('F6', 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->setCellValue('G6', 'Importe (Bs y/o US$)');


        //*************************************Detalle*****************************************
        //$columna = 5;
        foreach ($datos as $indice => $value) {
            $fila = $indice + 7;
            if ($value['estados_cotizacion'] == 'pago_habilitado' ||
                $value['estados_cotizacion'] == 'finalizada' ||
                $value['estado'] == 'finalizado') {


                foreach ($value as $key => $val) {

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['importe']);


                }


            }
        }


        //************************************************Fin Detalle***********************************************
    }

    function imprimeConcluidos()
    {
        $datos = $this->objParam->getParametro('concluidos');

        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);


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
        $this->docexcel->getActiveSheet()->getStyle('H6:I6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('H6:I6')->applyFromArray($styleTitulos);

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, 5, 'Procesos Concluidos');
        $this->docexcel->getActiveSheet()->getStyle('H5:I5')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->mergeCells('H5:I5');


        $this->docexcel->getActiveSheet()->setCellValue('H6', 'Tipo y N° de Proceso');
        $this->docexcel->getActiveSheet()->setCellValue('I6', 'Importe (Bs y/o US$)');


        //*************************************Detalle*****************************************

        $datos->$datos['estados_cotizacion'] && $datos['estado'];

        foreach ($datos as $indice => $value) {
            $fila = $indice + 7;
            if (
                $value['estados_cotizacion'] == 'finalizada' ||
                $value['estado'] == 'finalizado') {

                foreach ($value as $key => $val) {

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['num_tramite']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['importe']);

                    
                }


            }
        }


        //************************************************Fin Detalle***********************************************
    }


    function generarReporte()
    {
        $this->docexcel->setActiveSheetIndex(0);

        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);

    }


}

?>