<script type="text/javascript">
    var timmer;
    var dataProducts = [];
    var row_id = 1;
    var total_gross_amt = $('#total_gross_amt');
    var total_tax_ppn_amt = $('#total_tax_ppn_amt');
    var net_total_amount = $('#net_total_amount');
    var discountfield = $('#discountfield');
    var amount_recieved = $('#amount_recieved');
    var amount_back = $('#amount_back');
    var pos_form = $('#pos_form');
    var btn_create_invoice = $('#btn_create_invoice');
    var btn_create_bill = $('#btn_create_bill');
    var percent_ppn = $('#percent_ppn');
    var percent_pph = $('#percent_pph');
    var percent_fee = $('#percent_fee');
    var am_pph = $('#am_pph');
    var am_ppn = $('#am_ppn');
    var am_fee = $('#am_fee');
    var manual_math = $('#manual_math');
    var manual = false;

    btn_create_bill.on('click', function() {
        submit_form('addBill');
    })


    btn_create_invoice.on('click', function() {
        submit_form('addInvoice');
    })
    var swalSaveConfigure = {
        title: "Konfirmasi simpan",
        text: "Yakin akan menyimpan data ini?",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#18a689",
        confirmButtonText: "Ya, Simpan!",
        reverseButtons: true
    };

    function submit_form(action) {
        event.preventDefault();
        var url = "<?= base_url('production/') ?>" + action;
        Swal.fire(swalSaveConfigure).then((result) => {
            if (result.dismiss === "cancel") {
                return;
            }
            $.ajax({
                url: url,
                'type': 'POST',
                data: new FormData(pos_form[0]),
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
                    // dataProducts[d['id']] = d;
                    swal.fire("Success", 'Invoice berhasil dibuat', "success");

                    // swal.fire(swalSuccessConfigure);
                    window.location.href = '<?= base_url() ?>production/transaction/' + d;
                    // renderProducts(dataProducts);
                    // ProductModal.self.modal('hide');
                },
                error: function(e) {}
            });
        });
    }

    $(document).ready(function() {
        $('.mask2').mask('000.000.000.000.000,00', {
            reverse: true
        });
        $('.mask').mask('000.000.000.000.000', {
            reverse: true
        });

        manual_math.on('change', function() {
            console.log('change')
            manual = manual_math.is(':checked');
            if (manual) {
                am_fee.prop('readonly', false);
                am_pph.prop('readonly', false);
                am_ppn.prop('readonly', false);
                // am_fee.mask('000.000.000.000.000,00', {
                //     reverse: true
                // });
            } else {
                am_fee.prop('readonly', 'readonly');
                am_pph.prop('readonly', 'readonly');
                am_ppn.prop('readonly', 'readonly');

            }
            invoice_count();
        })
        getAllProduct()

        function getAllProduct() {
            Swal.fire({
                title: 'Loading ...',
                allowOutsideClick: false,
                onOpen: function() {
                    Swal.showLoading()
                }
            });
            return $.ajax({
                url: `<?php echo base_url('Production/getAllProduct?by_id=true') ?>`,
                'type': 'GET',
                data: {},
                success: function(data) {
                    swal.close();
                    var json = JSON.parse(data);
                    if (json['error']) {
                        return;
                    }
                    dataProducts = json['data'];
                    // renderProducts(dataProducts);
                },
                error: function(e) {}
            });
        }

    });
    //USED TO ADD ITEM IN TEMP TABLE
    function add_item_invoice(data) {
        if (/^[a-zA-Z0-9- ]*$/.test(data) == true) {
            //CHECK WEATHER THE VALUE IS IS NUMBERIC OR NOT
            var chk = $.isNumeric(data);
            if (chk && data.length < 16) {

                if (data != null) {
                    clearTimeout(timmer);
                    timmer = setTimeout(function callback() {

                        //get_search_result(search_item);
                        // SHOW AJAX RESPONSE ON REQUEST SUCCESS
                        $.ajax({
                            url: '<?php echo base_url('invoice/add_barcode_item/'); ?>' + data,
                            success: function(response) {
                                jQuery('#inner_invoice_area').html(response);
                                $('#barcode_scan_area').val('');
                                $('#barcode_scan_area').focus();
                                $('.search_result').css("display", "none");
                            }
                        });

                    }, 100);
                }

            } else {

                clearTimeout(timmer);
                timmer = setTimeout(function callback() {
                    get_search_result(data);
                }, 100);

                $('#barcode_scan_area').focus();
            }

        }
    }

    //USED TO CLEAR THE TEMP TABLE
    function clear_invoice() {

        // SHOW AJAX RESPONSE ON REQUEST SUCCESS
        $.ajax({
            url: '<?php echo base_url("invoice/clear_temp_invoice"); ?>',
            success: function(response) {
                jQuery('#inner_invoice_area').html(response);
                $('#barcode_scan_area').val('');
                $('#barcode_scan_area').focus();
            }
        });
    }

    //USED TO GET SEARCH RESULT
    function get_search_result(search_item) {
        if (search_item != '') {
            // alert('ree');
            // SHOW AJAX RESPONSE ON REQUEST SUCCESS
            $.ajax({
                url: '<?php echo base_url("General/product_search/"); ?>' + search_item,
                success: function(response) {
                    jQuery('#search_id_result_manual').html(response);
                    //$('#barcode_scan_area').val('');
                }
            });
        } else {
            $('.search_result').css("display", "none");
        }
    }

    //USED TO CLOSE OR HIDE THE SEARCH DIV
    function close_search_result() {
        $('#barcode_scan_area').val('');
        $('#barcode_scan_area').focus();
        $('.search_result').css("display", "none");
    }


    //USED TO ADD ITEM SEARCHED IN TEMP TABLE

    function add_search_item_invoice(id) {
        console.log(dataProducts[id])
        $('#barcode_scan_area').focus();
        $('.search_result').css("display", "none");
        row_data = `<tr id="row_${row_id}">
        <td> ${dataProducts[id]['product_name']} </td>
        <td> ${dataProducts[id]['name_unit']} </td>
        <td>
         <input hidden id="product_name[]" name="product_name[]" value="${dataProducts[id]['product_name']}" >
         <input hidden id="fix_tax_ppn[]" name="fix_tax_ppn[]" >
         <input hidden id="revenue_account[]" name="revenue_account[]" value="${dataProducts[id]['revenue_account']}" >
         <input hidden id="item_id[]" name="item_id[]" value="${dataProducts[id]['id']}" >
         <input hidden id="tax_ppn[]" name="tax_ppn[]" value="${dataProducts[id]['tax_ppn']}" >
         <input onkeyup="invoice_count()" class="mask" id="row_price[]" name="row_price[]" value="${parseInt( dataProducts[id]['default_price']).toFixed()}" >
          </td>
        <td> <input onkeyup="invoice_count()" type="number" id="row_qyt[]" name="row_qyt[]" class="supply_fields" value="1"> </td>
        <td> <a onclick="deleteRow('row_${row_id}')"><i class="flaticon2-trash text-primary"></i>
            </a> </td>
        </tr>`
        jQuery('#row_items').append(row_data);
        $('.mask').mask('000.000.000.000.000', {
            reverse: true
        });

        invoice_count()
        $('#barcode_scan_area').val('');
        row_id++;
        // SHOW AJAX RESPONSE ON REQUEST SUCCESS
        // $.ajax({
        //     url: '<?php echo base_url('General/getAllProduct/'); ?>' + id,
        //     success: function(response) {
        //         var json = JSON.parse(data);
        //         if (json['error']) {
        //             return;
        //         }
        //         response
        //     }
        // });
    }

    //KEYBOARD EVENT
    $(document).keyup(function(e) {
        // alert(e.keyCode);
        if (e.keyCode == 27) {
            clear_invoice();
        } else if (e.keyCode == 113) {
            window.location = '<?php echo base_url('invoice/manage') ?>';
        } else if (e.keyCode == 115) {
            window.location = '<?php echo base_url('invoice/manage') ?>';
        } else if (e.keyCode == 13) {
            jQuery('.invoice').focus();
            $('#submit_btn').submit();
        }
    });

    //USED TO FIND THE PREVIOUS BALANCES OF THE CUSTOMER 
    function search_customer_payments(cus_id) {
        $('#barcode_scan_area').focus();
        // SHOW AJAX RESPONSE ON REQUEST SUCCESS
        $.ajax({
            url: '<?php echo base_url('invoice/search_previous_cus_balance/'); ?>' + cus_id,
            success: function(response) {
                jQuery('#privious_balance').val(response);
            }
        });
    }

    //USE TO CHANGE THE QUANTITY
    function amend_qty(val, item_id) {
        clearTimeout(timmer);
        timmer = setTimeout(function callback() {
            //get_search_result(search_item);
            var int_val = parseInt(val);
            // SHOW AJAX RESPONSE ON REQUEST SUCCESS
            $.ajax({
                url: '<?php echo base_url('invoice/update_qty/'); ?>' + int_val + '/' + item_id,
                success: function(response) {
                    jQuery('#inner_invoice_area').html(response);
                    $('#barcode_scan_area').val('');
                    $('#barcode_scan_area').focus();
                    $('.search_result').css("display", "none");
                }
            });
        }, 400);
    }

    //USE TO CHANGE THE QUANTITY
    function amend_price(val, item_id) {
        clearTimeout(timmer);
        timmer = setTimeout(function callback() {
            //get_search_result(search_item);
            var int_val = parseInt(val);
            // SHOW AJAX RESPONSE ON REQUEST SUCCESS
            $.ajax({
                url: '<?php echo base_url('invoice/update_price/'); ?>' + int_val + '/' + item_id,
                success: function(response) {
                    jQuery('#inner_invoice_area').html(response);
                    $('#barcode_scan_area').val('');
                    $('#barcode_scan_area').focus();
                    $('.search_result').css("display", "none");
                }
            });
        }, 400);
    }

    function invoice_count() {
        var row_price = document.getElementsByName('row_price[]');
        var row_qyt = document.getElementsByName('row_qyt[]');
        var tax_ppn = document.getElementsByName('tax_ppn[]');
        var fix_tax_ppn = document.getElementsByName('fix_tax_ppn[]');

        // console.log(row_price[0].value)
        // foreach(row_price)
        i = 0;
        total_price = 0;
        // total_tax_ppn = 0;
        row_price.forEach(function() {
            cur_price = (row_price[i].value
                .split(".").join("") * row_qyt[i].value)
            cur_tax_ppn = (10 / 100) * cur_price;
            console.log(cur_price)
            console.log(cur_tax_ppn)
            fix_tax_ppn[i].value = cur_tax_ppn;
            total_price = total_price + cur_price;
            // total_tax_ppn = total_tax_ppn + cur_tax_ppn;
            i++;
        });

        // total_tax_ppn = parseFloat(total_tax_ppn).toFixed(2);
        // ar = amount_recieved.val().split(".").join("")
        // disc = discountfield.val().split(".").join("").split(",").join(".");
        // console.log(disc)
        // if (disc > 0) {
        //     disc = disc;
        // } else {
        //     disc = 0;
        // }
        // net = parseInt(total_price);
        console.log(percent_ppn.val())
        if (!manual) {
            if (parseFloat(percent_ppn.val()) > 0) {
                var_am_ppn = parseInt(parseFloat(percent_ppn.val()) / 100 * total_price);
                am_ppn.val(invformatRupiah(var_am_ppn));
            } else {
                am_ppn.val('');
                var_am_ppn = 0
            };


            if (parseFloat(percent_pph.val()) > 0) {
                var_am_pph = parseInt(parseFloat(percent_pph.val()) / 100 * total_price);
                am_pph.val(invformatRupiah(var_am_pph));
            } else {
                am_pph.val('');
                var_am_pph = 0
            };

            if (parseFloat(percent_fee.val()) > 0) {
                var_am_fee = parseInt(parseFloat(percent_fee.val()) / 100 * total_price);
                console.log(var_am_fee);
                am_fee.val(invformatRupiah(var_am_fee));
            } else {
                am_fee.val('');
                var_am_fee = 0
            };
        } else {
            var_am_fee = parseInt(am_fee.val().replace(/[^0-9]/g, ''));
            var_am_ppn = parseInt(am_ppn.val().replace(/[^0-9]/g, ''));
            var_am_pph = parseInt(am_pph.val().replace(/[^0-9]/g, ''));
            if (!(var_am_fee > 0)) var_am_fee = 0;
            if (!(var_am_pph > 0)) var_am_pph = 0;
            if (!(var_am_ppn > 0)) var_am_ppn = 0;
            console.log('par manual : ')
            console.log(var_am_fee)
            console.log(var_am_pph)
            console.log(var_am_ppn)
        }

        net = total_price + var_am_ppn + var_am_pph + var_am_fee;
        console.log(net)
        // console.log('total_tax_ppn = ' + disc);
        total_gross_amt.val(invformatRupiah(total_price));
        // total_tax_ppn_amt.val(invformatRupiah(total_tax_ppn));
        net_total_amount.val(invformatRupiah(net));
        // amount_back.html(ar - net);
    }

    function deleteRow(rowid) {
        var row = document.getElementById(rowid);
        row.parentNode.removeChild(row);
        invoice_count();
    }

    function invformatRupiah(angka, prefix) {
        var number_string = angka.toString();
        split = number_string.split(".");
        sisa = split[0].length % 3;
        rupiah = split[0].substr(0, sisa);
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }
        if (split[1] != undefined) {
            if (split[1].length == 1)
                x = split[1] + '0';
            else
                x = split[1].substr(0, 2);
        }
        rupiah = split[1] != undefined ? rupiah + "," + x : rupiah;
        return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
    }
</script>