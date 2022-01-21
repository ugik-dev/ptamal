<html>

<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="style.css">
    <link rel="license" href="https://www.opensource.org/licenses/mit-license/">
    <script src="script.js"></script>
</head>
<style>
    /* reset */

    * {
        border: 0;
        box-sizing: content-box;
        color: inherit;
        font-family: inherit;
        font-size: inherit;
        font-style: inherit;
        font-weight: inherit;
        line-height: inherit;
        list-style: none;
        margin: 0;
        padding: 0;
        text-decoration: none;
        vertical-align: top;
    }

    /* content editable */


    img.hover {
        background: #DEF;
        box-shadow: 0 0 1em 0.5em #DEF;
    }


    h1 {
        font: bold 100% sans-serif;
        letter-spacing: 0.5em;
        text-align: center;
        text-transform: uppercase;
    }

    /* table */

    table {
        font-size: 75%;
        table-layout: fixed;
        width: 100%;
    }

    table {
        border-collapse: separate;
        border-spacing: 2px;
    }

    th,
    td {
        border-width: 1px;
        padding: 0.5em;
        position: relative;
        text-align: left;
    }

    th,
    td {
        border-radius: 0.25em;
        /* border-style: solid; */
    }

    th {
        background: #FFF;
        border-color: #BBB;
    }

    td {
        border-color: #DDD;
    }

    /* page */

    html {
        font: 16px/1 'Open Sans', sans-serif;
        overflow: auto;
        padding: 0.5in;
    }

    html {
        background: #999;
        cursor: default;
    }

    body {
        box-sizing: border-box;
        height: 11in;
        margin: 0 auto;
        overflow: hidden;
        padding: 0.5in;
        width: 8.5in;
    }

    body {
        background: #FFF;
        border-radius: 1px;
        box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
    }

    /* header */

    header {
        margin: 0 0 3em;
    }

    header:after {
        clear: both;
        content: "";
        display: table;
    }

    header h1 {
        background: #0a4f10;
        border-radius: 0.25em;
        color: #FFF;
        margin: 0 0 1em;
        padding: 0.5em 0;
    }

    header address {
        float: left;
        font-size: 90%;
        font-style: normal;
        line-height: 1.25;
        margin: 0 1em 1em 0;
    }

    header address p {
        margin: 0 0 0.25em;
    }

    header span,
    header img {
        display: block;
        float: right;
    }

    header span {
        margin: 0 0 1em 1em;
        max-height: 25%;
        max-width: 60%;
        position: relative;
    }

    header img {
        max-height: 50%;
        max-width: 70%;
    }

    header input {
        cursor: pointer;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
        height: 100%;
        left: 0;
        opacity: 0;
        position: absolute;
        top: 0;
        width: 100%;
    }

    /* article */

    article,
    article address,
    table.meta,
    table.inventory {
        margin: 0 0 3em;
    }

    article::before {
        /* clear: both; */
        content: "";
        display: table;
    }

    article h1 {
        clip: rect(0 0 0 0);
        position: absolute;
    }

    article address {
        float: left;
        font-size: 90%;
        font-weight: bold;
    }

    .footer-text {
        /* float: left; */
        font-size: 90%;
        /* font-weight: bold; */
    }

    /* table meta & balance */

    table.meta,
    table.ttd,
    table.balance {
        float: right;
        width: 36%;
    }

    table.meta:after,
    table.balance:after {
        clear: both;
        content: "";
        display: table;
    }

    /* table meta */

    table.meta th {
        width: 50px;
        text-align: right;
    }

    table.meta td {
        text-align: right;
        width: 80%;
    }

    /* table items */

    table.inventory {
        clear: both;
        width: 100%;
        /* border: 1; */
        /* border-collapse: collapse; */
        /* border: 1px solid black; */
    }

    table.inventory th {
        font-weight: bold;
        text-align: center;
        border-top: 2px solid #0a4f10;
        /* border: solid 1px black; */
    }

    table.inventory tr {
        /* background-color: red;
        border-top: 10px !important;
        border-top-color: blue !important; */
    }

    .top-gey td {
        /* font-weight: bold; */
        /* border-left-color: red !important; */
        /* text-align: center !important; */
        border-top: 1px solid #2c9c35 !important;
        /* border-radius: 0; */
        /* margin: 0; */
        /* background-color: #575757; */
        /* border-top: 10px solid green !important; */
        /* border: solid 1px black; */
    }



    table.inventory .amount {
        text-align: right;
        width: 12%;
    }

    table.inventory .qyt {
        text-align: center;
        width: 30px;
    }

    table.inventory td:nth-child(4) {
        text-align: right;
        width: 12%;
    }

    table.inventory td:nth-child(5) {
        text-align: right;
        width: 12%;
    }

    /* table balance */

    table.balance th,
    table.balance td {
        width: 40%;
    }

    table.ttd td {
        text-align: center;
        width: 100%;
    }

    table.balance td {
        text-align: right;
    }

    table.bank-sheet {
        width: 60%;
    }

    table.bank-sheet .dash {
        width: 10px;
    }

    table.bank-sheet .info {
        width: 80px;
    }

    table.bank-sheet .item-bank {
        width: 200px;
    }

    .terbilang {
        font-size: 120%;
        font-weight: bold;
    }

    /* aside */

    aside h1 {
        border: none;
        border-width: 0 0 1px;
        margin: 0 0 1em;
    }

    aside h1 {
        border-color: #999;
        border-bottom-style: solid;
    }

    /* javascript */

    .add,
    .cut {
        border-width: 1px;
        display: block;
        font-size: .8rem;
        padding: 0.25em 0.5em;
        float: left;
        text-align: center;
        width: 0.6em;
    }

    .add,
    .cut {
        background: #9AF;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
        background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
        border-radius: 0.5em;
        border-color: #0076A3;
        color: #FFF;
        cursor: pointer;
        font-weight: bold;
        text-shadow: 0 -1px 2px rgba(0, 0, 0, 0.333);
    }

    .add {
        margin: -2.5em 0 0;
    }

    .add:hover {
        background: #00ADEE;
    }

    .cut {
        opacity: 0;
        position: absolute;
        top: 0;
        left: -1.5em;
    }

    .cut {
        -webkit-transition: opacity 100ms ease-in;
    }

    tr:hover .cut {
        opacity: 1;
    }

    @media print {
        * {
            -webkit-print-color-adjust: exact;
        }

        html {
            background: none;
            padding: 0;
        }

        body {
            box-shadow: none;
            margin: 0;
        }

        span:empty {
            display: none;
        }

        .add,
        .cut {
            display: none;
        }
    }

    @page {
        margin: 0;
    }
