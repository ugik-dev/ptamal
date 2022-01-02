 <div class="card card-custom" id="print-section">
     <div class="card-body">
         <div class="col-md-12">
             <div class="col-md-9">
                 <form class="form-inline" id="toolbar_form">
                     <div class="form-group">
                         <label>Dari : </label>
                         <input id="from" name="from" type="date" class="form-control ml-1 " />
                         <input id="id" name="id" value="<?= $bank['relation_head'] ?>" class="form-control ml-1 " />
                     </div>
                     <div class="mr-1 form-group">
                         <label>Sampai : </label>
                         <input id="to" name="to" type="date" class="form-control ml-1 " />
                     </div>
                     <div class="mr-1 form-group">
                         <label>Jenis : </label>
                         <select id="type" name="type" class="form-control ml-1 ">
                             <option value=""> Semua </option>
                             <option value="0"> Debit </option>
                             <option value="1"> Kredit </option>
                         </select>
                     </div>
                 </form>
             </div>
             <div class="pull pull-right">
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
                     <h3 class="box-title"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Tambah Bank </h3>
                 </div>
                 <div class="box-body">
                     <div class="table-responsive col-md-12">
                         <table class="table table-bordered table-hover table-checkable mt-10" id="FDataTable">
                             <thead>
                                 <tr>
                                     <th width="150px">Tanggal</th>
                                     <th class="text-center">Nominal</th>
                                     <th width="150px">Patner</th>
                                     <th width="100px">Type</th>
                                     <th>Deskripsi</th>
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

 <script>
     $('#menu_id_18').addClass('menu-item-active menu-item-open menu-item-here"')
     $('#submenu_id_31').addClass('menu-item-active')
     $(document).ready(function() {
         var dataBanks = [];
         var vcrud = <?= json_encode($vcrud) ?>;
         var add_new_data_btn = $('#add_new_data_btn');
         vcrud['hk_create'] == 0 ? add_new_data_btn.prop('hidden', true) : add_new_data_btn.prop('hidden', false);
         var toolbar = {
             'self': $('#toolbar_form'),
             'from': $('#toolbar_form').find('#from'),
             'to': $('#toolbar_form').find('#to'),
             'id': $('#toolbar_form').find('#id'),
             'type': $('#toolbar_form').find('#type'),
         };

         toolbar.type.on('change', () => {
             getAllBook()
         })

         var FDataTable = $('#FDataTable').DataTable({
             'columnDefs': [{
                 targets: [1],
                 className: 'text-right'
             }, {
                 targets: [4, 5],
                 className: 'text-left'
             }, ],
             deferRender: true,
             "order": [
                 [0, "desc"]
             ]
         });

         getAllBook()

         function getAllBook() {
             swal.fire({
                 title: 'Loading Transaction...',
                 allowOutsideClick: false
             });
             swal.showLoading();
             return $.ajax({
                 url: `<?php echo base_url('Bank/getBook?by_id=true') ?>`,
                 'type': 'GET',
                 data: toolbar.self.serialize(),
                 success: function(data) {
                     swal.close();
                     var json = JSON.parse(data);
                     if (json['error']) {
                         return;
                     }
                     dataBanks = json['data'];
                     renderBanks(dataBanks);
                 },
                 error: function(e) {}
             });
         }


         function renderBanks(data) {
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

                 var info = `Sumber : ${d['generated_source']} <br>Jurnal : <a href="<?= base_url() ?>/accounting/show_journal">${d['ref_number']} </a>`;
                 renderData.push([d['date'], formatRupiah2(d['amount']), d['customer_name'], d['type'] == 0 ? 'Debit' : 'Kredit', d['naration'], info]);
             });
             FDataTable.clear().rows.add(renderData).draw('full-hold');
         }

     });
 </script>
 <!-- Bootstrap model  -->
 <?php
    //  $this->load->view('bootstrap_model.php'); 
    ?>
 <!-- Bootstrap model  ends-->