<?php
/*

*/
defined('BASEPATH') or exit('No direct script access allowed');
class Production extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('SecurityModel', 'Accounting_model', 'Production_model', 'General_model'));
        // $this->load->helper(array('DataStructure'));
        $this->db->db_debug = TRUE;
    }


    public function getAllTransactions()
    {
        try {
            $filter = $this->input->get();
            $data = $this->Production_model->getAllTransactions($filter);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function getAllProduct()
    {
        try {
            $filter = $this->input->get();
            $data = $this->Production_model->getAllProduct($filter);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function getProduct()
    {
        try {
            $filter = $this->input->get();
            $accounts = $this->Production_model->getAllProduct($filter);
            echo json_encode(array('error' => false, 'data' => $accounts));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    public function search()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('statement', 'journal', 'view');
            $filter = $this->input->get();
            $filter['by_DataStructure'] = true;
            $crud = $this->SecurityModel->Aksessbility_VCRUD('production', 'pos', 'view');
            $data['transaction'] = $this->Production_model->getDetailTransaction($filter, false)[0];

            // die();
            $data['transaction']['date_1'] = $this->tgl_indo($data['transaction']['date_1']);
            $data['title'] = 'Transaction';
            $data['main_view'] = 'production/invoice_template2';
            $data['vcrud'] = $crud;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function transaction($id)
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('production', 'pos', 'view');
            $data['transaction'] = $this->Production_model->getDetailTransaction(array('by_DataStructure' => true, 'id' => $id))[$id];
            // echo json_encode($data);
            // die();
            $data['transaction']['date_1'] = $this->tgl_indo($data['transaction']['date_1']);
            $data['title'] = 'Transaction';
            $data['main_view'] = 'production/invoice_template3';
            $data['vcrud'] = $crud;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function inv_print($id)
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('production', 'pos', 'view');
            // $data['parent'] = $this->Production_model->getAllTransactions(array('by_DataStructure' => true, 'id' => $id));
            $data['transaction'] = $this->Production_model->getDetailTransaction(array('by_DataStructure' => true))[$id];
            // $data['payment_method'] = $this->General_model->getAllRefAccount(array('ref_type' => 'payment_method'));
            // echo json_encode($data['transaction']);
            // die();

            $data['title'] = 'Transaction';
            $data['main_view'] = 'production/invoice_detail';
            $data['vcrud'] = $crud;
            $this->load->view('production/template_inv_print', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    public function pos()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('production', 'pos', 'view');
            $data['revenue'] = $this->Accounting_model->getAllBaganAkun(array('by_DataStructure' => true, 'nature' => 'Revenue'));
            $data['customers'] = $this->General_model->getAllPayee(array('by_DataStructure' => true));
            $data['payment_method'] = $this->General_model->getAllRefAccount(array('ref_type' => 'payment_method'));
            // echo json_encode($data['payment_method']);
            // die();

            $data['title'] = 'List Product / Jasa';
            $data['main_view'] = 'production/pos';
            $data['vcrud'] = $crud;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    public function product_list()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('production', 'product_list', 'view');
            $data['revenue'] = $this->Accounting_model->getAllBaganAkun(array('by_DataStructure' => true, 'nature' => 'Revenue'));
            $data['title'] = 'List Product / Jasa';
            $data['main_view'] = 'production/product_list';
            $data['vcrud'] = $crud;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function transactions()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('production', 'transactions', 'view');
            $data['title'] = 'Transaction';
            $data['main_view'] = 'production/transactions';
            $data['vcrud'] = $crud;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    public function addProduct()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('production', 'product_list', 'create', true);
            $data = $this->input->post();
            $data['default_price'] =
                number_format(
                    str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['default_price'])),
                    2,
                    '.',
                    ''
                );
            $accounts = $this->Production_model->addProduct($data);
            $data = $this->Production_model->getAllProduct(array('id' => $accounts, 'by_id' => true))[$accounts];


            echo json_encode(array('error' => false, 'data' => $data));
            // $this->load->view('accounting/accounts_modal');
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function addInvoice()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('production', 'pos', 'create', true);
            $data = $this->input->post();
            if (empty($data['row_price'])) {
                throw new UserException('Ada yang salah harap perisak data!');
            }
            if (empty($data['am_ppn'])) {
                $data['am_ppn'] = 0;
            } else {
                $data['am_ppn'] =
                    number_format(
                        str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['am_ppn'])),
                        2,
                        '.',
                        ''
                    );
            }

            if (empty($data['am_pph'])) {
                $data['am_pph'] = 0;
            } else {
                $data['am_pph'] =
                    number_format(
                        str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['am_pph'])),
                        2,
                        '.',
                        ''
                    );
            }
            if (empty($data['am_fee'])) {
                $data['am_fee'] = 0;
            } else {
                $data['am_fee'] =
                    number_format(
                        str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['am_fee'])),
                        2,
                        '.',
                        ''
                    );
            }
            if (empty($data['net_total_amount'])) {
                $data['net_total_amount'] = 0;
            } else {
                $data['net_total_amount'] =
                    number_format(
                        str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['net_total_amount'])),
                        2,
                        '.',
                        ''
                    );
            }

            if (empty($data['total_gross_amt'])) {
                $data['total_gross_amt'] = 0;
            } else {
                $data['total_gross_amt'] =
                    number_format(
                        str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['total_gross_amt'])),
                        2,
                        '.',
                        ''
                    );
            }

            // $data['amount_recieved'] =
            //     number_format(
            //         str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['amount_recieved'])),
            //         2,
            //         '.',
            //         ''
            //     );
            // if (empty($data['amount_recieved'])) $data['amount_recieved'] = 0;

            $data_post['generalentry'] = array(
                'customer_id' => $data['customer_id'],
                'date' => $data['date'],
                'naration' => 'Pembayaran',
                'generated_source' => 'invoice',
                'user_update' => $this->session->userdata('user_id')['id'],
            );
            $i = 0;
            $for_count_amount = 0;
            $marg = array();
            $tmp = array();
            foreach ($data['item_id'] as $it) {
                $cur_amount = preg_replace('#[^0-9-]+#i', '', $data['row_price'][$i]);
                if (empty($cur_amount)) $cur_amount = 0;
                $data['row_price'][$i] = $cur_amount;
                // if (empty($marg[$data['revenue_account'][$i]])) {
                //     $marg[$data['revenue_account'][$i]] =
                //         array(
                //             'parent_id' => '',
                //             'accounthead' => $data['revenue_account'][$i],
                //             'type' => 1,
                //             'amount' => ((int)$cur_amount * $data['row_qyt'][$i]),
                //             'sub_keterangan' => 'Pembayaran Jasa '
                //         );
                // } else {
                //     $marg[$data['revenue_account'][$i]]['amount'] +=
                //         (int)$cur_amount * $data['row_qyt'][$i];
                // };

                $to_fee_ac = $data['revenue_account'][$i];
                $marg[$i] =
                    array(
                        'parent_id' => '',
                        'accounthead' => $data['revenue_account'][$i],
                        'type' => 1,
                        'amount' => $data['percent_fee'] > 0 ? (((float) $data['percent_fee'] / 100 * ((int)$cur_amount * $data['row_qyt'][$i]))) + ((int)$cur_amount * $data['row_qyt'][$i]) : ((int)$cur_amount * $data['row_qyt'][$i]),
                        'sub_keterangan' => $data['product_name'][$i]
                    );

                // $for_count_amount += (int)$cur_amount * $data['row_qyt'][$i];
                $i++;
                // $data['sub_entry'][0] = array('parent_id' => '', 'accounthead' => $data['relation_head'], 'type' => 0, 'amount' => $data['amount'], 'sub_keterangan' => 'Deposito Bank');
            }
            // AKUN ASET
            $data['total_gross_amt'] =
                number_format(
                    str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['total_gross_amt'])),
                    2,
                    '.',
                    ''
                );

            if ($data['status'] == 'unpaid') {
                $ac_assets = $this->General_model->getAllRefAccount(array('ref_type' => 'piutang'))[0]['ref_account'];
                // $status = 'unpaid';
            } else  if ($data['status'] == 'paid') {
                $ac_assets = $this->General_model->getAllRefAccount(array('ref_id' => $data['payment_id']))[0]['ref_account'];
            }
            $tmp[0] =
                array(
                    'parent_id' => '',
                    'accounthead' => $ac_assets,
                    'type' => 0,
                    'amount' =>  $data['net_total_amount'],
                    'sub_keterangan' => 'Pembayaran Barang / Jasa'
                );
            $i = 1;
            if ($data['am_ppn'] > 0) {
                $tmp[$i] =
                    array(
                        'parent_id' => '',
                        'accounthead' => $this->General_model->getAllRefAccount(array('ref_type' => 'tax_ppn'))[0]['ref_account'],
                        'type' => 1,
                        'amount' =>  $data['am_ppn'],
                        'sub_keterangan' => 'PPn'
                    );
                $i++;
            }

            if ($data['am_pph'] > 0) {
                $tmp[$i] =
                    array(
                        'parent_id' => '',
                        'accounthead' => $this->General_model->getAllRefAccount(array('ref_type' => 'tax_pph'))[0]['ref_account'],
                        'type' => 1,
                        'amount' =>  $data['am_pph'],
                        'sub_keterangan' => 'PPh'
                    );
                $i++;
            }
            if ($data['am_fee'] > 0) {
                $tmp[$i] =
                    array(
                        'parent_id' => '',
                        'accounthead' => $to_fee_ac,
                        'type' => 1,
                        'amount' =>  $data['am_fee'],
                        'sub_keterangan' => 'Fee'
                    );
                $i++;
            }
            foreach ($marg as $m) {
                $tmp[$i] = $m;
                $i++;
            }

            $data_post['sub_entry'] = $tmp;

            $order_id =  $this->Production_model->addInvoice($data_post, $data);
            echo json_encode(array('error' => false, 'data' => $order_id));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
    public function addInvoiceOLD()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('production', 'pos', 'create', true);
            $data = $this->input->post();
            if (empty($data['row_price'])) {
                throw new UserException('Ada yang salah harap perisak data!');
            }
            $data['discountfield'] =
                number_format(
                    str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['discountfield'])),
                    2,
                    '.',
                    ''
                );
            if (empty($data['discountfield'])) $data['discountfield'] = 0;
            $data['amount_recieved'] =
                number_format(
                    str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['amount_recieved'])),
                    2,
                    '.',
                    ''
                );
            if (empty($data['amount_recieved'])) $data['amount_recieved'] = 0;

            $data_post['generalentry'] = array(
                'customer_id' => $data['customer_id'],
                'date' => $data['date'],
                'naration' => 'Invoice',
                'generated_source' => 'invoice',
                'user_update' => $this->session->userdata('user_id')['id'],
            );
            $i = 0;
            $for_count_amount = 0;
            $marg = array();
            $tmp = array();
            foreach ($data['item_id'] as $it) {
                $cur_amount = preg_replace('#[^0-9-]+#i', '', $data['row_price'][$i]);
                if (empty($cur_amount)) $cur_amount = 0;
                $data['row_price'][$i] = $cur_amount;
                if (empty($marg[$data['revenue_account'][$i]])) {
                    $marg[$data['revenue_account'][$i]] =
                        array(
                            'parent_id' => '',
                            'accounthead' => $data['revenue_account'][$i],
                            'type' => 1,
                            'amount' => (int)$cur_amount * $data['row_qyt'][$i],
                            'sub_keterangan' => 'Invoice'
                        );
                } else {
                    $marg[$data['revenue_account'][$i]]['amount'] +=
                        (int)$cur_amount * $data['row_qyt'][$i];
                };
                $for_count_amount += (int)$cur_amount * $data['row_qyt'][$i];
                $i++;
                // $data['sub_entry'][0] = array('parent_id' => '', 'accounthead' => $data['relation_head'], 'type' => 0, 'amount' => $data['amount'], 'sub_keterangan' => 'Deposito Bank');
            }
            // AKUN ASET
            $tot_gros =
                number_format(
                    str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['total_gross_amt'])),
                    2,
                    '.',
                    ''
                );
            $data['total_gross_amt'] = $tot_gros;
            $tot_tax =
                number_format(
                    str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['total_tax_amt'])),
                    2,
                    '.',
                    ''
                );
            $data['total_tax_amt'] = $tot_tax;

            if (!empty($tot_tax)) {
                $i = 2;
                $ac_tax = $this->General_model->getAllRefAccount(array('ref_type' => 'payment_tax'))[0]['ref_account'];
                $tmp[1] =
                    array(
                        'parent_id' => '',
                        'accounthead' => $ac_tax,
                        'type' => 1,
                        'amount' =>  $tot_tax,
                        'sub_keterangan' => 'PPn Keluaran'
                    );
                $tot_assets = $tot_gros + $tot_tax;
            } else {
                $tot_assets = $tot_gros;
                $tot_tax = 0;
                $i = 1;
            }
            $kembalian = $data['amount_recieved'] - ($data['total_gross_amt'] + $data['total_tax_amt'] - $data['discountfield']);
            if ($kembalian < 0) {
                $ac_assets = $this->General_model->getAllRefAccount(array('ref_type' => 'piutang'))[0]['ref_account'];
                $status = 'unpaid';
            } else {
                $ac_assets = $this->General_model->getAllRefAccount(array('ref_id' => $data['payment_id']))[0]['ref_account'];
                $status = 'paid';
            }

            $tmp[0] =
                array(
                    'parent_id' => '',
                    'accounthead' => $ac_assets,
                    'type' => 0,
                    'amount' =>  $tot_assets,
                    'sub_keterangan' => 'Pendapatan Barang / Jasa'
                );

            foreach ($marg as $m) {
                $tmp[$i] = $m;
                $i++;
            }

            if ($for_count_amount + $tot_tax != $tot_assets) {
                throw new UserException('Ada yang salah harap perisak data!');
            }
            $data_post['sub_entry'] = $tmp;

            $order_id =  $this->Production_model->addInvoice($data_post, $data);
            echo json_encode(array('error' => false, 'data' => $order_id));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function deleteTransactionPembayaran()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('production', 'transactions', 'delete', true);
            $id = $this->input->get()['id'];
            $data = $this->Production_model->getDetailTransaction(array('by_DataStructure' => true, 'id' => $id))[$id];

            $accounts = $this->Production_model->deleteTransactionPembayaran($data);
            // $data = $this->Production_model->getAllProduct(array('id' => $accounts, 'by_id' => true))[$accounts];
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
    public function editProduct()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('production', 'product_list', 'update', true);
            $data = $this->input->post();
            $data['default_price'] =
                number_format(
                    str_replace(',', '.', preg_replace('#[^,0-9-]+#i', '', $data['default_price'])),
                    2,
                    '.',
                    ''
                );
            $accounts = $this->Production_model->editProduct($data);
            $data = $this->Production_model->getAllProduct(array('id' => $accounts, 'by_id' => true))[$accounts];
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function index()
    {
        $this->product_list();
    }

    function tgl_indo($tanggal)
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
        $pecahkan = explode('-', $tanggal);

        // variabel pecahkan 0 = tanggal
        // variabel pecahkan 1 = bulan
        // variabel pecahkan 2 = tahun

        return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
    }
}