</style>
<?php $company = Company_Profile() ?>

<body>
    <header>
        <h1>Invoice</h1>
        <span><img alt="" src="<?= base_url('assets/img/') . $company['slider1'] ?>"></span>
        <address>
            <p>To : </p>
            <p><?= $transaction['customer_name'] ?></p>
            <p><?= $transaction['cus_address'] ?></p>
            <p><?= $transaction['cus_town'] ?></p>
        </address>
    </header>
    <article>
        <table class="meta">
            <tr>
                <th><span>Invoice #</span></th>
                <td><span><?= $transaction['no_invoice'] ?></span></td>
            </tr>
            <tr>
                <th><span>Date</span></th>
                <td><span><?= $transaction['date'] ?></span></td>
            </tr>
        </table>
        <table style="width: 60% ; font-size : 95%">
            <tr>
                <th><?= $transaction['description'] ?></th>
            </tr>
        </table>
        <?php
        $keterangan_item = false;
        $date_item = false;
        $satuan = false;
        $qyt = false;

        foreach ($transaction['items'] as $cek) {
            if (!empty($cek['keterangan_item']))
                $keterangan_item = true;
            if (!empty($cek['date_item']))
                $date_item = true;
        } ?>
        <table class="inventory">
            <thead>
                <tr>
                    <?= $keterangan_item ? '<th><span >Item #</span></th>' : '' ?>
                    <?= $date_item ? '<th><span >Date #</span></th>' : '' ?>
                    <!-- <th><span >Description</span></th> -->
                    <th><span>Rate #</span></th>
                    <th style="width: 80px !important"><span>Quantity #</span></th>
                    <th><span>Price #</span></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($transaction['items'] as $cek) {
                    echo '<tr class="top-gey">';
                    if ($keterangan_item)
                        echo '    <td><span >' . $cek['keterangan_item'] . '</span></td>';
                    if ($date_item)
                        echo '    <td><span >' . $cek['date_item'] . '</span></td>';
                    echo '    <td class="amount"><span >' . number_format($cek['amount'], 2, ',', '.') . '</span></td>
                           <td class="qyt"><span >' . $cek['qyt'] . ' ' . $cek['satuan'] . '</span></td>
                           <td class="amount"><span >' . number_format(($cek['qyt'] * $cek['amount']), 2, ',', '.') . '</span></td>';
                    echo '</tr>';
                } ?>
                <tr style="padding : 0px ; margin: 0px">
                    <?= $keterangan_item ? '<th></th>' : '' ?>
                    <?= $date_item ? '<th></th>' : '' ?>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tbody>
        </table>
        <table class="balance">
            <tr>
                <th><span>SUB TOTAL</span></th>
                <td><span>Rp </span><span><?= number_format($transaction['sub_total'], 2, ',', '.') ?></span></td>
            </tr>
            <?php if ($transaction['ppn_pph'] == 1) { ?>
                <tr>
                    <th><span>PPN 10%</span></th>
                    <td><span>Rp </span><span><?= number_format(10 / 100 * $transaction['sub_total'], 2, ',', '.') ?></span></td>
                </tr>
            <?php } ?>
            <tr>
                <th><span>TOTAL FINAL</span></th>
                <td><span>Rp </span><span><?= number_format($transaction['total_final'], 2, ',', '.') ?></span></td>
            </tr>
        </table>
        <table style="width : 60%">
            <tr>
                <th style="width : 50px">Terbilang</th>
                <th style="width : 1px"><span>:</span></th>
                <td class="terbilang"><b><?= $terbilang ?></b></td>
            </tr>
        </table>
    </article>
    <aside>
        <h1><span></span></h1>
        <div>
            <?php if (!empty($payment['bank_name'])) {
            ?>
                <h2 class="footer-text">MAKE ALL CHECKS PAYBLE TO</h2>
                <table class="bank-sheet">
                    <!-- <tr>
                        <td colspan="3">BANK </td>
                    </tr> -->
                    <tr>
                        <th class="info">BANK </th>
                        <th class="dash">: </th>
                        <td class="item-bank"> <?= $payment['bank_name'] ?></td>
                    </tr>
                    <tr>
                        <th class="info">Kantor Kacabang </th>
                        <th class="dash">: </th>
                        <td class="item-bank"> <?= $payment['branch'] ?></td>
                    </tr>
                    <tr>
                        <th class="info">Account Name </th>
                        <th class="dash">: </th>
                        <td class="item-bank"> <?= $payment['title_bank'] ?></td>
                    </tr>
                    <tr>
                        <th class="info">Account Number </th>
                        <th class="dash">: </th>
                        <td class="item-bank"> <?= $payment['bank_number'] ?></td>
                    </tr>
                </table>
            <?php    } ?>
            <br>
            <h2 class="footer-text">NPWP DETAILS</h2>
            <table class="bank-sheet">
                <!-- <tr>
                    <th colspan="3" class="">NPWP DETAILS </th>
                </tr> -->
                <tr>
                    <th class="info">Name </th>
                    <th class="dash">: </th>
                    <td class="item-bank"> <?= $company['npwp_name'] ?></td>
                </tr>
                <tr>
                    <th class="info">Number </th>
                    <th class="dash">: </th>
                    <td class="item-bank"> <?= $company['npwp_number'] ?></td>
                </tr>
            </table>

            <table class="ttd">
                <tr>
                    <td class="info"><?= $company['companyname'] ?> </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td class="item-bank"> <?= $transaction['acc_1_name'] ?></td>
                </tr>
            </table>
        </div>
    </aside>
    <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            window.print();
            var beforePrint = function() {};
            var afterPrint = function() {
                window.close();
                console.log('re')
            };

            if (window.matchMedia) {
                var mediaQueryList = window.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (mql.matches) {
                        beforePrint();
                    } else {
                        afterPrint();
                    }
                });
            }

            window.onbeforeprint = beforePrint;
            window.onafterprint = afterPrint;
        });
    </script>
</body>

</html>