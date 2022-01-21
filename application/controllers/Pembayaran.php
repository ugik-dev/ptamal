<?php
/*

*/
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\Writer\Word2007;

class Pembayaran extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('General_model', 'PembayaranModel', 'Statement_model', 'PembayaranModel'));
        // $this->load->helper(array('DataStructure'));
        $this->db->db_debug = TRUE;
    }


    function index($data_return = NULL)
    {


        $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', '', 'create');
        $data['vcrud'] = $crud;

        $this->load->model('Crud_model');

        $data['currency'] = $this->Crud_model->fetch_record_by_id('mp_langingpage', 1)[0]->currency;

        //$ledger
        $from = html_escape($this->input->post('from'));
        $to   = html_escape($this->input->post('to'));

        if ($from == NULL or $to == NULL) {

            $from = date('Y-m-') . '1';
            $to =  date('Y-m-') . '31';
        }
        $this->load->model('Accounts_model');

        // $data['banks'] = $this->Accounts_model->getAllBank();
        // DEFINES PAGE TITLE
        $data['title'] = 'Entry Pembayaran';
        $data['data_return'] = $data_return;
        $this->load->model('Statement_model');
        // $data['accounts_records'] = $this->Statement_model->chart_list();
        $data['patner_record'] = $this->Statement_model->patners_cars_list();
        $data['jenis_pembayaran'] = $this->General_model->getAllJenisInvoice();
        $data['approval_users'] = $this->General_model->getAprovalUsers();

        $data['satuan'] = $this->General_model->getAllUnit();
        $data['ref_account'] = $this->General_model->getAllRefAccount(array('ref_type' => 'payment_method'));
        $data['form_url'] = 'create_pembayaran';

        // echo json_encode($data);
        // die();
        // DEFINES WHICH PAGE TO RENDER
        $data['main_view'] = 'pembayaran/form_pembayaran';

        // DEFINES GO TO MAIN FOLDER FOND INDEX.PHP  AND PASS THE ARRAY OF DATA TO THIS PAGE
        $this->load->view('main/index.php', $data);
    }


    public function delete($id)
    {

        $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', '', 'delete');
        $data['vcrud'] = $crud;
        $this->load->model(array('SecurityModel', 'PembayaranModel'));
        $dataContent = $this->PembayaranModel->getAllPembayaran(array('id' =>  $id))[0];
        $dataContent['data_pelunasan'] = $this->PembayaranModel->getAllPelunasan(array('parent_id' => $id));

        if ($dataContent['agen_id'] != $this->session->userdata('user_id')['id'])
            throw new UserException('Sorry, Yang dapat mengahapus dan edit hanya agen yang bersangkutan', UNAUTHORIZED_CODE);

        $this->PembayaranModel->delete($id, $dataContent);
        $array_msg = array(
            'msg' => '<i style="color:#fff" class="fa fa-check-circle-o" aria-hidden="true"></i> Delete Successfully',
            'alert' => 'info'
        );
        $this->session->set_flashdata('status', $array_msg);
        // $this->index($data);
        // return;
        redirect('pembayaran/history');
    }
    public function history()
    {
        $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', '', 'view');
        $data['vcrud'] = $crud;
        // DEFINES PAGE TITLE
        $data['title'] = 'Pembayaran';

        $collection = array();

        // DEFINES TO LOAD THE MODEL
        $this->load->model('Accounts_model');
        $filter['first_date'] = html_escape($this->input->get('date1'));
        $filter['second_date'] = html_escape($this->input->get('date2'));
        $filter['no_pembayaran'] = html_escape($this->input->get('pembayaran_no'));

        if ($filter['first_date'] == NULL && $filter['second_date'] == NULL) {
            $filter['first_date'] = date('Y-m-01');
            $filter['second_date'] = date('Y-m-31');

            // FETCH SALES RECORD FROM pembayarans TABLE
            // $result_pembayarans = $this->Accounts_model->get('mp_pembayarans', $first_date, $second_date);
        }
        $data['filter'] = $filter;
        $this->load->model(array('PembayaranModel'));
        // $this->SecurityModel->rolesOnlyGuard(array('accounting'), TRUE);

        $result_pembayarans = $this->PembayaranModel->getAllPembayaranWithItem($filter);
        // echo json_encode($result_pembayarans);
        // die();
        $data['Model_Title'] = "Edit pembayaran";
        $data['Model_Button_Title'] = "Update pembayarans";
        $data['pembayarans_Record'] = $result_pembayarans;

        $data['main_view'] = 'pembayaran/list_pembayaran';
        $this->load->view('main/index.php', $data);
    }

    public function edit($id)
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', '', 'update');
            $data['vcrud'] = $crud;
            $data['approval_users'] = $this->General_model->getAprovalUsers();

            $this->load->model(array('SecurityModel', 'PembayaranModel'));

            $dataContent = $this->PembayaranModel->getAllPembayaranWithItem(array('id' =>  $id))[0];
            if ($dataContent['agen_id'] != $this->session->userdata('user_id')['id'])
                throw new UserException('Sorry, Yang dapat mengahapus dan edit hanya agen yang bersangkutan', UNAUTHORIZED_CODE);
            if ($id != NULL) {
                $i = 0;
                foreach ($dataContent['items'] as $item) {

                    // die();
                    // for ($i = 0; $i < $item; $i++) {
                    // if (!empty($data['amount'][$i]) && !empty($data['qyt'][$i]))
                    // 	$status = TRUE;
                    $dataContent['id_item'][$i] =  $item['item_id'];
                    $dataContent['amount'][$i] =  $item['amount'];
                    $dataContent['satuan'][$i] =  $item['satuan'];
                    $dataContent['qyt'][$i] =  $item['qyt'];
                    $dataContent['keterangan_item'][$i] =  $item['keterangan_item'];
                    $i++;
                    // $dataContent['date_item'][$i] =  $dataContent['item'][$i]->date_item;
                    // $dataContent['satuan'][$i] =  $dataContent['item'][$i]->satuan;

                    // $dataContent['keterangan_item'][$i] =  $dataContent['item'][$i]->keterangan_item;
                    // $dataContent['qyt'][$i] =  $dataContent['item'][$i]->qyt;
                    // $dataContent['qyt'][$i] = preg_replace("/[^0-9]/", "", $dataContent['item'][$i]->qyt);
                }
            } else {
                echo 'ngapain cok';
                return;
            }
            // echo json_encode($item);
            // echo json_encode($dataContent);
            // die();
            // $this->index($dataContent);
            $this->load->model('Crud_model');

            $data['currency'] = $this->Crud_model->fetch_record_by_id('mp_langingpage', 1)[0]->currency;

            $this->load->model('Accounts_model');

            $data['banks'] = $this->Accounts_model->getAllBank();
            // DEFINES PAGE TITLE
            $data['title'] = 'Edit Jurnal';
            $data['data_return'] = $dataContent;
            $this->load->model('Statement_model');
            // $data['accounts_records'] = $this->Statement_model->chart_list();
            $data['patner_record'] = $this->Statement_model->patners_cars_list();
            $data['jenis_pembayaran'] = $this->General_model->getAllJenisInvoice();
            $data['approval_users'] = $this->General_model->getAprovalUsers();

            $data['satuan'] = $this->General_model->getAllUnit();
            $data['ref_account'] = $this->General_model->getAllRefAccount(array('ref_type' => 'payment_method'));

            $data['form_url'] = 'edit_process_pembayaran';
            // DEFINES WHICH PAGE TO RENDER
            $data['main_view'] = 'pembayaran/form_pembayaran';

            // DEFINES GO TO MAIN FOLDER FOND INDEX.PHP  AND PASS THE ARRAY OF DATA TO THIS PAGE
            $this->load->view('main/index.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function copy($id)
    {
        $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', '', 'create');
        $data['vcrud'] = $crud;
        $this->load->model(array('SecurityModel', 'PembayaranModel'));
        if ($id != NULL) {
            $dataContent = $this->PembayaranModel->getAllPembayaranWithItem(array('id' =>  $id))[0];
            $dataContent['id'] = '';
            $i = 0;
            foreach ($dataContent['items'] as $item) {
                // for ($i = 0; $i < $item; $i++) {
                // if (!empty($data['amount'][$i]) && !empty($data['qyt'][$i]))
                //     $status = TRUE;
                $dataContent['id_item'][$i] =  '';
                $dataContent['amount'][$i] =  $item['amount'];
                $dataContent['satuan'][$i] =  $item['satuan'];
                $dataContent['qyt'][$i] =  $item['qyt'];
                $dataContent['keterangan_item'][$i] =  $item['keterangan_item'];
                $i++;    // $dataContent['keterangan_item'][$i] =  $dataContent['item'][$i]->keterangan_item;
                // $dataContent['satuan'][$i] =  $dataContent['item'][$i]->satuan;

                // $dataContent['qyt'][$i] =  $dataContent['item'][$i]->qyt;
                // $dataContent['qyt'][$i] = preg_replace("/[^0-9]/", "", $dataContent['item'][$i]->qyt);
                $i++;
            }
        } else {
            echo 'ngapain cok';
            return;
        }
        // echo json_encode($item);
        // echo json_encode($dataContent);
        // die();
        $this->index($dataContent);
    }


    function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai / 10) . " Puluh" . $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai / 100) . " Ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai / 1000) . " Ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai / 1000000) . " Juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai / 1000000000) . " Milyar" . $this->penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai / 1000000000000) . " Trilyun" . $this->penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    function terbilang($nilai)
    {
        if ($nilai < 0) {
            $hasil = "minus " . trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }
        return $hasil;
    }

    function tanggal_indo($tanggal)
    {
        $bulan = array(
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }
    public function show($pembayaran_no)
    {

        $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', '', 'view');
        $data['vcrud'] = $crud;
        // DEFINES PAGE TITLE
        $data['title'] = 'Pembayaran';

        $collection = array();

        // DEFINES TO LOAD THE MODEL
        $this->load->model('PembayaranModel');
        if ($pembayaran_no != NULL) {
            $result = $this->PembayaranModel->getAllPembayaranWithItem(array('id' =>  $pembayaran_no));


            if (empty($result)) {
                $data['main_view'] = 'error-5';
                $data['message'] = 'Sepertinya data yang anda cari tidak ditemukan atau sudah di hapus.';
            } else {
                $data['dataContent'] = $result[0];
                $data['main_view'] = 'pembayaran/pembayaran_detail';
                $data['payment_metode'] = $this->General_model->getAllRefAccount(array('ref_id' => $data['dataContent']['payment_metode']))[0];
                $data['customer_data'] = $this->General_model->getAllPayee(array('id' => $data['dataContent']['customer_id']));
                $data['ref_account'] = $this->General_model->getAllRefAccount(array('ref_type' => 'payment_method'));
                $data['accounts'] = $this->General_model->getAllBaganAkun(array('by_DataStructure' => true));
            }
            // echo json_encode($data);
            // die();

            // DEFINES GO TO MAIN FOLDER FOND INDEX.PHP  AND PASS THE ARRAY OF DATA TO THIS PAGE
            $this->load->view('main/index.php', $data);
            return;
        } else {
            echo 'NOT FOUND';
            return;
        }
    }


    //pembayaran/popup
    //DEFINES A POPUP MODEL OG GIVEN PARAMETER






    function create_pembayaran()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', '', 'create');
            $data['vcrud'] = $crud;
            $status = FALSE;
            $data = $this->input->post();
            if (empty($data['manual_math'])) {
                $data['manual_math'] = 'off';
            }
            if ($data['manual_math'] == 'on') {
                $data['manual_math'] = 1;
            } else {
                $data['manual_math'] = 0;
            }

            $count_rows = count($data['amount']);
            // if (empty($data['ppn_pph'])) {
            //     $data['ppn_pph'] = '0';
            // } else {
            //     $data['ppn_pph'] = '1';
            // }

            $data['sub_total'] = substr(preg_replace("/[^0-9]/", "", $data['sub_total']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['sub_total']), -2);
            $data['sub_total_2'] = substr(preg_replace("/[^0-9]/", "", $data['sub_total_2']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['sub_total_2']), -2);
            $data['payed'] = substr(preg_replace("/[^0-9]/", "", $data['payed']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['payed']), -2);
            // if ($data['ppn_pph'] == '1') $data['ppn_pph_count'] = substr(preg_replace("/[^0-9]/", "", $data['ppn_pph_count']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['ppn_pph_count']), -2);
            $data['total_final'] = substr(preg_replace("/[^0-9]/", "", $data['total_final']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['total_final']), -2);

            if (empty($data['date'])) {
                $data['date'] = date('Y-m-d');
            }
            if (empty($data['date2'])) {
                $data['date2'] = $data['date'];
            }

            for ($i = 0; $i < $count_rows; $i++) {
                if (!empty($data['amount'][$i]) && !empty($data['qyt'][$i]))
                    $status = TRUE;
                $data['amount'][$i] = preg_replace("/[^0-9]/", "", $data['amount'][$i]);
            }

            $data['no_pembayaran'] = $this->PembayaranModel->getLastNoPembayaran() + 1;
            // echo $data['no_pembayaran'];
            // die();

            if ($status) {
                $this->load->model('Transaction_model');
                $this->load->model('Crud_model');
                $jp = $this->General_model->getAllJenisInvoice(array('by_id' => true, 'id' => $data['jenis_pembayaran']))[$data['jenis_pembayaran']];
                // $data['jp'] = $jp;
                // if ($data['ppn_pph'] == 1) {
                //     $data['generalentry_ppn'] = array(
                //         'date' => $data['date2'],
                //         'customer_id' => $data['customer_id'],
                //         'generated_source' => 'pembayaran_ppn'
                //     );
                //     $data['generalentry_ppn']['ref_number'] = $this->General_model->gen_number($data['date2'], 'JU');

                //     $data['sub_entry_ppn'][0] = array(
                //         'accounthead' => $jp['ac_ppn_piut'],
                //         'type' => 0,
                //         'sub_keterangan' => 'Piut WAPU PPN ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
                //         'amount' => $data['ppn_pph_count'],
                //     );
                //     $data['sub_entry_ppn'][1] = array(
                //         'accounthead' => $jp['ac_ppn'],
                //         'type' => 1,
                //         'sub_keterangan' => 'PPN ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
                //         'amount' => $data['ppn_pph_count'],
                //     );
                // }

                $data['generalentry'] = array(
                    'date' => $data['date'],
                    'customer_id' => $data['customer_id'],
                    'naration' => 'Tagihan ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
                    'generated_source' => 'pembayaran'
                );
                $data['generalentry']['ref_number'] = $this->General_model->gen_number($data['date'], $jp['ref_nojur_pembayaran']);
                $i = 0;
                $data['status_pembayaran'] = 'unpaid';
                // NEW CODE
                // echo json_encode($data);
                // die();
                $data['sub_entry'][$i] = array(
                    'accounthead' => $jp['ac_hutang'],
                    'type' => 1,
                    // 'sub_keterangan' => 'Htg ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
                    'amount' => $data['sub_total_2'],
                );
                $i++;
                $data['sub_entry'][$i] = array(
                    'accounthead' => $jp['ac_expense'],
                    'type' => 0,
                    // 'sub_keterangan' => 'Pdpt ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
                    'amount' => $data['sub_total_2'],
                );
                $i++;
                // if ($data['ppn_pph'] == '1') {
                // echo json_encode($data);
                // die();

                $result = $this->PembayaranModel->pembayaran_entry($data);
            } else {
                throw new UserException('Please check data!');
            }
            echo json_encode(array('error' => false, 'data' => $result['order_id']));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    function make_journal($data, $jp, $ref)
    {
        $data['generalentry'] = array(
            'date' => $data['date'],
            'customer_id' => $data['customer_id'],
            'naration' => 'Tagihan ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
            'generated_source' => 'pembayaran'
        );
        $i = 0;
        $data['sub_entry'][$i] = array(
            'accounthead' => $jp['ac_hutang'],
            'type' => 1,
            // 'sub_keterangan' => 'Htg ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
            'amount' => $data['sub_total_2'],
        );
        $i++;
        $data['sub_entry'][$i] = array(
            'accounthead' => $jp['ac_expense'],
            'type' => 0,
            // 'sub_keterangan' => 'Pdpt ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
            'amount' => $data['sub_total_2'],
        );
        $i++;

        return $data;
    }
    function edit_process_pembayaran()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', '', 'update');
            $data['vcrud'] = $crud;
            $status = FALSE;
            $data = $this->input->post();
            if (empty($data['manual_math'])) {
                $data['manual_math'] = 'off';
            }
            if ($data['manual_math'] == 'on') {
                $data['manual_math'] = 1;
            } else {
                $data['manual_math'] = 0;
            }

            $count_rows = count($data['amount']);
            // if (empty($data['ppn_pph'])) {
            //     $data['ppn_pph'] = '0';
            // } else {
            //     $data['ppn_pph'] = '1';
            // }

            $data['sub_total'] = substr(preg_replace("/[^0-9]/", "", $data['sub_total']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['sub_total']), -2);
            $data['sub_total_2'] = substr(preg_replace("/[^0-9]/", "", $data['sub_total_2']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['sub_total_2']), -2);
            $data['payed'] = substr(preg_replace("/[^0-9]/", "", $data['payed']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['payed']), -2);
            // if ($data['ppn_pph'] == '1') $data['ppn_pph_count'] = substr(preg_replace("/[^0-9]/", "", $data['ppn_pph_count']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['ppn_pph_count']), -2);
            $data['total_final'] = substr(preg_replace("/[^0-9]/", "", $data['total_final']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['total_final']), -2);


            if (empty($data['date'])) {
                $data['date'] = date('Y-m-d');
            }

            for ($i = 0; $i < $count_rows; $i++) {
                if (!empty($data['amount'][$i]) && !empty($data['qyt'][$i]))
                    $status = TRUE;
                $data['amount'][$i] = preg_replace("/[^0-9]/", "", $data['amount'][$i]);
            }

            if ($status) {
                $this->load->model('Transaction_model');
                $this->load->model('Crud_model');

                $data['old_data'] = $this->PembayaranModel->getAllPembayaran(array('id' => $data['id'], 'by_id' => true))[$data['id']];

                $jp = $this->General_model->getAllJenisInvoice(array('by_id' => true, 'id' => $data['old_data']['jenis_pembayaran']))[$data['old_data']['jenis_pembayaran']];
                $ref = $this->General_model->getAllRefAccount(array('by_id' => true, 'ref_id' => $data['payment_method']))[$data['payment_method']];
                $data['generalentry_old'] = $this->General_model->getAllGeneralentry(array('id' => $data['old_data']['general_id'], 'by_id' => true))[$data['old_data']['general_id']];

                $data['status_pembayaran'] = 'unpaid';
                $data['data_pelunasan'] = $this->General_model->getAllPelunasanPembayaran(array('parent_id' => $data['id']));
                $total_pelunasan = 0;
                foreach ($data['data_pelunasan'] as $dp) {
                    $total_pelunasan = $total_pelunasan + $dp['sum_child'];
                }
                if ($total_pelunasan >= $data['sub_total_2'])
                    $data['status_pembayaran'] = 'paid';

                $journal = $this->make_journal($data, $jp, $ref);
                $data['generalentry'] = $journal['generalentry'];
                $data['sub_entry'] = $journal['sub_entry'];

                if (substr($data['generalentry_old']['date'], 0, -3) != substr($data['date'], 0, -3))
                    $data['generalentry']['ref_number'] = $this->General_model->gen_number($data['date'], $jp['ref_nojur_pembayaran']);
                $i = 0;
                $this->PembayaranModel->pembayaran_edit($data);
                // echo json_encode($data);
                // die();
            } else {
                throw new UserException('Please check data!');
            }
            echo json_encode(array('error' => false, 'data' => $data['id']));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    function addPelunasan()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', '', 'create');
            $data['vcrud'] = $crud;

            $status = FALSE;
            $data = $this->input->post();
            // echo json_encode($data);
            $data['nominal'] = substr(preg_replace("/[^0-9]/", "", $data['nominal']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['nominal']), -2);

            if (empty($data['date_pembayaran'])) {
                $data['date_pembayaran'] = date('Y-m-d');
            }

            $data['old_data'] = $this->PembayaranModel->getAllPembayaran(array('id' => $data['parent_id'], 'by_id' => true))[$data['parent_id']];
            $data['data_pelunasan'] = $this->General_model->getAllPelunasanPembayaran(array('parent_id' => $data['parent_id']));
            $total_in_data_pelunasan = 0;
            foreach ($data['data_pelunasan'] as $p) {
                $total_in_data_pelunasan = $total_in_data_pelunasan + $p['sum_child'];
            }

            $jp = $this->General_model->getAllJenisInvoice(array('by_id' => true, 'id' => $data['old_data']['jenis_pembayaran']))[$data['old_data']['jenis_pembayaran']];
            // $data['status'] = 'unpaid';
            // $uang_muka_pph = number_format(($data['sub_total'] * 0.02), 2, '.', '');
            $ref = $this->General_model->getAllRefAccount(array('by_id' => true, 'ref_id' => $data['payment_metode']))[$data['payment_metode']];
            $data['gen_old'] = $this->Statement_model->getSingelJurnal(array('id' => $data['old_data']['general_id']))['parent'];
            $data['generalentry'] = array(
                'date' => $data['date_pembayaran'],
                'ref_number' => $this->General_model->gen_number($data['date_pembayaran'], $jp['ref_nojur_pel_pembayaran']),
                // 'naration' => $data['old_data']['description'],
                'naration' => 'Bayar Tagihan ' . $data['old_data']['description'] . ' (' . $data['gen_old']->ref_number . ')',
                'customer_id' => $data['old_data']['customer_id'],
                'generated_source' => 'Pelunasan Pembayaran'
            );
            // echo json_encode($ref);
            // die();
            $i = 0;
            $total = 0;
            $ac_bank = false;
            if (!empty($data['nominal'])) {
                if ($data['nominal'] > 0) {
                    $data['sub_entry'][$i] = array(
                        'accounthead' => $ref['ref_account'],
                        'type' => 1,
                        'amount' => $data['nominal'],
                        'sub_keterangan' => "Piut " . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['old_data']['description'],
                    );
                    $ac_bank = true;
                    $i++;
                    $total = $total + $data['nominal'];
                }
            }

            if (!empty($data['ac_potongan'])) $potongan = count($data['ac_potongan']);
            else $potongan = 0;

            $k = 0;
            for ($j = 0; $j < $potongan; $j++) {
                if (!empty($data['ac_potongan'][$j]) && !empty($data['ac_nominal'][$j])) {
                    $data['ac_nominal'][$j] = substr(preg_replace("/[^0-9]/", "", $data['ac_nominal'][$j]), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['ac_nominal'][$j]), -2);
                    $data['sub_entry'][$i] = array(
                        'accounthead' => $data['ac_potongan'][$j],
                        'type' => 1,
                        'amount' => $data['ac_nominal'][$j],
                        'sub_keterangan' => $data['ac_desk'][$j],
                    );
                    $i++;
                    $total = $total + $data['ac_nominal'][$j];
                    $data['child_pembayaran'][$k] = array(
                        'ac_potongan' => $data['ac_potongan'][$j],
                        'ac_nominal' => $data['ac_nominal'][$j],
                        'ac_desk' => $data['ac_desk'][$j],
                        'no_bukti' => $data['no_bukti'][$j],
                    );
                }
            }
            // act lebih
            if (!empty($data['ac_lebih'])) $lebih = count($data['ac_lebih']);
            else $lebih = 0;
            $total_lebih = 0;
            $k = 0;
            for ($j = 0; $j < $lebih; $j++) {
                if (!empty($data['ac_lebih'][$j]) && !empty($data['ac_nominal_lebih'][$j])) {
                    $data['ac_nominal_lebih'][$j] = substr(preg_replace("/[^0-9]/", "", $data['ac_nominal_lebih'][$j]), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['ac_nominal_lebih'][$j]), -2);
                    $data['sub_entry'][$i] = array(
                        'accounthead' => $data['ac_lebih'][$j],
                        'type' => 0,
                        'amount' => $data['ac_nominal_lebih'][$j],
                        'sub_keterangan' => $data['ac_desk_lebih'][$j],
                    );
                    $i++;
                    $total_lebih = $total_lebih + $data['ac_nominal_lebih'][$j];
                    $data['child_lebih'][$k] = array(
                        'ac_lebih' => $data['ac_lebih'][$j],
                        'ac_nominal_lebih' => $data['ac_nominal_lebih'][$j],
                        'ac_desk_lebih' => $data['ac_desk_lebih'][$j],
                        'no_bukti_lebih' => $data['no_bukti_lebih'][$j],
                    );
                }
            }

            if ($total_lebih > 0)
                if ($ac_bank) {
                    $data['sub_entry'][0]['amount'] = $data['sub_entry'][0]['amount']  + $total_lebih;
                } else {
                    $data['sub_entry'][$i] = array(
                        'accounthead' => $ref['ref_account'],
                        'type' => 1,
                        'amount' => $total_lebih,
                        'sub_keterangan' => "Lebih Bayar " . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['old_data']['description'],
                    );
                    $i++;
                }

            // end lebih
            $data['sub_entry'][$i] = array(
                'accounthead' => $jp['ac_hutang'],
                'type' => 0,
                'amount' => $total,
                'sub_keterangan' => 'Htg ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['old_data']['description'],
            );
            $i++;
            // $data['total_bayar'] = $total_bayar;
            if ($total_in_data_pelunasan >= $data['old_data']['sub_total_2']) {
                throw new UserException('Data ini sudah lunas!');
            }
            if ($total + $total_in_data_pelunasan >= $data['old_data']['sub_total_2']) {
                $data['status_pembayaran'] = 'paid';
            } else {
                $data['status_pembayaran'] = 'unpaid';
            };

            $result = $this->PembayaranModel->add_pelunasan($data);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    function editPelunasan()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', '', 'update');
            $data['vcrud'] = $crud;
            $status = FALSE;
            $data = $this->input->post();
            // echo json_encode($data);
            $data['nominal'] = substr(preg_replace("/[^0-9]/", "", $data['nominal']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['nominal']), -2);

            if (empty($data['date_pembayaran'])) {
                $data['date_pembayaran'] = date('Y-m-d');
            }


            $data['old_data'] = $this->PembayaranModel->getAllPembayaran(array('id' => $data['parent_id'], 'by_id' => true))[$data['parent_id']];
            $data['data_pelunasan'] = $this->General_model->getAllPelunasanPembayaran(array('parent_id' => $data['parent_id']));

            $total_in_data_pelunasan = 0;
            foreach ($data['data_pelunasan'] as $p) {
                if ($p['id'] != $data['id']) $total_in_data_pelunasan = $total_in_data_pelunasan + $p['nominal'];
                else {
                    $old_pelunasan = $p;
                }
            }

            // $data['total_bayar'] = $total_bayar;
            if ($total_in_data_pelunasan >= $data['old_data']['sub_total_2']) {
                throw new UserException('Data ini sudah lunas!');
            }
            $jp = $this->General_model->getAllJenisInvoice(array('by_id' => true, 'id' => $data['old_data']['jenis_pembayaran']))[$data['old_data']['jenis_pembayaran']];
            $ref = $this->General_model->getAllRefAccount(array('by_id' => true, 'ref_id' => $data['payment_metode']))[$data['payment_metode']];
            $data['gen_old'] = $this->Statement_model->getSingelJurnal(array('id' => $data['old_data']['general_id']))['parent'];
            $data['generalentry'] = array(
                'id' => $old_pelunasan['general_id'],
                'date' => $data['date_pembayaran'],
                // 'naration' => $data['old_data']['description'],
                'naration' => 'Pelunasan Tagihan ' . $data['old_data']['description'] . ' (' . $data['gen_old']->ref_number . ')',
                'customer_id' => $data['old_data']['customer_id'],
                'generated_source' => 'Pelunasan Pembayaran'
            );
            // if($data['date_pembayaran'] !=)
            if (substr($old_pelunasan['date_pembayaran'], 0, -3) != substr($data['date_pembayaran'], 0, -3))
                $data['generalentry']['ref_number'] = $this->General_model->gen_number($data['date_pembayaran'], $jp['ref_nojur_pel_pembayaran']);
            // 'ref_number' => $this->General_model->gen_number($data['date_pembayaran'], $jp['ref_nojur_pel']),
            // echo json_encode($ref);
            // die();
            $i = 0;
            $total = 0;
            $ac_bank = false;
            if (!empty($data['nominal'])) {
                if ($data['nominal'] > 0) {
                    $data['sub_entry'][$i] = array(
                        'accounthead' => $ref['ref_account'],
                        'type' => 1,
                        'amount' => $data['nominal'],
                        'sub_keterangan' => "Piut " . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['old_data']['description'],
                    );
                    $i++;
                    $ac_bank = true;
                    $total = $total + $data['nominal'];
                }
            }
            if (!empty($data['ac_potongan'])) $potongan = count($data['ac_potongan']);
            else $potongan = 0;

            $k = 0;
            for ($j = 0; $j < $potongan; $j++) {
                if (!empty($data['ac_potongan'][$j]) && !empty($data['ac_nominal'][$j])) {
                    $data['ac_nominal'][$j] = substr(preg_replace("/[^0-9]/", "", $data['ac_nominal'][$j]), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['ac_nominal'][$j]), -2);
                    $data['sub_entry'][$i] = array(
                        'accounthead' => $data['ac_potongan'][$j],
                        'type' => 1,
                        'amount' => $data['ac_nominal'][$j],
                        'sub_keterangan' => $data['ac_desk'][$j],
                    );
                    $i++;
                    $total = $total + $data['ac_nominal'][$j];
                    $data['child_pembayaran'][$k] = array(
                        'ac_potongan' => $data['ac_potongan'][$j],
                        'ac_nominal' => $data['ac_nominal'][$j],
                        'ac_desk' => $data['ac_desk'][$j],
                        'no_bukti' => $data['no_bukti'][$j],
                    );
                }
            }

            // act lebih
            if (!empty($data['ac_lebih'])) $lebih = count($data['ac_lebih']);
            else $lebih = 0;
            $total_lebih = 0;
            $k = 0;
            for ($j = 0; $j < $lebih; $j++) {
                if (!empty($data['ac_lebih'][$j]) && !empty($data['ac_nominal_lebih'][$j])) {
                    $data['ac_nominal_lebih'][$j] = substr(preg_replace("/[^0-9]/", "", $data['ac_nominal_lebih'][$j]), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['ac_nominal_lebih'][$j]), -2);
                    $data['sub_entry'][$i] = array(
                        'accounthead' => $data['ac_lebih'][$j],
                        'type' => 0,
                        'amount' => $data['ac_nominal_lebih'][$j],
                        'sub_keterangan' => $data['ac_desk_lebih'][$j],
                    );
                    $i++;
                    $total_lebih = $total_lebih + $data['ac_nominal_lebih'][$j];
                    $data['child_lebih'][$k] = array(
                        'ac_lebih' => $data['ac_lebih'][$j],
                        'ac_nominal_lebih' => $data['ac_nominal_lebih'][$j],
                        'ac_desk_lebih' => $data['ac_desk_lebih'][$j],
                        'no_bukti_lebih' => $data['no_bukti_lebih'][$j],
                    );
                }
            }

            if ($total_lebih > 0)
                if ($ac_bank) {
                    $data['sub_entry'][0]['amount'] = $data['sub_entry'][0]['amount']  + $total_lebih;
                } else {
                    $data['sub_entry'][$i] = array(
                        'accounthead' => $ref['ref_account'],
                        'type' => 1,
                        'amount' => $total_lebih,
                        'sub_keterangan' => "Lebih Bayar " . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['old_data']['description'],
                    );
                    $i++;
                }

            // end lebih

            $data['sub_entry'][$i] = array(
                'accounthead' => $jp['ac_hutang'],
                'type' => 0,
                'amount' => $total,
                'sub_keterangan' => 'Htg ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['old_data']['description'],
            );
            $i++;
            // $data['total_bayar'] = $total_bayar;
            // if ($total_in_data_pelunasan >= $data['old_data']['total_final']) {
            //     throw new UserException('Data ini sudah lunas!');
            // }
            if ($total + $total_in_data_pelunasan >= $data['old_data']['sub_total_2']) {
                $data['status_pembayaran'] = 'paid';
            } else {
                $data['status_pembayaran'] = 'unpaid';
            };

            $result = $this->PembayaranModel->edit_pelunasan($data);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    function deletePelunasan()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', '', 'delete');
            $data['vcrud'] = $crud;
            $status = FALSE;
            $data = $this->input->post();
            // echo json_encode($data);

            $data['self_data'] = $this->PembayaranModel->getAllPelunasan(array('id' => $data['id']))[0];
            $data['data_pelunasan'] = $this->PembayaranModel->getAllPelunasan(array('parent_id' => $data['self_data']['parent_id']));
            $data['old_data'] = $this->PembayaranModel->getAllPembayaran(array('id' => $data['self_data']['parent_id'], 'by_id' => true))[$data['self_data']['parent_id']];
            $total_bayar = 0;
            foreach ($data['data_pelunasan'] as $p) {
                if ($p['id'] != $data['id']) $total_bayar = $total_bayar + $p['nominal'];
                else {
                    $old_pelunasan = $p;
                }
            }
            $data['total_bayar'] = $total_bayar;
            if ($total_bayar >= $data['old_data']['sub_total']) {
                // throw new UserException('Data ini sudah lunas!');
            }
            if ($data['total_bayar'] >= $data['old_data']['sub_total_2']) {
                $data['status_pembayaran'] = 'paid';
            } else {
                $data['status_pembayaran'] = 'unpaid';
            }
            // echo json_encode($data);
            // die();

            $result = $this->PembayaranModel->delete_pelunasan($data);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function kwitansi_print($id)
    {
        // $data = $this->input->get();
        // $data['company'] = Company_Profile();
        $data['transaction'] = $this->PembayaranModel->getAllPembayaranWithItem(array('id' =>  $id))[0];
        $data['template'] = $this->General_model->getAllJenisInvoice(array('by_id' => true, 'id' => $data['transaction']['jenis_pembayaran']))[$data['transaction']['jenis_pembayaran']];

        if (!empty($data['template']['text_kwitansi']))
            $data['text_kwitansi'] = $this->find_char($data['template']['text_kwitansi'], $data['transaction']);
        else
            $data['text_kwitansi'] =  $data['transaction']['description'];


        if (empty($data['date'])) $data['date'] = date('Y-m-d');
        $data['date'] = 'Pangkalpinang, ' . tanggal_indonesia($data['date']);
        // echo json_encode($data);
        // die();

        $data['terbilang'] = $this->terbilang((int)$data['transaction']['sub_total_2']) . ' Rupiah';
        $data['nominal'] = number_format((int)$data['transaction']['sub_total_2'], 0, ',', '.');
        $this->load->view('pembayaran/print_kwitansi.php', $data);
    }

    function find_char($string, $data)
    {
        $pos = strpos($string, '{', 2);
        if (!empty($pos)) {
            $pos2 = strpos($string, '}');
            $tx1 = substr($string, 0, $pos);
            $fx = $tx1 . $this->susunchar(substr($string, $pos + 1, $pos2 - $pos - 1), $data) . substr($string, $pos2 + 1);
            $fx = $this->find_char($fx, $data);
            return $fx;
        } else {
            return $string;
        }
    }

    function susunchar($type, $data)

    {
        if ($type == 'description') {
            return $data['description'];
        } else if ($type == 'patner_name') {
            return $data['customer_name'];
        } else
            return '';
    }

    public function print($id)
    {
        // $data['transaction'] = $this->PembayaranModel->getAllPembayaranDetail(array('id' => $id))[$id];
        $data['transaction'] = $this->PembayaranModel->getAllPembayaranWithItem(array('id' =>  $id))[0];
        $data['template'] = $this->General_model->getAllJenisInvoice(array('by_id' => true, 'id' => $data['transaction']['jenis_pembayaran']))[$data['transaction']['jenis_pembayaran']];
        $data['payment'] = $this->General_model->getAllRefAccount(array('by_id' => true, 'ref_id' => $data['transaction']['payment_metode']))[$data['transaction']['payment_metode']];

        if (!empty($data['template']['paragraph_1']))
            $data['p1'] = $this->find_char($data['template']['paragraph_1'], $data['transaction']);
        else
            $data['p1'] = 'Bersamaan dengan ini kami sampaikan tagihan ' . $data['transaction']['description'] . ' sebagai berikut :';
        if (empty($data['transaction']['date'])) $data['transaction']['date'] = date('Y-m-d');
        $data['transaction']['date'] = tanggal_indonesia($data['transaction']['date']);

        $data['terbilang'] = $this->terbilang((int)$data['transaction']['sub_total_2']) . ' Rupiah';
        $data['nominal'] = number_format((int)$data['transaction']['sub_total_2'], 0, ',', '.');
        // echo json_encode($data);
        // die();
        $this->load->view('pembayaran/print_template.php', $data);
    }
}
