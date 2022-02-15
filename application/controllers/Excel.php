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
        $this->load->model(array('SecurityModel', 'Accounting_model', 'General_model', 'Statment_model_new'));
        // $this->load->helper(array('DataStructure'));
        $this->db->db_debug = TRUE;
    }

    function bagan_akun()
    {
        $data = $this->General_model->getAllBaganAkun(array('by_DataStructure' => true));
        $company = Company_Profile();
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
    function buku_besar()
    {
        $filter = $this->input->get();
        if (empty($filter['search'])) $filter['search'] =  '';
        if (empty($filter['date_start'])) $filter['date_start'] = date('Y-01-' . '01');
        if (empty($filter['date_end'])) $filter['date_end'] = date('Y-m-' . date('t', strtotime($filter['date_start'])));
        $filter['by_DataStructure'] = true;
        $data['accounts'] = $this->General_model->getAllBaganAkun(array('by_DataStructure' => true));
        $data['journals'] = array();
        $data['journals'] = $this->General_model->getAllBaganAkun($filter);

        $render = $this->Statment_model_new->the_ledger($data['journals'], $filter);
        $company = Company_Profile();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.25);
        $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0);
        $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0);
        $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.75);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $spreadsheet->getActiveSheet()->getStyle('A6:F6')->getAlignment()->setHorizontal('center')->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('A6:F6')->getFont()->setSize(13)->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(13)->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:A4')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);

        $sheet->getStyle('D:F')->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

        $this->load->model('Statement_model');
        $sheet->mergeCells("A1:F1");
        $sheet->mergeCells("A2:F2");
        $sheet->mergeCells("A3:F3");
        $sheet->mergeCells("A4:F4");
        $sheet->setCellValue('A1', $company['title1']);
        $sheet->setCellValue('A2', $company['address'] . ' - ' . $company['town']);
        $sheet->setCellValue('A3', 'Buku Besar');
        $sheet->setCellValue('A4', 'Periode : ' . $filter['date_start'] . ' s.d. ' . $filter['date_end']);
        $spreadsheet->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);

        $sheet->setCellValue('A6', 'TANGGAL');
        $sheet->setCellValue('B6', 'JURNAL');
        $sheet->setCellValue('C6', 'KETERANGAN');
        $sheet->setCellValue('D6', 'DEBIT');
        $sheet->setCellValue('E6', 'KREDIT');
        $sheet->setCellValue('F6', 'SALDO');
        $sheetrow = 8;
        foreach ($render as $r) {
            $sheet->mergeCells("A" . $sheetrow . ":F" . $sheetrow);
            $sheet->setCellValue('A' . $sheetrow, $r['head_number'] . ' - ' . strtoupper($r['name']));
            $sheet->getRowDimension($sheetrow)->setRowHeight(20);
            $sheet->getStyle('A' . $sheetrow)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
            $sheet->getStyle('A' . $sheetrow)->getFont()->setSize(14)->setBold(true);
            $sheetrow++;
            foreach ($r['children'] as $r2) {
                $sheet->mergeCells("A" . $sheetrow . ":F" . $sheetrow);
                $sheet->setCellValue('A' . $sheetrow, '    [' . $r['head_number'] . '.' . $r2['head_number'] . '] - ' . strtoupper($r2['name']));
                $sheet->getRowDimension($sheetrow)->setRowHeight(19);
                $sheet->getStyle('A' . $sheetrow)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
                $sheet->getStyle('A' . $sheetrow)->getFont()->setSize(13)->setBold(true);
                $sheetrow++;
                if ($r2['open'])
                    foreach ($r2['children'] as $r3) {
                        if (!empty($r3['data']['transactions'])) {
                            $sheet->mergeCells("A" . $sheetrow . ":F" . $sheetrow);
                            $sheet->setCellValue('A' . $sheetrow, '         [' . $r['head_number'] . '.' . $r2['head_number'] . '.' . $r3['head_number'] . '] - ' . strtoupper($r3['name']));
                            $sheet->getRowDimension($sheetrow)->setRowHeight(18);
                            $sheet->getStyle('A' . $sheetrow)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
                            $sheet->getStyle('A' . $sheetrow)->getFont()->setSize(12)->setBold(true);
                            $sheetrow++;
                            $total_ledger = 0;
                            foreach ($r3['data']['transactions'] as $r4) {
                                $debitamount = '';
                                $creditamount = '';
                                $sheet->setCellValue('A' . $sheetrow, $r4['date']);
                                $sheet->setCellValue('B' . $sheetrow, $r4['ref_number']);
                                $sheet->setCellValue('C' . $sheetrow, $r4['naration']);
                                if ($r4['type'] == 0) {
                                    $debitamount = $r4['amount'];
                                    $sheet->setCellValue('D' . $sheetrow, $r4['amount']);
                                    $total_ledger = $total_ledger + $debitamount;
                                } else {
                                    $creditamount = $r4['amount'];
                                    $sheet->setCellValue('E' . $sheetrow, $r4['amount']);
                                    $total_ledger = $total_ledger - $creditamount;
                                }
                                $sheet->setCellValue('F' . $sheetrow, $total_ledger);
                                $sheetrow++;
                            }
                            $sheetrow++;
                        }
                    }
            }
        }


        $sheet->getHeaderFooter()->setOddFooter('&L' . $company['title1'] . ' - BUKU BESAR &R &P of &N');
        $writer = new Xlsx($spreadsheet);

        $filename = 'buku_besar_' . $filter['date_start'] . '_sd_' . $filter['date_end'] . '_' . $company['title1'];

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // download file 

    }
    function trail_balance()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('statement', 'trail_balance', 'view');
            $filter = $this->input->get();
            if (empty($filter['search'])) $filter['search'] =  '';
            if (empty($filter['date_start'])) $filter['date_start'] = '2021-01-01';
            if (empty($filter['date_end'])) $filter['date_end'] = date('Y-m-' . date('t', strtotime($filter['date_start'])));
            $filter['akum_laba_rugi'] = $this->General_model->getAllRefAccount(array('by_type' => true, 'ref_type' => 'akum_laba_rugi'));

            $data['journals'] = array();
            $filter['nature'] = array('Assets', 'Liability', 'Equity');
            $data['journals'] = $this->General_model->getAllBaganAkun($filter);
            $render = $this->Statment_model_new->trail_balance($data['journals'], $filter);
            $company = Company_Profile();

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.25);
            $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0);
            $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0);
            $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.75);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(5);
            $sheet->getColumnDimension('C')->setWidth(45);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            // $sheet->getColumnDimension('F')->setWidth(20);
            $spreadsheet->getActiveSheet()->getStyle('A6:E6')->getAlignment()->setHorizontal('center')->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('A6:E6')->getFont()->setSize(13)->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(13)->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A1:A4')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);

            $sheet->getStyle('D:E')->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

            $this->load->model('Statement_model');
            $sheet->mergeCells("A1:E1");
            $sheet->mergeCells("A2:E2");
            $sheet->mergeCells("A3:E3");
            $sheet->mergeCells("A4:E4");
            $sheet->mergeCells("A6:C6");
            $sheet->setCellValue('A1', $company['title1']);
            $sheet->setCellValue('A2', $company['address'] . ' - ' . $company['town']);
            $sheet->setCellValue('A3', 'Neraca Saldo');
            $sheet->setCellValue('A4', 'Periode : ' . $filter['date_start'] . ' s.d. ' . $filter['date_end']);
            $spreadsheet->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);

            $sheet->setCellValue('A6', 'AKUN');
            // $sheet->setCellValue('B6', 'JURNAL');
            // $sheet->setCellValue('C6', 'KETERANGAN');
            $sheet->setCellValue('D6', 'DEBIT');
            $sheet->setCellValue('E6', 'KREDIT');
            $sheetrow = 8;
            foreach ($render as $r) {
                $sheet->mergeCells("A" . $sheetrow . ":C" . $sheetrow);
                $sheet->setCellValue('A' . $sheetrow, $r['head_number'] . ' - ' . strtoupper($r['name']));
                $sheetrow++;
                foreach ($r['children'] as $r2) {
                    $sheet->mergeCells("B" . $sheetrow . ":C" . $sheetrow);
                    $sheet->setCellValue('B' . $sheetrow, '[' . $r['head_number'] . '.' . $r2['head_number'] . '] - ' . strtoupper($r2['name']));
                    if ($r2['data'] > 0)
                        $sheet->setCellValue('D' . $sheetrow, $r2['data']);
                    else if ($r2['data'] < 0)
                        $sheet->setCellValue('E' . $sheetrow, -$r2['data']);

                    $sheetrow++;

                    if ($r2['open'])
                        foreach ($r2['children'] as $r3) {
                            $sheet->setCellValue('C' . $sheetrow, '         [' . $r['head_number'] . '.' . $r2['head_number'] . '.' . $r3['head_number'] . '] - ' . strtoupper($r3['name']));
                            if ($r3['data'] > 0)
                                $sheet->setCellValue('D' . $sheetrow, $r3['data']);
                            else if ($r3['data'] < 0)
                                $sheet->setCellValue('E' . $sheetrow, -$r3['data']);
                            $sheetrow++;
                        }
                }
                $sheetrow++;
            }
            $sheet->getHeaderFooter()->setOddFooter('&L' . $company['title1'] . ' - NERACA SALDO &R &P of &N');
            $writer = new Xlsx($spreadsheet);

            $filename = 'Neraca_Saldo_' . $filter['date_start'] . '_sd_' . $filter['date_end'] . '_' . $company['title1'];

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output'); // download file 
            // echo json_encode($render);
            // $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    function income_statement()
    {
        try {
            // die();
            $crud = $this->SecurityModel->Aksessbility_VCRUD('statement', 'trail_balance', 'view');
            $filter = $this->input->get();
            if (empty($filter['search'])) $filter['search'] =  '';
            if (empty($filter['date_start'])) $filter['date_start'] = '2021-01-01';
            if (empty($filter['date_end'])) $filter['date_end'] = date('Y-m-' . date('t', strtotime($filter['date_start'])));

            $data['journals'] = array();
            $filter['nature'] = array('Expense', 'Revenue');
            $data['journals'] = $this->General_model->getAllBaganAkun($filter);
            $render = $this->Statment_model_new->trail_balance($data['journals'], $filter);
            $company = Company_Profile();

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.25);
            $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0);
            $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0);
            $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.75);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(5);
            $sheet->getColumnDimension('C')->setWidth(45);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            // $sheet->getColumnDimension('F')->setWidth(20);
            $spreadsheet->getActiveSheet()->getStyle('A6:E6')->getAlignment()->setHorizontal('center')->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('A6:E6')->getFont()->setSize(13)->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(13)->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('A1:A4')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);

            $sheet->getStyle('D:E')->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

            $this->load->model('Statement_model');
            $sheet->mergeCells("A1:E1");
            $sheet->mergeCells("A2:E2");
            $sheet->mergeCells("A3:E3");
            $sheet->mergeCells("A4:E4");
            $sheet->mergeCells("A6:E6");
            $sheet->setCellValue('A1', $company['title1']);
            $sheet->setCellValue('A2', $company['address'] . ' - ' . $company['town']);
            $sheet->setCellValue('A3', 'LABA RUGI');
            $sheet->setCellValue('A4', 'Periode : ' . $filter['date_start'] . ' s.d. ' . $filter['date_end']);
            $spreadsheet->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);

            $sheet->setCellValue('A6', 'AKUN');
            // $sheet->setCellValue('B6', 'JURNAL');
            // $sheet->setCellValue('C6', 'KETERANGAN');
            // $sheet->setCellValue('D6', 'DEBIT');
            // $sheet->setCellValue('E6', 'KREDIT');
            $sheetrow = 8;
            $tot_pendapatan = 0;
            $tot_beban = 0;
            foreach ($render as $r) {
                $sheet->mergeCells("A" . $sheetrow . ":C" . $sheetrow);

                $sheet->setCellValue('A' . $sheetrow, $r['head_number'] . ' - ' . strtoupper($r['name']));
                $sheetrow++;
                foreach ($r['children'] as $r2) {
                    $sheet->mergeCells("B" . $sheetrow . ":C" . $sheetrow);
                    $sheet->setCellValue('B' . $sheetrow, '[' . $r['head_number'] . '.' . $r2['head_number'] . '] - ' . strtoupper($r2['name']));
                    // if ($r2['data'] > 0)
                    // $sheet->setCellValue('E' . $sheetrow, $r2['data']);
                    // else if ($r2['data'] < 0)
                    //     $sheet->setCellValue('E' . $sheetrow, -$r2['data']);

                    $sheetrow++;

                    if ($r2['open'])
                        foreach ($r2['children'] as $r3) {
                            // if ($r3['data'] > 0)
                            $sheet->setCellValue('C' . $sheetrow, '         [' . $r['head_number'] . '.' . $r2['head_number'] . '.' . $r3['head_number'] . '] - ' . strtoupper($r3['name']));
                            if ($r['head_number'] == 4) {
                                $tot_pendapatan += (float)$r3['data'];
                                $sheet->setCellValue('D' . $sheetrow, -$r3['data']);
                            } else {
                                $sheet->setCellValue('D' . $sheetrow, $r3['data']);
                                $tot_beban += (float)$r3['data'];
                            }
                            // else if ($r3['data'] < 0)
                            //     $sheet->setCellValue('E' . $sheetrow, -$r3['data']);
                            $sheetrow++;
                        }
                    if ($r['head_number'] == 4) {
                        $sheet->setCellValue('E' . $sheetrow, -$r2['data']);
                    } else {
                        $sheet->setCellValue('E' . $sheetrow, $r2['data']);
                    }

                    // $sheet->setCellValue('E' . $sheetrow, $r2['data']);
                    $sheetrow++;
                }



                // $sheet->setCellValue('E' . $sheetrow, $r2['data']);
                $sheetrow++;
            }
            // if ($r['head_number'] == 4) {
            $sheet->setCellValue('A' . $sheetrow, 'TOTAL PENDAPATAN');
            $sheet->setCellValue('E' . $sheetrow, -$tot_pendapatan);
            $sheetrow++;
            $sheet->setCellValue('A' . $sheetrow, 'TOTAL BEBAN');
            $sheet->setCellValue('E' . $sheetrow, $tot_beban);
            $sheetrow++;

            $sheet->setCellValue('A' . $sheetrow, 'LABA / (RUGI)');
            $sheet->setCellValue('E' . $sheetrow, (-$tot_pendapatan) - $tot_beban);
            $sheetrow++;
            // } else {
            // $sheet->setCellValue('E' . $sheetrow, $r2['data']);
            // }
            // echo $tot_pendapatan;
            // echo $tot_beban;
            // die();
            $sheet->getHeaderFooter()->setOddFooter('&L' . $company['title1'] . ' - LAPORAN LABA RUGI &R &P of &N');
            $writer = new Xlsx($spreadsheet);

            $filename = 'LABA_RUGI_' . $filter['date_start'] . '_sd_' . $filter['date_end'] . '_' . $company['title1'];

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output'); // download file 
            // echo json_encode($render);
            // $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    function cash_flow()
    {
        $filter = $this->input->get();
        if (empty($filter['tahun'])) $filter['tahun'] = date('Y');
        if (empty($filter['bulan'])) $filter['bulan'] = date('m');
        for ($i = 1; $i <= $filter['bulan']; $i++) {
            $data[$i] = $this->Statment_model_new->cash_flow(array(
                'date_start' => $filter['tahun'] . '-' . $i . '-01',
                'tahun' => $filter['tahun'], 'bulan' => 1
            ));
        }

        // echo json_encode($data);
        // die();
        $company = Company_Profile();
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
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(5);
        $sheet->getColumnDimension('C')->setWidth(5);
        $sheet->getColumnDimension('D')->setWidth(5);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(13);
        $spreadsheet->getActiveSheet()->getStyle('A6:G6')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        // $spreadsheet->getActiveSheet()->getStyle('A99:G6')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        // $spreadsheet->getActiveSheet()->getStyle('A6:H6')->getFont()->setSize(13)->setBold(true);
        // $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(13)->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:A5')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);


        $spreadsheet->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 6);
        $sheet->getHeaderFooter()->setOddFooter('&L' . $company['title1'] . ' - CASH FLOW &R &P of &N');

        $sheet->setCellValue('A1', $company['title1']);
        $sheet->setCellValue('A2', $company['address'] . ' - ' . $company['town']);
        $sheet->setCellValue('A3', 'BAGAN AKUN');
        $spreadsheet->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 4);

        $namaBulan = array('-', "JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI", "AGUSTUS", "September", "Oktober", "November", "Desember");
        // $sheet->setCellValue('A5', 'KETERANGAN');
        $sheet->mergeCells("A5:E5")->setCellValue('A5', 'KETERANGAN');
        $sheet->mergeCells("F5:G5")->setCellValue('F5', 'NOMINAL');
        // $sheet->mergeCells("A7:E7")->setCellValue('A5', 'KETERANGAN');

        $sheet->mergeCells("A7:E7")->setCellValue('A7', 'ARUS KAS DARI AKTIVITAS OPERASI');
        $sheet->mergeCells("B8:E8")->setCellValue('B8', 'Pendapatan Usaha');
        $sheet->mergeCells("B9:E9")->setCellValue('B9', 'Pendapatan Bank');
        $sheet->mergeCells("B10:E10")->setCellValue('B10', 'Pendapatan Lainnya');
        $sheet->mergeCells("B11:E11")->setCellValue('B11', 'Pengeluaran HPP');
        $sheet->mergeCells("B12:E12")->setCellValue('B12', 'Pengeluaran Biaya General Adm');
        $sheet->mergeCells("B13:E13")->setCellValue('B13', 'Pengeluaran Pajak');
        $sheet->mergeCells("B14:E14")->setCellValue('B14', '-- TOTAL --');

        $sheet->mergeCells("A16:E16")->setCellValue('A16', 'ARUS KAS DARI AKTIVITAS PENDANAAN');
        $sheet->mergeCells("B17:E17")->setCellValue('B17', 'Pinjaman Modal');
        $sheet->mergeCells("B18:E18")->setCellValue('B18', '-- TOTAL --');

        $sheet->mergeCells("A20:E20")->setCellValue('A20', 'Kenaikkan KAS');
        $sheet->mergeCells("A21:E21")->setCellValue('A21', 'Kas Periode Sebelumnya');
        $sheet->mergeCells("A22:E22")->setCellValue('A22', 'Kas Periode Sekarang');

        // $asci = 'E';
        // echo ord($asci);
        for ($i = 1; $i <= $filter['bulan']; $i++) {
            $mat1 = (($i - 1) * 2);
            $row_1 = chr(ord('F') + $mat1);
            $row_2 = chr(ord('F') + $mat1 + 1);
            $sheet->mergeCells($row_1 . "5:" . $row_2 . "5")->setCellValue($row_1 . '5', $namaBulan[$i]);
            $sheet->getColumnDimension($row_1)->setWidth(18);
            $sheet->getColumnDimension($row_2)->setWidth(18);
            $spreadsheet->getActiveSheet()->getStyle($row_1 . "5:" . $row_2 . "5")->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);

            // $row_1 = chr(ord('E'));  
            // echo $row_1;
            // echo $row_2;
            $current_data = $this->Statment_model_new->cash_flow(array(
                'date_start' => $filter['tahun'] . '-' . $i . '-01',
                'tahun' => $filter['tahun'], 'bulan' => $i
            ));
            if (!empty($current_data)) {
                $sheet->setCellValue($row_1 . "8", $current_data['in_usaha']);
                $sheet->setCellValue($row_1 . "9", $current_data['in_bank']);
                $sheet->setCellValue($row_1 . "10", $current_data['in_dll']);
                $sheet->setCellValue($row_1 . "11", $current_data['out_usaha']);
                $sheet->setCellValue($row_1 . "12", $current_data['out_general']);
                $sheet->setCellValue($row_1 . "13", $current_data['out_pajak']);
                $sheet->setCellValue($row_2 . "14", $current_data['total']['operasi']);

                $sheet->setCellValue($row_1 . "16", $current_data['inves_pinjaman']);
                $sheet->setCellValue($row_2 . "17", $current_data['total']['inves']);

                $sheet->setCellValue($row_2 . "20", $current_data['total']['all']);
                $sheet->setCellValue($row_2 . "21", $current_data['total']['saldo_sebelum']);
                $sheet->setCellValue($row_2 . "22", $current_data['total']['all'] + $current_data['total']['saldo_sebelum']);
            }
        }

        $sheet->mergeCells("A1:" . $row_2 . "1");
        $sheet->mergeCells("A2:" . $row_2 . "2");
        $sheet->mergeCells("A3:" . $row_2 . "3");
        $sheet->mergeCells("A4:" . $row_2 . "4");
        $sheet->getStyle('F:' . $row_2)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
        $writer = new Xlsx($spreadsheet);


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="cash-flow-' . $filter['tahun'] . '-' . $namaBulan[$filter['bulan']] . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // download file 

    }
}
