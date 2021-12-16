<?php
/*

*/
class Accounting_model extends CI_Model
{


    public function getAllBaganAkun($filter = [])
    {
        $this->db->from('dt_head');
        // $this->db->order_by('dt_head.name');
        // $this->db->where("SUBSTRING_INDEX(SUBSTRING_INDEX(mp_head.name, '.', -3), ']', 1) = '00.000.000'");
        if (!empty($filter['account_head'])) $this->db->where('dt_head.id', $filter['account_head']);
        if (!empty($filter['id'])) $this->db->where('dt_head.id', $filter['id']);
        if (!empty($filter['nature'])) {
            if (is_array($filter['nature'])) {
                $this->db->where_in('dt_head.nature', $filter['nature']);
            } else
                $this->db->where('dt_head.nature', $filter['nature']);
        }
        $this->db->order_by('dt_head.head_number', 'ASC');
        $query = $this->db->get();
        if (!empty($filter['by_id'])) {
            return DataStructure::keyValue($query->result_array(), 'id');
        }
        if (!empty($filter['by_DataStructure'])) {
            return DataStructure::TreeAccounts(
                $query->result_array(),
                ['id'],
                ['page_id'],
                [
                    ['parent_id', 'name', 'link', 'icon'],
                    ['page_id', 'sub_name', 'sub_link']
                ],
                ['children'],
                false
            );
        }
        $res = $query->result_array();
        return $res;
    }


    public function getAllJournalVoucher($filter = [])
    {
        $this->db->select("gen.id as parent_id,generated_source, sub.id as sub_id,gen.ref_number,gen.date,gen.naration,gen.customer_id,gen.user_update,sub.accounthead,sub.amount,sub.type,sub.sub_keterangan,head.name as head_name, head_number    ");
        $this->db->from('dt_generalentry as gen');
        $this->db->join('mp_sub_entry as sub', "gen.id = sub.parent_id", 'LEFT');
        $this->db->join('dt_head as head', "head.id = sub.accounthead", 'LEFT');
        if (!empty($filter['id'])) $this->db->where('gen.id', $filter['id']);
        if (!empty($filter['source'])) $this->db->where('gen.generated_source', $filter['source']);
        $this->db->order_by('gen.date, gen.id,  sub.id ', 'DESC');
        $res = $this->db->get();
        // echo json_encode(DataStructure::groupBy2($query->result_array(), 'parent_id', 'parent_id', ['parent_id', 'name', 'order_number'], 'items'));
        if (!empty($filter['by_id'])) {
            return DataStructure::keyValue($res->result_array(), 'parent_id');
        }
        $ret = DataStructure::groupByRecursive2(
            $res->result_array(),
            ['parent_id'],
            ['sub_id'],
            [
                ['parent_id', 'generated_source', 'ref_number', 'date', 'naration', 'customer_id', 'user_update'],
                ['sub_id', 'accounthead', 'head_name', 'amount', 'type', 'sub_keterangan', 'head_number']
            ],
            ['children'],
            false
        );
        // $ret = $res->result_array();
        // echo json_encode($ret);
        // die();
        return $ret;
    }

