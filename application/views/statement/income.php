<div class="card card-custom position-relative overflow-hidden">
    <!--begin::Shape-->
    <div class="container no-print">
        <div class="alert alert-custom alert-white alert-shadow gutter-b" role="alert">

            <?php
            $attributes = array('id' => 'leadgerAccounst', 'method' => 'get', 'class' => 'form col-lg-12');
            ?>
            <?php echo form_open_multipart('statement/income_statement', $attributes); ?>
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
                            <select name="account_head" id="account_head" class="form-control select2">
                                <?php echo $accounts_records; ?>
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
                <tbody>
                    <?php
                    $tot_pendapatan = 0;
                    $tot_beban = 0;

                    foreach ($journals as $lv1) {
                        // echo $lv1['name'];
                        echo '
                <tr><td colspan="2">  <div class="row">
                <div class="col-md-12">
                <h4 class="income-style"><b>' . $lv1['name']  . ' </b></h4>
                </div>
                </div>
                </td></tr>
                <tr><td colspan="2"><span class="income-style-sub"><b> Akun </b></span></td></tr>
               
                ';
                        foreach ($lv1['children'] as $lv2) {
                            if ($lv2['open']) {
                                echo '<tr><td style="text-align:left;"><h4><b>&nbsp&nbsp&nbsp' . $lv1['head_number'] . '.' . $lv2['head_number'] . ' - ' . $lv2['name'] . ' : </b></h4></td><td></td></tr>';
                                // echo '<h7 class=""><b>' . $lv1['head_number'] . '.' . $lv2['head_number'] . ' - ' . $lv2['name']  . ' : </b></h7>';

                                foreach ($lv2['children'] as $lv3) {
                                    echo '<tr><td style="text-align:left;"><h4>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp' . $lv1['head_number'] . '.' . $lv2['head_number'] . '.' . $lv3['head_number'] .  ' - ' . $lv3['name'] . ' : </h4></td>';


                                    if ($lv1['head_number'] == 4) {
                                        $tot_pendapatan += (float)$lv3['data'];
                                        if ((float) $lv3['data'] < 0) {
                                            echo '<td style="text-align:right;">' . number_format(-$lv3['data'], 2, ',', '.') . '</td></tr>';
                                            // echo 'tru';
                                        } else {
                                            echo '<td style="text-align:right;">(' . number_format($lv3['data'], 2, ',', '.') . ')</td></tr>';
                                            // $tot_pendapatan -= (float)$lv3['data'];
                                        }
                                    } else {
                                        $tot_beban += (float)$lv3['data'];
                                        if ((float) $lv3['data'] < 0) {
                                            echo '<td style="text-align:right;">(' . number_format(-$lv3['data'], 2, ',', '.') . ')</td></tr>';
                                        } else {
                                            echo '<td style="text-align:right;">' . number_format($lv3['data'], 2, ',', '.') . '</td></tr>';
                                        }
                                    }
                                    // echo '<td style="text-align:right;">' . $lv3['data'] . '</td></tr>';
                                }
                            }
                        }
                        if ($lv1['head_number'] == 4) {
                            if ($tot_pendapatan < 0) {
                                echo '<tr><td><h4><b>TOTAL PENDAPATAN</b></h4></td><td style="text-align:right;"><h4><b>' . number_format(-$tot_pendapatan, 2, ',', '.') . '</b></h4></td></tr>';
                                // echo 'tru';
                            } else {
                                echo '<tr><td><h4><b>TOTAL PENDAPATAN</b></h4></td><td style="text-align:right;"><h4><b>(' . number_format($tot_pendapatan, 2, ',', '.') . ')</b></h4></td></tr>';
                            }
                        } else {
                            if ($tot_beban < 0) {
                                echo '<tr><td><h4><b>TOTAL BEBAN</b></h4></td><td style="text-align:right;"> <h4><b>(' . number_format($tot_beban, 2, ',', '.') . ')</b></h4></td></tr>';
                                // echo 'tru';
                            } else {
                                echo '<tr><td><h4><b>TOTAL BEBAN</b></h4></td><td style="text-align:right;"><h4><b>' . number_format($tot_beban, 2, ',', '.') . '</b></h4></td></tr>';
                            }
                        }
                    }
                    echo '

                    <tr class=" total-income"><td colspan=""><h4 class=""><b> Laba / Rugi</b></h4></td><td style="text-align:right;"><h4 ><b>' . number_format(((-$tot_pendapatan) - ($tot_beban)), 2, ',', '.') . '</b></h4></td></tr>';

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
        from = $('#from').val()
        to = $('#to').val()
        account_head = $('#account_head').val()
        url = `<?= base_url('statements/export_excel_ledger?from=') ?>` + from + '&to=' + to + '&account_head=' + account_head;
        location.href = url;
    })
</script>
<!-- Bootstrap model  -->


<!-- Bootstrap model  ends-->