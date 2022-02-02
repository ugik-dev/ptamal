<?php
/*

*/
defined('BASEPATH') or exit('No direct script access allowed');
class Statement extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('SecurityModel', 'Accounting_model', 'Statment_model_new', 'General_model'));
        // $this->load->helper(array('DataStructure'));
        $this->db->db_debug = TRUE;
    }

    // MultipleRoles
    public function index()
    {
        $this->journal();
    }
    public function getAllJournalVoucher()
    {
        try {
            $filter = $this->input->get();
            $accounts = $this->Accounting_model->getAllJournalVoucher($filter);
            echo json_encode(array('error' => false, 'data' => $accounts));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function show_journal($id)
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('accounting', 'journal_voucher', 'view');

            $data['journals'] = array();
            $data['journals'] = $this->Accounting_model->getAllJournalVoucher(array('id' => $id))[$id];
            // echo json_encode($data);
            // die();
            $data['title'] = 'Jurnal Umum';
            $data['table_name'] = 'Jurnal Umum';
            $data['main_view'] = 'accounting/detail_journal_voucher';
            $data['vcrud'] = $crud;
            // $data['filter'] = $filter;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function ledger()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('statement', 'journal', 'view');
            $filter = $this->input->get();
            // $filter['by_DataStructure'] = true;
            if (empty($filter['search'])) $filter['search'] =  '';
            if (empty($filter['date_start'])) $filter['date_start'] = date('Y-01-' . '01');
            if (empty($filter['date_end'])) $filter['date_end'] = date('Y-m-' . date('t', strtotime($filter['date_start'])));
            $filter['by_DataStructure'] = true;
            $data['accounts'] = $this->General_model->getAllBaganAkun(array('by_DataStructure' => true));
            $data['journals'] = array();
            $data['journals'] = $this->General_model->getAllBaganAkun($filter);

            // echo json_encode($data['journals']);
            // die();
            // $data['journals'] = $this->Accounting_model->getAllBaganAkun($filter);
            $data['journals'] = $this->Statment_model_new->the_ledger($data['journals'], $filter);
            $data['title'] = 'Jurnal Umum';
            $data['table_name'] = 'Jurnal Umum';
            $data['main_view'] = 'statement/ledger';
            $data['vcrud'] = $crud;
            $data['filter'] = $filter;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function trail_balance()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('statement', 'trail_balance', 'view');
            $filter = $this->input->get();
            // $filter['by_DataStructure'] = true;
            if (empty($filter['search'])) $filter['search'] =  '';
            // if (empty($filter['date_start'])) $filter['date_start'] = date('Y-m-' . '01');
            if (empty($filter['date_start'])) $filter['date_start'] = '2021-01-01';
            if (empty($filter['date_end'])) $filter['date_end'] = date('Y-m-' . date('t', strtotime($filter['date_start'])));
            $filter['akum_laba_rugi'] = $this->General_model->getAllRefAccount(array('by_type' => true, 'ref_type' => 'akum_laba_rugi'));
            $data['journals'] = array();
            $filter['nature'] = array('Assets', 'Liability', 'Equity');
            $data['journals'] = $this->Accounting_model->getAllBaganAkun($filter);
            $data['journals'] = $this->Statment_model_new->trail_balance($data['journals'], $filter);
            // echo json_encode($data['journals']['3']);
            // die();
            $data['title'] = 'Jurnal Umum';
            $data['table_name'] = 'Jurnal Umum';
            $data['main_view'] = 'statement/trail_balance';
            $data['vcrud'] = $crud;
            $data['filter'] = $filter;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    public function income_statement()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('statement', 'income_statement', 'view');
            $filter = $this->input->get();
            // $filter['by_DataStructure'] = true;
            if (empty($filter['search'])) $filter['search'] =  '';
            if (empty($filter['date_start'])) $filter['date_start'] = date('Y-01-' . '01');
            if (empty($filter['date_end'])) $filter['date_end'] = date('Y-m-' . date('t', strtotime($filter['date_start'])));
            $filter['nature'] = array('Expense', 'Revenue');

            $data['journals'] = array();
            $data['journals'] = $this->Accounting_model->getAllBaganAkun($filter);
            $data['journals'] = $this->Statment_model_new->trail_balance($data['journals'], $filter);
            $data['income'] = $this->Statment_model_new->income($filter);

            $data['title'] = 'Jurnal Umum';
            $data['table_name'] = 'Jurnal Umum';
            $data['main_view'] = 'statement/income';
            $data['vcrud'] = $crud;
            $data['filter'] = $filter;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    public function journal()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('statement', 'journal', 'view');
            $filter = $this->input->get();
            // $filter['ref_number'] = '';
            // $filter['search'] = '';
            // $filter['date_start'] = '';
            if (empty($filter['from']))
                $filter['from'] = date('Y-01-' . '01');
            if (empty($filter['to']))
                $filter['to'] = date('Y-m-' . date('t', strtotime($filter['from'])));
            $data['journals'] = array();
            $data['journals'] = $this->Accounting_model->getAllJournalVoucher($filter);
            // echo json_encode($filter);
            // die();

            $data['title'] = 'Jurnal Umum';
            $data['table_name'] = 'Jurnal Umum';
            $data['main_view'] = 'accounting/journal_voucher';
            $data['vcrud'] = $crud;
            $data['filter'] = $filter;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function journal_voucher()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('accounting', 'journal_voucher', 'create');
            $data['accounts'] = $this->Accounting_model->getAllBaganAkun(array('by_DataStructure' => true));
            $data['title'] = 'Buat Jurnal';
            $data['table_name'] = 'Buat Jurnal Umum';
            $data['main_view'] = 'accounting/add_journal_voucher';
            $data['form_url'] = 'addJournalVoucher';
            $data['vcrud'] = $crud;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    // public function edit_jurnal($id)
    // {
    //     try {
    //         $crud = $this->SecurityModel->Aksessbility_VCRUD('accounting', 'journal_voucher', 'update');

    //         $data['return_data'] = array();
    //         $data['return_data'] = $this->Accounting_model->getAllJournalVoucher(array('id' => $id))[$id];
    //         $data['accounts'] = $this->Accounting_model->getAllBaganAkun(array('by_DataStructure' => true));
    //         $data['form_url'] = 'editJournalVoucher';
    //         $data['title'] = 'Edit Jurnal';
    //         $data['table_name'] = 'Edit Jurnal';
    //         $data['main_view'] = 'accounting/add_journal_voucher';
    //         $data['vcrud'] = $crud;
    //         // $data['filter'] = $filter;
    //         $this->load->view('main/index2.php', $data);
    //     } catch (Exception $e) {
    //         ExceptionHandler::handle($e);
    //     }
    // }


    public function addJournalVoucher()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('accounting', 'journal_voucher', 'create', true);
            $data = $this->input->post();
            $deb = (float) 0;
            $kred = (float) 0;
            $i = 0;
            foreach ($data['account_head'] as $h) {
                if (!empty($data['debitamount'][$i])) {
                    $cur_debit =
                        number_format(
                            str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['debitamount'][$i])),
                            2,
                            '.',
                            ''
                        );
                    $data['debitamount'][$i] = $cur_debit;
                } else {
                    $cur_debit = 0;
                    $data['debitamount'][$i] = 0;
                }
                if (!empty($data['creditamount'][$i])) {
                    $cur_kredit =
                        number_format(
                            str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['creditamount'][$i])),
                            2,
                            '.',
                            ''
                        );
                    $data['creditamount'][$i] = $cur_kredit;
                } else {
                    $cur_kredit = 0;
                    $data['creditamount'][$i] = 0;
                }
                if ($cur_debit > 0) {
                    $deb = $deb + $cur_debit;
                } else if ($cur_kredit  > 0) {
                    $kred = $kred + $cur_kredit;
                }
                $i++;
            }
            if ($deb != $kred) {
                throw new UserException('Nilai debit dan kredit tidak imbang!');
            }
            $data['ref_number'] = $this->Accounting_model->gen_number($data);
            $accounts = $this->Accounting_model->addJournalVoucher($data);

            echo json_encode(array('error' => false, 'data' => $data));
            // $this->load->view('accounting/accounts_modal');
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function editJournalVoucher()
    {
        try {
            $data = $this->input->post();
            $deb = (float) 0;
            $kred = (float) 0;
            $i = 0;
            foreach ($data['account_head'] as $h) {
                if (!empty($data['debitamount'][$i])) {
                    $cur_debit =
                        number_format(
                            str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['debitamount'][$i])),
                            2,
                            '.',
                            ''
                        );
                    $data['debitamount'][$i] = $cur_debit;
                } else {
                    $cur_debit = 0;
                    $data['debitamount'][$i] = 0;
                }
                if (!empty($data['creditamount'][$i])) {
                    $cur_kredit =
                        number_format(
                            str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['creditamount'][$i])),
                            2,
                            '.',
                            ''
                        );
                    $data['creditamount'][$i] = $cur_kredit;
                } else {
                    $cur_kredit = 0;
                    $data['creditamount'][$i] = 0;
                }
                if ($cur_debit > 0) {
                    $deb = $deb + $cur_debit;
                } else if ($cur_kredit  > 0) {
                    $kred = $kred + $cur_kredit;
                }
                $i++;
            }
            if ($deb != $kred) {
                throw new UserException('Nilai debit dan kredit tidak imbang!');
            }
            $accounts = $this->Accounting_model->editJournalVoucher($data);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function delete_jurnal($id)
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('accounting', 'journal_voucher', 'delete', true);
            // $data = $this->input->get();
            $data['id'] = $id;
            $this->Accounting_model->deleteJournalVoucher($data);
            // $data = $this->Accounting_model->getAllBaganAkun(array('id' => $accounts, 'by_id' => true))[$accounts];


            header("Refresh:0; url='" . base_url() . "accounting/journal_voucher");
            // $this->load->view('accounting/accounts_modal');
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }



    // END Jurnal Voucher
}
