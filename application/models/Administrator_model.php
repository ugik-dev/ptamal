<?php
/*

*/
class Administrator_model extends CI_Model
{

    public function getAllUser($filter = [])
    {
        $this->db->select("mp_users.id as user_id , mp_users.user_name , mp_users.user_email , mp_users.user_description");
        $this->db->from('mp_users');
        if (!empty($filter['user_id'])) $this->db->where('mp_users.id', $filter['user_id']);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return NULL;
        }
    }
    public function getHakAksess($filter)
    {
        $this->db->select("mp_menu.id as parent_id,mp_menulist.id as page_id,mp_menu.name,mp_menu.icon,mp_menu.order_number,title as sub_name, link as sub_link,slug as link");
        // $CI->db->select("mp_menu.id as parent_id,mp_menulist.id as page_id,mp_menu.name,mp_menu.icon,mp_menu.order_number");
        $this->db->from('mp_menu');
        $this->db->join('mp_menulist', "mp_menu.id = mp_menulist.menu_Id");
        $this->db->where('mp_menulist.active', 1);

        // $CI->db->join('mp_multipleroles', "mp_menu.id = mp_multipleroles.menu_Id and mp_multipleroles.user_id = '" . $this->session->userdata('user_id')['id'] . "'");
        $this->db->order_by('mp_menu.order_number');
        $res = $this->db->get();
        // echo json_encode(DataStructure::groupBy2($query->result_array(), 'parent_id', 'parent_id', ['parent_id', 'name', 'order_number'], 'items'));
        $ret = DataStructure::jstreeStructure(
            $res->result_array(),
            ['parent_id'],
            ['page_id'],
            [
                ['parent_id', 'name', 'link', 'icon'],
                ['page_id', 'sub_name', 'sub_link']
            ],
            ['children'],
            false
        );
        return $ret;
    }

    public function getHakAksess2($filter = [])
    {
        $this->db->select("mp_menu.id as parent_id,mp_menulist.id as page_id,mp_menu.name,mp_menu.icon,mp_menu.order_number,title as sub_name, link as sub_link,slug as link, , id_hak_aksess, view, hk_create,hk_update, hk_delete");
        $this->db->from('mp_menulist');
        $this->db->join('mp_menu', "mp_menu.id = mp_menulist.menu_Id");
        $this->db->join('hak_aksess', "mp_menulist.id = hak_aksess.id_menulist AND hak_aksess.id_user = " . $filter['user_id'], 'left');

        $this->db->where('mp_menulist.active', 1);
        // $this->db->where('hak_aksess.id_user = "' . $filter['user_id'] . '" OR ( hak_aksess.id_user IS NULL) ',);
        $this->db->order_by('mp_menu.order_number');
        $res = $this->db->get();
        $ret = DataStructure::jstreeStructure(
            $res->result_array(),
            ['parent_id'],
            ['page_id'],
            [
                ['parent_id', 'name', 'link', 'icon'],
                ['page_id', 'sub_name', 'sub_link', 'id_hak_aksess', 'view', 'hk_create', 'hk_update', 'hk_delete']
            ],
            ['children'],
            false
        );
        return $ret;
    }
    public function clear_hak_aksess($data)
    {
        $this->db->where('id_user', $data['user_id']);
        $this->db->delete('hak_aksess');
    }
    public function update_hak_aksess($data)
    {
        $sub_data  = array(
            'id_menulist'   => $data['id_menulist'],
            'id_user'   => $data['user_id'],
        );

        if (!empty($data['view'])) $sub_data['view'] = $data['view'];
        if (!empty($data['create'])) $sub_data['hk_create'] = $data['create'];
        if (!empty($data['update'])) $sub_data['hk_update'] = $data['update'];
        if (!empty($data['delete'])) $sub_data['hk_delete'] = $data['delete'];
        $this->db->insert('hak_aksess', $sub_data);
    }

    public function editRefAccount($data)
    {
        $this->db->where('ref_id', $data['ref_id']);

        $this->db->update('ref_account', $data);
        ExceptionHandler::handleDBError($this->db->error(), "Edit Referensi Akun", "Referensi Akun");
        return $data['ref_id'];
    }
    public function addRefAccount($data)
    {

        $this->db->insert('ref_account', $data);
        $insert_id = $this->db->insert_id();
        ExceptionHandler::handleDBError($this->db->error(), "Add Referensi Akun", "Referensi Akun");
        return $insert_id;
    }

    public function deleteRefAccount($data)
    {

        $this->db->where('ref_id', $data['ref_id']);
        $this->db->delete('ref_account');
        // $insert_id = $this->db->insert_id();
        ExceptionHandler::handleDBError($this->db->error(), "Delete Referensi Akun", "Referensi Akun");
        // return $insert_id;
    }
}
