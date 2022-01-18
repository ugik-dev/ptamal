 <div class="card card-custom" id="print-section">
     <div class="card-body">

         <div class="col-md-12">
             <div class="pull pull-right">

                 <!-- Button trigger modal-->
                 <button type="button" class="btn btn-primary" id="add_new_data_btn" data-toggle="modal" data-target="#exampleModalLong">
                     <i class="fa fa-plus-square" aria-hidden="true"></i> Tambah Patners
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
                     <h3 class="box-cus_contact_1"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Tambah Patners </h3>
                 </div>
                 <div class="box-body">
                     <div class="table-responsive col-md-12">
                         <table class="table table-bordered table-hover table-checkable mt-10" id="FDataTable">
                             <thead>
                                 <tr>
                                     <th>Nama Patners</th>
                                     <th>Nama Perusahaan</th>
                                     <th>Lokasi</th>
                                     <th>Kontak</th>
                                     <th>Jenis</th>
                                     <th>Status</th>
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
 <div class="modal fade" id="patners_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <form opd="form" id="patners_form" onsubmit="return false;" type="multipart" autocomplete="off">
                 <div class="modal-header">
                     <h5 class="modal-cus_contact_1" id="exampleModalLabel">Modal Title</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <i aria-hidden="true" class="ki ki-close"></i>
                     </button>
                 </div>
                 <div class="modal-body">
                     <div class="form-group">
                         <?php
                            echo form_label('Nama Patner:');
                            $data = array('class' => 'form-control ', 'type' => 'text', 'name' => 'customer_name', 'id' => 'customer_name', 'placeholder' => 'e.g Sulaiman', 'reqiured' => '');
                            echo form_input($data);
                            $data = array('type' => 'text', 'name' => 'id', 'id' => 'id', 'placeholder' => '');
                            echo form_input($data);
                            ?>
                     </div>
                     <div class="form-group">
                         <?php
                            echo form_label('Nama Perusahaan :');
                            $data = array('class' => 'form-control ', 'type' => 'text', 'name' => 'cus_company', 'id' => 'cus_company',  'placeholder' => 'e.g PT Example', 'reqiured' => '');
                            echo form_input($data);
                            ?>
                     </div>

                     <div class="form-group">
                         <?php
                            echo form_label('Telp  :');
                            $data = array('class' => 'form-control ', 'type' => 'text', 'name' => 'cus_contact_1', 'id' => 'cus_contact_1', 'placeholder' => 'e.g (0717) 12342', 'reqiured' => '');
                            echo form_input($data);
                            ?>
                     </div>
                     <div class="form-group">
                         <?php
                            echo form_label('Alamat :');
                            $data = array('class' => 'form-control ', 'type' => 'text', 'id' => 'cus_address', 'name' => 'cus_address', 'placeholder' => 'e.g Jl Jend No 125', 'reqiured' => '');
                            echo form_input($data);
                            ?>
                     </div>
                     <div class="form-group">
                         <?php
                            echo form_label('Kota :');
                            $data = array('class' => 'form-control ', 'type' => 'text', 'name' => 'cus_town', 'id' => 'cus_town',  'placeholder' => 'e.g Pangkalpinang - Bangka', 'reqiured' => '');
                            echo form_input($data);
                            ?>
                     </div>

                     <div class="form-group">
                         <?php
                            echo form_label('Regional :');
                            $data = array('class' => 'form-control ', 'type' => 'text', 'id' => 'cus_region', 'name' => 'cus_region', 'placeholder' => 'e.g Indonesia', 'reqiured' => '');
                            echo form_input($data);
                            ?>

                     </div>
                     <div class="form-group">
                         <?php
                            echo form_label('Deskripsi :');
                            $data = array('class' => 'form-control ', 'type' => 'text', 'id' => 'cus_description', 'name' => 'cus_description', 'placeholder' => '', 'reqiured' => '');
                            echo form_input($data);
                            ?>

                     </div>
                     <div class="form-group">
                         <label>Status :</label>
                         <select class="form-control" id="cus_status">
                             <option value="0">Aktif</option>
                             <option value="1">Non Aktif</option>
                         </select>

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
     $('#menu_id_18').addClass('menu-item-active menu-item-open menu-item-here"')
     $('#submenu_id_31').addClass('menu-item-active')
     $(document).ready(function() {
         var dataPatnerss = [];
         var vcrud = <?= json_encode($vcrud) ?>;
         var add_new_data_btn = $('#add_new_data_btn');
         vcrud['hk_create'] == 0 ? add_new_data_btn.prop('hidden', true) : add_new_data_btn.prop('hidden', false);
         var PatnersModal = {
             'self': $('#patners_modal'),
             'info': $('#patners_modal').find('.info'),
             'form': $('#patners_modal').find('#patners_form'),
             'addBtn': $('#patners_modal').find('#add_btn'),
             'saveEditBtn': $('#patners_modal').find('#save_edit_btn'),
             'id': $('#patners_modal').find('#id'),
             'cus_address': $('#patners_modal').find('#cus_address'),
             'customer_name': $('#patners_modal').find('#customer_name'),
             'cus_status': $('#patners_modal').find('#cus_status'),
             'cus_company': $('#patners_modal').find('#cus_company'),
             'cus_town': $('#patners_modal').find('#cus_town'),
             'cus_description': $('#patners_modal').find('#cus_description'),
             'cus_contact_1': $('#patners_modal').find('#cus_contact_1'),
             'cus_region': $('#patners_modal').find('#cus_region'),
         }
         var swalSaveConfigure = {
             cus_contact_1: "Konfirmasi simpan",
             text: "Yakin akan menyimpan data ini?",
             icon: "info",
             showCancelButton: true,
             confirmButtonColor: "#18a689",
             confirmButtonText: "Ya, Simpan!",
             reverseButtons: true
         };

         var swalDeleteConfigure = {
             cus_contact_1: "Konfirmasi hapus",
             text: "Yakin akan menghapus data ini?",
             icon: "warning",
             showCancelButton: true,
             confirmButtonColor: "#DD6B55",
             confirmButtonText: "Ya, Hapus!",
         };

         var swalSuccessConfigure = {
             cus_contact_1: "Simpan berhasil",
             icon: "success",
             timer: 500
         };

         var swalSuccessConfigure = {
             cus_contact_1: "Simpan berhasil",
             icon: "success",
             timer: 500
         };


         function FormReset() {
             //  PatnersModal.head_number.mask('0.00.000', {});
         }
         //   PatnersModal.self.mod   al('show');
         add_new_data_btn.on('click', (e) => {
             PatnersModal.form.trigger('reset');
             PatnersModal.self.modal('show');
             PatnersModal.addBtn.show();
             PatnersModal.saveEditBtn.hide();
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
                 cus_contact_1: 'Loading Patners...',
                 allowOutsideClick: false
             });
             swal.showLoading();
             return $.ajax({
                 url: `<?php echo base_url('General/getAllPayee?by_id=true') ?>`,
                 'type': 'GET',
                 data: {},
                 success: function(data) {
                     swal.close();
                     var json = JSON.parse(data);
                     if (json['error']) {
                         return;
                     }
                     dataPatnerss = json['data'];
                     renderPatnerss(dataPatnerss);
                 },
                 error: function(e) {}
             });
         }


         function renderPatnerss(data) {
             if (data == null || typeof data != "object") {
                 return;
             }
             var i = 0;

             var renderData = [];
             Object.values(data).forEach((d) => {
                 var editButton = `
                 <button type="button" class="edit btn btn-primary  btn-icon" data-id='${d['id']}' cus_contact_1="Edit"><i class='la la-pencil-alt'></i></button>
                 `;
                 var deleteButton = `
                 <button  type="button" class="delete btn btn-warning btn-icon" data-id='${d['id']}' cus_contact_1="Delete"><i class='la la-trash'></i></button>
                 `;
                 var button = `    ${vcrud['hk_update'] == 1 ? editButton : ''}  ${vcrud['hk_delete'] == 1 ? deleteButton : ''}`;
                 var lokasi = `
                 ${d['cus_address']  ? d['cus_address']+'<br>' : ''} 
                 ${d['cus_town']  ? d['cus_town']+'<br>' : ''} 
                 ${d['cus_region']  ? d['cus_region'] : ''} 
                 `;

                 renderData.push([d['customer_name'], d['cus_company'], lokasi, d['cus_contact_1'], d['cus_description'], button]);
             });
             FDataTable.clear().rows.add(renderData).draw('full-hold');
         }

         FDataTable.on('click', '.edit', function() {
             PatnersModal.form.trigger('reset');
             PatnersModal.self.modal('show');
             PatnersModal.addBtn.hide();
             PatnersModal.saveEditBtn.show();
             var currentData = dataPatnerss[$(this).data('id')];
             PatnersModal.id.val(currentData['id']);
             PatnersModal.cus_status.val(currentData['cus_status']).change();

             PatnersModal.cus_address.val(currentData['cus_address']);
             PatnersModal.cus_description.val(currentData['cus_description']);
             PatnersModal.customer_name.val(currentData['customer_name']);
             PatnersModal.cus_company.val(currentData['cus_company']);
             PatnersModal.cus_town.val(currentData['cus_town']);
             PatnersModal.cus_contact_1.val(currentData['cus_contact_1']);
             PatnersModal.cus_region.val(currentData['cus_region']);

         })

         FDataTable.on('click', '.delete', function() {
             var currentData = $(this).data('id');
             Swal.fire(swalDeleteConfigure).then((result) => {
                 if (result.isConfirmed == false) {
                     return;
                 }
                 $.ajax({
                     url: "<?= base_url('patners/deletePatners') ?>",
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
                         delete dataPatnerss[d['id']];
                         swal.fire("Simpan Berhasil", "", "success");
                         renderPatnerss(dataPatnerss);
                         PatnersModal.self.modal('hide');
                     },
                     error: function(e) {}
                 });
             });

         })

         PatnersModal.form.submit(function(event) {
             event.preventDefault();
             var isAdd = PatnersModal.addBtn.is(':visible');
             var url = "<?= site_url('patners/') ?>";
             url += isAdd ? "addPatners" : "editPatners";
             var button = isAdd ? PatnersModal.addBtn : PatnersModal.saveEditBtn;

             Swal.fire(swalSaveConfigure).then((result) => {
                 if (result.isConfirmed == false) {
                     return;
                 }
                 $.ajax({
                     url: url,
                     'type': 'POST',
                     data: new FormData(PatnersModal.form[0]),
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
                         dataPatnerss[d['id']] = d;
                         swal.fire(swalSuccessConfigure);
                         renderPatnerss(dataPatnerss);
                         PatnersModal.self.modal('hide');
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