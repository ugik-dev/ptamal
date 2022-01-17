<div class="card card-custom">
	<div class="card-body">
		<div class="box-body ">
			<div class="">
				<?php
				$attributes = array('id' => 'journal_voucher', 'method' => 'post', 'class' => '');
				?>
				<form opd="form" id="jurnal_form" onsubmit="return false;" type="multipart" autocomplete="off">
					<div class="">
						<div class="row no-print invoice">
							<h4 class=""> <i class="fa fa-check-circle"></i>
								Entri Jurnal Transaksi
							</h4>
							<div class="col-lg-12">
								<div class="row">
									<div class="col-lg-6">
										<div class="form-group">
											<?php echo form_label('Patner'); ?>
											<select name="customer_id" id="customer_id" class="form-control select2 input-lg">
												<option value="0"> ------- </option>
												<?php
												// echo $patner_record;
												foreach ($patner_record as $pr) {
													echo '<option value="' . $pr['id'] . '">' . $pr['customer_name'] . '</option>';
												}
												?>
											</select>
										</div>
									</div>

								</div>
								<div class="form-group">
									<?php echo form_label('No Jurnal');
									if (!empty($return_data['parent_id'])) {
										$data = array('class' => 'hidden', 'type' => 'text', 'name' => 'id', 'hidden' => true);
										$data['value'] = $return_data['parent_id'];
										echo form_input($data);
									}

									$data = array('class' => 'form-control input-lg', 'type' => 'text', 'id' => 'no_jurnal', 'disabled' => true);
									if (!empty($return_data['ref_number'])) $data['value'] = $return_data['ref_number'];
									echo form_input($data);
									$data = array('class' => 'hidden', 'type' => 'hidden', 'name' => 'url', 'id' => 'url', 'value' => '');
									echo form_input($data);

									?>
								</div>
								<div class="form-group">
									<?php echo form_label('Deskripsi');
									$data = array('class' => 'form-control input-lg', 'type' => 'text', 'name' => 'naration', 'id' => 'naration', 'reqiured' => '');
									if (!empty($return_data['naration'])) $data['value'] = $return_data['naration'];
									echo form_input($data);
									?>
								</div>
								<div class="form-group">
									<?php echo form_label('Tanggal');
									$data = array('class' => 'form-control input-lg', 'type' => 'date', 'name' => 'date', 'id' => 'date', 'reqiured' => '', 'value' => Date('Y-m-d'));
									if (!empty($return_data['date'])) $data['value'] = $return_data['date'];

									echo form_input($data);
									?>
								</div>
							</div>
						</div>


						<div class="row invoice">
							<div class="col-lg-12 table-responsive">
								<table class="table table-striped table-hover  ">
									<thead>
										<tr>
											<th class="col-lg-6">Akun</th>
											<th class="">Debit</th>
											<th class="">Kredit</th>
										</tr>
									</thead>
									<tbody id="transaction_table_body">


									</tbody>
									<tfoot>
										<tr>
											<td colspan="1">
												<button type="button" class="btn btn-primary" id="addNewLine"><i class="fa fa-plus-circle"></i>Tambah Baris </button>
												<!-- <button type="button" class="btn btn-primary" name="addline" onclick="add_new_row('<?php echo base_url() . 'statements/popup/new_row'; ?>')"><i class="fa fa-plus-circle"></i>Tambah Baris </button> -->
											</td>
											<td id="row_loading_status"></td>
										</tr>
										<tr>

											<th>Total: </th>
											<th><?php
												$data = array('name' => 'total_debit_amount', 'value' => '0', 'disabled' => 'disabled', 'class' => 'accounts_total_amount', 'reqiured' => '');
												echo form_input($data);
												?></th>
											<th><?php
												$data = array('name' => 'total_credit_amount',  'value' => '0', 'disabled' => 'disabled', 'class' => 'accounts_total_amount', 'reqiured' => '');
												echo form_input($data);
												?></th>
										</tr>
										<tr>
											<td colspan="4" class="transaction_validity" id="transaction_validity"></td>
										</tr>
									</tfoot>
								</table>
							</div>
							<div class="col-lg-12">
								<div class="row">
									<div class="col-lg-3">
										<div class="form-group"><label>Disetujui</label><select name="acc_1" id="acc_1" class="form-control select2 input-lg">
												<option value="0">----- </option>
												<option value="7">SADINA </option>
											</select></div>
									</div>
									<div class="col-lg-3">
										<div class="form-group" id='label_kendaraan'><label>Diverifikasi</label><select name="acc_2" id="acc_2" class="form-control select2 input-lg">
												<option value="0">----- </option>
												<option value="4">IKA MILAWATI </option>
												<option value="5">ERIN MEILANI </option>
											</select></div>
									</div>
									<div class="col-lg-3">
										<div class="form-group" id='label_kendaraan'><label>Dibuat</label><select name="acc_3" id="acc_3" class="form-control select2 input-lg">
												<option value="0">----- </option>
												<option value="4">IKA MILAWATI </option>
												<option value="5">ERIN MEILANI </option>
											</select></div>
									</div>
									<div class="col-lg-3">
										<div class="form-group" id='label_kendaraan'><label>Dibukukan</label><input type="text" disabled id="dibukukan" class="form-control input-lg"></div>
									</div>
								</div>
							</div><input type="hidden" id="draft_value" name="draft_value">
							<div class="col-lg-12 ">
								<div class="form-group"><a class="btn btn-info margin btn-lg pull-right mr-1" id="btn_save_fix"><i class="fa fa-floppy-o" aria-hidden="true"></i>Simpan </a><a hidden class="btn btn-warning margin btn-lg pull-right mr-1" id="btn_draft"><i class="fa fa-floppy-o" aria-hidden="true"></i>Draft </a></div>
							</div>
						</div>
					</div><?php form_close(); ?>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo base_url(); ?>assets/dist/js/backend/journal_voucher.js?v=0.3"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.mask.min.js"></script>
