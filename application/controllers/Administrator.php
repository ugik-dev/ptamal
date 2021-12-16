<?php
/*

*/
defined('BASEPATH') or exit('No direct script access allowed');
class Administrator extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('SecurityModel', 'Administrator_model', 'General_model', 'Accounting_model'));
        // $this->load->helper(array('DataStructure'));
        $this->db->db_debug = TRUE;
    }

    // MultipleRoles
    public function index()
    {
        $this->hak_aksess();
    }

    public function hak_aksess()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('administrator', 'hak_aksess', 'view');
            $privileges = $this->Administrator_model->getAllUser();
            $data['title'] = 'Hak Akses';
            $data['table_name'] = 'HAK AKSES';
            $data['main_view'] = 'administrator/hak_aksess';
            $data['vcrud'] = $crud;
            $data['privileges'] = $privileges;
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function edit_hak_aksess($id)
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('administrator', 'hak_aksess', 'update', true);

            $privileges = $this->Administrator_model->getHakAksess2(array('user_id' => $id));
            $data['result_roles'] = $privileges;
            $data['user_id'] = $id;
            $data['main_view'] = 'administrator/edit_hak_aksess';
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function hak_aksess_update()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('administrator', 'hak_aksess', 'update', true);
            $data = $this->input->post();

            $datas = explode(',', $data['selected_value']);
            $ret = array();
            foreach ($datas as $dat) {
                $res = array();
                $res = explode('_', $dat);
                // $tmp = array('view' => 0, 'create' => 0, 'update' => 0, 'delete' => 0);
                if ($res[0] == 'v') {
                    $ret[$res[1]]['user_id'] = $data['user_id'];
                    $ret[$res[1]]['id_menulist'] = $res[1];
                    $ret[$res[1]]['view'] = 1;
                };
                if ($res[0] == 'c') {
                    $ret[$res[1]]['user_id'] = $data['user_id'];
                    $ret[$res[1]]['id_menulist'] = $res[1];
                    $ret[$res[1]]['create'] = 1;
                };
                if ($res[0] == 'u') {
                    $ret[$res[1]]['user_id'] = $data['user_id'];
                    $ret[$res[1]]['id_menulist'] = $res[1];
                    $ret[$res[1]]['update'] = 1;
                };
                if ($res[0] == 'd') {
                    $ret[$res[1]]['user_id'] = $data['user_id'];
                    $ret[$res[1]]['id_menulist'] = $res[1];
                    $ret[$res[1]]['delete'] = 1;
                };
            }
            $this->Administrator_model->clear_hak_aksess(array('user_id' => $data['user_id']));

            foreach ($ret as $ret_p) {
                $this->Administrator_model->update_hak_aksess($ret_p);
            }
            echo json_encode(array("data" => $ret));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function ref_accounts()
    {
        try {
            $crud = $this->SecurityModel->Aksessbility_VCRUD('administrator', 'ref_accounts', 'view');
            $data['accounts'] = $this->Accounting_model->getAllBaganAkun(array('by_DataStructure' => true));
            $data['payment_method'] = $this->General_model->getAllRefAccount(array('ref_type' => 'payment_method'));
            $data['title'] = 'List Ref Accounts';
            $data['main_view'] = 'administrator/ref_accounts';
            $data['vcrud'] = $crud;
            // $data['vcrud'] = array('parent_id' => 32, 'id_menulist' => 89);
            $this->load->view('main/index2.php', $data);
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function editRefAccount()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('administrator', 'ref_accounts', 'update', true);
            $data = $this->input->post();
            $accounts = $this->Administrator_model->editRefAccount($data);
            $data = $this->General_model->getAllRefAccount(array('id' => $accounts, 'by_id' => true))[$accounts];
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function addRefAccount()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('administrator', 'ref_accounts', 'create', true);
            $data = $this->input->post();
            $insert_id = $this->Administrator_model->addRefAccount($data);
            $data = $this->General_model->getAllRefAccount(array('id' => $insert_id, 'by_id' => true))[$insert_id];
            echo json_encode(array('error' => false, 'data' => $data));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    public function deleteRefAcounts()
    {
        try {
            $this->SecurityModel->Aksessbility_VCRUD('administrator', 'ref_accounts', 'delete', true);
            $data = $this->input->get();
            $this->Administrator_model->deleteRefAccount($data);
            echo json_encode(array('error' => false, 'data' => ''));
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }
}
