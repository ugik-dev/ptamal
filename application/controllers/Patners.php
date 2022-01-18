<?php
/*

*/
defined('BASEPATH') or exit('No direct script access allowed');
class Patners extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('SecurityModel', 'Accounting_model', 'Patners_model', 'General_model'));
        // $this->load->helper(array('DataStructure'));
        $this->db->db_debug = TRUE;
    }

    public function getAllPayee()
    {
        try {
            $filter = $this->input->get();
            // $filter['nature'] = 'Assets';
            $data = $this->Patners_model->getAllPayee($filter);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function getPatnersTransaction()
    {
        try {
            $filter = $this->input->get();
            // $filter['nature'] = 'Assets';
            $data = $this->Patners_model->getPatnersTransaction($filter);
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function index()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('patners', '', 'view');
            // $data['accounts'] = $this->Accounting_model->getAllBaganAkun(array('by_DataStructure' => true, 'nature' => 'Assets'));

            $data['title'] = 'Daftar Patners';
            $data['main_view'] = 'patners/daftar_patners';
            $data['vcrud'] = $crud;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }



    public function editPatners()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('patners', '', 'update', true);
            $data = $this->input->post();
            $accounts = $this->Patners_model->editPatners($data);
            $data = $this->General_model->getAllPayee(array('id' => $accounts, 'by_id' => true))[$accounts];
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }


    public function addPatners()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('patners', '', 'create', true);
            $data = $this->input->post();
            $accounts = $this->Patners_model->addPatners($data);
            $data = $this->General_model->getAllPayee(array('id' => $accounts, 'by_id' => true))[$accounts];
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function deletePatners()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('patners', '', 'delete', true);
            $data = $this->input->get();
            $accounts = $this->Patners_model->deletePatners($data);
            // $data = $this->General_model->getAllPayee(array('id' => $accounts, 'by_id' => true))[$accounts];
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
}
