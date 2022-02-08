<?php
/*

*/
class PembayaranModel extends CI_Model
{

    function getLastNoPembayaran()
    {
        $this->db->select("no_pembayaran");
        $this->db->from('mp_pembayaran');
        // if (!empty($id)) $this->db->where('id <> "' . $id . '"');
        // $this->db->where('no_invoice', $data);
        $this->db->order_by('no_pembayaran', 'DESC');
        $this->db->limit('1');

        $query = $this->db->get();
        $res =  $query->result_array();
        // echo json_encode($res);
        // die();
        if (!empty($res[0]['no_pembayaran'])) return $res[0]['no_pembayaran'];
        else return 0;
    }

    public function getAllPembayaran($filter = [])
    {
        $this->db->select('mpp.* , gen.ref_number , customer_name');
        $this->db->from('mp_pembayaran mpp');
        $this->db->join('dt_generalentry gen', 'gen.id = mpp.general_id', 'LEFT');
        $this->db->join('mp_payee pay', 'pay.id = mpp.customer_id', 'LEFT');

        if (!empty($filter['id'])) $this->db->where('mpp.id', $filter['id']);
        // if (!empty($filter['id_parent1'])) $this->db->where('gen.id', $filter['id']);
        // $this->db->order_by('gen.status, gen.id,  sub.id_item ', 'DESC');
        $res = $this->db->get();
        if (!empty($filter['by_id'])) {
            return DataStructure::keyValue($res->result_array(), 'id');
        }
        $res = $res->result_array();
        return $res;
    }

    public function getAllPembayaranWithItem($filter = [])
    {
        $this->db->select('mpp.* , customer_name,cus_address,gen.ref_number,sub.id as item_id,ac_1.agentname acc_1_name, ac_1.title_user as acc_1_title,sub.parent_id as parent_item, amount, qyt, date_item, keterangan_item, satuan');
        $this->db->from('mp_pembayaran mpp');
        $this->db->join('dt_generalentry gen', 'gen.id = mpp.general_id', 'LEFT');
        $this->db->join('mp_payee pay', 'mpp.customer_id = pay.id', 'LEFT');
        $this->db->join('mp_users ac_1', 'ac_1.id = mpp.acc_1', 'LEFT');

        $this->db->join('mp_sub_pembayaran sub', 'mpp.id = sub.parent_id', 'LEFT');
        if (!empty($filter['id'])) $this->db->where('mpp.id', $filter['id']);
        // if (!empty($filter['id_parent1'])) $this->db->where('gen.id', $filter['id']);
        // $this->db->order_by('gen.status, gen.id,  sub.id_item ', 'DESC');
        $res = $this->db->get();
        $ret = DataStructure::groupByRecursive2(
            $res->result_array(),
            ['id'],
            ['item_id'],
            [
                [
                    'id', 'ref_number', 'customer_name', 'cus_address', 'id', 'input_date', 'agen_id', 'acc_0', 'acc_1', 'acc_2', 'acc_3', 'date', 'acc_1_name', 'acc_1_title',
                    'description', 'customer_id', 'payment_metode', 'ppn_pph', 'no_pembayaran', 'inv_key', 'percent_jasa', 'percent_pph',
                    'am_jasa', 'am_pph', 'manual_math', 'par_label', 'par_am', 'sub_total', 'sub_total_2', 'jenis_pembayaran',
                    'lebih_bayar_ket', 'lebih_bayar_am', 'kurang_bayar_ket', 'kurang_bayar_am', 'pembulatan', 'payed', 'am_back', 'status_pembayaran', 'general_id'
                ],
                ["item_id", "amount", "qyt", "date_item", 'nopol', "keterangan_item", "satuan"]
            ],
            ['items'],
            false
        );
        // $res = $res->result_array();
        return $ret;
    }

    public function getAllPembayaranItem($filter = [])
    {
        $this->db->select("mp_sub_pembayaran.*");
        $this->db->from('mp_sub_pembayaran');
        $this->db->where('mp_sub_pembayaran.parent_id =', $filter['parent_id']);
        $sub_query = $this->db->get();
        if ($sub_query->num_rows() > 0) {
            $sub_query =  $sub_query->result_array();
            $sub_query;
        }
    }

