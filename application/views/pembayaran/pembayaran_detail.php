<div class="card card-custom position-relative overflow-hidden" id="print-section">
    <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
        <div class="col-md-9">
            <div class="d-flex font-size-sm flex-wrap">
                <?php if ($dataContent['status_pembayaran'] == 'paid') {
                    echo '<div class="alert alert-custom alert-outline-2x alert-outline-primary fade show mr-3" style="padding:  5px; margin: 2px" role="alert">
                                <div class="alert-icon"><i class="flaticon2-checkmark"></i></div>
                                <div class="alert-text mr-2">Sudah dibayar</div>
                            </div>
                        ';
                } else {
                    echo '<div class="alert alert-custom alert-outline-2x alert-outline-danger fade show mr-3" style="padding-bottom:  0px;padding-top:  0px; margin: 2px" role="alert">
                                <div class="alert-icon"><i class="flaticon2-exclamation"></i></div>
                                <div class="alert-text mr-2">Belum dibayar</div>
                            </div>
                        ';
                } ?>

                <button <?= (($dataContent['status_pembayaran'] == 'unpaid') ? '' : 'hidden') ?> type="button" class="btn btn-warning  py-3 mr-2  my-1 font-weight-bolde" id="add_pelunasan" data-toggle="modal" data-target="#exampleModalLong">
                    <i class="fa fa-plus-square" aria-hidden="true"></i> Buat Pelunasan
                </button>
                <!-- <button type="button" class="btn btn-primary font-weight-bolder py-4 mr-3 mr-sm-14 my-1" onclick="printDiv('print-section')">Print Pembayaran</button> -->
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle py-3 mr-3 mr-sm-14 my-1 font-weight-bolder" style="width : 200px" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">File</button>
                    <div class="dropdown-menu">
                        <a type="button" id="btn_print_kwitansi" class="dropdown-item"> <i class="fa fa-print mr-2" aria-hidden="true"></i> Print Kwitansi</a>
                        <a type="button" id="btn_print_dokumen" class="dropdown-item"> <i class="fa fa-print mr-2" aria-hidden="true"></i> Print Dokumen</a>
                        <a type="button" href="<?= base_url('pembayaran/download/') . $dataContent['id'] ?>" class="btn">Pembayaran PDF</a>
                    </div>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle py-3 mr-3 mr-sm-14 my-1 font-weight-bolder" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>
                    <div class="dropdown-menu">
                        <a type="button" href="<?php echo base_url() . 'pembayaran/edit/' . $dataContent['id'] ?>" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i> Edit</a>
                        <a type="button" href="<?php echo base_url() . 'pembayaran/copy/' . $dataContent['id'] ?>" class="dropdown-item"><i class="fas fa-copy mr-2"> </i> Copy</a>
                        <a type="button" class="dropdown-item" href="<?= base_url() . 'pembayaran/delete/' . $dataContent['id']   ?>"><i class="fa fa-trash mr-2"></i> Delete </a>
                    </div>
                </div>
                <!-- <a type="button" href="<?= base_url('pembayaran') ?>" class="btn btn-light-primary font-weight-bolder mr-3 my-1"><i class="fas fa-reply mr-3 my-1"> </i>Create New Pembayaran</a> -->
            </div>
        </div>
    </div>
    <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
        <div class="col-md-9">
            <!--begin::Pembayaran body-->
            <div class="row pb-4">
                <div class="col-md-3 border-right-md pr-md-10 py-md-10">
                    <!--begin::Pembayaran To-->
                    <div class="text-dark-50 font-size-lg font-weight-bold mb-3">PEMBAYARAN TO.</div>
                    <div class="font-size-lg font-weight-bold mb-10"><?= $dataContent['customer_name'] ?>
                        <br /><?= $dataContent['cus_address'] ?>
                    </div>
                    <!--end::Pembayaran To-->
                    <!--begin::Pembayaran No-->
                    <div class="text-dark-50 font-size-lg font-weight-bold mb-3">PEMBAYARAN NO.</div>
                    <div class="font-size-lg font-weight-bold mb-10"><?= $dataContent['no_pembayaran'] ?></div>
                    <!--end::Pembayaran No-->
                    <!--begin::Pembayaran Date-->
                    <div class="text-dark-50 font-size-lg font-weight-bold mb-3">DATE</div>
                    <div class="font-size-lg font-weight-bold"><?= $dataContent['date'] ?></div>
                    <!--end::Pembayaran Date-->
                </div>
                <div class="col-md-9 py-10 pl-md-10">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="pt-1 pb-9 pl-0 pl-md-5 font-weight-bolder text-muted font-size-lg text-uppercase">Keterangan</th>
                                    <th class="pt-1 pb-9 text-right font-weight-bolder text-muted font-size-lg text-uppercase"></th>
                                    <th class="pt-1 pb-9 text-right font-weight-bolder text-muted font-size-lg text-uppercase">Qyt</th>
                                    <th class="pt-1 pb-9 text-right pr-0 font-weight-bolder text-muted font-size-lg text-uppercase">Harga</th>
                                    <th class="pt-1 pb-9 text-right pr-0 font-weight-bolder text-muted font-size-lg text-uppercase">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                $total_qyt = 0;
                                if (!empty($dataContent['items'])) {
                                    foreach ($dataContent['items'] as $item) {
                                        $total = $total + ($item['amount'] * $item['qyt']);
                                        $total_qyt =  $total_qyt + ($item['qyt']);

                                ?>
                                        <tr class="font-weight-bolder border-bottom-0 font-size-lg">
                                            <td class="border-top-0 pl-0 pl-md-5 py-4 d-flex align-items-center">
                                                <span class="navi-icon mr-2">
                                                    <i class="fa fa-genderless text-primary font-size-h2"></i>
                                                </span><?= $item['keterangan_item'] ?>
                                            </td>
                                            <td class="border-top-0 text-right py-4"><?= $item['date_item'] ?></td>
                                            <td class="border-top-0 text-right py-4"><?= $item['qyt'] . $item['satuan'] ?></td>
                                            <td class="border-top-0 pr-0 py-4 font-size-h6 font-weight-boldest text-right"><?= number_format(($item['amount']), 0, ',', '.') ?></td>
                                            <td class="border-top-0 pr-0 py-4 font-size-h6 font-weight-boldest text-right currency"><?= number_format(($item['amount'] * $item['qyt']), 0, ',', '.')  ?></td>
                                        </tr>
                                <?php }
                                } ?>
                                <tr>
                                    <th class="pt-1 pb-9 pl-0 pl-md-5 font-weight-bolder  font-size-lg text-uppercase"></th>
                                    <th class="pt-1 pb-9 text-right font-weight-bolder text-muted font-size-lg text-uppercase"></th>
                                    <th class="pt-1 pb-9 text-right pr-0 font-weight-bolder  font-size-lg text-uppercase"><?= $total_qyt ?></th>
                                    <th class="pt-1 pb-9 text-right pr-0 font-weight-bolder text-muted font-size-lg text-uppercase"></th>
                                    <th class="pt-1 pb-9 text-right font-weight-bolder  font-size-lg text-uppercase"><?= number_format(($total), 0, ',', '.') ?></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end::Pembayaran body-->
            <!--begin::Pembayaran footer-->
            <div class="row">
                <div class="col-md-5 border-top pt-14 pb-10 pb-md-10">
                    <?php
                    if (!empty($payment_metode['bank_name'])) {
                    ?>
                        <div class="d-flex flex-column flex-md-row">
                            <div class="d-flex flex-column">
                                <div class="font-weight-bold font-size-h6 mb-3">BANK TRANSFER</div>
                                <div class="d-flex justify-content-between font-size-lg mb-3">
                                    <span class="font-weight-bold mr-15">Bank :</span>
                                    <span class="text-right"><?= $payment_metode['bank_name'] ?></span>
                                </div>
                                <div class="d-flex justify-content-between font-size-lg mb-3">
                                    <span class="font-weight-bold mr-15">Account Name:</span>
                                    <span class="text-right"><?= $payment_metode['title_bank'] ?></span>
                                </div>
                                <div class="d-flex justify-content-between font-size-lg mb-3">
                                    <span class="font-weight-bold mr-15">Account Number:</span>
                                    <span class="text-right"><?= $payment_metode['bank_number'] ?></span>
                                </div>
                            </div>
                        </div>

                    <?php
                    } else {
                    ?>
                        <div class="d-flex flex-column flex-md-row">
                            <div class="d-flex flex-column">
                                <div class="font-weight-bold font-size-h6 mb-3">CASH</div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="col-md-7 pt-md-25">
                    <div class="bg-primary rounded d-flex align-items-center justify-content-between text-white max-w-350px position-relative ml-auto p-7">
                        <!--begin::Shape-->
                        <div class="position-absolute opacity-30 top-0 right-0">
                            <span class="svg-icon svg-icon-2x svg-logo-white svg-icon-flip">
                                <!--begin::Svg Icon | path:assets/media/svg/shapes/abstract-8.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" width="176" height="165" viewBox="0 0 176 165" fill="none">
                                    <g clip-path="url(#clip0)">
                                        <path d="M-10.001 135.168C-10.001 151.643 3.87924 165.001 20.9985 165.001C38.1196 165.001 51.998 151.643 51.998 135.168C51.998 118.691 38.1196 105.335 20.9985 105.335C3.87924 105.335 -10.001 118.691 -10.001 135.168Z" fill="#AD84FF" />
                                        <path d="M28.749 64.3117C28.749 78.7296 40.8927 90.4163 55.8745 90.4163C70.8563 90.4163 83 78.7296 83 64.3117C83 49.8954 70.8563 38.207 55.8745 38.207C40.8927 38.207 28.749 49.8954 28.749 64.3117Z" fill="#AD84FF" />
                                        <path d="M82.9996 120.249C82.9996 144.964 103.819 165 129.501 165C155.181 165 176 144.964 176 120.249C176 95.5342 155.181 75.5 129.501 75.5C103.819 75.5 82.9996 95.5342 82.9996 120.249Z" fill="#AD84FF" />
                                        <path d="M98.4976 23.2928C98.4976 43.8887 115.848 60.5856 137.249 60.5856C158.65 60.5856 176 43.8887 176 23.2928C176 2.69692 158.65 -14 137.249 -14C115.848 -14 98.4976 2.69692 98.4976 23.2928Z" fill="#AD84FF" />
                                        <path d="M-10.0011 8.37466C-10.0011 20.7322 0.409554 30.7493 13.2503 30.7493C26.0911 30.7493 36.5 20.7322 36.5 8.37466C36.5 -3.98287 26.0911 -14 13.2503 -14C0.409554 -14 -10.0011 -3.98287 -10.0011 8.37466Z" fill="#AD84FF" />
                                        <path d="M-2.24881 82.9565C-2.24881 87.0757 1.22081 90.4147 5.50108 90.4147C9.78135 90.4147 13.251 87.0757 13.251 82.9565C13.251 78.839 9.78135 75.5 5.50108 75.5C1.22081 75.5 -2.24881 78.839 -2.24881 82.9565Z" fill="#AD84FF" />
                                        <path d="M55.8744 12.1044C55.8744 18.2841 61.0788 23.2926 67.5001 23.2926C73.9196 23.2926 79.124 18.2841 79.124 12.1044C79.124 5.92653 73.9196 0.917969 67.5001 0.917969C61.0788 0.917969 55.8744 5.92653 55.8744 12.1044Z" fill="#AD84FF" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </div>
                        <!--end::Shape-->
                        <div class="font-weight-boldest font-size-h5">Jumlah</div>
                        <div class="text-right d-flex flex-column">
                            <span class="font-weight-boldest font-size-h3 line-height-sm"><?= number_format(($total), 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <?php
                    if ($dataContent['percent_jasa'] > 0) {
                        $fee = $dataContent['percent_jasa'] / 100 * $total; ?>
                        <br>
                        <div class="bg-info rounded d-flex align-items-center justify-content-between text-white max-w-350px position-relative ml-auto p-7">
                            <!--begin::Shape-->
                            <div class="position-absolute opacity-30 top-0 right-0">
                                <span class="svg-icon svg-icon-2x svg-logo-white svg-icon-flip">
                                    <!--begin::Svg Icon | path:assets/media/svg/shapes/abstract-8.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="176" height="165" viewBox="0 0 176 165" fill="none">
                                        <g clip-path="url(#clip0)">
                                            <path d="M-10.001 135.168C-10.001 151.643 3.87924 165.001 20.9985 165.001C38.1196 165.001 51.998 151.643 51.998 135.168C51.998 118.691 38.1196 105.335 20.9985 105.335C3.87924 105.335 -10.001 118.691 -10.001 135.168Z" fill="#AD84FF" />
                                            <path d="M28.749 64.3117C28.749 78.7296 40.8927 90.4163 55.8745 90.4163C70.8563 90.4163 83 78.7296 83 64.3117C83 49.8954 70.8563 38.207 55.8745 38.207C40.8927 38.207 28.749 49.8954 28.749 64.3117Z" fill="#AD84FF" />
                                            <path d="M82.9996 120.249C82.9996 144.964 103.819 165 129.501 165C155.181 165 176 144.964 176 120.249C176 95.5342 155.181 75.5 129.501 75.5C103.819 75.5 82.9996 95.5342 82.9996 120.249Z" fill="#AD84FF" />
                                            <path d="M98.4976 23.2928C98.4976 43.8887 115.848 60.5856 137.249 60.5856C158.65 60.5856 176 43.8887 176 23.2928C176 2.69692 158.65 -14 137.249 -14C115.848 -14 98.4976 2.69692 98.4976 23.2928Z" fill="#AD84FF" />
                                            <path d="M-10.0011 8.37466C-10.0011 20.7322 0.409554 30.7493 13.2503 30.7493C26.0911 30.7493 36.5 20.7322 36.5 8.37466C36.5 -3.98287 26.0911 -14 13.2503 -14C0.409554 -14 -10.0011 -3.98287 -10.0011 8.37466Z" fill="#AD84FF" />
                                            <path d="M-2.24881 82.9565C-2.24881 87.0757 1.22081 90.4147 5.50108 90.4147C9.78135 90.4147 13.251 87.0757 13.251 82.9565C13.251 78.839 9.78135 75.5 5.50108 75.5C1.22081 75.5 -2.24881 78.839 -2.24881 82.9565Z" fill="#AD84FF" />
                                            <path d="M55.8744 12.1044C55.8744 18.2841 61.0788 23.2926 67.5001 23.2926C73.9196 23.2926 79.124 18.2841 79.124 12.1044C79.124 5.92653 73.9196 0.917969 67.5001 0.917969C61.0788 0.917969 55.8744 5.92653 55.8744 12.1044Z" fill="#AD84FF" />
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                            </div>
                            <!--end::Shape-->
                            <div class="font-weight-boldest font-size-h5">Fee <?= floatval($dataContent['percent_jasa']) ?>%</div>
                            <div class="text-right d-flex flex-column">
                                <span class="font-weight-boldest font-size-h3 line-height-sm"><?= number_format($fee, 0, ',', '.') ?></span>
                            </div>
                        </div>

                    <?php } else {
                        $fee = 0;
                    }

                    $total = $total - $fee ?>
                    <?php
                    if ($dataContent['ppn_pph'] == 1) {
                        $tmp1 = floor($total * 0.10);
                        $total = $total - $tmp1;
                    } else {
                        $tmp1 = 0;
                    }
                    ?>

                    <br>
                    <div class="bg-success rounded d-flex align-items-center justify-content-between text-white max-w-350px position-relative ml-auto p-7">
                        <!--begin::Shape-->
                        <div class="position-absolute opacity-30 top-0 right-0">
                            <span class="svg-icon svg-icon-2x svg-logo-white svg-icon-flip">
                                <!--begin::Svg Icon | path:assets/media/svg/shapes/abstract-8.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" width="176" height="165" viewBox="0 0 176 165" fill="none">
                                    <g clip-path="url(#clip0)">
                                        <path d="M-10.001 135.168C-10.001 151.643 3.87924 165.001 20.9985 165.001C38.1196 165.001 51.998 151.643 51.998 135.168C51.998 118.691 38.1196 105.335 20.9985 105.335C3.87924 105.335 -10.001 118.691 -10.001 135.168Z" fill="#AD84FF" />
                                        <path d="M28.749 64.3117C28.749 78.7296 40.8927 90.4163 55.8745 90.4163C70.8563 90.4163 83 78.7296 83 64.3117C83 49.8954 70.8563 38.207 55.8745 38.207C40.8927 38.207 28.749 49.8954 28.749 64.3117Z" fill="#AD84FF" />
                                        <path d="M82.9996 120.249C82.9996 144.964 103.819 165 129.501 165C155.181 165 176 144.964 176 120.249C176 95.5342 155.181 75.5 129.501 75.5C103.819 75.5 82.9996 95.5342 82.9996 120.249Z" fill="#AD84FF" />
                                        <path d="M98.4976 23.2928C98.4976 43.8887 115.848 60.5856 137.249 60.5856C158.65 60.5856 176 43.8887 176 23.2928C176 2.69692 158.65 -14 137.249 -14C115.848 -14 98.4976 2.69692 98.4976 23.2928Z" fill="#AD84FF" />
                                        <path d="M-10.0011 8.37466C-10.0011 20.7322 0.409554 30.7493 13.2503 30.7493C26.0911 30.7493 36.5 20.7322 36.5 8.37466C36.5 -3.98287 26.0911 -14 13.2503 -14C0.409554 -14 -10.0011 -3.98287 -10.0011 8.37466Z" fill="#AD84FF" />
                                        <path d="M-2.24881 82.9565C-2.24881 87.0757 1.22081 90.4147 5.50108 90.4147C9.78135 90.4147 13.251 87.0757 13.251 82.9565C13.251 78.839 9.78135 75.5 5.50108 75.5C1.22081 75.5 -2.24881 78.839 -2.24881 82.9565Z" fill="#AD84FF" />
                                        <path d="M55.8744 12.1044C55.8744 18.2841 61.0788 23.2926 67.5001 23.2926C73.9196 23.2926 79.124 18.2841 79.124 12.1044C79.124 5.92653 73.9196 0.917969 67.5001 0.917969C61.0788 0.917969 55.8744 5.92653 55.8744 12.1044Z" fill="#AD84FF" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </div>
                        <!--end::Shape-->
                        <div class="font-weight-boldest font-size-h5">TOTAL</div>
                        <div class="text-right d-flex flex-column">
                            <span class="font-weight-boldest font-size-h3 line-height-sm"><?= number_format(($total), 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Pembayaran footer-->
        </div>
    </div>
    <!-- begin: Pembayaran action-->

    <!-- end: Pembayaran action-->
</div>
<div class="card card-custom">
    <div class="card-body">
        <div class="col-xs-12">
            <div class="box" id="print-section">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Data Jurnal </h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-hover table-checkable mt-10" id="FDataTable">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nominal</th>
                                    <th>Nama Agent </th>
                                    <th>Jurnal </th>
                                    <th>Aksi</th>
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

<div class="modal fade" id="pelunasan_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form opd="form" id="pelunasan_form" onsubmit="return false;" type="multipart" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- <div class="row"> -->
                    <div class="form-group">
                        <input name="id" id="id" value="" type="hidden" />
                        <input name="parent_id" id="parent_id" value="<?= $dataContent['id'] ?>" type="hidden" required />
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="date_pembayaran" id="date_pembayaran" required />
                    </div>
                    <div class="row">
                        <div class="col-lg-6">

                            <label>Metode</label>
                            <div class="form-group">
                                <select name="payment_metode" id="payment_metode" class="form-control input-lg">
                                    <?php
                                    foreach ($ref_account as $ji) {
                                        echo '<option value="' . $ji['ref_id'] . '">' . $ji['ref_text'] . '</option>';
                                    } ?>
                                    <!-- <option value="2" selected> Transfer Mandiri A (112-0098146017) </option> -->
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label>Nominal</label>
                                <input type="text" class="form-control mask" name="nominal" id="nominal" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="col-lg-12">
                                <a class="btn btn-light my-1 mr-sm-2" id="btn_add_potongan"><strong>Tambahkan Potongan</strong></a>
                            </div>
                            <div class="col-lg-12" id="freame_potongan"> </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="col-lg-12">
                                <a class="btn btn-light my-1 mr-sm-2" id="btn_add_lebih"><strong>Tambahkan Lebih</strong></a>
                            </div>
                            <div class="col-lg-12" id="freame_lebih"> </div>
                        </div>
                    </div>

                    <!-- </div> -->
                    <!-- </div> -->
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
    $('#menu_id_32').addClass('menu-item-active menu-item-open menu-item-here"');
    $('#submenu_id_87').addClass('menu-item-active');
    $(document).ready(function() {
        var add_pelunasan = $('#add_pelunasan');
        var btn_print_kwitansi = $('#btn_print_kwitansi');
        var btn_print_dokumen = $('#btn_print_dokumen');
        var freame_potongan = $('#freame_potongan');
        var btn_add_potongan = $('#btn_add_potongan');
        var freame_lebih = $('#freame_lebih');
        var btn_add_lebih = $('#btn_add_lebih');

        var row_num = 1;
        var row_num_lebih = 1;

        function add_row(dat = false) {


            var layout_potongan = `
                            <hr>
                            <h2>Potongan ke ${row_num}</h2>
                            <div class="row" id="row_pelunasan_${row_num}">
                            <div class="form-group col-lg-6">
                             <label> Akun Potongan </label>
                             <select name="ac_potongan[]" id="ac_potongan_${row_num}" ${dat ? 'value="'+dat['ac_potongan']+'"' : ''} class="form-control select2">
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
                         </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                <label>Nominal Potongan</label>
                                <input type="text" class="form-control mask" name="ac_nominal[]" id="ac_nominal_${row_num}" ${dat ? 'value="'+dat['ac_nominal']+'"' : ''}  />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                            <label>Keterangn Potongan</label>
                            <input type="text" class="form-control" name="ac_desk[]" id="ac_desk_${row_num}"  ${dat ? 'value="'+dat['ac_desk']+'"' : ''} />
                        </div>
                        </div>
                         <div class="col-lg-6">
                            <div class="form-group">
                            <label>Nomor Potongan</label>
                            <input type="text" class="form-control" name="no_bukti[]" id="no_bukti_${row_num}" ${dat ? 'value="'+dat['no_bukti']+'"' : ''}  />
                        </div>
                        </div>
                     </div>
                </div>
                    `;
            freame_potongan.append(layout_potongan);
            // freame_potongan.select2();
            $('#ac_potongan_' + row_num).select2()
            $('.mask').mask('000.000.000.000.000,00', {
                reverse: true
            });
            if (dat) {
                $('#ac_potongan_' + row_num).val(dat['ac_potongan']);
                $('#ac_potongan_' + row_num).trigger('change');
            }
            row_num++;
        }

        function add_row_lebih_bayar(dat = false) {


            var layout_lebih = `
                            <hr>
                            <h2>Lebih ke ${row_num_lebih}</h2>
                            <div class="row" id="row_lebih_${row_num_lebih}">
                            <div class="form-group col-lg-6">
                             <label> Akun Lebih </label>
                             <select name="ac_lebih[]" id="ac_lebih_${row_num_lebih}" ${dat ? 'value="'+dat['ac_lebih']+'"' : ''} class="form-control select2">
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
                         </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                <label>Nominal Lebih</label>
                                <input type="text" class="form-control mask" name="ac_nominal_lebih[]" id="ac_nominal_lebih_${row_num_lebih}" ${dat ? 'value="'+dat['ac_nominal_lebih']+'"' : ''}  />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                            <label>Keterangn Lebih</label>
                            <input type="text" class="form-control" name="ac_desk_lebih[]" id="ac_desk_lebih_${row_num_lebih}"  ${dat ? 'value="'+dat['ac_desk_lebih']+'"' : ''} />
                        </div>
                        </div>
                         <div class="col-lg-6">
                            <div class="form-group">
                            <label>Nomor Lebih</label>
                            <input type="text" class="form-control" name="no_bukti_lebih[]" id="no_bukti_lebih${row_num_lebih}" ${dat ? 'value="'+dat['no_bukti_lebih']+'"' : ''}  />
                        </div>
                        </div>
                     </div>
                </div>
                    `;
            freame_lebih.append(layout_lebih);
            // freame_lebih.select2();
            $('#ac_lebih_' + row_num_lebih).select2()
            $('.mask').mask('000.000.000.000.000,00', {
                reverse: true
            });
            if (dat) {
                $('#ac_lebih_' + row_num_lebih).val(dat['ac_lebih']);
                $('#ac_lebih_' + row_num_lebih).trigger('change');
            }
            row_num_lebih++;
        }

        btn_add_potongan.on('click', () => {
            add_row()
        })

        btn_add_lebih.on('click', () => {
            add_row_lebih_bayar()
        })
        var dataPayments = [];
        var PelunasanModal = {
            'self': $('#pelunasan_modal'),
            'info': $('#pelunasan_modal').find('.info'),
            'form': $('#pelunasan_modal').find('#pelunasan_form'),
            'addBtn': $('#pelunasan_modal').find('#add_btn'),
            'saveEditBtn': $('#pelunasan_modal').find('#save_edit_btn'),
            'id': $('#pelunasan_modal').find('#id'),
            'parent_id': $('#pelunasan_modal').find('#parent_id'),
            'date_pembayaran': $('#pelunasan_modal').find('#date_pembayaran'),
            'nominal': $('#pelunasan_modal').find('#nominal'),
        }

        var FDataTable = $('#FDataTable').DataTable({
            deferRender: false,
            order: false

        });


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

        add_pelunasan.on('click', (e) => {
            PelunasanModal.form.trigger('reset');
            PelunasanModal.self.modal('show');
            PelunasanModal.addBtn.show();
            PelunasanModal.saveEditBtn.hide();
            form_reset()
        });

        function form_reset() {
            freame_potongan.html('');
            freame_lebih.html('');
            PelunasanModal.id.val('');
            PelunasanModal.nominal.val('');
            PelunasanModal.date_pembayaran.val('<?= date('Y-m-d') ?>');
        }
        getAllPelunsan()

        function getAllPelunsan() {
            swal.fire({
                title: 'Loading Pembayaran...',
                allowOutsideClick: false
            });
            swal.showLoading();
            return $.ajax({
                url: `<?php echo base_url('General/getAllPelunasanPembayaran') ?>`,
                'type': 'GET',
                data: {
                    'parent_id': '<?= $dataContent['id'] ?>',
                    'by_id': true,
                    'get_potongan': true,
                    'get_lebih': true
                },
                success: function(data) {
                    swal.close();
                    var json = JSON.parse(data);
                    if (json['error']) {
                        return;
                    }
                    dataPayments = json['data'];
                    renderPayments(dataPayments);
                },
                error: function(e) {}
            });
        }


        function renderPayments(data) {
            if (data == null || typeof data != "object") {
                return;
            }
            var i = 0;

            var renderData = [];
            <?php if (!empty($dataContent['general_id_ppn'])) { ?>
                link = '<a href="<?= base_url() ?>accounting/show_journal/<?= $dataContent['general_id_ppn'] ?>"> PPN <?= $dataContent['ref_number_ppn'] ?> </a>'
                renderData.push(['<?= $dataContent['date'] ?>', '<?= number_format(0, 2, ',', '.') ?>', '<?= $dataContent['acc_0'] ?>', link, '']);
            <?php   } ?>
            link = '<a href="<?= base_url() ?>accounting/show_journal/<?= $dataContent['general_id'] ?>"> <?= $dataContent['ref_number'] ?> </a>'
            renderData.push(['<?= $dataContent['date'] ?>', '<?= number_format(0, 2, ',', '.') ?>', '<?= $dataContent['acc_0'] ?>', link, '']);
            total = 0;
            Object.values(data).forEach((d) => {
                link = `<a href="<?= base_url() ?>accounting/show_journal/${d['general_id']}"> ${d['ref_number']}</a>`;
                var editButton = `
                <button type="button" class="edit btn btn-primary  btn-icon" data-id='${d['id']}' title="Edit"><i class='la la-pencil-alt'></i></button>
                `;
                var deleteButton = `
                <button  type="button" class="delete btn btn-danger btn-icon" data-id='${d['id']}' title="Delete"><i class='la la-trash'></i></button>
                `;
                var printButton = `
                <button  type="button" class="print btn btn-light btn-icon" data-id='${d['id']}'   title="Print"><i class='la la-print'></i></button>
                `;

                // <button type="button" class="btn btn-outline-secondary btn-icon"><i class="la la-file-text-o"></i></button>
                // <button type="button" class="btn btn-outline-secondary btn-icon"><i class="la la-bold"></i></button>
                // <button type="button" class="btn btn-outline-secondary btn-icon"><i class="la la-paperclip"></i></button>

                var button = `   <div class="btn-group mr-2" role="group" aria-label="...">  ${ printButton+ editButton + deleteButton  }    </div> `;
                renderData.push([d['date_pembayaran'], formatRupiah2(d['sum_child']), d['agentname'], link, button]);
                total = parseFloat(total) + parseFloat(d['sum_child']);
                // console.log(d['nominal'])
            });
            renderData.push(['<b>Total Dibayarkan</b>', '<b> Rp. ' + formatRupiah2(total) + '</b>', '', '', '']);
            FDataTable.clear().rows.add(renderData).draw('full-hold');
        }

        function formatRupiah2(angka, prefix) {
            var number_string = angka.toString();
            expl = number_string.split(".", 2);
            // console.log("ex");
            if (expl[1] == undefined) {
                expl[1] = "00";
            } else {
                if (expl[1].length == 1) expl[1] = expl[1] + "0";
                else expl[1] = expl[1].slice(0, 2);
            }

            sisa = expl[0].length % 3;
            (rupiah = expl[0].substr(0, sisa)),
            (ribuan = expl[0].substr(sisa).match(/\d{3}/gi));

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = expl[1] != undefined ? rupiah + "," + expl[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
        }


        FDataTable.on('click', '.edit', function() {
            form_reset();
            PelunasanModal.form.trigger('reset');
            PelunasanModal.self.modal('show');
            PelunasanModal.addBtn.hide();
            PelunasanModal.saveEditBtn.show();
            var currentData = dataPayments[$(this).data('id')];
            console.log($(this).data('id'))
            console.log(currentData);
            PelunasanModal.id.val(currentData['id']);
            PelunasanModal.date_pembayaran.val(currentData['date_pembayaran']);
            PelunasanModal.nominal.val(formatRupiah2(currentData['nominal']));
            currentData['data_potongan'].forEach((child) => {
                add_row(child);
            })
            currentData['data_lebih'].forEach((child) => {
                add_row_lebih_bayar(child);
            })
            // tmp = $('')
            // currentData['data_potongan'].forEach((child) => {
            //     console.log(child)
            //     add_row(child);
            // })
        })

        FDataTable.on('click', '.print', function() {
            var currentData = dataPayments[$(this).data('id')];
            print_kwitansi(currentData['nominal'], currentData['date_pembayaran'], '')
        })


        function print_kwitansi(nominal, date, item) {
            // getss = `to=<?= !empty($dataContent['name_acc_1']) ? $dataContent['name_acc_1'] : 'PT Indometal Asia' ?>&from=<?= !empty($customer_data[0]['customer_name']) ? $customer_data[0]['customer_name']  : '' ?>&date=${date}&nominal=${nominal}&description=<?= $dataContent['description'] ?>${item}`;
            // url = "<?= base_url('pembayaran/kwitansi_print/' . $dataContent['id']) ?>?" + getss;
            // window.open(url, "_blank");
        };

        btn_print_kwitansi.on('click', () => {
            url = "<?= base_url('pembayaran/kwitansi_print/' . $dataContent['id']) ?>?";
            window.open(url, "_blank");
        });

        btn_print_dokumen.on('click', () => {
            url = "<?= base_url('pembayaran/print/' . $dataContent['id']) ?>";
            window.open(url, "_blank");
        });

        PelunasanModal.form.submit(function(event) {
            event.preventDefault();
            var isAdd = PelunasanModal.addBtn.is(':visible');
            var url = "<?= base_url('pembayaran/') ?>";
            url += isAdd ? "addPelunasan" : "editPelunasan";
            var button = isAdd ? PelunasanModal.addBtn : PelunasanModal.saveEditBtn;

            Swal.fire(swalSaveConfigure).then((result) => {
                if (result.isConfirmed == false) {
                    return;
                }
                swal.fire({
                    title: 'Loading ...',
                    allowOutsideClick: false
                });
                swal.showLoading();
                $.ajax({
                    url: url,
                    'type': 'POST',
                    data: new FormData(PelunasanModal.form[0]),
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var json = JSON.parse(data);
                        if (json['error']) {
                            swal.fire("Simpan Gagal", json['message'], "error");
                            return;
                        }
                        swal.fire("Simpan Berhasil", "", "success");

                        location.reload();
                        //  return;
                        // var d = json['data']
                        // dataPayments[d['id']] = d;
                        // renderPayments(dataPayments);
                        // PelunasanModal.self.modal('hide');
                    },
                    error: function(e) {}
                });
            });
        });

        FDataTable.on('click', '.delete', function() {
            var currentData = $(this).data('id');
            Swal.fire(swalDeleteConfigure).then((result) => {
                if (result.isConfirmed == false) {
                    return;
                }
                $.ajax({
                    url: "<?= base_url('pembayaran/deletePelunasan') ?>",
                    'type': 'post',
                    data: {
                        'id': currentData
                    },

                    success: function(data) {
                        var json = JSON.parse(data);
                        if (json['error']) {
                            swal("Delete Gagal", json['message'], "error");
                            return;
                        }
                        //  return;
                        swal.fire("Delete Berhasil", "", "success");
                        location.reload();

                        // renderPayments(dataPayments);
                        // PaymentModal.self.modal('hide');
                    },
                    error: function(e) {}
                });
            });

        })

        $('.mask').mask('000.000.000.000.000,00', {
            reverse: true
        });
    });
</script>