    public function getHakAksess2($filter = [])
    {
        $this->db->select("mp_menu.id as parent_id,mp_menulist.id as page_id,mp_menu.name,mp_menu.icon,mp_menu.order_number,title as sub_name, link as sub_link,slug as link, , id_hak_aksess, view, hk_create,hk_update, hk_delete");
        $this->db->from('mp_menulist');
        $this->db->join('mp_menu', "mp_menu.id = mp_menulist.menu_Id");
        $this->db->join('hak_aksess', "mp_menulist.id = hak_aksess.id_menulist AND hak_aksess.id_user = " . $filter['user_id'], 'left');
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

    public function addAccounts($data)
    {
        $this->db->insert('dt_head', $data);
        ExceptionHandler::handleDBError($this->db->error(), "Tambah Akun", "Akun");
        $id_ins = $this->db->insert_id();
        return $id_ins;
    }

    public function editAccounts($data)
    {
        $this->db->where('id', $data['id']);

        $this->db->update(
            'dt_head',
            DataStructure::slice(
                $data,
                [
                    'head_number', 'name', 'nature', 'expense_type', 'type', 'relation_id'
                ],
            )
        );
        ExceptionHandler::handleDBError($this->db->error(), "Edit Akun", "Akun");
        return $data['id'];
    }
    public function deleteAccounts($data)
    {
        // ini_set('date.timezone', 'Asia/Jakarta');
        // $data['date_modified'] = date("Y-m-d h:i:s");
        $this->db->where('id', $data['id']);
        $this->db->delete('dt_head');
        ExceptionHandler::handleDBError($this->db->error(), "Delete Akun", "Akun");
        return $data['id'];
    }

    // Journal Voucher
    public function addJournalVoucher($data)
    {
        $trans_data = array(
            'date' => $data['date'],
            'naration' => $data['naration'],
            'customer_id' => $data['customer_id'],
            'ref_number' => $data['ref_number'],
            'generated_source' => 'Journal Voucher'

        );

        $this->db->insert('dt_generalentry', $trans_data);

        $order_id = $this->db->insert_id();
        $total_heads = count($data['account_head']);
        for ($i = 0; $i < $total_heads; $i++) {

            if (!empty($data['account_head'][$i]) and (!empty($data['debitamount'][$i]) or !empty($data['creditamount'][$i]))) {
                if ($data['debitamount'][$i] != 0) {
                    $sub_data  = array(
                        'parent_id'   => $order_id,
                        'accounthead' => $data['account_head'][$i],
                        'amount'      => $data['debitamount'][$i],
                        'type'        => 0,
                        'sub_keterangan' => $data['sub_keterangan'][$i]
                    );
                } else if ($data['creditamount'][$i] != 0) {
                    $sub_data  = array(
                        'parent_id'   => $order_id,
                        'accounthead' => $data['account_head'][$i],
                        'amount'      => $data['creditamount'][$i],
                        'type'        => 1,
                        'sub_keterangan' => $data['sub_keterangan'][$i]
                    );
                }

                // if ($data['draft_value'] == 'true')
                //     $this->db->insert('draft_sub_entry', $sub_data);
                // else
                $this->db->insert('mp_sub_entry', $sub_data);
            }
        }
        ExceptionHandler::handleDBError($this->db->error(), "Tambah Jurnal Voucher", "Jurnal Voucher");
        return $order_id;
    }

    public function editJournalVoucher($data)
    {
        $this->db->trans_begin();

        $this->db->where('id', $data['id']);
        $trans_data = array(
            'date' => $data['date'],
            'naration' => $data['naration'],
            'customer_id' => $data['customer_id'],
        );

        $this->db->where('generated_source', 'Journal Voucher');
        $this->db->update('dt_generalentry', $trans_data);

        // $order_id = $this->db->insert_id();
        $total_heads = count($data['account_head']);
        for ($i = 0; $i < $total_heads; $i++) {

            if (!empty($data['account_head'][$i]) and (!empty($data['debitamount'][$i]) or !empty($data['creditamount'][$i]))) {
                if ($data['debitamount'][$i] != 0) {
                    $sub_data  = array(
                        'parent_id'   => $data['id'],
                        'accounthead' => $data['account_head'][$i],
                        'amount'      => $data['debitamount'][$i],
                        'type'        => 0,
                        'sub_keterangan' => $data['sub_keterangan'][$i]
                    );
                } else if ($data['creditamount'][$i] != 0) {
                    $sub_data  = array(
                        'parent_id'   => $data['id'],
                        'accounthead' => $data['account_head'][$i],
                        'amount'      => $data['creditamount'][$i],
                        'type'        => 1,
                        'sub_keterangan' => $data['sub_keterangan'][$i]
                    );
                }
                // if ($data['draft_value'] == 'true')
                //     $this->db->insert('draft_sub_entry', $sub_data);
                // else
                if (!empty($data['sub_id'][$i])) {
                    // die();
                    $this->db->where('id', $data['sub_id'][$i]);
                    $this->db->update('mp_sub_entry', $sub_data);
                } else
                    $this->db->insert('mp_sub_entry', $sub_data);
            } else if (!empty($data['sub_id'][$i])) {
                $this->db->where('id', $data['sub_id'][$i]);
                $this->db->delete('mp_sub_entry');
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            ExceptionHandler::handleDBError($this->db->error(), "Delete Bank", "Bank");
        } else {
            $this->db->trans_commit();
        }
        return $data['id'];
    }
    public function deleteJournalVoucher($data)
    {
        // ini_set('date.timezone', 'Asia/Jakarta');
        // $data['date_modified'] = date("Y-m-d h:i:s");
        $this->db->where('generated_source', 'Journal Voucher');
        $this->db->where('id', $data['id']);
        $this->db->delete('dt_generalentry');

        $this->db->where('parent_id', $data['id']);
        $this->db->delete('mp_sub_entry');
        ExceptionHandler::handleDBError($this->db->error(), "Delete Akun", "Akun");
        return $data['id'];
    }

    public function gen_number($data = [], $source = 'Journal Voucher')
    {
        $this->db->from('dt_generalentry');
        // $this->db->from('limit', 1);
        $this->db->limit(1);
        $this->db->order_by('id', 'DESC');

        // if (!empty($filter['account_head'])) $this->db->where('dt_head.id', $filter['account_head']);
        // if (!empty($filter['id'])) $this->db->where('dt_head.id', $filter['id']);
        $this->db->where('generated_source', $source);
        $this->db->where('MONTH(DATE)', explode('-', $data['date'])[1]);
        $this->db->where('YEAR(DATE)', explode('-', $data['date'])[0]);
        $query = $this->db->get();
        // if (!empty($filter['by_id'])) {
        $res =  $query->result_array();
        // var_dump($res);
        // die();
        if ($source == 'deposit') {
            $s2 = 'DEP';
        } else if ($source == 'paid') {
            $s2 = 'CEK';
        } else if ($source == 'invoice') {
            $s2 = 'INV';
        } else if ($source == 'Journal Voucher') {
            $s2 = 'JV';
        } else if ($source == 'Openning') {
            $s2 = 'OPN';
        }


        $number = explode('-', $data['date'])[0] . '/' . $s2 . '/' . $this->getRomawi((int)explode('-', $data['date'])[1]) . '/';

        if (!empty($res)) {
            $res = $res[0];

            if (!empty(explode('/', $res['ref_number'])[3])) {
                $res_num =  (int)explode('/', $res['ref_number'])[3] + 1;
                $numlength = strlen((string)$res_num);
                if ($numlength == 1) {
                    $res_num = '00' . $res_num;
                } else if ($numlength == 2) {
                    $res_num = '0' . $res_num;
                }
            } else {
                $res_num = '001';
            }
            $number .= $res_num;
            // echo $numlength;
            // echo $number;
            // die();
        } else {
            $number .= '001';
        }
        return $number;
        // }
        // MONTH(happened_at) = 1 and YEAR(happened_at) = 2009
    }
    function getRomawi($bln)
    {
        switch ($bln) {
            case 1:
                return "I";
                break;
            case 2:
                return "II";
                break;
            case 3:
                return "III";
                break;
            case 4:
                return "IV";
                break;
            case 5:
                return "V";
                break;
            case 6:
                return "VI";
                break;
            case 7:
                return "VII";
                break;
            case 8:
                return "VIII";
                break;
            case 9:
                return "IX";
                break;
            case 10:
                return "X";
                break;
            case 11:
                return "XI";
                break;
            case 12:
                return "XII";
                break;
        }
    }

    public function addOpenning($data)
    {
        $trans_data = array(
            'date' => $data['date'],
            'naration' => $data['naration'],
            'ref_number' => $this->gen_number($data, 'Openning'),
            'generated_source' => 'Openning'
        );

        $this->db->insert('dt_generalentry', $trans_data);

        $order_id = $this->db->insert_id();
        $sub_data  = array(
            'parent_id'   => $order_id,
            'accounthead' => $data['accounthead'],
            'amount'      => $data['amount'],
            'type'        => $data['type'],
            'sub_keterangan' => $data['naration']
        );
        $this->db->insert('mp_sub_entry', $sub_data);
        ExceptionHandler::handleDBError($this->db->error(), "Tambah Openning Saldo", "Openning Saldo");
        return $order_id;
    }


    public function editOpenning($data)
    {
        $trans_data = array(
            'date' => $data['date'],
            'naration' => $data['naration'],
            // 'ref_number' => $this->gen_number($data, 'Openning'),
            // 'generated_source' => 'Openning'
        );
        $this->db->where('id', $data['id']);
        $this->db->where('generated_source', 'Openning');
        $this->db->update('dt_generalentry', $trans_data);

        $sub_data  = array(
            'accounthead' => $data['accounthead'],
            'amount'      => $data['amount'],
            'type'        => $data['type'],
            'sub_keterangan' => $data['naration']
        );
        $this->db->where('parent_id', $data['id']);
        $this->db->update('mp_sub_entry', $sub_data);
        ExceptionHandler::handleDBError($this->db->error(), "Tambah Openning Saldo", "Openning Saldo");
        // return $order_id;
    }


    public function deleteOpenning($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->where('generated_source', 'Openning');
        $this->db->delete('dt_generalentry');

        $this->db->where('parent_id', $data['id']);
        $this->db->delete('mp_sub_entry');
        ExceptionHandler::handleDBError($this->db->error(), "Tambah Openning Saldo", "Openning Saldo");
        // return $order_id;
    }
}
