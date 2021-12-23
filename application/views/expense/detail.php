<div class="modal-content">
    <form opd="form" id="accounts_form" onsubmit="return false;" type="multipart" autocomplete="off">
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-lg-8">
                    <label><i class="fa fa-check-circle"></i> Nama </label>
                    <h5><?= $return['customer_name'] ?></h5>
                </div>
                <div class="form-group col-lg-6">
                    <label><i class="fa fa-check-circle"></i> Akun Beban</label>
                    <h5><?= $return['head_name'] ?></h5>
                </div>
                <div class="form-group col-lg-6">
                    <label><i class="fa fa-check-circle"></i> Metode Pembayaran </label>
                    <h5><?= $return['payment_name'] ?></h5>
                </div>
            </div>
            <div class="row">

                <div class="form-group col-lg-6">
                    <label><i class="fa fa-check-circle"></i> Tanggal</label>
                    <h5><?= $return['date'] ?></h5>
                </div>
                <div class="form-group col-lg-6">
                    <?php echo form_label(''); ?>
                    <label><i class="fa fa-check-circle"></i> Nomor Bukti</label>
                    <h5><?= $return['ref_no'] ?></h5>
                </div>
            </div>


            <div class="row">
                <div class="form-group col-lg-6">
                    <label><i class="fa fa-check-circle"></i> Jumlah</label>
                    <h5>Rp <?= number_format($return['total_paid'], 2, '.', ',') ?></h5>
                </div>
                <div class="form-group col-lg-6">
                    <label><i class="fa fa-check-circle"></i> Rincian Transaksi</label>
                    <h5><?= $return['description'] ?></h5>
                </div>

            </div>
            <!-- </div> -->

        </div>
        <div class="modal-footer">
            <?php if (!empty($vcrud['hk_delete'] == 1)) { ?>
                <a class="btn btn-danger my-1 mr-sm-2" href="<?= base_url() . 'expense/delete/' . $return['id'] ?>/true" id="" data-loading-text="Loading..."><i class='la la-trash'></i><strong>Delete</strong></a>
            <?php } ?>
            <?php if (!empty($vcrud['hk_update'] == 1)) { ?>
                <a class="btn btn-success my-1 mr-sm-2" href="<?= base_url() . 'expense/edit/' . $return['id'] ?>" id="" data-loading-text="Loading..."><i class='la la-pencil-alt'></i><strong>Edit</strong></a>
            <?php } ?>
            <a class="btn btn-info my-1 mr-sm-2" href="<?= base_url() . 'accounting/show_journal/' . $return['transaction_id'] ?>" id="" data-loading-text="Loading..."><i class='la la-eye'></i><strong>Jurnal</strong></a>
        </div>
    </form>
</div>

<script>
    $('#menu_id_<?= $vcrud['parent_id'] ?>').addClass('menu-item-active menu-item-open menu-item-here"')
    $('#submenu_id_<?= $vcrud['id_menulist'] ?>').addClass('menu-item-active')
    $(document).ready(function() {
        var vcrud = <?= json_encode($vcrud) ?>;
        // var FormModal = {
        //     'form': $('#accounts_form'),
        //     'submit_btn': $('#accounts_form').find('#submit_btn'),
        //     'id': $('#accounts_form').find('#id'),
        //     'bank_id': $('#accounts_form').find('#bank_id'),
        //     'payee_id': $('#accounts_form').find('#payee_id'),
        //     'method': $('#accounts_form').find('#method'),
        //     'head_id': $('#accounts_form').find('#head_id'),
        //     'total_paid': $('#accounts_form').find('#amount'),
        //     'description': $('#accounts_form').find('#description'),
        //     'ref_no': $('#accounts_form').find('#ref_no'),
        //     'date': $('#accounts_form').find('#date'),
        // }
        // var swalSaveConfigure = {
        //     title: "Konfirmasi simpan",
        //     text: "Yakin akan menyimpan data ini?",
        //     icon: "info",
        //     showCancelButton: true,
        //     confirmButtonColor: "#18a689",
        //     confirmButtonText: "Ya, Simpan!",
        //     reverseButtons: true
        // };


        // var swalSuccessConfigure = {
        //     title: "Simpan berhasil",
        //     icon: "success",
        //     timer: 500
        // };





        <?php if (!empty($return)) { ?>
            // FormModal.id.val('<?= $return['id'] ?>');
            // FormModal.payee_id.val('<?= $return['payee_id'] ?>');
            // FormModal.head_id.val('<?= $return['head_id'] ?>');
            // FormModal.method.val('<?= $return['method'] ?>');
            // FormModal.total_paid.val('<?= $return['total_paid'] ?>');
            // FormModal.description.val('<?= $return['description'] ?>');
            // FormModal.date.val('<?= $return['date'] ?>');
            // FormModal.ref_no.val('<?= $return['ref_no'] ?>');


        <?php } ?>
    });
</script>
<!-- Bootstrap model  -->
<?php
//  $this->load->view('bootstrap_model.php'); 
?>
<!-- Bootstrap model  ends-->