    public function getAllPelunasan($filter = [])
    {
        $this->db->select('mpp.* , us.agentname , gen.ref_number');
        $this->db->from('dt_pelunasan_pembayaran mpp');
        $this->db->join('mp_users us', 'mpp.agen_id = us.id', 'LEFT');
        $this->db->join('dt_generalentry gen', 'gen.id = mpp.general_id', 'LEFT');
        if (!empty($filter['id'])) $this->db->where('mpp.id', $filter['id']);
        if (!empty($filter['parent_id'])) $this->db->where('mpp.parent_id', $filter['parent_id']);
        if (!empty($filter['ex_id'])) $this->db->where('mpp.id <> ' . $filter['ex_id']);
        // if (!empty($filter['id_parent1'])) $this->db->where('gen.id', $filter['id']);
        // $this->db->order_by('gen.status, gen.id,  sub.id_item ', 'DESC');
        $res = $this->db->get();
        if (!empty($filter['by_id'])) {
            return DataStructure::keyValue($res->result_array(), 'id');
        }
        $res = $res->result_array();
        return $res;
    }





    public function gen_number($data = [])
    {
        $this->db->from('dt_generalentry');
        // $this->db->from('limit', 1);
        $this->db->limit(1);
        $this->db->order_by('date,id', 'DESC');

        // if (!empty($filter['account_head'])) $this->db->where('dt_head.id', $filter['account_head']);
        // if (!empty($filter['id'])) $this->db->where('dt_head.id', $filter['id']);
        $this->db->where('generated_source', $data['generated_source']);
        $this->db->where('MONTH(DATE)', explode('-', $data['date'])[1]);
        $this->db->where('YEAR(DATE)', explode('-', $data['date'])[0]);
        $query = $this->db->get();
        // if (!empty($filter['by_id'])) {
        $res =  $query->result_array();
        if ($data['generated_source'] == 'deposit') {
            $s2 = 'DEP';
        } else if ($data['generated_source'] == 'paid') {
            $s2 = 'CEK';
        } else if ($data['generated_source'] == 'invoice') {
            $s2 = 'INV';
        } else {
            $s2 = 'JV';
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
            // echo $numlength;
            // echo $number;
            // die();
        } else {
            $res_num = '001';
        }
        $number .= $res_num;
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

    public  function addInvoice($data, $trans)
    {
        $this->db->trans_begin();
        // $data['generalentry']['ref_number'] = $this->gen_number($data['generalentry']);
        // $this->db->insert('dt_generalentry', $data['generalentry']);

        // $order_id = $this->db->insert_id();
        // $i = 0;
        // foreach ($data['sub_entry'] as $sub) {
        //     $data['sub_entry'][$i]['parent_id'] = $order_id;
        //     $this->db->insert('mp_sub_entry', $data['sub_entry'][$i]);
        //     $i++;
        // }

        $kembalian = $trans['amount_recieved'] - ($trans['total_gross_amt'] + $trans['total_tax_amt'] - $trans['discountfield']);
        if ($kembalian < 0) {
            $status = 'unpaid';
        } else {
            $status = 'paid';
        }
        $data_trans = array(
            // 'id_parent1' => $order_id,
            'source' => 'invoice',
            'customer_id' => $trans['customer_id'],
            'total_gross' => $trans['total_gross_amt'],
            'total_tax' => $trans['total_tax_amt'],
            'discount' => $trans['discountfield'],
            'amount_received_1' => $trans['amount_recieved'],
            'amount_back_1' => $kembalian,
            'status' => $status,
            'date_1' => $trans['date'],
            'payment_id' => $trans['payment_id']

        );

        $this->db->insert('dt_transaction', $data_trans);
        $id_trans = $this->db->insert_id();
        // $order_id = $this->db->insert_id();
        $i = 0;
        foreach ($trans['row_price'] as $sub) {
            if (!empty($trans['row_price'][$i]) && !empty($trans['row_qyt'][$i]) && !empty($trans['item_id'][$i])) {
                $data_trans = array(
                    'id_parent' => $id_trans,
                    'id_product' => $trans['item_id'][$i],
                    'price' => $trans['row_price'][$i],
                    'tax' => $trans['fix_tax'][$i],
                    'qyt' => $trans['row_qyt'][$i]
                );
                $this->db->insert('dt_transaction_item', $data_trans);
            }
            $i++;
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            ExceptionHandler::handleDBError($this->db->error(), "Delete Bank", "Bank");
        } else {

            $this->db->trans_commit();
            return $id_trans;
        }
    }


    function pembayaran_entry($data)
    {
        $this->db->trans_start();
        $trans_data = array(
            'date' => $data['date'],
            'description' => $data['description'],
            'customer_id' => $data['customer_id'],
            'payment_metode' => $data['payment_method'],
            // 'ppn_pph' => $data['ppn_pph'],
            'percent_jasa' => $data['percent_jasa'],
            // 'percent_pph' => $data['percent_pph'],
            'manual_math' => $data['manual_math'],
            'am_jasa' => $data['am_jasa'],
            // 'am_pph' => $data['am_pph'],
            // 'lebih_bayar_am' => $data['lebih_bayar_am'],
            // 'kurang_bayar_am' => $data['kurang_bayar_am'],
            // 'lebih_bayar_ket' => $data['lebih_bayar_ket'],
            // 'kurang_bayar_ket' => $data['kurang_bayar_ket'],
            'jenis_pembayaran' => $data['jenis_pembayaran'],
            'sub_total' => $data['sub_total'],
            'sub_total_2' => $data['sub_total_2'],
            // 'pembulatan' => $data['pembulatan'],
            'status_pembayaran' => $data['status_pembayaran'],
            // 'payed' => $data['payed'],
            // 'lebih_bayar_ac' => $data['lebih_bayar_ac'],
            // 'kurang_bayar_ac' => $data['kurang_bayar_ac'],
            // 'inv_key' => $generateRandomString,
            'acc_1' => $data['acc_1'],
            // 'acc_2' => $data['acc_2'],
            // 'acc_3' => $data['acc_3'],
            'acc_0' => $this->session->userdata('user_id')['name'],
            'agen_id' => $this->session->userdata('user_id')['id'],
        );

        $this->db->insert('mp_pembayaran', $trans_data);
        $order_id = $this->db->insert_id();
        $total_heads = count($data['amount']);
        for ($i = 0; $i < $total_heads; $i++) {

            if (!empty($data['amount'][$i] && !empty($data['qyt'][$i]))) {
                $trans_data  = array(
                    'parent_id'   => $order_id,
                    'qyt' => $data['qyt'][$i],
                    'satuan' => $data['satuan'][$i],
                    'date_item' => $data['date_item'][$i],
                    // 'nopol' => $data['nopol'][$i],
                    'keterangan_item' => $data['keterangan_item'][$i],
                    'amount'      => substr($data['amount'][$i], 0, -2) . '.' . substr($data['amount'][$i], -2),
                );
                $this->db->insert('mp_sub_pembayaran', $trans_data);
            }
        }

        $data['generalentry']['ref_url'] = 'pembayaran/show/' . $order_id;
        $this->db->insert('dt_generalentry', $data['generalentry']);

        $gen_id = $this->db->insert_id();

        foreach ($data['sub_entry'] as $sub) {
            $sub['parent_id'] = $gen_id;
            $this->db->insert('mp_sub_entry', $sub);
        }

        $this->db->set('general_id', $gen_id);
        $this->db->where('id', $order_id);
        $this->db->update('mp_pembayaran');

        $this->db->set("acc_0", $this->session->userdata('user_id')['name']);
        $this->db->set("date_acc_0", date('Y-m-d'));
        $this->db->set("acc_1", $data['acc_1']);
        $this->db->set("id_transaction", $gen_id);
        $this->db->insert('mp_approv');

        $this->record_activity(array('jenis' => '0', 'color' => 'primary', 'url_activity' => 'pembayaran/show/' . $order_id, 'sub_id' => $order_id, 'desk' => 'Entry Pembayaran'));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $data = NULL;
            return NULL;
        } else {
            $this->db->trans_commit();
        }
        return array('order_id' => $order_id, 'parent2_id' => $gen_id);
    }

