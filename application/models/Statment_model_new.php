<?php
/*

*/
class Statment_model_new extends CI_Model
{

    public function the_ledger($data, $filter = [])
    {
        foreach ($data as $k => $s) {
            $data[$k]['open'] = true;
            foreach ($data[$k]['children'] as $l => $t) {
                $data[$k]['children'][$l]['open'] = false;
                // echo $l;
                foreach ($data[$k]['children'][$l]['children'] as $m => $u) {
                    $cur = $this->get_ledger_transactions($u['id_head'], $filter);
                    if (!empty($cur)) {
                        $data[$k]['open'] = true;
                        $data[$k]['children'][$l]['open'] = true;
                        $data[$k]['children'][$l]['children'][$m]['data'] = $cur;
                    }
                }
            }
        }
        return $data;
    }

    public function trail_balance($data, $filter = [])
    {
        foreach ($data as $k) {
            if (substr($k['head_number'], 1, 6) == '00000') {
                if ($k['nature'] == 'Equity') {
                    $income = $this->income($filter);
                }
                $res[substr($k['head_number'], 0, 1)] = array('head_number' => substr($k['head_number'], 0, 1), 'name' => $k['name']);
                $res[substr($k['head_number'], 0, 1)]['children'] = array();
            } else if (substr($k['head_number'], 3, 3) == '000') {
                // if ($k['nature'] == 'Equity')
                //     $res[substr($k['head_number'], 0, 1)]['children'][substr($k['head_number'], 1, 2)] =  array('open' => true, 'head_number' => substr($k['head_number'], 1, 2), 'name' => $k['name'], 'children' => array());
                $res[substr($k['head_number'], 0, 1)]['children'][substr($k['head_number'], 1, 2)] =  array('open' => false, 'head_number' => substr($k['head_number'], 1, 2), 'name' => $k['name'], 'children' => array());
            } else {
                $cur = $this->get_trail_balance($k['id'], $filter);
                if (!empty($cur)) {
                    // echo json_encode($cur);
                    // die();
                    $res[substr($k['head_number'], 0, 1)]['children'][substr($k['head_number'], 1, 2)]['open'] = true;
                    $res[substr($k['head_number'], 0, 1)]['children'][substr($k['head_number'], 1, 2)]['children'][substr($k['head_number'], 3, 3)] =  array('head_number' =>  substr($k['head_number'], 3, 3), 'name' => $k['name'], 'id_head' => $k['id'], 'data' => $cur);
                }
                if ($k['nature'] == 'Equity') {
                    if (!empty($filter['akum_laba_rugi']['akum_laba_rugi']['ref_account'])) {
                        if ($filter['akum_laba_rugi']['akum_laba_rugi']['ref_account'] == $k['id']) {
                            $res[substr($k['head_number'], 0, 1)]['children'][substr($k['head_number'], 1, 2)]['open'] = true;
                            $res[substr($k['head_number'], 0, 1)]['children'][substr($k['head_number'], 1, 2)]['children'][substr($k['head_number'], 3, 3)] =  array('head_number' =>  substr($k['head_number'], 3, 3), 'name' => $k['name'], 'id_head' => $k['id'], 'data' => $income);
                            // echo json_encode($res[substr($k['head_number'], 0, 1)]['children'][substr($k['head_number'], 1, 2)]['children'][substr($k['head_number'], 3, 3)]);
                            // die();
                            // die();
                        }
                    }
                }
            }
        }
        return $res;
    }

