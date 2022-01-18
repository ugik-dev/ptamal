<?php
/*

*/
defined('BASEPATH') or exit('No direct script access allowed');
class General extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('General_model', 'Production_model'));
        // $this->load->helper(array('DataStructure'));
        $this->db->db_debug = TRUE;
    }

    public function getAllPayee()
    {
        try {
            $filter = $this->input->get();
            // $filter['nature'] = 'Assets';
            $data = $this->General_model->getAllPayee($filter);
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

    public function getAllPaymentMethod()
    {
        try {
            $filter = $this->input->get();
            $data = $this->Production_model->getAllPaymentMethod($filter);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    public function product_search($search_result)
    {
        if ($search_result != NULL) {
            $result = $this->Production_model->search_items_stock($search_result);
            $data['search_result'] = $result;
            $this->load->view('production/search_list.php', $data);
        }
    }

    public function getAllUnit()
    {
        try {
            $filter = $this->input->get();
            echo json_encode(array('error' => false, 'data' => $this->General_model->getAllUnit($filter)));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function getAllRefAccounts()
    {
        try {
            $filter = $this->input->get();
            $data = $this->General_model->getAllRefAccount($filter);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    function getAllBaganAkun()
    {
        try {
            $filter = $this->input->get();
            $data = $this->General_model->getAllBaganAkun($filter);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    function getAprovalUsers()
    {
        try {
            $filter = $this->input->get();
            $data = $this->General_model->getAprovalUsers($filter);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    function getAllPelunasanInvoice()
    {
        try {
            $filter = $this->input->get();
            $data = $this->General_model->getAllPelunasanInvoice($filter);
            if (!empty($filter['get_potongan']))
                foreach ($data as $key => $dt) {
                    $data[$key]['data_potongan'] = $this->General_model->getChildrenPelunasan(array('id_pelunasan' => $dt['id']));
                }
            if (!empty($filter['get_lebih']))
                foreach ($data as $key => $dt) {
                    $data[$key]['data_lebih'] = $this->General_model->getChildrenLebih(array('id_pelunasan' => $dt['id']));
                }
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    function getAllPelunasanPembayaran()
    {
        try {
            $filter = $this->input->get();
            $data = $this->General_model->getAllPelunasanPembayaran($filter);
            if (!empty($filter['get_potongan']))
                foreach ($data as $key => $dt) {
                    $data[$key]['data_potongan'] = $this->General_model->getChildrenPelunasanPembayaran(array('id_pelunasan' => $dt['id']));
                }
            if (!empty($filter['get_lebih']))
                foreach ($data as $key => $dt) {
                    $data[$key]['data_lebih'] = $this->General_model->getChildrenPelunasanPembayaranLebih(array('id_pelunasan' => $dt['id']));
                }
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function getAllJenisInvoice()
    {
        try {
            $filter = $this->input->get();
            // $filter
            $data = $this->General_model->getAllJenisInvoice($filter);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
}