<script>
	$('#menu_id_23').addClass('menu-item-active menu-item-open menu-item-here"');
	$('#submenu_id_64').addClass('menu-item-active');
	btn_save = $('#btn_save_fix');
	draft_value = $('#draft_value');
	form_journal_voucher = $('#journal_voucher');
	btn_draft = $('#btn_draft');
	no_jurnal = $('#no_jurnal');
	url_jour = $('#url');
	naration = $('#naration');
	date_jurnal = $('#date');
	transaction_table_body = $('#transaction_table_body');
	addNewLine = $('#addNewLine');
	jurnal_form = $('#jurnal_form');



	acc_1 = $('#acc_1');
	acc_2 = $('#acc_2');
	acc_3 = $('#acc_3');
	var swalSuccessConfigure = {
		title: "Simpan berhasil",
		icon: "success",
		timer: 500
	};

	btn_draft.on('click', (ev) => {
			draft_value.val(true)
			jurnal_form.submit();
		}

	);
	btn_save.on('click', (ev) => {
			draft_value.val(false)
			jurnal_form.submit();
		}

	);

	function add_line() {
		transaction_table_body.append(html_add_data);
		$('.select2').select2();

		$('.mask').mask('000.000.000.000.000,00', {
			reverse: true
		});
	};

	addNewLine.on('click', function() {
		add_line();
	})

	html_add_data = `	<tr><td><select name="account_head[]" class="form-control select2 input-lg">
												<option>---</option>
												<?php
												foreach ($accounts as $lv1) {
													echo '<optgroup label="[' . $lv1['head_number'] . '] ' . $lv1['name'] . '">';
													foreach ($lv1['children'] as $lv2) {
														echo '<optgroup label="&nbsp&nbsp&nbsp [' . $lv1['head_number'] . '.' . (!empty($lv2['head_number']) ? $lv2['head_number'] : '00') . '] ' . (!empty($lv2['name']) ? $lv2['name'] : '') . '">';
														foreach ($lv2['children'] as $lv3) {
															echo '<option value="' . $lv3['id_head'] . '">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp [' . $lv1['head_number'] . '.' . (!empty($lv2['head_number']) ? $lv2['head_number'] : '00')  . '.' . $lv3['head_number'] . '] ' . $lv3['name'] . '';
															echo '</option>';
														}
														echo '</optgroup>';
													}
													echo '</optgroup>';
												}
												?>
											</select>
										</td>
										<td><?php
											$data = array('class' => 'form-control input-lg mask', 'name' => 'debitamount[]', 'value' => '', 'reqiured' => '', 'onkeyup' => 'count_debits()');
											echo form_input($data);
											?></td>
										<td><?php
											$data = array('class' => 'form-control input-lg mask',  'name' => 'creditamount[]', 'value' => '', 'reqiured' => '', 'onkeyup' => 'count_debits()');
											echo form_input($data);
											?></td>
									</tr>
									<tr>
										<td colspan="3"><?php
														$data = array('class' => 'form-control input-lg', 'placeholder' => 'keterangan', 'type' => 'text', 'name' => 'sub_keterangan[]', 'id' => 'sub_keterangan[]', 'value' => '');
														echo form_input($data);
														$data = array('class' => ' form-control input-lg', 'hidden' => true, 'type' => 'text', 'name' => 'sub_id[]', 'id' => 'sub_id[]');
														echo form_input($data);
														?></td>
									</tr>
									<tr style="height: 1px; margin: 1px; padding : 1px">
									<td colspan="3" style="height: 0px; margin: 0px; padding : 0px">
									<hr>
									</td>
									</tr>
									<tr hidden ><td></td></tr>
									`


	<?php if (!empty($return_data['children'])) {
		for ($i = 0; $i < count($return_data['children']); $i++) {

	?>
			transaction_table_body.append(html_add_data)
			// add_line();
		<?php
		} ?>
		$('.mask').mask('000.000.000.000.000,00', {
			reverse: true
		});
	<?php
	} else {
		echo "	transaction_table_body.append(html_add_data);
	
		$('.mask').mask('000.000.000.000.000,00', {
			reverse: true
		});";
	} ?>


	var sub_keterangan = document.getElementsByName('sub_keterangan[]');
	var account_head = document.getElementsByName('account_head[]');
	var debitamount = document.getElementsByName('debitamount[]');
	var creditamount = document.getElementsByName('creditamount[]');
	var sub_id = document.getElementsByName('sub_id[]');

	<?php
	$i = 0;
	if (!empty($return_data['children']))
		foreach (array_reverse($return_data['children']) as $key => $sub_parents) { ?>
		account_head[<?= $i ?>].value = '<?= $sub_parents['accounthead'] ?>';
		sub_keterangan[<?= $i ?>].value = '<?= $sub_parents['sub_keterangan'] ?>';
		sub_id[<?= $i ?>].value = '<?= $sub_parents['sub_id'] ?>';


		<?php if ($sub_parents['type'] == 0) { ?>
			debitamount[<?= $i ?>].value = '<?= number_format($sub_parents['amount'], 2, ',', '.') ?>';
		<?php } else { ?>
			creditamount[<?= $i ?>].value = '<?= number_format($sub_parents['amount'], 2, ',', '.') ?>';
	<?php
			}
			$i++;
		} ?>
	count_debits(true);

	var swalSaveConfigure = {
		title: "Konfirmasi simpan",
		text: "Yakin akan menyimpan data ini?",
		icon: "info",
		showCancelButton: true,
		confirmButtonColor: "#18a689",
		confirmButtonText: "Ya, Simpan!",
		reverseButtons: true
	};
	jurnal_form.submit(function(event) {
		event.preventDefault();
		Swal.fire(swalSaveConfigure).then((result) => {
			if (result.dismiss === "cancel") {
				return;
			}
			$.ajax({
				url: '<?= base_url() ?>accounting/<?= $form_url ?>',
				'type': 'POST',
				data: new FormData(jurnal_form[0]),
				contentType: false,
				processData: false,
				success: function(data) {
					var json = JSON.parse(data);
					if (json['error']) {
						swal.fire("Simpan Gagal", json['message'], "error");
						return;
					}
					//  return;
					var d = json['data']
					// dataAccounts[d['id']] = d;
					swal.fire(swalSuccessConfigure);
					window.location.href = '<?= base_url() ?>accounting/show_journal/' + d;

				},
				error: function(e) {}
			});
		});
	});
</script><?php
			$this->load->view('bootstrap_model.php');
			?>