<div class="card card-custom position-relative overflow-hidden">
    <!--begin::Shape-->
    <div class="container no-print">
        <div class="alert alert-custom alert-white alert-shadow gutter-b" role="alert">

            <?php
            $attributes = array('id' => 'leadgerAccounst', 'method' => 'get', 'class' => 'form col-lg-12');
            ?>
            <?php echo form_open_multipart('statement/ledger', $attributes); ?>
            <div class="col-lg-12">
                <div class="col-lg-12">
                    <!-- <div class="col-lg-3"> -->
                    <div style="float: right" class="form-group" style="margin-top: 16px;">
                        <?php
                        $data = array('class' => 'btn btn-default btn-outline-primary  mr-2', 'type' => 'button', 'id' => 'btn_export_excel', 'value' => 'true', 'content' => '<i class="fa fa-download" aria-hidden="true"></i> Export Excel');
                        echo form_button($data);
                        ?>
                    </div>
                    <!-- </div> -->
                    <!-- <div class="col-lg-3"> -->
                    <div style="float: right" class="form-group" style="margin-top: 16px;">
                        <a onclick="printDiv('print-section')" class="btn btn-default btn-outline-primary  mr-2"><i class="fa fa-print  pull-left"></i> Cetak</a>
                    </div>
                    <!-- </div> -->
                </div>
                <div class="row col-lg-12">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <?php echo form_label('Akun'); ?>
                            <select name="account_head_struckter" id="account_head_struckter" class="form-control select2">
                                <?php
                                foreach ($accounts as $lv1) {
                                    echo '<optgroup label="[' . $lv1['head_number'] . '] ' . $lv1['name'] . '">';
                                    foreach ($lv1['children'] as $lv2) {
                                        echo '<optgroup label="&nbsp&nbsp&nbsp [' . $lv1['head_number'] . '.' . $lv2['head_number'] . '] ' . $lv2['name'] . '">';
                                        foreach ($lv2['children'] as $lv3) {
                                            echo '<option value="' . $lv3['id_head'] . '">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp [' . $lv1['head_number'] . '.' . $lv2['head_number'] . '.' . $lv3['head_number'] . '] ' . $lv3['name'] . '';
                                            echo '</option>';
                                        }
                                        echo '</optgroup>';
                                    }
                                    echo '</optgroup>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <?php echo form_label('Search'); ?>
                            <input class="form-control" type="text" placeholder="Search" name="search" value="<?= $filter['search'] ?>" />
                        </div>
                    </div>
                    <div class="col-lg-3 ">
                        <div class="form-group">
                            <?php echo form_label('Dari Tanggal'); ?>
                            <?php
                            $data = array('class' => 'form-control', 'type' => 'date', 'id' => 'date_start', 'name' => 'date_start', 'reqiured' => '', 'value' => $filter['date_start']);
                            echo form_input($data);
                            ?>
                        </div>
                    </div>
                    <div class="col-lg-3 ">
                        <div class="form-group">
                            <?php
                            echo form_label('Sampai Tanggal'); ?>
                            <?php
                            $data = array('class' => 'form-control input-lg', 'type' => 'date', 'id' => 'date_end', 'name' => 'date_end', 'reqiured' => '', 'value' => $filter['date_end']);
                            echo form_input($data);
                            ?>
                        </div>
                    </div>
                    <!-- <div class="col-lg-3"> -->
                    <div class="form-group" style="margin-top: 24px; float: right">
                        <button class="btn btn-info btn-flat mr-2" type="submit" name="btn_submit_customer" value="true"> <i class=" fa fa-search pull-left"></i> Buat Statement</button>

                    </div>
                    <!-- </div> -->
                </div>
                <?php form_close(); ?>
            </div>
        </div>
        <?php form_close(); ?>
    </div>
</div>
<div class="card card-custom col-lg-12" id="print-section">
    <div class="card-body">

        <!-- <div class="row"> -->
        <!-- <div class="col-lg-3"></div> -->
        <div class="col-lg-12">
            <h2 style="text-align:center">BUKU BESAR </h2>
            <h3 style="text-align:center">
                <?php echo $this->db->get_where('mp_langingpage', array('id' => 1))->result_array()[0]['companyname'];
                ?>
            </h3>
            <h4 style="text-align:center"><b> Dari </b> <?= $filter['date_start'] ?> <b> Sampai </b> <?php echo $filter['date_end']; ?></h4>
            <h4 style="text-align:center"><b> Dibuat </b> <?php echo Date('Y-m-d'); ?> </h4>
        </div>
        <!-- <div class="col-lg-3"></div> -->
        <!-- </div> -->
        <div>
            <?php
            foreach ($journals as $lv1) {
                // echo $lv1['name'];
                echo '<h4 class=""><b>' . $lv1['head_number']  . ' ' . $lv1['name'] . ' : </b></h4>';
                foreach ($lv1['children'] as $lv2) {
                    if ($lv2['open']) {
                        echo '<h7 class=""><b>' . $lv1['head_number'] . '.' . $lv2['head_number'] . ' - ' . $lv2['name']  . ' : </b></h7>';

                        foreach ($lv2['children'] as $lv3) {

                            if (!empty($lv3['data'])) {

                                if ($lv3['data']['saldo_awal'] == 0) {
                                    $total_ledger = 0;
                                } else {
                                    $total_ledger = $lv3['data']['saldo_awal'];
                                }

                                echo '<hr />                                       

                                                    <table id="1" class="table table-striped table-hover">
                                                    <div class=" ledger_row_head" style=" text-transform:uppercase;">
                                                            <b>' . $lv1['head_number'] . '.' . $lv2['head_number'] . '.' . $lv3['head_number']  . ' - ' . $lv3['name'] . '</b>
                                                    </div>

                                                    <thead class="ledger-table-head">
                                                        <th class="">TANGGAL</th>
                                                        <th class="">NO JURNAL</th>
                                                        <th class="">TRANSAKSI</th>
                                                        <th class="">DEBIT</th>                
                                                        <th class="">KREDIT</th>
                                                        <th class="">SALDO</th>
                                                    </thead>
                                                    <tbody><tr>
                                                    <td></td><td></td><td>Saldo Sebelum</td><td>
                                                    </td>
                                                    <td>
                                                    </td>
                                                    <td>' . ($total_ledger < 0 ? '( <a class="currency">' . number_format(-$total_ledger, 2, ',', '.') . '</a>)' : '<a class="currency">' . number_format($total_ledger, 2, ',', '.') . '</a>') . '</td>            
                                                    </tr>
                                                    ';

                                foreach ($lv3['data']['transactions'] as $lv4) {
                                    $debitamount = '';
                                    $creditamount = '';

                                    if ($lv4['type'] == 0) {
                                        $debitamount = $lv4['amount'];
                                        $total_ledger = $total_ledger + $debitamount;
                                    } else if ($lv4['type'] == 1) {
                                        $creditamount = $lv4['amount'];
                                        $total_ledger = $total_ledger - $creditamount;
                                    }
                                    // var_dump($lv4);
                                    echo '<tr>
                                        <td>' . $lv4['date'] . '</td><td> <a href="' . base_url('accounting/show_journal/') . $lv4['parent_id'] . '">' . $lv4['ref_number'] . '</td><td><a >' . $lv4['sub_keterangan'] . '</a></td><td>
                                            <a  class="currency">' .
                                        (!empty($debitamount) ? number_format($debitamount, 2, ',', '.') : '') .
                                        '</a>
                                        </td>
                                        <td>
                                            <a   class="currency">' .
                                        (!empty($creditamount) ? number_format($creditamount, 2, ',', '.') : '')
                                        . '</a>
                                        </td>
                                        <td  >' . ($total_ledger < 0 ? '( <a class="currency">' . number_format(-$total_ledger, 2, ',', '.') . '</a>)' : '<a class="currency">' . number_format($total_ledger, 2, ',', '.') . '</a>') . '</td>            
                                    </tr>';
                                }
                                echo '</tbody></table>';
                            }
                        }
                    }
                }
                echo "<hr><hr>";
            }
            // echo $ledger_records;
            ?>
        </div>
    </div>
</div>
<!-- </div> -->

<script>
    $('#menu_id_24').addClass('menu-item-active menu-item-open menu-item-here"')
    $('#submenu_id_60').addClass('menu-item-active')

    $('#btn_export_excel').on('click', function() {
        console.log('s')
        from = $('#from').val()
        to = $('#to').val()
        account_head = $('#account_head').val()
        url = `<?= base_url('statements/export_excel_ledger?from=') ?>` + from + '&to=' + to + '&account_head=' + account_head;
        location.href = url;
    })
</script>
<!-- Bootstrap model  -->
<?php $this->load->view('bootstrap_model.php');
if (!empty($account_head)) {
?>
    <script>
        $('#account_head').val('<?= $account_head ?>')
    </script>
<?php
}
?>
<!-- Bootstrap model  ends-->