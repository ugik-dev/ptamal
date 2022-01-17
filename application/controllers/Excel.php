<?php
/*

*/
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('SecurityModel', 'Accounting_model', 'General_model'));
        // $this->load->helper(array('DataStructure'));
        $this->db->db_debug = TRUE;
    }

    function bagan_akun()
    {
        $data = $this->General_model->getAllBaganAkun(array('by_DataStructure' => true));
        $company = Company_Profile();
        // echo json_encode($data);
        // die();
        $spreadsheet = new Spreadsheet();
        $styleArray = array(
            'font'  => array(
                'size'  => 10,
                'name'  => 'Courier'
            )
        );
        $spreadsheet->getDefaultStyle()
            ->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->setPrintGridlines(false);
        $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);
        $sheet = $spreadsheet->getActiveSheet();

        // $spreadsheet->getActiveSheet()->getColumnDimension('A')->setVisible(false);
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(5);
        $sheet->getColumnDimension('C')->setWidth(5);
        $sheet->getColumnDimension('D')->setWidth(5);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(13);
        $spreadsheet->getActiveSheet()->getStyle('A6:G6')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        // $spreadsheet->getActiveSheet()->getStyle('A6:H6')->getFont()->setSize(13)->setBold(true);
        // $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(13)->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:A5')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);

        $sheet->getStyle('F:H')->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
        // $sheet->getStyle('F:H')->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $this->load->model('Statement_model');
        $sheet->mergeCells("A1:G1");
        $sheet->mergeCells("A2:G2");
        $sheet->mergeCells("A3:G3");
        $sheet->mergeCells("A4:G4");
        // $sheet->mergeCells("A5:G5");
        $spreadsheet->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 6);
        $sheet->getHeaderFooter()->setOddFooter('&L' . $company['title1'] . ' - BAGAN AKUN &R &P of &N');

        $sheet->setCellValue('A1', $company['title1']);
        $sheet->setCellValue('A2', $company['address'] . ' - ' . $company['town']);
        $sheet->setCellValue('A3', 'BAGAN AKUN');

        $namaBulan = array("Januari", "Februaru", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $sheet->setCellValue('A5', 'NO AKUN');
        $sheet->mergeCells("B5:E5")->setCellValue('B5', 'NAMA AKUN');
        $sheet->setCellValue('F5', 'KELOMPOK');
        $sheet->setCellValue('G5', 'TIPE');
        // $sheet->setCellValue('H6', 'SALDO');
        // $sheet->setCellValue('F5', 'SALDO');

        $sheet->getStyle('A5:G5')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);
        $sheet->getStyle('A5:G5')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);
        // start row
        $sheetrow = 7;
        foreach ($data as $re) {
            $sheet->setCellValue('A' . $sheetrow, $re['head_number'] . '.00.000');
            $sheet->mergeCells("B" . $sheetrow . ":E" . $sheetrow)->setCellValue('B' . $sheetrow,   $re['name']);
            $sheet->setCellValue('F' . $sheetrow,  $re['nature']);
            // $sheet->setCellValue('G' . $sheetrow,  $re->type);
            $sheetrow++;
            foreach ($re['children'] as $re2) {
                $sheet->setCellValue('A' . $sheetrow, $re['head_number'] . '.' . $re2['head_number'] . '.000');
                $sheet->mergeCells("C" . $sheetrow . ":E" . $sheetrow)->setCellValue('C' . $sheetrow,   $re2['name']);
                $sheet->setCellValue('F' . $sheetrow,  $re2['nature']);
                $sheet->setCellValue('G' . $sheetrow,  $re2['type']);
                $sheetrow++;
                foreach ($re2['children'] as $re3) {
                    $sheet->setCellValue('A' . $sheetrow, $re['head_number'] . '.' . $re2['head_number'] . '.' . $re3['head_number']);
                    $sheet->mergeCells("D" . $sheetrow . ":E" . $sheetrow)->setCellValue('D' . $sheetrow,   $re3['name']);
                    $sheet->setCellValue('F' . $sheetrow,  $re2['nature']);
                    $sheet->setCellValue('G' . $sheetrow,  $re2['type']);
                    $sheetrow++;
                }
            }
        }
        // $this->AccountModel->chart_of_account($sheet);
        $writer = new Xlsx($spreadsheet);


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="bagan-akun.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // download file 
    }
}