    function pembayaran_edit($data)
    {

        $trans_data = array(
            'date' => $data['date'],
            'description' => $data['description'],
            'customer_id' => $data['customer_id'],
            'payment_metode' => $data['payment_method'],
            // 'ppn_pph' => $data['ppn_pph'],
            'percent_jasa' => $data['percent_jasa'],
            // 'percent_pph' => $data['percent_pph'],
            'manual_math' => $data['manual_math'],
            'am_jasa' => $data['am_jasa'],
            // 'am_pph' => $data['am_pph'],
            // 'lebih_bayar_am' => $data['lebih_bayar_am'],
            // 'kurang_bayar_am' => $data['kurang_bayar_am'],
            // 'lebih_bayar_ket' => $data['lebih_bayar_ket'],
            // 'kurang_bayar_ket' => $data['kurang_bayar_ket'],
            'jenis_pembayaran' => $data['jenis_pembayaran'],
            'sub_total' => $data['sub_total'],
            'sub_total_2' => $data['sub_total_2'],
            // 'pembulatan' => $data['pembulatan'],
            'status_pembayaran' => $data['status_pembayaran'],
            // 'payed' => $data['payed'],
            // 'lebih_bayar_ac' => $data['lebih_bayar_ac'],
            // 'kurang_bayar_ac' => $data['kurang_bayar_ac'],
            // 'inv_key' => $generateRandomString,
            'acc_1' => $data['acc_1'],
            // 'acc_2' => $data['acc_2'],
            // 'acc_3' => $data['acc_3'],
            'acc_0' => $this->session->userdata('user_id')['name'],
            'agen_id' => $this->session->userdata('user_id')['id'],
        );

        $this->db->trans_start();
        $this->db->where('id', $data['id']);
        $this->db->update('mp_pembayaran', $trans_data);
        $total_heads = count($data['amount']);

        for ($i = 0; $i < $total_heads; $i++) {
            if (!empty($data['id_item'][$i])) {
                if (!empty($data['amount'][$i] && !empty($data['qyt'][$i]))) {
                    $trans_data  = array(
                        'qyt' => $data['qyt'][$i],
                        'satuan' => $data['satuan'][$i],
                        'date_item' => $data['date_item'][$i],
                        'keterangan_item' => $data['keterangan_item'][$i],
                        'amount'      => substr($data['amount'][$i], 0, -2) . '.' . substr($data['amount'][$i], -2),
                    );
                    $this->db->where(
                        'mp_sub_pembayaran.id',
                        $data['id_item'][$i]
                    );
                    $this->db->where('mp_sub_pembayaran.parent_id', $data['id']);
                    $this->db->update('mp_sub_pembayaran', $trans_data);
                } else {
                    $this->db->where(
                        'mp_sub_pembayaran.id',
                        $data['id_item'][$i]
                    );
                    // $this->db->where('mp_sub_invoice.parent_id', $data['id']);
                    $this->db->delete('mp_sub_pembayaran');
                }
            } else if (!empty($data['amount'][$i] && !empty($data['qyt'][$i]))) {
                $trans_data  = array(
                    'parent_id'   => $data['id'],
                    'satuan' => $data['satuan'][$i],
                    'qyt' => $data['qyt'][$i],
                    'date_item' => $data['date_item'][$i],
                    'keterangan_item' => $data['keterangan_item'][$i],
                    'amount'      => substr($data['amount'][$i], 0, -2) . '.' . substr($data['amount'][$i], -2),
                );
                $this->db->insert('mp_sub_pembayaran', $trans_data);
            }
        }

        // UPDATE GENERAL ENTRY 

        $this->db->where('id', $data['old_data']['general_id']);
        $this->db->update('dt_generalentry', $data['generalentry']);

        $this->db->where('parent_id', $data['old_data']['general_id']);
        $this->db->delete('mp_sub_entry');

        foreach ($data['sub_entry'] as $sub) {
            $sub['parent_id'] = $data['old_data']['general_id'];
            $this->db->insert('mp_sub_entry', $sub);
        }

        $this->db->set("acc_0", $this->session->userdata('user_id')['name']);
        $this->db->set("acc_1", $data['acc_1']);
        $this->db->set("date_acc_0", date('Y-m-d'));
        $this->db->where("id_transaction", $data['old_data']['general_id']);
        $this->db->update('mp_approv');

        $this->record_activity(array('jenis' => '0', 'color' => 'primary', 'url_activity' => 'pembayaran/show/' . $data['id'], 'sub_id' => $data['id'], 'desk' => 'Edit Pembayaran'));


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $data = NULL;
            return NULL;
        } else {
            $this->db->trans_commit();
            $this->record_activity(array('jenis' => 8, 'sub_id' => $data['id'], 'desk' => 'Edit Pembayaran'));
        }

