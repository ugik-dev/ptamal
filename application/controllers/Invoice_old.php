<?php
/*

*/
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\Writer\Word2007;

class Invoice extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('General_model', 'InvoiceModel'));
		// $this->load->helper(array('DataStructure'));
		$this->db->db_debug = TRUE;
	}

	public function index($data_return = NULL)
	{
		try {
			$crud = $this->SecurityModel->Aksessbility_VCRUD('invoice', '', 'view');
			// $data['accounts'] = $this->Accounting_model->getAllBaganAkun(array('by_DataStructure' => true, 'nature' => 'Assets'));
			$data['patner_record'] = $this->General_model->getAllPayee();
			$data['jenis_invoice'] = $this->General_model->getAllJenisInvoice();
			$data['satuan'] = $this->General_model->getAllUnit();
			$data['ref_account'] = $this->General_model->getAllRefAccount(array('ref_type' => 'payment_method'));
			$data['form_url'] = 'create_invoice';

			$data['data_return'] = $data_return;
			$data['title'] = 'Form Invoice';
			$data['table_name'] = 'Buat Data Baru';
			$data['main_view'] = 'invoice/form_invoice';
			$data['vcrud'] = $crud;
			$this->load->view('main/index2.php', $data);
		} catch (Exception $e) {
			ExceptionHandler::handle($e);
		}
	}


	public function editJenisInvoice()
	{
		try {
			$crud = $this->SecurityModel->Aksessbility_VCRUD('invoice', 'jenis_invoice', 'update');
			$data = $this->input->post();
			$this->InvoiceModel->editJenisInvoice($data);
			$data = $this->General_model->getAllJenisInvoice(array('id' =>  $data['id'], 'by_id' => true))[$data['id']];
			echo json_encode(array("error" => false, "data" => $data));
		} catch (Exception $e) {
			ExceptionHandler::handle($e);
		}
	}

	public function deleteJenisInvoice()
	{
		try {
			$crud = $this->SecurityModel->Aksessbility_VCRUD('invoice', 'jenis_invoice', 'delete');
			$data = $this->input->get();
			$this->InvoiceModel->deleteJenisInvoice($data);
			echo json_encode(array("error" => false, "data" => $data));
		} catch (Exception $e) {
			ExceptionHandler::handle($e);
		}
	}

	public function addJenisInvoice()
	{
		try {
			$crud = $this->SecurityModel->Aksessbility_VCRUD('invoice', 'jenis_invoice', 'create');
			$data = $this->input->post();
			$id = $this->InvoiceModel->addJenisInvoice($data);
			$data = $this->General_model->getAllJenisInvoice(array('id' =>  $id, 'by_id' => true))[$id];
			echo json_encode(array("error" => false, "data" => $data));
		} catch (Exception $e) {
			ExceptionHandler::handle($e);
		}
	}





	public function delete($id)
	{
		$this->load->model(array('SecurityModel', 'InvoiceModel'));
		$this->SecurityModel->MultiplerolesStatus(array('Akuntansi', 'Invoice'), TRUE);
		$dataContent = $this->InvoiceModel->getAllInvoice(array('id' =>  $id))[0];
		$dataContent['data_pelunasan'] = $this->Invoice_model->getAllPelunasan(array('parent_id' => $id));

		if ($dataContent['agen_id'] != $this->session->userdata('user_id')['id'])
			throw new UserException('Sorry, Yang dapat mengahapus dan edit hanya agen yang bersangkutan', UNAUTHORIZED_CODE);

		$this->InvoiceModel->delete($id, $dataContent);
		$array_msg = array(
			'msg' => '<i style="color:#fff" class="fa fa-check-circle-o" aria-hidden="true"></i> Delete Successfully',
			'alert' => 'info'
		);
		$this->session->set_flashdata('status', $array_msg);
		// $this->index($data);
		// return;
		redirect('invoice/history');
	}

	public function history()
	{

		// DEFINES PAGE TITLE
		$data['title'] = 'Invoice';

		$collection = array();

		// DEFINES TO LOAD THE MODEL
		$this->load->model('Accounts_model');
		$filter['first_date'] = html_escape($this->input->post('date1'));
		$filter['second_date'] = html_escape($this->input->post('date2'));
		$filter['no_invoice'] = html_escape($this->input->post('invoice_no'));

		if ($filter['first_date'] == NULL && $filter['second_date'] == NULL) {
			$filter['first_date'] = date('Y-m-01');
			$filter['second_date'] = date('Y-m-31');

			// FETCH SALES RECORD FROM invoices TABLE
			// $result_invoices = $this->Accounts_model->get('mp_invoices', $first_date, $second_date);
		}
		$data['filter'] = $filter;
		$this->load->model(array('InvoiceModel'));
		// $this->SecurityModel->rolesOnlyGuard(array('accounting'), TRUE);

		$result_invoices = $this->InvoiceModel->getAllInvoice($filter);
		$data['Model_Title'] = "Edit invoice";
		$data['Model_Button_Title'] = "Update invoices";
		$data['invoices_Record'] = $result_invoices;

		// var_dump($data);
		// die();
		$data['main_view'] = 'invoice/list_invoice';
		$this->load->view('main/index.php', $data);
	}

	public function edit($id)
	{
		try {

			$crud = $this->SecurityModel->Aksessbility_VCRUD('invoice', '', 'update');
			$this->SecurityModel->MultiplerolesStatus(array('Akuntansi', 'Invoice'), TRUE);

			$dataContent = $this->InvoiceModel->getAllInvoice(array('id' =>  $id))[0];
			if ($dataContent['agen_id'] != $this->session->userdata('user_id')['id'])
				throw new UserException('Sorry, Yang dapat mengahapus dan edit hanya agen yang bersangkutan', UNAUTHORIZED_CODE);
			if ($id != NULL) {
				$item = count($dataContent['item']);

				// die();
				for ($i = 0; $i < $item; $i++) {
					// if (!empty($data['amount'][$i]) && !empty($data['qyt'][$i]))
					// 	$status = TRUE;
					$dataContent['id_item'][$i] =  $dataContent['item'][$i]->id;
					$dataContent['amount'][$i] = preg_replace("/[^0-9]/", "", $dataContent['item'][$i]->amount);
					$dataContent['date_item'][$i] =  $dataContent['item'][$i]->date_item;
					$dataContent['satuan'][$i] =  $dataContent['item'][$i]->satuan;

					$dataContent['keterangan_item'][$i] =  $dataContent['item'][$i]->keterangan_item;
					$dataContent['qyt'][$i] =  $dataContent['item'][$i]->qyt;
					// $dataContent['qyt'][$i] = preg_replace("/[^0-9]/", "", $dataContent['item'][$i]->qyt);
				}
			} else {
				echo 'ngapain cok';
				return;
			}
			// echo json_encode($item);
			// echo json_encode($dataContent);
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
			$data['jenis_invoice'] = $this->General_model->getAllJenisInvoice();
			$data['satuan'] = $this->General_model->getAllUnit();
			$data['ref_account'] = $this->General_model->getAllRefAccount(array('ref_type' => 'payment_method'));

			$data['form_url'] = 'edit_process_invoice';
			// DEFINES WHICH PAGE TO RENDER
			$data['main_view'] = 'invoice/form_invoice';

			// DEFINES GO TO MAIN FOLDER FOND INDEX.PHP  AND PASS THE ARRAY OF DATA TO THIS PAGE
			$this->load->view('main/index.php', $data);
		} catch (Exception $e) {
			ExceptionHandler::handle($e);
		}
	}

	public function copy($id)
	{
		$this->load->model(array('SecurityModel', 'InvoiceModel'));
		$this->SecurityModel->MultiplerolesStatus(array('Akuntansi', 'Invoice'), TRUE);

		if ($id != NULL) {
			$dataContent = $this->InvoiceModel->getAllInvoice(array('id' =>  $id))[0];
			$dataContent['id'] = '';
			$item = count($dataContent['item']);
			for ($i = 0; $i < $item; $i++) {
				// if (!empty($data['amount'][$i]) && !empty($data['qyt'][$i]))
				// 	$status = TRUE;
				$dataContent['amount'][$i] = preg_replace("/[^0-9]/", "", $dataContent['item'][$i]->amount);
				$dataContent['date_item'][$i] =  $dataContent['item'][$i]->date_item;
				$dataContent['keterangan_item'][$i] =  $dataContent['item'][$i]->keterangan_item;
				$dataContent['satuan'][$i] =  $dataContent['item'][$i]->satuan;

				$dataContent['qyt'][$i] =  $dataContent['item'][$i]->qyt;
				// $dataContent['qyt'][$i] = preg_replace("/[^0-9]/", "", $dataContent['item'][$i]->qyt);
			}
		} else {
			echo 'ngapain cok';
			return;
		}
		// echo json_encode($item);
		// echo json_encode($dataContent);
		$this->index($dataContent);
	}

	function head_invoice($pdf, $dataContent)
	{
		$pdf->Image(base_url() . 'assets/img/bg-invoice.jpg', 10, 10, 190, 50);
		$pdf->Image(base_url() . "assets/img/ima-outline-blue.png", 20, 15, 20, 20);
		$pdf->Image(base_url() . "assets/img/ima-text-white.png", 40, 15, 76, 20);

		$pdf->Cell(173, 20, '', 0, 1, 'C');
		$pdf->SetDrawColor(255, 255, 225);
		$pdf->SetTextColor(255, 255, 225);
		$pdf->Cell(10, 30, '', 0, 0, 'C');
		$pdf->Cell(173, 6, 'Jalan Sanggul Dewa No.6 Pangkalpinang', 0, 1);
		$pdf->Cell(10, 30, '', 0, 0, 'C');
		$pdf->Cell(173, 6, 'Bangka Belitung - Indonesia', 0, 1);
		$pdf->SetLineWidth(1);
		$pdf->Line(116, 20, 116, 53);
		$pdf->SetXY(0, 0);
		$pdf->Cell(179, 20, '', 0, 1, 'C');
		$pdf->SetFont('Arial', 'B', 30);
		$pdf->Cell(96, 12, '', 0, 0, 'R');
		$pdf->Cell(77, 12, 'INVOICE', 0, 1, 'R');
		$pdf->SetFont('Arial', 'B', 15);
		$pdf->Cell(96, 6, '', 0, 0, 'R');
		$pdf->Cell(77, 6, 'Number#', 0, 1, 'R');
		$pdf->Cell(96, 6, '', 0, 0, 'R');
		$pdf->Cell(77, 6,  $dataContent['no_invoice'], 0, 1, 'R');
		$pdf->SetFont('Arial', 'BI', 14);
		$pdf->SetDrawColor(0, 0, 0);
		$pdf->SetXY(12, 65);
	}

	function tanggal_indonesia($tanggal)
	{
		if (empty($tanggal)) return '';
		$BULAN = [0, 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
		$t = explode('-', $tanggal);
		return "{$t[2]} {$BULAN[intval($t[1])]} {$t[0]}";
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
	public function show($invoice_no)
	{

		// DEFINES PAGE TITLE
		$data['title'] = 'Invoice';

		$collection = array();

		// DEFINES TO LOAD THE MODEL
		$this->load->model('InvoiceModel');
		if ($invoice_no != NULL) {
			$result = $this->InvoiceModel->getAllInvoice(array('id' =>  $invoice_no));

			if (empty($result)) {
				$data['main_view'] = 'error-5';
				$data['message'] = 'Sepertinya data yang anda cari tidak ditemukan atau sudah di hapus.';
			} else {
				$data['dataContent'] = $result[0];
				$data['main_view'] = 'invoice/invoice_detail';
				$data['payment_metode'] = $this->General_model->getAllRefAccount(array('ref_id' => $data['dataContent']['payment_metode']))[0];
				$data['customer_data'] = $this->General_model->getAllPayee(array('id' => $data['dataContent']['customer_id']));
			}
			echo json_encode($data);
			die();

			// DEFINES GO TO MAIN FOLDER FOND INDEX.PHP  AND PASS THE ARRAY OF DATA TO THIS PAGE
			$this->load->view('main/index.php', $data);
			return;
		} else {
			echo 'NOT FOUND';
			return;
		}
	}


	//invoice/popup
	//DEFINES A POPUP MODEL OG GIVEN PARAMETER




	function create_invoice()
	{
		try {
			$crud = $this->SecurityModel->Aksessbility_VCRUD('invoice', '', 'create');

			$status = FALSE;
			$data = $this->input->post();
			// echo json_encode($data);
			// die();
			if (empty($data['manual_math'])) {
				$data['manual_math'] = 'off';
			}
			if ($data['manual_math'] == 'on') {
				$data['manual_math'] = 1;
			} else {
				$data['manual_math'] = 0;
			}
			// if ($res != 0) {
			// 	throw new UserException('Nomor Invoice sudah ada!!');
			// }

			$count_rows = count($data['amount']);
			// if()
			// if (empty($data['ppn_pph'])) {
			// 	$data['ppn_pph'] = '0';
			// } else {
			// 	$data['ppn_pph'] = '1';
			// }
			$data['fee_amount'] = substr(preg_replace("/[^0-9]/", "", $data['fee_amount']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['fee_amount']), -2);
			$data['ppn_amount'] = substr(preg_replace("/[^0-9]/", "", $data['ppn_amount']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['ppn_amount']), -2);
			$data['pph_amount'] = substr(preg_replace("/[^0-9]/", "", $data['pph_amount']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['pph_amount']), -2);
			$data['sub_total'] = substr(preg_replace("/[^0-9]/", "", $data['sub_total']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['sub_total']), -2);
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
				$jp = $this->General_model->getAllJenisInvoice(array('by_id' => true, 'id' => $data['jenis_invoice']))[$data['jenis_invoice']];
				$no_jur = $this->General_model->gen_number_jurnal(array('code' => $jp['ref_nojur'], 'date' => $data['date']));

				$data['generalentry'] = array(
					'date' => $data['date'],
					'naration' => '' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
					'customer_id' => $data['customer_id'],
					'ref_number' => $no_jur,
					'generated_source' => 'invoice'
				);
				// $data['generalentry']['no_jurnal'] = $this->General_model->gen_number($data['date'], $jp['ref_nojur']);
				$i = 0;
				$data['status'] = 'unpaid';
				// NEW CODE
				// echo json_encode($data);
				// die();
				$data['sub_entry'][$i] = array(
					'accounthead' => $jp['ac_unpaid'],
					'type' => 0,
					'sub_keterangan' => 'Piut ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
					'amount' => $data['sub_total'] + $data['fee_amount'],
				);
				$i++;
				$data['sub_entry'][$i] = array(
					'accounthead' => $jp['ac_paid'],
					'type' => 1,
					'sub_keterangan' => 'Pdpt ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
					'amount' => $data['sub_total'] + $data['fee_amount'],
				);
				$i++;
				if ($data['ppn_amount'] > 0) {
					// $data['ppn_pph_count'] = $data['ppn_pph_count'] * 0.1;
					$data['sub_entry'][$i] = array(
						'accounthead' => $jp['ac_ppn'],
						'type' => 1,
						'sub_keterangan' => 'PPN ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
						'amount' => $data['ppn_amount'],
					);
					$i++;
					$data['sub_entry'][$i] = array(
						'accounthead' => $jp['ac_ppn_piut'],
						'type' => 0,
						'sub_keterangan' => 'Piut PPN ' . (!empty($jp['text_jurnal']) ? $jp['text_jurnal'] . ' ' : '') . $data['description'],
						'amount' => $data['ppn_amount'],
					);
					$i++;
				}
				// echo json_encode($data['sub_entry']);
				// die();

				// dari sini untuk saat pelunasan
				// $uang_muka_pph = number_format(($data['sub_total'] * 0.02), 2, '.', '');
				// $ref = $this->General_model->getAllRefAccount(array('by_id' => true, 'ref_id' => $data['payment_metode']))[$data['payment_metode']];
				// $data['sub_entry'][$i] = array(
				// 	'accounthead' => $ref['ref_account'],
				// 	'type' => 0,
				// 	'sub_keterangan' => 'kas ' . $data['description'],
				// 	'amount' => $data['sub_total'],
				// );
				// $i++;
				// $ref = $this->General_model->getAllRefAccount(array('ref_type' => 'um_pph_23'))[0];
				// $data['sub_entry'][$i] = array(
				// 	'accounthead' => $ref['ref_account'],
				// 	'type' => 1,
				// 	'sub_keterangan' => 'uang muka ' . $data['description'],
				// 	'amount' => $uang_muka_pph
				// );
				// $i++;
				// $data['sub_entry'][$i] = array(
				// 	'accounthead' => $jp['ac_unpaid'],
				// 	'type' => 1,
				// 	'sub_keterangan' => "piutang " . $data['description'],
				// 	'amount' => $data['sub_total'] - $uang_muka_pph
				// );
				// $i++;

				// end saat pelunasan

				// echo json_encode($data);
				// die();

				// $data['old_data'] = $this->Payment_model->getAllPembayaran(array('id' => $data['id'], 'by_id' => true))[$data['id']];
				$result = $this->InvoiceModel->invoice_entry($data);
			} else {
				throw new UserException('Please check data!');
			}
			echo json_encode(array('error' => false, 'data' => $data));
		} catch (Exception $e) {
			ExceptionHandler::handle($e);
		}
	}

	function edit_process_invoice()
	{
		try {
			$status = FALSE;
			$data = $this->input->post();
			// echo json_encode($data);
			// die();
			if (empty($data['manual_math'])) {
				$data['manual_math'] = 'off';
			}
			if ($data['manual_math'] == 'on') {
				$data['manual_math'] = 1;
			} else {
				$data['manual_math'] = 0;
			}
			$res = $this->Invoice_model->check_no_invoice($data['no_invoice']);
			// if ($res != 0) {
			// 	throw new UserException('Nomor Invoice sudah ada!!');
			// }

			$count_rows = count($data['amount']);
			// if()
			if (empty($data['ppn_pph'])) {
				$data['ppn_pph'] = '0';
			} else {
				$data['ppn_pph'] = '1';
			}
			$data['sub_total'] = substr(preg_replace("/[^0-9]/", "", $data['sub_total']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['sub_total']), -2);
			if ($data['ppn_pph'] == '1') $data['ppn_pph_count'] = substr(preg_replace("/[^0-9]/", "", $data['ppn_pph_count']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['ppn_pph_count']), -2);
			$data['total_final'] = substr(preg_replace("/[^0-9]/", "", $data['total_final']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['total_final']), -2);

			if (empty($data['date'])) {
				$data['date'] = date('Y-m-d');
			}

			for ($i = 0; $i < $count_rows; $i++) {
				if (!empty($data['amount'][$i]) && !empty($data['qyt'][$i]))
					$status = TRUE;
				$data['amount'][$i] = preg_replace("/[^0-9]/", "", $data['amount'][$i]);
			}
			$data['old_data'] = $this->Invoice_model->getAllInvoice(array('id' => $data['id'], 'by_id' => true))[$data['id']];
			$data['generalentry_old'] = $this->General_model->getAllGeneralentry(array('id' => $data['old_data']['general_id'], 'by_id' => true))[$data['old_data']['general_id']];
			$data['data_pelunasan'] = $this->General_model->getAllPelunasanInvoice(array('parent_id' => $data['id']));
			if ($status) {
				$this->load->model('Transaction_model');
				$this->load->model('Crud_model');
				$data['generalentry'] = array(
					'id' => $data['generalentry_old']['id'],
					'date' => $data['date'],
					'naration' => 'Invoice ' . $data['no_invoice'],
					'customer_id' => $data['customer_id'],
					'generated_source' => 'invoice'
				);
				$pelunasan = 0;
				foreach ($data['data_pelunasan'] as $dp) {
					$pelunasan = $pelunasan + $dp['amount'];
				}
				if ($data['sub_total'] <= $pelunasan) {
					$data['status'] = 'paid';
				} else {
					$data['status'] = 'unpaid';
				}
				// echo json_encode($data);
				// die();
				// $data['generalentry']['no_jurnal'] = $this->General_model->gen_numberABC($data['date'], 'AM', 'INVOICE');
				$i = 0;

				$jp = $this->General_model->getAllJenisInvoice(array('by_id' => true, 'id' => $data['jenis_invoice']))[$data['jenis_invoice']];
				// $data['status'] = 'unpaid';
				$uang_muka_pph = number_format(($data['sub_total'] * 0.02), 2, '.', '');
				$ref = $this->General_model->getAllRefAccount(array('by_id' => true, 'ref_id' => $data['payment_metode']))[$data['payment_metode']];
				$data['sub_entry'][$i] = array(
					'accounthead' => $ref['ref_account'],
					'type' => 0,
					'sub_keterangan' => 'kas ' . $data['description'],
					'amount' => $data['sub_total'],
				);
				$i++;
				$ref = $this->General_model->getAllRefAccount(array('ref_type' => 'um_pph_23'))[0];
				$data['sub_entry'][$i] = array(
					'accounthead' => $ref['ref_account'],
					'type' => 1,
					'sub_keterangan' => 'uang muka ' . $data['description'],
					'amount' => $uang_muka_pph
				);
				$i++;
				$data['sub_entry'][$i] = array(
					'accounthead' => $jp['ac_unpaid'],
					'type' => 1,
					'sub_keterangan' => "piutang " . $data['description'],
					'amount' => $data['sub_total'] - $uang_muka_pph
				);
				$i++;

				// echo json_encode($data);
				// die();

				// $data['old_data'] = $this->Payment_model->getAllPembayaran(array('id' => $data['id'], 'by_id' => true))[$data['id']];
				$result = $this->Invoice_model->invoice_edit($data);
			} else {
				throw new UserException('Please check data!');
			}
			echo json_encode(array('error' => false, 'data' => $data['id']));
		} catch (Exception $e) {
			ExceptionHandler::handle($e);
		}
	}

	public function addQRCode($url, $id, $token)
	{
		$this->load->library('ciqrcode'); //pemanggilan library QR CODE

		$config['cacheable']    = false; //boolean, the default is true
		$config['cachedir']     = './assets/'; //string, the default is application/cache/
		$config['errorlog']     = './assets/'; //string, the default is application/logs/
		$config['imagedir']     = './uploads/qrcode/'; //direktori penyimpanan qr code
		$config['quality']      = true; //boolean, the default is true
		$config['size']         = '600'; //interger, the default is 1024
		$config['black']        = array(224, 255, 255); // array, default is array(255,255,255)
		$config['white']        = array(70, 130, 180); // array, default is array(0,0,0)
		$this->ciqrcode->initialize($config);

		$image_name = $token . '_' . $id . '.png'; //buat name dari qr code sesuai dengan nim

		$params['data'] = 'https://apps.indometalasia.com/' . $url . $token . '/' . $id; //data yang akan di jadikan QR CODE
		$params['level'] = 'S'; //H=High
		$params['size'] = 10;
		$params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
		$this->ciqrcode->generate($params); // fungsi untuk generate QR CODE
	}

	public function jenis_invoice()
	{
		try {
			// $crud = $this->SecurityModel->Aksessbility_VCRUD('pembayaran', 'jenis_pembayaran', 'view');
			$data['accounts'] = $this->General_model->getAllBaganAkun(array('by_DataStructure' => true));
			$data['title'] = 'List Jenis Invoice';
			$data['main_view'] = 'invoice/jenis_invoice';
			// $data['vcrud'] = $crud;
			$data['vcrud'] = array('parent_id' => 32, 'id_menulist' => 89);
			$this->load->view('main/index2.php', $data);
		} catch (Exception $e) {
			ExceptionHandler::handle($e);
		}
	}

	function addPelunasan()
	{
		try {
			$status = FALSE;
			$data = $this->input->post();
			// echo json_encode($data);
			$data['nominal'] = substr(preg_replace("/[^0-9]/", "", $data['nominal']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['nominal']), -2);

			if (empty($data['date_pembayaran'])) {
				$data['date_pembayaran'] = date('Y-m-d');
			}

			$data['old_data'] = $this->Invoice_model->getAllInvoice(array('id' => $data['parent_id'], 'by_id' => true))[$data['parent_id']];
			$data['data_pelunasan'] = $this->General_model->getAllPelunasanInvoice(array('parent_id' => $data['parent_id']));
			$total_bayar = 0;
			foreach ($data['data_pelunasan'] as $p) {
				$total_bayar = $total_bayar + $p['nominal'];
			}
			$data['total_bayar'] = $total_bayar;
			if ($total_bayar >= $data['old_data']['sub_total']) {
				throw new UserException('Data ini sudah lunas!');
			}
			if ($data['total_bayar'] + $data['nominal'] >= $data['old_data']['sub_total']) {
				$data['status'] = 'paid';
			} else {
				$data['status'] = 'unpaid';
			}
			$jp = $this->General_model->getAllJenisInvoice(array('by_id' => true, 'id' => $data['old_data']['jenis_invoice']))[$data['old_data']['jenis_invoice']];
			$data['gen_old'] = $this->Statement_model->getSingelJurnal(array('id' => $data['old_data']['general_id']))['parent'];
			$data['generalentry'] = array(
				'date' => $data['date_pembayaran'],
				// 'naration' => $data['old_data']['description'],
				'naration' => $data['old_data']['description'] . ' (' . $data['gen_old']->no_jurnal . ')',
				'customer_id' => $data['old_data']['customer_id'],
				'generated_source' => 'Pelunasan Invoice'
			);

			$data['generalentry']['no_jurnal'] = $this->General_model->gen_number($data['date_pembayaran'], 'JMB');

			$data['sub_entry'][0] = array(
				'accounthead' => $jp['ac_paid'],
				'type' => 1,
				'amount' => $data['nominal'],
				'sub_keterangan' => "Htg " . $data['old_data']['description'],
			);
			$data['sub_entry'][1] = array(
				'accounthead' => $jp['ac_unpaid'],
				'type' => 0,
				'amount' => $data['nominal'],
				'sub_keterangan' => "Htg " . $data['old_data']['description'],
			);
			// echo json_encode($data);
			// die();

			$result = $this->Invoice_model->add_pelunasan($data);
			echo json_encode(array('error' => false, 'data' => $data));
		} catch (Exception $e) {
			ExceptionHandler::handle($e);
		}
	}


	function editPelunasan()
	{
		try {
			$status = FALSE;
			$data = $this->input->post();
			// echo json_encode($data);
			$data['nominal'] = substr(preg_replace("/[^0-9]/", "", $data['nominal']), 0, -2) . '.' . substr(preg_replace("/[^0-9]/", "", $data['nominal']), -2);

			if (empty($data['date_pembayaran'])) {
				$data['date_pembayaran'] = date('Y-m-d');
			}


			$data['old_data'] = $this->Invoice_model->getAllInvoice(array('id' => $data['parent_id'], 'by_id' => true))[$data['parent_id']];
			$data['data_pelunasan'] = $this->General_model->getAllPelunasanInvoice(array('parent_id' => $data['parent_id']));

			$total_bayar = 0;
			foreach ($data['data_pelunasan'] as $p) {
				if ($p['id'] != $data['id']) $total_bayar = $total_bayar + $p['nominal'];
				else {
					$old_pelunasan = $p;
				}
			}
			$data['total_bayar'] = $total_bayar;
			if ($total_bayar >= $data['old_data']['sub_total']) {
				throw new UserException('Data ini sudah lunas!');
			}
			if ($data['total_bayar'] + $data['nominal'] >= $data['old_data']['sub_total']) {
				$data['status'] = 'paid';
			} else {
				$data['status'] = 'unpaid';
			}
			$jp = $this->General_model->getAllJenisInvoice(array('by_id' => true, 'id' => $data['old_data']['jenis_pembayaran']))[$data['old_data']['jenis_pembayaran']];
			$data['gen_old'] = $this->Statement_model->getSingelJurnal(array('id' => $data['old_data']['general_id']))['parent'];
			$data['generalentry'] = array(
				'id' => $old_pelunasan['general_id'],
				'date' => $data['date_pembayaran'],
				// 'naration' => $data['old_data']['description'],
				'naration' => $data['old_data']['description'] . ' (' . $data['gen_old']->no_jurnal . ')',
				'customer_id' => $data['old_data']['customer_id'],
				'generated_source' => 'Pelunasan Invoice'
			);

			// $data['generalentry']['no_jurnal'] = $this->General_model->gen_number($data['date_pembayaran'], 'JMB');
			$data['sub_entry'][0] = array(
				'accounthead' => $jp['ac_paid'],
				'type' => 1,
				'amount' => $data['nominal'],
				'sub_keterangan' => "Ptg " . $data['old_data']['description'],
			);
			$data['sub_entry'][1] = array(
				'accounthead' => $jp['ac_unpaid'],
				'type' => 0,
				'amount' => $data['nominal'],
				'sub_keterangan' => "Ptg " . $data['old_data']['description'],
			);
			$result = $this->Invoice_model->edit_pelunasan($data);
			echo json_encode(array('error' => false, 'data' => $data));
		} catch (Exception $e) {
			ExceptionHandler::handle($e);
		}
	}


	function deletePelunasan()
	{
		try {
			$status = FALSE;
			$data = $this->input->post();
			// echo json_encode($data);

			$data['self_data'] = $this->Invoice_model->getAllPelunasan(array('id' => $data['id']))[0];
			$data['data_pelunasan'] = $this->Invoice_model->getAllPelunasan(array('parent_id' => $data['self_data']['parent_id']));
			$data['old_data'] = $this->Invoice_model->getAllInvoice(array('id' => $data['self_data']['parent_id'], 'by_id' => true))[$data['self_data']['parent_id']];
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
			if ($data['total_bayar'] >= $data['old_data']['sub_total']) {
				$data['status'] = 'paid';
			} else {
				$data['status'] = 'unpaid';
			}
			// echo json_encode($data);
			// die();

			$result = $this->Invoice_model->delete_pelunasan($data);
			echo json_encode(array('error' => false, 'data' => $data));
		} catch (Exception $e) {
			ExceptionHandler::handle($e);
		}
	}

	public function kwitansi_print()
	{
		$data = $this->input->get();
		// echo json_encode($data);
		// die();
		if (empty($data['date'])) $data['date'] = date('Y-m-d');
		$data['date'] = 'Pangkalpinang, ' . $this->tanggal_indonesia($data['date']);

		$data['terbilang'] = $this->terbilang((int)$data['nominal']) . ' Rupiah';
		$data['nominal'] = number_format((int)$data['nominal'], 0, ',', '.');
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
			// echo json_encode($fx);
			// die();
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
		// $data = $this->input->get();
		$data['transaction'] = $this->Invoice_model->getAllInvoiceDetail(array('id' => $id))[$id];
		$data['template'] = $this->General_model->getAllJenisInvoice(array('by_id' => true, 'id' => $data['transaction']['jenis_invoice']))[$data['transaction']['jenis_invoice']];
		$data['payment'] = $this->General_model->getAllRefAccount(array('by_id' => true, 'ref_id' => $data['transaction']['payment_metode']))[$data['transaction']['payment_metode']];

		// $pos = strpos($data['template']['paragraph_1'], '{', 2);
		if (!empty($data['template']['paragraph_1']))
			$data['p1'] = $this->find_char($data['template']['paragraph_1'], $data['transaction']);
		else
			$data['p1'] = 'Bersamaan dengan ini kami sampaikan tagihan ' . $data['transaction']['description'] . ' sebagai berikut :';
		// if (!empty($pos)) {
		// 	$pos2 = strpos($data['template']['paragraph_1'], '}');
		// 	$tx1 = substr($data['template']['paragraph_1'], $pos + 1, $pos2 - $pos - 1);
		// 	$data['aa'] = substr($data['template']['paragraph_1'], $pos + 1, $pos2 - $pos - 1);
		// }

		// echo json_encode($data);
		// die();
		if (empty($data['transaction']['date'])) $data['transaction']['date'] = date('Y-m-d');
		$data['transaction']['date'] = $this->tanggal_indonesia($data['transaction']['date']);

		$data['terbilang'] = $this->terbilang((int)$data['transaction']['total_final']) . ' Rupiah';
		$data['nominal'] = number_format((int)$data['transaction']['total_final'], 0, ',', '.');
		$this->load->view('invoice/print_template.php', $data);
	}
}