    public function get_trail_balance($head_id, $filter = [])
    {
        $count_total_amt = 0;
        $this->db->select('ROUND(sum(IF(mp_sub_entry.type = 0,  mp_sub_entry.amount,-mp_sub_entry.amount)),2) as amount');
        // $this->db->select("dt_generalentry.id as transaction_id,dt_generalentry.date,dt_generalentry.naration,dt_generalentry.ref_number,mp_sub_entry.*");
        $this->db->from('mp_sub_entry');
        $this->db->join('dt_generalentry', 'dt_generalentry.id = mp_sub_entry.parent_id');
        $this->db->where('mp_sub_entry.accounthead', $head_id);
        if (!empty($filter['date_start'])) $this->db->where('dt_generalentry.date >=', $filter['date_start']);
        if (!empty($filter['date_end'])) $this->db->where('dt_generalentry.date <=', $filter['date_end']);

        $query = $this->db->get();
        return  $query->result_array()[0]['amount'];
    }

    public function income($filter = [])
    {
        $nature = array('Expense', 'Revenue');
        $this->db->select('ROUND(sum(IF(mp_sub_entry.type = 0,  mp_sub_entry.amount,-mp_sub_entry.amount)),2) as amount');
        $this->db->from('mp_sub_entry');
        $this->db->join('dt_generalentry', 'dt_generalentry.id = mp_sub_entry.parent_id');
        $this->db->join('dt_head', 'dt_head.id = mp_sub_entry.accounthead');
        $this->db->where_in('dt_head.nature', $nature);
        if (!empty($filter['date_start'])) $this->db->where('dt_generalentry.date >=', $filter['date_start']);
        if (!empty($filter['date_end'])) $this->db->where('dt_generalentry.date <=', $filter['date_end']);
        $query = $this->db->get();
        return  $query->result_array()[0]['amount'];
    }


    public function get_ledger_transactions($head_id, $filter)
    {

        $date1 = $filter['date_start'];
        $date2 = $filter['date_end'];
        $this->db->select("SUM(IF(mp_sub_entry.type = 0,amount, -amount)) as saldo_awal");
        $this->db->from('mp_sub_entry');
        $this->db->join('dt_head', "dt_head.id = mp_sub_entry.accounthead");
        $this->db->join('dt_generalentry', 'dt_generalentry.id = mp_sub_entry.parent_id');
        $this->db->order_by('dt_generalentry.date', 'asc');
        $year = explode('-', $date1)[0];
        $this->db->where('dt_head.id', $head_id);
        if ($date1 == $year . '-1-1' or $date1 == $year . '-01-1' or $date1 == $year . '-1-01' or $date1 == $year . '-01-01') {
            $this->db->where('dt_generalentry.id = -' . explode('-', $date1)[0]);
            $this->db->where('mp_sub_entry.parent_id = -' . explode('-', $date1)[0]);
        } else {
            $this->db->where('dt_generalentry.date >=', $year . '-1-1');
            $this->db->where('dt_generalentry.date <', $date1);
        }
        $query = $this->db->get();
        $query = $query->result_array();
        if (empty($query)) {
            $saldo_awal = 0;
        } else {
            $saldo_awal = $query[0]['saldo_awal'];
        }
        $this->db->select("dt_generalentry.id as transaction_id,dt_generalentry.date,dt_generalentry.ref_number,dt_generalentry.naration,dt_generalentry.ref_number,mp_sub_entry.*");
        $this->db->from('mp_sub_entry');
        $this->db->join('dt_head', "dt_head.id = mp_sub_entry.accounthead");
        $this->db->join('dt_generalentry', 'dt_generalentry.id = mp_sub_entry.parent_id');
        $this->db->where('dt_generalentry.id > 0');
        $this->db->where('mp_sub_entry.parent_id > 0');
        if (!empty($filter['search'])) {
            $this->db->where('(mp_sub_entry.sub_keterangan like "%' . $filter['search'] . '%" OR dt_generalentry.naration like "%' . $filter['search'] . '%")');
        }

        $this->db->where('dt_head.id', $head_id);
        $this->db->where('dt_generalentry.date >=', $date1);
        $this->db->where('dt_generalentry.date <=', $date2);
        $this->db->order_by('dt_generalentry.date', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $res['transactions'] = $query->result_array();
            $res['saldo_awal'] = $saldo_awal;
            return $res;
        } else {
            return NULL;
        }
    }
}
