  <div class="card card-custom" id="print-section">
      <div class="card-body">
          <div class="box-body ">
              <div class="">
                  <?php
                    $attributes = array('id' => 'journal_voucher', 'method' => 'post', 'class' => '');
                    ?>
                  <?php echo form_open('statements/create_journal_voucher', $attributes); ?>
                  <div class="">
                      <div class="row no-print invoice">
                          <h4 class="ml-3"> <i class="fa fa-check-circle mr-2 ml-2"></i>
                              <?= $journals['ref_number'] ?>
                          </h4>

                          <div class="col-md-12 ">
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>Nama Mitra</label>

                                      <h4><strong id="mitra_name"><?= $journals['customer_name'] ?> </strong></h4>
                                      <hr>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>No Jurnal</label>
                                      <h4><strong id="no_jurnal"><?= $journals['ref_number'] ?></strong></h4>
                                      <hr>
                                  </div>
                                  <div class="col-md-6">
                                      <label>Tanggal</label>
                                      <h4><strong id="transaction_date"><?= explode('-', $journals['date'])[2] . '-' . explode('-', $journals['date'])[1] . '-' . explode('-', $journals['date'])[0] ?></strong></h4>
                                      <hr>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label>Deskripsi</label>
                                  <h4><strong id="naration"><?= $journals['naration'] ?></strong></h4>
                                  <hr>
                              </div>
                          </div>
                      </div>
                      <div class="row invoice">
                          <div class="col-md-12 table-responsive">
                              <table class="table table-striped table-hover  ">
                                  <thead>
                                      <tr>
                                          <th class="">Akun</th>
                                          <th class="">Debit</th>
                                          <th class="">Kredit</th>
                                          <th class="">Keterangan</th>
                                      </tr>
                                  </thead>
                                  <tbody id="transaction_table_body">
                                      <?php
                                        $totdeb = 0;
                                        $totkredit = 0;
                                        $i = 0;
                                        $doc = false;
                                        if (!empty($journals['children']))
                                            foreach ($journals['children'] as $key => $sub) {

                                                if ($sub['accounthead'] == '13') {
                                                    $doc = true;
                                                }
                                        ?>
                                          <tr>
                                              <td class=""><?= substr($sub['head_number'], 0, 1) . '.' . substr($sub['head_number'], 1, 2) . '.' . substr($sub['head_number'], 3, 3) . ' ' . $sub['head_name'] ?> </td>
                                              <?php if ($sub['type'] == '0') {
                                                    $totdeb = $totdeb + $sub['amount'];
                                                ?>
                                                  <td class="currency rinc_debit"><?= $sub['amount'] ?></td>
                                                  <td class="rinc_kredit"></td>
                                              <?php } else {
                                                    $totkredit = $totkredit + $sub['amount'];
                                                ?>
                                                  <td class="rinc_debit"></td>
                                                  <td class="currency rinc_kredit"><?= $sub['amount'] ?></td>
                                              <?php } ?>
                                              <td class="rinc_ket"><?= $sub['sub_keterangan'] ?> </td>
                                          </tr>
                                      <?php } ?>
                                  </tbody>
                                  <tfoot>
                                      <tr>
                                          <!-- <th></th> -->
                                          <th>Total: </th>
                                          <th>
                                              <p class='currency_rp'><?= $totdeb ?></p>
                                          </th>
                                          <th>
                                              <p class='currency_rp'><?= $totkredit ?></p>
                                          </th>
                                          <th>
                                          </th>
                                      </tr>
                                      <tr>
                                          <td colspan="4" class="transaction_validity" id="transaction_validity">
                                          </td>
                                      </tr>
                                  </tfoot>
                              </table>
                          </div>
                          <div class="col-md-12" hidden>
                              <div class="row">
                                  <div class="col-md-3">
                                      <div class="form-group">
                                          <label>Disetujui</label>
                                          <h4 id="acc_1" class="form-control input-lg"> </h4>
                                      </div>
                                  </div>
                                  <div class="col-md-3">
                                      <div class="form-group" id='label_kendaraan'>
                                          <label>Diverifikasi</label>
                                          <h4 id="acc_2" class="form-control input-lg"> </h4>
                                      </div>
                                  </div>
                                  <div class="col-md-3">
                                      <div class="form-group" id='label_kendaraan'>
                                          <label>Dibuat</label>
                                          <h4 id="acc_3" class="form-control input-lg"> </h4>
                                      </div>
                                  </div>
                                  <div class="col-md-3">
                                      <div class="form-group" id='label_kendaraan'>
                                          <label>Dibukukan</label>
                                          <h4 id="dibukukan" class="form-control input-lg"> </h4>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="col-md-12 ">
                              <div class="form-group">
                                  <?php
                                    if (!empty($journals['ref_url'])) {
                                        echo '<a href="' . base_url() .  $journals['ref_url']  . '" class="btn btn-primary margin btn-lg pull-right ml-2" style="float: right"> <i class="fa fa-eye" aria-hidden="true"></i> Lihat ' . ucfirst(explode('/', $journals['ref_url'])[0]) . '</a> ';
                                    } else {
                                        echo '<a href="' . base_url() . 'accounting/edit_jurnal/' . $journals['parent_id'] . '" class="btn btn-info margin btn-lg ml-2 pull-right" style="float: right"> <i class="fa fa-list-alt" aria-hidden="true"></i> Edit</a> ';
                                    }

                                    if ($doc) {
                                    ?>
                                      <a href="<?= base_url() . 'statements/export_doc/' . $journals['parent_id'] ?>" class="btn btn-secondary  margin btn-lg pull-right" style="float: right"> <i class="fa fa-download" aria-hidden="true"></i>
                                          DoC</a>
                                  <?php } ?>
                                  <a onclick="printSingleJurnal2()" class="btn btn-secondary  margin btn-lg pull-right" style="float: right"> <i class="fa fa-print" aria-hidden="true"></i>
                                      Print</a>



                              </div>
                          </div>
                      </div>
                  </div>
                  <?php form_close(); ?>
              </div>
          </div>
      </div>
  </div>
  <script src="<?php echo base_url(); ?>assets/dist/js/backend/journal_voucher.js"></script>
  <script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.mask.min.js"></script>

  <script>
      $('#menu_id_24').addClass('menu-item-active menu-item-open menu-item-here"')
      $('#submenu_id_59').addClass('menu-item-active')

      function formatRupiah(angka, prefix) {
          var number_string = angka.toString();
          split = number_string.split(".");
          sisa = split[0].length % 3;
          rupiah = split[0].substr(0, sisa);
          ribuan = split[0].substr(sisa).match(/\d{3}/gi);
          if (ribuan) {
              separator = sisa ? "." : "";
              rupiah += separator + ribuan.join(".");
          }

          rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
          return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
      }


      var elements = document.getElementsByClassName('currency')

      for (var i = 0; i < elements.length; i++) {
          elements[i].innerHTML = formatRupiah(elements[i].innerHTML);
      }

      var currency_rp = document.getElementsByClassName('currency_rp')

      for (var i = 0; i < currency_rp.length; i++) {
          currency_rp[i].innerHTML = formatRupiah(currency_rp[i].innerHTML, true);
      }

      $('.mask').mask('000.000.000.000.000,00', {
          reverse: true
      });


      function printSingleJurnal2(id) {
          var acc_1 = document.getElementById("acc_1").innerHTML;
          var acc_2 = document.getElementById("acc_2").innerHTML;
          var acc_3 = document.getElementById("acc_3").innerHTML;
          var dibukukan = document.getElementById("dibukukan").innerHTML;
          var naration = document.getElementById("naration").innerHTML;
          var no_jurnal = document.getElementById("no_jurnal").innerHTML;
          //   return;
          //   var arr_cars = document.getElementById("arr_cars").innerHTML;
          var mitra_name = document.getElementById("mitra_name").innerHTML;
          var name = document.getElementsByClassName("rinc_name");
          var ket = document.getElementsByClassName("rinc_ket");
          var debit = document.getElementsByClassName("rinc_debit");
          var kredit = document.getElementsByClassName("rinc_kredit");
          var date = document.getElementById("transaction_date").innerHTML;
          console.log(name)
          isi = ` <?php
                    $i = 0;
                    foreach ($journals['children'] as  $sub) {
                        echo '
                <tr  style="height : 22px; padding : 10px">
                <td> [' . $sub['head_number'] . '] ' . $sub['head_name'] . '</td>
                <td> ' . $sub['sub_keterangan'] . '</td>
                <td> ' . ($sub['type'] == 0 ? $sub['amount'] : '') . '</td>
                <td> ' . ($sub['type'] == 1 ? $sub['amount'] : '') . '</td>
                </tr>
              ';
                        $i++;
                    }
                    ?>  `;
          var consdebit = 0;
          var show = 0;
          var conskredit = 0;
          var displyhide = true
          // console.log(fixdate)
          for (var j = <?= $i ?>; j < 7; j++) {
              isi += `<tr  style="height : 22px; padding : 10px">
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            </tr>
            `;
          }
          isi += `<tr  style="height : 22px; padding : 10px">
            <td colspan="2"><strong>Jumlah</strong> </td>
        
            <td style="text-align:right ; padding-right : 5px"><strong><?= number_format($totdeb, 2, '.', ',') ?></strong> </td>
            <td style="text-align:right ; padding-right : 5px"><strong><?= number_format($totkredit, 2, '.', ',') ?></strong> </td>
            </tr>
            `;

          no_rek = '';
          var printContents = `
            <table style="font-size: 17px;width: 100%" border="0">
                <tr>
                    <td style="width: 50%"><img style="heigt: 20px; width : 65%" src="<?= base_url() ?>assets/img/ptamal.jpg" alt="Paris" class="center"></td>                 
                    <td style="width: 50%">
                                <h3 style="text-align:center">JURNAL VOUCHER</h3>    
                    </td>                 
                </tr>
            </table>   
            <br>
                    <table style="font-size: 15px;" border="0">
                              <tr>
                            <td style=";text-align:left ;width: 100px"> Kepada</td>
                            <td style=;width: 10px">:</td>
                            <td style=";text-align:left ;width: 400px"> </td>
                            <td style="text-align:left ;width: 100px">No Jurnal</td>
                            <td style="width: 10px">:</td>
                            <td style="text-align:left; width: 200px ">${no_jurnal}</td>
                        </tr>
                        <tr>
                            <td style="text-align:left ;width: 100px"> Deskripsi</td>
                            <td style="width: 10px">:</td>
                            <td style="text-align:left ;width: 400px">${ naration}</td>
                            <td style="text-align:left ;width: 100px">Tanggal</td>
                            <td style="width: 10px">:</td>
                                <td style="text-align:left; width: 200px">${date}</td>
                           
                            </tr>
                            <tr style="">
                            <td style="text-align:left ;width: 100px">Sejumlah</td>
                            <td style="width: 10px">:</td>
                            <td style="text-align:left ;width: 400px">Rp. ${formatRupiah(show)}</td>
                        </tr>
                            <tr style="${displyhide ? 'display: none' : ''} ;">
                            <td style="text-align:left ;width: 100px">Terbilang</td>
                            <td style="width: 10px">:</td>
                            <td style="text-align:left ;width: 400px;font-style: italic;">${terbilang(show.toString())}</td>
                        
                        </tr>
                    </table>
                   <br>
                    <table style="font-size: 15; width : 990px" border="1" cellspacing="0">
                        <tr>
                            <td style="width: 295px ;text-align:center">No Akun</td>
                            <td style="width: 195px ; text-align:center">Keterangan</td>
                            <td style="width: 130px ; text-align:center">Debit (Rp)</td>
                            <td style="width: 130px; text-align:center">Kredit  (Rp)</td>
                        </tr>
                        ${isi}
                        
                    </table>
                    <br>
                    <table style="font-size: 15; width: 100%" border="0" cellspacing="0">
                        <tr>
                            <td style="width: 400 ;text-align:left; padding : 0px">
                            
                                <table style="" border="1" cellspacing="0">
                                    <tr>
                                        <td style="width: 130 ;text-align:left; padding : 3px">Pengeluaran Berupa</td>
                                        <td style="width: 130 ; text-align:left ; padding : 3px">Kas/Cek/Trans*)</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 130 ;text-align:left; padding : 3px">Nomor</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 130 ;text-align:left; padding : 3px">Tanggal</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 130 ;text-align:left; padding : 3px">A/C No.</td>
                                        <td style="width: 130 ; text-align:left ; padding : 3px">${no_rek}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 160 ; align:right ; padding : 0px">
                                <table style="float: right"  border="1" cellspacing="0">
                                    <tr>
                                        <td style="width: 130px ;text-align:center">Disetujui</td>
                                        <td style="width: 130px ; text-align:center">Diverifikasi</td>
                                        <td style="width: 130px ; text-align:center">Dibuat Oleh</td>
                                        <td style="width: 130px ; text-align:center">Diterima</td>
                                        <td style="width: 130px ; text-align:center">Dibukukan</td>
                                    </tr>
                                    <tr style="height: 90px">
                                        <td style="vertical-align: bottom; text-align:center">${acc_1}</td>
                                        <td style="vertical-align: bottom; text-align:center">${acc_2}</td>
                                        <td style="vertical-align: bottom; text-align:center">${acc_3}</td>
                                        <td></td>
                                        <td style="vertical-align: bottom; text-align:center">${dibukukan}</td>
                                    
                                    </tr>
                                    <tr>
                                        <td style="text-align:left; margin-right: 1px">Tgl</td>
                                        <td style="text-align:left">Tgl</td>
                                        <td style="text-align:left">Tgl</td>
                                        <td style="text-align:left">Tgl</td>
                                        <td style="text-align:left">Tgl : </td>                             
                                    </tr>
                            
                                </table>
                            </td>
                        </tr>
                    </table>
             `;
          var originalContents = document.body.innerHTML;
          document.body.innerHTML = printContents;
          setTimeout(() => {
              window.print();
              document.body.innerHTML = originalContents;
          }, 4000);
      }

      <?php if (!empty($acc)) {

        ?>
          document.getElementById("acc_1").innerHTML = '<?= $acc->name_1 ?>';
          document.getElementById("acc_2").innerHTML = '<?= $acc->name_2 ?>';
          document.getElementById("acc_3").innerHTML = '<?= $acc->name_3 ?>';
          document.getElementById("dibukukan").innerHTML = '<?= $acc->acc_0 ?>';
      <?php  } ?>
  </script>
  <?php $this->load->view('bootstrap_model.php'); ?>