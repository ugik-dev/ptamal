<div class="card card-custom position-relative overflow-hidden">
    <!--begin::Shape-->
    <div class="container no-print">
        <div class="alert alert-custom alert-white alert-shadow gutter-b" role="alert">

            <?php
            $attributes = array('id' => 'leadgerAccounst', 'method' => 'get', 'class' => 'form col-lg-12');
            ?>
            <?php echo form_open_multipart('statement/trail_balance', $attributes); ?>
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
                    <div class="col-lg-6" hidden>
                        <div class="form-group">
                            <?php echo form_label('Akun'); ?>
                            <select name="account_head" id="account_head" class="form-control select2">
                                <?php echo $accounts_records; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6" hidden>
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
            <h2 style="text-align:center">NERACA SALDO </h2>
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
            <table id="1" class="table table-striped table-hover">
                <thead class="ledger-table-head">
                    <th class="">NAMA AKUN</th>
                    <th class="">DEBIT</th>
                    <th class="">KREDIT</th>
                </thead>
                <tbody>
                    <?php
                    $total_debit = 0;
                    $total_kredit = 0;
                    foreach ($journals as $lv1) {
                        // echo $lv1['name'];
                        echo '<tr><td style="text-align:left;"><h4><b>' . $lv1['name']  . ' </b></h4></td><td></td><td></td></tr>';
                        foreach ($lv1['children'] as $lv2) {
                            if ($lv2['open']) {
                                echo '<tr><td style="text-align:left;"><h4><b>&nbsp&nbsp&nbsp' . $lv1['head_number'] . '.' . $lv2['head_number'] . ' - ' . $lv2['name'] . ' : </b></h4></td><td></td><td></td></tr>';
                                // echo '<h7 class=""><b>' . $lv1['head_number'] . '.' . $lv2['head_number'] . ' - ' . $lv2['name']  . ' : </b></h7>';

                                foreach ($lv2['children'] as $lv3) {
                                    echo '<tr><td style="text-align:left;"><h4>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp' . $lv1['head_number'] . '.' . $lv2['head_number'] . '.' . $lv3['head_number'] .  ' - ' . $lv3['name'] . ' : </h4></td>';


                                    if ($lv3['data'] > 0) {
                                        $total_debit = $total_debit + $lv3['data'];
                                        echo '<td style="text-align:right;">' . number_format($lv3['data'], 2, ',', '.') . '</td><td></td></tr>';
                                    } else {
                                        $total_kredit = $total_kredit + $lv3['data'];
                                        echo '<td></td><td style="text-align:right;">' . number_format(abs($lv3['data']), 2, ',', '.') . '</td></tr>';
                                    }

                                    //             echo '<hr />                                       

                                    //         <b>' . $lv1['head_number'] . '.' . $lv2['head_number'] . '.' . $lv3['head_number']  . ' - ' . $lv3['name'] . '</b>

                                    // <td>' . ($total_ledger < 0 ? '( <a class="currency">' . number_format(-$total_ledger, 2, ',', '.') . '</a>)' : '<a class="currency">' . number_format($total_ledger, 2, ',', '.') . '</a>') . '</td>            
                                    // </tr>
                                    // ';


                                    // var_dump($lv4);

                                }
                            }
                        }
                    }
                    echo '<tr><td style="text-align:left;"><h4><b>TOTAL</b></h4></td><td>' . number_format($total_debit, 2, ',', '.') . '</td><td>' . number_format(abs($total_kredit), 2, ',', '.') . '</td></tr>';

                    // echo $ledger_records;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- </div> -->

<script>
    $('#menu_id_<?= $vcrud['parent_id'] ?>').addClass('menu-item-active menu-item-open menu-item-here"')
    $('#submenu_id_<?= $vcrud['id_menulist'] ?>').addClass('menu-item-active')

    $('#btn_export_excel').on('click', function() {
        console.log('s')
        date_start = $('#date_start').val()
        date_end = $('#date_end').val()
        account_head = $('#account_head').val()
        url = `<?= base_url('excel/trail_balance?date_start=') ?>` + date_start + '&date_end=' + date_end;
        location.href = url;
    })
</script>
<!-- Bootstrap model  -->


<!-- Bootstrap model  ends-->