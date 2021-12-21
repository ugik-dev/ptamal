<?php
/*

*/
defined('BASEPATH') or exit('No direct script access allowed');
class Accounting extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('SecurityModel', 'Accounting_model', 'General_model'));
        // $this->load->helper(array('DataStructure'));
        $this->db->db_debug = TRUE;
    }

    // MultipleRoles
    public function index()
    {
        $this->accounts();
    }

    public function getAllBaganAkun()
    {
        try {
            $filter = $this->input->get();
            $accounts = $this->Accounting_model->getAllBaganAkun($filter);
            echo json_encode(array('error' => false, 'data' => $accounts));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function accounts()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('accounting', 'accounts', 'view');
            $data['title'] = 'Bagan Akun';
            $data['table_name'] = 'BAGAN AKUN';
            $data['main_view'] = 'accounting/accounts';
            $data['vcrud'] = $crud;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function addAccounts()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('accounting', 'accounts', 'create', true);
            $data = $this->input->post();
            $data['head_number'] =  preg_replace("/[^0-9]/", "", $data['head_number']);

            $accounts = $this->Accounting_model->addAccounts($data);
            $data = $this->Accounting_model->getAllBaganAkun(array('id' => $accounts, 'by_id' => true))[$accounts];


            echo json_encode(array('error' => false, 'data' => $data));
            // $this->load->view('accounting/accounts_modal');
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }



    public function editAccounts()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('accounting', 'accounts', 'update', true);
            $data = $this->input->post();
            $data['head_number'] =  preg_replace("/[^0-9]/", "", $data['head_number']);
            if (empty($data['type'])) $data['type'] = '';
            if (empty($data['expense_type'])) $data['expense_type'] = '';
            $accounts = $this->Accounting_model->editAccounts($data);
            $data = $this->Accounting_model->getAllBaganAkun(array('id' => $accounts, 'by_id' => true))[$accounts];
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function deleteAccounts()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('accounting', 'accounts', 'delete', true);
            $data = $this->input->get();
            $this->Accounting_model->deleteAccounts($data);
            // $data = $this->Accounting_model->getAllBaganAkun(array('id' => $accounts, 'by_id' => true))[$accounts];


            echo json_encode(array('error' => false, 'data' => $data));
            // $this->load->view('accounting/accounts_modal');
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    //  Jurnal Voucher
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
            $data['journals'] = $this->Accounting_model->getAllJournalVoucher(array('id' => $id))[0];
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




    public function journal_voucher()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('accounting', 'journal_voucher', 'create');
            $data['accounts'] = $this->Accounting_model->getAllBaganAkun(array('by_DataStructure' => true));
            $data['patner_record'] = $this->General_model->getAllPayee();
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

    public function edit_jurnal($id)
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('accounting', 'journal_voucher', 'update');
            $data['return_data'] = array();
            $data['return_data'] = $this->Accounting_model->getAllJournalVoucher(array('id' => $id))[0];
            if ($data['return_data']['generated_source'] != 'Journal Voucher') {
                if ($data['return_data']['generated_source'] == 'invoice') {
                    header("Refresh:0; url='" . base_url() . "production/search/?parent_1=" . $data['return_data']['parent_id']);
                    return;
                }
                header("Refresh:0; url='" . base_url() . "statement/journal");
                return;
            }
            $data['patner_record'] = $this->General_model->getAllPayee();
            $data['accounts'] = $this->Accounting_model->getAllBaganAkun(array('by_DataStructure' => true));
            $data['form_url'] = 'editJournalVoucher';
            $data['title'] = 'Edit Jurnal';
            $data['table_name'] = 'Edit Jurnal';
            $data['main_view'] = 'accounting/add_journal_voucher';
            $data['vcrud'] = $crud;
            // $data['filter'] = $filter;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


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
            $data['ref_number'] = $this->General_model->gen_number($data['date'], 'JV');
            $order_id = $this->Accounting_model->addJournalVoucher($data);

            echo json_encode(array('error' => false, 'data' => $order_id));
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

            $old_data = $this->Accounting_model->getAllJournalVoucher(array('id' => $data['id']))[0];
            // var_dump($old_data);
            // echo 'arrasdasda ';
            // if (substr($old_data['date'], 0, -3) != substr($data['date'], 0, -3))
            $data['ref_number'] = $this->General_model->gen_number($data, 'JV');
            // die();
            $accounts = $this->Accounting_model->editJournalVoucher($data);
            echo json_encode(array('error' => false, 'data' => $data['id']));
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

            header("Refresh:0; url='" . base_url() . "statement/journal");
            // $this->load->view('accounting/accounts_modal');
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    public function openning()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('accounting', 'openning', 'view');
            $data['accounts'] = $this->Accounting_model->getAllBaganAkun(array('by_DataStructure' => true));
            // $data['payment_method'] = $this->General_model->getAllOpenning(array('ref_type' => 'payment_method'));
            $data['title'] = 'List Openning Saldo';
            $data['main_view'] = 'accounting/openning';
            $data['vcrud'] = $crud;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function editOpenning()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('accounting', 'openning', 'update', true);
            $data = $this->input->post();
            if (!empty($data['amount'])) {
                $data['amount'] =
                    number_format(
                        str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['amount'])),
                        2,
                        '.',
                        ''
                    );
            } else {
                throw new UserException('Nilai nominal harus diisi!');
            }
            $this->Accounting_model->editOpenning($data);
            $data = $this->Accounting_model->getAllJournalVoucher(array('id' => $data['id'], 'by_id' => true))[$data['id']];
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function addOpenning()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('accounting', 'openning', 'create', true);
            $data = $this->input->post();
            if (!empty($data['amount'])) {
                $data['amount'] =
                    number_format(
                        str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['amount'])),
                        2,
                        '.',
                        ''
                    );
            } else {
                throw new UserException('Nilai nominal harus diisi!');
            }
            $insert_id = $this->Accounting_model->addOpenning($data);
            $data = $this->Accounting_model->getAllJournalVoucher(array('id' => $insert_id, 'by_id' => true))[$insert_id];
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function deleteOpenning()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('accounting', 'openning', 'delete', true);
            $data = $this->input->get();
            $this->Accounting_model->deleteOpenning($data);
            echo json_encode(array('error' => false, 'data' => ''));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    // END Jurnal Voucher
}
