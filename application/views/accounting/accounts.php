 <div class="card card-custom" id="print-section">
     <div class="card-body">

         <div class="col-md-12">
             <div class="pull pull-right">

                 <!-- Button trigger modal-->
                 <button type="button" class="btn btn-primary" id="add_new_data_btn" data-toggle="modal" data-target="#exampleModalLong">
                     <i class="fa fa-plus-square" aria-hidden="true"></i> Buat Akun
                     <!-- Launch demo modal -->
                 </button>

                 <!-- Modal-->

                 <button onclick="printDiv('print-section')" class="btn btn-default btn-outline-primary   pull-right "><i class="fa fa-print  pull-left"></i> Cetak</button>
             </div>
         </div>
     </div>
 </div>
 <div class="card card-custom">
     <div class="card-body">
         <div class="col-xs-12">
             <div class="box" id="print-section">
                 <div class="box-header">
                     <h3 class="box-title"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Daftar Akun </h3>
                 </div>
                 <div class="box-body">
                     <div class="table-responsive col-md-12">
                         <table class="table table-bordered table-hover table-checkable mt-10" id="FDataTable">
                             <thead>
                                 <tr>
                                     <th>Kode</th>
                                     <th>Nama</th>
                                     <th>Kelompok</th>
                                     <th>Tipe</th>
                                     <th>Relasi</th>
                                     <th>Jenis Beban</th>
                                     <th>Tindakan</th>
                                 </tr>
                             </thead>
                             <tbody>
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
 <div class="modal fade" id="accounts_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <form opd="form" id="accounts_form" onsubmit="return false;" type="multipart" autocomplete="off">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">Modal Title</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <i aria-hidden="true" class="ki ki-close"></i>
                     </button>
                 </div>
                 <div class="modal-body">
                     <div class="col-lg-6">

                         <div class="form-group">
                             <label>Kelompok Akun:</label>
                             <select class="form-control input-lg" name="nature" id="nature" style="width: 100%;">
                                 <option value="Assets">Assets</option>
                                 <option value="Liability">Liability</option>
                                 <option value="Equity">Equity</option>
                                 <option value="Revenue">Revenue</option>
                                 <option value="Expense">Expense</option>
                             </select>
                         </div>
                     </div>
                     <div class="row">
                         <div class="col-lg-3">
                             <div class="form-group">
                                 <label>Kode Akun : </label>
                                 <input type="hidden" id="id" name="id">
                                 <input type="text" id="head_number" name="head_number" class="form-control input-lg mask_account_number" required>
                             </div>
                         </div>
                         <div class="col-lg-9">
                             <div class="form-group">
                                 <label>Nama Akun : </label>
                                 <input type="text" name="name" id="name" class="form-control input-lg" required>
                             </div>
                         </div>
                     </div>
                     <div class="row">

                         <div class="col-lg-6" id="label_type">
                             <div class="form-group">
                                 <label>Tipe Akun :</label>
                                 <select class="form-control input-lg" name="type" id="type" style="width: 100%;">
                                     <option value="Lancar">Lancar </option>
                                     <option value="Tidak Lancar">Tidak Lancar</option>
                                     <option value="Tetap">Tetap</option>
                                     <option value="Tidak Tetap">Tidak Tetap</option>
                                 </select>
                             </div>
                         </div>
                         <div class="col-lg-6">
                             <div class="form-group" id="label_expense_type" style="display: none;">
                                 <label>Jenis Beban :</label>
                                 <select class="form-control input-lg" name="expense_type" id="expense_type">
                                     <option value="Beban Kas">Beban Kas</option>
                                     <option value="Beban Non Kas">Beban Non Kas</option>
                                     <option value="Beban Barang">Beban Barang</option>
                                 </select>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                     <button class="btn btn-success my-1 mr-sm-2" type="submit" id="add_btn" data-loading-text="Loading..."><strong>Add Data</strong></button>
                     <button class="btn btn-success my-1 mr-sm-2" type="submit" id="save_edit_btn" data-loading-text="Loading..."><strong>Save Change</strong></button>
                 </div>
             </form>
         </div>
     </div>
 </div>
 <script>
     $('#menu_id_23').addClass('menu-item-active menu-item-open menu-item-here"')
     $('#submenu_id_58').addClass('menu-item-active')
     $(document).ready(function() {
         var dataAccounts = [];
         var vcrud = <?= json_encode($vcrud) ?>;
         var add_new_data_btn = $('#add_new_data_btn');
         //  add_new_data_btn.prop('hidden', false)
         vcrud['hk_create'] == 0 ? add_new_data_btn.prop('hidden', true) : add_new_data_btn.prop('hidden', false);
         var AccountsModal = {
             'self': $('#accounts_modal'),
             'info': $('#accounts_modal').find('.info'),
             'form': $('#accounts_modal').find('#accounts_form'),
             'addBtn': $('#accounts_modal').find('#add_btn'),
             'saveEditBtn': $('#accounts_modal').find('#save_edit_btn'),
             'idAccounts': $('#accounts_modal').find('#id'),
             'head_number': $('#accounts_modal').find('#head_number'),
             'name': $('#accounts_modal').find('#name'),
             'nature': $('#accounts_modal').find('#nature'),
             'type': $('#accounts_modal').find('#type'),
             'label_type': $('#accounts_modal').find('#label_type'),
             'expense_type': $('#accounts_modal').find('#expense_type'),
             'label_expense': $('#accounts_modal').find('#label_expense_type'),
         }
         var swalSaveConfigure = {
             title: "Konfirmasi simpan",
             text: "Yakin akan menyimpan data ini?",
             icon: "info",
             showCancelButton: true,
             confirmButtonColor: "#18a689",
             confirmButtonText: "Ya, Simpan!",
             reverseButtons: true
         };

         var swalDeleteConfigure = {
             title: "Konfirmasi hapus",
             text: "Yakin akan menghapus data ini?",
             icon: "warning",
             showCancelButton: true,
             confirmButtonColor: "#DD6B55",
             confirmButtonText: "Ya, Hapus!",
         };

         var swalSuccessConfigure = {
             title: "Simpan berhasil",
             icon: "success",
             timer: 500
         };

         var swalSuccessConfigure = {
             title: "Simpan berhasil",
             icon: "success",
             timer: 500
         };

         AccountsModal.nature.on('change', function() {
             NatureValidation();
         })

         function numberFormatNumber(value) {
             return value.substring(0, 1) + "." + value.substring(1, 3) + "." + value.substring(3, 6);
         }

         function NatureValidation() {
             var old_num = AccountsModal.head_number.val();
             if (AccountsModal.nature.val() == 'Assets') {
                 AccountsModal.head_number.mask('1.00.000', {});
                 AccountsModal.head_number.val("1." + old_num.substring(2, 4) + (old_num.substring(5, 8) ? "." : '') + old_num.substring(5, 8))
                 AccountsModal.label_expense.css({
                     display: "none"
                 });
                 AccountsModal.label_type.css({
                     display: "block"
                 });
                 AccountsModal.type.prop('disabled', false);
                 AccountsModal.expense_type.prop('disabled', true);
             } else if (AccountsModal.nature.val() == 'Liability') {
                 AccountsModal.head_number.mask('2.00.000', {});
                 AccountsModal.head_number.val("2." + old_num.substring(2, 4) + (old_num.substring(5, 8) ? "." : '') + old_num.substring(5, 8))
                 AccountsModal.label_expense.css({
                     display: "none"
                 });
                 AccountsModal.label_type.css({
                     display: "none"
                 });
                 AccountsModal.type.prop('disabled', true);
                 AccountsModal.expense_type.prop('disabled', false);
             } else if (AccountsModal.nature.val() == 'Equity') {
                 AccountsModal.head_number.mask('3.00.000', {});
                 AccountsModal.type.prop('disabled', true);
                 AccountsModal.expense_type.prop('disabled', true);
                 AccountsModal.head_number.val("3." + old_num.substring(2, 4) + (old_num.substring(5, 8) ? "." : '') + old_num.substring(5, 8))
                 AccountsModal.label_expense.css({
                     display: "none"
                 });
                 AccountsModal.label_type.css({
                     display: "none"
                 });
             } else if (AccountsModal.nature.val() == 'Revenue') {
                 AccountsModal.head_number.mask('4.00.000', {});
                 AccountsModal.type.prop('disabled', true);
                 AccountsModal.expense_type.prop('disabled', true);
                 AccountsModal.head_number.val("4." + old_num.substring(2, 4) + (old_num.substring(5, 8) ? "." : '') + old_num.substring(5, 8))
                 AccountsModal.label_expense.css({
                     display: "none"
                 });
                 AccountsModal.label_type.css({
                     display: "none"
                 });
             } else if (AccountsModal.nature.val() == 'Expense') {
                 AccountsModal.head_number.mask('5.00.000', {});
                 AccountsModal.label_expense.css({
                     display: "block"
                 });
                 AccountsModal.label_type.css({
                     display: "none"
                 });
                 AccountsModal.type.prop('disabled', true);
                 AccountsModal.expense_type.prop('disabled', false);
                 AccountsModal.head_number.val("5." + old_num.substring(2, 4) + (old_num.substring(5, 8) ? "." : '') + old_num.substring(5, 8))
             }
         }

         function FormReset() {
             AccountsModal.head_number.mask('0.00.000', {});
             AccountsModal.idAccounts.val('');
             AccountsModal.head_number.val('');
             AccountsModal.name.val('');
             AccountsModal.expense_type.val('');
             AccountsModal.type.val('');
             AccountsModal.nature.val('');
         }
         //   AccountsModal.self.mod   al('show');
         add_new_data_btn.on('click', (e) => {
             AccountsModal.form.trigger('reset');
             FormReset()
             NatureValidation()
             AccountsModal.self.modal('show');
             AccountsModal.addBtn.show();
             AccountsModal.saveEditBtn.hide();
             AccountsModal.head_number.mask('0.00.000', {});
         });
         var FDataTable = $('#FDataTable').DataTable({
             'columnDefs': [],
             deferRender: true,
             "order": [
                 [0, "desc"]
             ]
         });

         function getAllBaganAkun() {
             swal.fire({
                 title: 'Loading Bagan Akun...',
                 allowOutsideClick: false
             });
             swal.showLoading();
             return $.ajax({
                 url: `<?php echo base_url('Accounting/getAllBaganAkun?by_id=true') ?>`,
                 'type': 'GET',
                 data: {},
                 success: function(data) {
                     swal.close();
                     var json = JSON.parse(data);
                     if (json['error']) {
                         return;
                     }
                     dataAccounts = json['data'];
                     renderAccounts(dataAccounts);
                 },
                 error: function(e) {}
             });
         }


         function renderAccounts(data) {
             if (data == null || typeof data != "object") {
                 return;
             }
             var i = 0;

             var renderData = [];
             Object.values(data).forEach((d) => {
                 var editButton = `
                 <button type="button" class="edit btn btn-primary  btn-icon" data-id='${d['id']}' title="Edit"><i class='la la-pencil-alt'></i></button>
                 `;
                 var deleteButton = `
                 <button  type="button" class="delete btn btn-warning btn-icon" data-id='${d['id']}' title="Delete"><i class='la la-trash'></i></button>
                 `;
                 var button = `    ${vcrud['hk_update'] == 1 ? editButton : ''}  ${vcrud['hk_delete'] == 1 ? deleteButton : ''}`;


                 renderData.push([numberFormatNumber(d['head_number']), d['name'], d['nature'], d['type'], d['relation_id'], d['expense_type'], button]);
             });
             FDataTable.clear().rows.add(renderData).draw('full-hold');
         }

         FDataTable.on('click', '.edit', function() {
             AccountsModal.form.trigger('reset');
             FormReset();
             AccountsModal.self.modal('show');
             AccountsModal.addBtn.hide();
             AccountsModal.saveEditBtn.show();
             var currentData = dataAccounts[$(this).data('id')];
             AccountsModal.idAccounts.val(currentData['id']);
             AccountsModal.head_number.val(numberFormatNumber(currentData['head_number']));
             AccountsModal.name.val(currentData['name']);
             AccountsModal.expense_type.val(currentData['expense_type']);
             AccountsModal.type.val(currentData['type']);
             AccountsModal.nature.val(currentData['nature']);
             NatureValidation()

         })

         FDataTable.on('click', '.delete', function() {
             var currentData = $(this).data('id');
             Swal.fire(swalDeleteConfigure).then((result) => {
                 if (result.isConfirmed == false) {
                     return;
                 }
                 $.ajax({
                     url: "<?= base_url('Accounting/deleteAccounts') ?>",
                     'type': 'get',
                     data: {
                         'id': currentData
                     },

                     success: function(data) {
                         var json = JSON.parse(data);
                         if (json['error']) {
                             swal("Simpan Gagal", json['message'], "error");
                             return;
                         }
                         //  return;
                         var d = json['data']
                         delete dataAccounts[d['id']];
                         swal.fire("Simpan Berhasil", "", "success");
                         renderAccounts(dataAccounts);
                         AccountsModal.self.modal('hide');
                     },
                     error: function(e) {}
                 });
             });

         })

         AccountsModal.form.submit(function(event) {
             lengHeadNumber = AccountsModal.head_number.val();
             lengHeadNumber = lengHeadNumber.length;
             if (lengHeadNumber != 8) {
                 swal.fire("Kesalahan", 'format kode akun salah! <br> gunakan 6 digit angka!', "warning");
                 return;
             }
             event.preventDefault();
             var isAdd = AccountsModal.addBtn.is(':visible');
             var url = "<?= site_url('Accounting/') ?>";
             url += isAdd ? "addAccounts" : "editAccounts";
             var button = isAdd ? AccountsModal.addBtn : AccountsModal.saveEditBtn;

             Swal.fire(swalSaveConfigure).then((result) => {
                 if (result.isConfirmed == false) {
                     return;
                 }
                 $.ajax({
                     url: url,
                     'type': 'POST',
                     data: new FormData(AccountsModal.form[0]),
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
                         dataAccounts[d['id']] = d;
                         swal.fire(swalSuccessConfigure);
                         renderAccounts(dataAccounts);
                         AccountsModal.self.modal('hide');
                     },
                     error: function(e) {}
                 });
             });
         });
         getAllBaganAkun()
     });
 </script>
 <!-- Bootstrap model  -->
 <?php
    //  $this->load->view('bootstrap_model.php'); 
    ?>
 <!-- Bootstrap model  ends-->