<style>
    .fr-currency {
        text-align: right;
        /* font-weight: 100px; */
    }
</style>
<div class="card card-custom position-relative overflow-hidden">
    <!--begin::Shape-->
    <div class="container no-print">
        <div class="alert alert-custom alert-white alert-shadow gutter-b" role="alert">

            <?php
            $attributes = array('id' => 'leadgerAccounst', 'method' => 'get', 'class' => 'form col-lg-12');
            ?>
            <?php echo form_open_multipart('statement/cash_flow', $attributes); ?>
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
                    <!-- <div class="col-lg-6">
                        <div class="form-group">
                            <?php echo form_label('Search'); ?>
                            <input class="form-control" type="text" placeholder="Search" name="search" value="<?= $filter['search'] ?>" />
                        </div>
                    </div> -->
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
            <h2 style="text-align:center">ARUS KAS </h2>
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
                    <th class=""></th>
                    <th class=""></th>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:left;">
                            <h4><b> ARUS KAS DARI AKTIVITAS OPERASI
                                </b></h4>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>';
                    <?php
                    echo '<tr><td style="text-align:left;"><h4>&nbsp&nbsp&nbsp Pendapatan Usaha : </h4></td><td></td><td class="fr-currency">' . number_format($journals['in_usaha']['value']) . '</td><td></td></tr>';
                    echo '<tr><td style="text-align:left;"><h4>&nbsp&nbsp&nbsp Pendapatan Bank : </h4></td><td></td><td class="fr-currency">' . number_format($journals['in_bank']['value']) . '</td><td></td></tr>';
                    echo '<tr><td style="text-align:left;"><h4>&nbsp&nbsp&nbsp Pendapatan Lainnya : </h4></td><td></td><td class="fr-currency">' . number_format($journals['in_dll']['value']) . '</td><td></td></tr>';
                    echo '<tr><td style="text-align:left;"><h4>&nbsp&nbsp&nbsp Pengeluaran HPP : </h4></td><td></td><td class="fr-currency">' . number_format($journals['out_usaha']['value']) . '</td><td></td></tr>';
                    echo '<tr><td style="text-align:left;"><h4>&nbsp&nbsp&nbsp Pengeluaran Biaya General Administrasi : </h4></td><td></td><td class="fr-currency">' . number_format($journals['out_general']['value']) . '</td><td></td></tr>';
                    if (!empty($journals['out_general']['children'])) {
                        foreach ($journals['out_general']['children'] as $ch)
                            echo '<tr><td style="text-align:left;"><a href="' . base_url('accounting/show_journal/' . $ch['id']) . '">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp ' . $ch['naration'] . ' : </a></td><td class="fr-currency">' . number_format($ch['amount']) . '</td><td></td></tr>';
                    }
                    echo '<tr><td style="text-align:left;"><h4>&nbsp&nbsp&nbsp Pengeluaran Pajak :</h4></td><td></td><td class="fr-currency">' . number_format($journals['out_pajak']['value']) . '</td><td></td></tr>';
                    echo '<tr><td style="text-align:left;"><h4><b>&nbsp&nbsp&nbsp TOTAL : </b></h4></td><td></td><td></td><td class="fr-currency"> ' . number_format($journals['total']['operasi']) . '</td></tr>';
                    ?>
                    <tr>
                        <td style="text-align:left;">
                            <h4><b> ARUS KAS DARI AKTIVITAS PENDANAAN
                                </b></h4>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    echo '<tr><td style="text-align:left;"><h4>&nbsp&nbsp&nbsp Pinjaman Modal : </h4></td><td></td><td class="fr-currency">' . number_format($journals['inves_pinjaman']['value']) . '</td><td></td></tr>';
                    echo '<tr><td style="text-align:left;"><h4><b>&nbsp&nbsp&nbsp TOTAL : </b></h4></td><td></td><td></td><td class="fr-currency"> ' . number_format($journals['total']['inves']) . '</td></tr>';
                    ?>
                    <tr>
                        <td style="text-align:left;">
                            <h4><b> Kenaikkan Kas
                                </b></h4>
                        </td>
                        <td></td>
                        <td></td>
                        <td class="fr-currency"><?= number_format($journals['total']['operasi'] + $journals['total']['inves']) ?></td>
                    </tr>
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