        return $data['id'];
    }

    function add_pelunasan($data)
    {
        $this->db->trans_start();
        $trans_data = array(
            'parent_id' => $data['parent_id'],
            'date_pembayaran' => $data['date_pembayaran'],
            'nominal' => $data['nominal'],
            'agen_id' => $this->session->userdata('user_id')['id'],
        );


        $this->db->insert('dt_pelunasan_pembayaran', $trans_data);
        $order_id = $this->db->insert_id();
        if (!empty($data['child_pembayaran']))
            foreach ($data['child_pembayaran'] as $sub) {
                $sub['id_pelunasan'] = $order_id;
                $this->db->insert('dt_pel_pem_potongan', $sub);
            }
        if (!empty($data['child_lebih']))
            foreach ($data['child_lebih'] as $sub_b) {
                $sub_b['id_pelunasan'] = $order_id;
                $this->db->insert('dt_pel_pem_lebih', $sub_b);
            }
        $data['generalentry']['ref_url'] = 'pembayaran/show/' . $data['parent_id'];
        $this->db->insert('dt_generalentry', $data['generalentry']);

        $gen_id = $this->db->insert_id();

        foreach ($data['sub_entry'] as $sub) {
            $sub['parent_id'] = $gen_id;
            $this->db->insert('mp_sub_entry', $sub);
        }


        $this->db->set('general_id', $gen_id);
        $this->db->where('id', $order_id);
        $this->db->update('dt_pelunasan_pembayaran');

        $this->db->set("status_pembayaran", $data['status_pembayaran']);
        $this->db->where("id", $data['parent_id']);
        $this->db->update('mp_pembayaran');

        $this->db->set("acc_0", $this->session->userdata('user_id')['name']);
        $this->db->set("date_acc_0", date('Y-m-d'));
        $this->db->set("id_transaction", $gen_id);
        $this->db->insert('mp_approv');

        $this->record_activity(array('jenis' => '0', 'color' => 'primary', 'url_activity' => 'pembayaran/show/' . $data['parent_id'], 'sub_id' => $order_id, 'desk' => 'Entry Pembayaran Invoice'));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $data = NULL;
            return NULL;
        } else {
            $this->db->trans_commit();
        }
        return array('order_id' => $order_id, 'parent2_id' => $gen_id);
    }

    function edit_pelunasan($data)
    {
        $this->db->trans_start();
        $trans_data = array(
            'parent_id' => $data['parent_id'],
            'date_pembayaran' => $data['date_pembayaran'],
            'nominal' => $data['nominal'],
            'agen_id' => $this->session->userdata('user_id')['id'],
        );

        $this->db->where('id', $data['id']);
        $this->db->update('dt_pelunasan_pembayaran', $trans_data);

        $this->db->where('id_pelunasan', $data['id']);
        $this->db->delete('dt_pel_pem_potongan');

        $this->db->where('id_pelunasan', $data['id']);
        $this->db->delete('dt_pel_pem_lebih');


        if (!empty($data['child_pembayaran']))
            foreach ($data['child_pembayaran'] as $sub) {
                $sub['id_pelunasan'] = $data['id'];
                $this->db->insert('dt_pel_pem_potongan', $sub);
            }
        if (!empty($data['child_lebih']))
            foreach ($data['child_lebih'] as $sub_b) {
                $sub_b['id_pelunasan'] = $data['id'];
                $this->db->insert('dt_pel_pem_lebih', $sub_b);
            }
        // $order_id = $this->db->insert_id();


        //   UPDATE GENERALENTRY
        $this->db->where('id', $data['generalentry']['id']);
        $this->db->update('dt_generalentry', $data['generalentry']);

        $this->db->where('parent_id', $data['generalentry']['id']);
        $this->db->delete('mp_sub_entry');

        foreach ($data['sub_entry'] as $sub) {
            $sub['parent_id'] = $data['generalentry']['id'];
            $this->db->insert('mp_sub_entry', $sub);
        }

        $this->db->set("acc_0", $this->session->userdata('user_id')['name']);
        $this->db->set("date_acc_0", date('Y-m-d'));
        $this->db->where("id_transaction", $data['generalentry']['id']);
        $this->db->update('mp_approv');

        $this->db->set("status_pembayaran", $data['status_pembayaran']);
        $this->db->where("id", $data['parent_id']);
        $this->db->update('mp_pembayaran');

        $this->record_activity(array('jenis' => '0', 'color' => 'primary', 'url_activity' => 'pembayaran/show/' . $data['parent_id'], 'sub_id' => $data['parent_id'], 'desk' => 'Edit Invoice'));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $data = NULL;
            return NULL;
        } else {
            $this->db->trans_commit();
        }
        return array('order_id' => $data['parent_id'], 'parent2_id' => $data['parent_id']);
    }

    function delete_pelunasan($data)
    {
        $this->db->trans_start();


        $this->db->where('id', $data['id']);
        $this->db->delete('dt_pelunasan_pembayaran');
        // $order_id = $this->db->insert_id();

        // $data['generalentry']['url'] = 'pembayaran/show/' . $data['parent_id'];
        $this->db->where('id', $data['self_data']['general_id']);
        $this->db->delete('dt_generalentry');

        // $gen_id = $this->db->insert_id();
        $this->db->where('parent_id', $data['self_data']['general_id']);
        $this->db->delete('mp_sub_entry');

        // $this->db->set('general_id', $gen_id);
        // $this->db->where('id', $order_id);
        // $this->db->update('dt_pelunasan_pembayaran');

        $this->db->where("id_transaction", $data['self_data']['general_id']);
        $this->db->delete('mp_approv');

        $this->db->set("status_pembayaran", $data['status_pembayaran']);
        $this->db->where("id", $data['old_data']['id']);
        $this->db->update('mp_pembayaran');


        $this->record_activity(array('jenis' => '0', 'color' => 'primary',  'desk' => 'Delelte Pembayaran'));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $data = NULL;
            return NULL;
        } else {
            $this->db->trans_commit();
        }
        // return array('order_id' => $data['parent_id'], 'parent2_id' => $data['parent_id']);
    }

    public function delete($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->delete('mp_pembayaran');


        $this->db->where('parent_id', $id);
        $this->db->delete('mp_sub_pembayaran');

        $this->db->where('parent_id', $data['id']);
        $this->db->delete('dt_pelunasan_pembayaran');


        if (!empty($data['general_id'])) {
            $this->db->where('id', $data['general_id']);
            $this->db->delete('dt_generalentry');
        }
        if (!empty($data['data_pelunasan'])) {
            foreach ($data['data_pelunasan'] as $dp) {

                $this->db->where('id', $dp['general_id']);
                $this->db->delete('dt_generalentry');
            }
        }
        $this->record_activity(array('jenis' => 6, 'sub_id' => $id, 'desk' => 'Delete Invoice'));
    }


    function record_activity($data)
    {
        // $sub_data  = array(
        $data['user_id']  = $this->session->userdata('user_id')['id'];
        //     'jenis'   => $data['jenis'],
        //     'desk'   => $data['desk'],
        //     'sub_id'   => $data['sub_id']
        // );

        $this->db->insert('mp_activity', $data);
    }
}
