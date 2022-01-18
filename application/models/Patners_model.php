<?php
/*

*/
class Patners_model extends CI_Model
{
    public function addPatners($data)
    {
        $this->db->insert('mp_payee', $data);
        ExceptionHandler::handleDBError($this->db->error(), "Tambah Patners", "Patners");
        $id_ins = $this->db->insert_id();
        return $id_ins;
    }

    public function editPatners($data)
    {
        $this->db->where('id', $data['id']);

        $this->db->update('mp_payee', $data);
        ExceptionHandler::handleDBError($this->db->error(), "Edit Patners", "Patners");
        return $data['id'];
    }
    public function deletePatners($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->delete('mp_payee');
        ExceptionHandler::handleDBError($this->db->error(), "Delete Patners", "Patners");
        return $data['id'];
    }
}
