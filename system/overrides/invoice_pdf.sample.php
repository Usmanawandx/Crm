<html>

<head>

    <style>

        /*

        PDF library using PHP have some limitations and all CSS properties may not support. Before Editing this file, Please create a backup, so that You can restore this.

        The location of this file is here- system/lib/invoices/pdf-x2.php

        */

        * { margin: 0; padding: 0; }
        body {
            /*

            Important: Do not Edit Font Name, Unless you are sure. It's required for PDF Rendering Properly

            */

        <?php if (isset($creating_pdf)) {
            echo 'font: 14px/1.4  dejavusanscondensed;';
        } else {
            echo 'font: 14px/1.4 Helvetica, Arial, sans-serif;';
        } ?>



            /*

            Font Name End

            */
        }

        #page-wrap { width: 800px; margin: 0 auto; }

        table { border-collapse: collapse; }
        table td, table th { border: 1px solid black; padding: 5px; }


        #customer { overflow: hidden; }

        #logo { text-align: right; float: right; position: relative; margin-top: 25px; border: 1px solid #fff; max-width: 540px; overflow: hidden; }

        #meta { margin-top: 1px; width: 100%; float: right; }
        #meta td { text-align: right;  }
        #meta td.meta-head { text-align: left; background: #eee; }
        #meta td textarea { width: 100%; height: 20px; text-align: right; }

        #items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
        #items th { background: #eee; }
        #items textarea { width: 80px; height: 50px; }
        #items tr.item-row td {  vertical-align: top; }
        #items td.description { width: 300px; }
        #items td.item-name { width: 175px; }
        #items td.description textarea, #items td.item-name textarea { width: 100%; }




        <?php if ($config['rtl'] == 1) { ?>

        #items td.total-line { border-left: 0; text-align: left; }

        <?php } else { ?>

        #items td.total-line { border-right: 0; text-align: right; }
        <?php } ?>


        #items td.total-value { border-left: 0; padding: 10px; }
        #items td.total-value textarea { height: 20px; background: none; }
        #items td.balance { background: #eee; }
        #items td.blank { border: 0; }

        #terms { text-align: left; margin: 20px 0 0 0; }
        #terms h5 { text-transform: uppercase; font-size: 13px; letter-spacing: 10px; border-bottom: 1px solid black; padding: 0 0 8px 0; margin: 0 0 8px 0; }
        #terms textarea { width: 100%; text-align: center;}

        <?php if ($config['rtl'] == 1) { ?>

        html body {
            direction: rtl;
        }
        table td, table th {
            text-align: right;
        }

        <?php } else { ?>
        #items td.blank { border: 0; }
        <?php } ?>

    </style>

</head>

<body style="font-family: helvetica,serif; font-size: 10pt;">

<div id="page-wrap">

    <table width="100%">
        <tr>
            <td style="border: 0;  <?php if ($config['rtl'] == 1) { ?>

                    text-align: right;

            <?php } else { ?>

                    text-align: left;
            <?php } ?>" width="62%">
                <span style="font-size: 18px; color: #2f4f4f"><strong><?php echo $_L[
                    'INVOICE'
                ]; ?> # <?php
 $dispid = $d['cn'] != '' ? $d['cn'] : $d['id'];
 echo $d['invoicenum'] . $dispid;
 ?></strong></span>

                <?php if (
                    !empty($config['invoice_show_qr_code']) &&
                    $config['invoice_show_qr_code'] == '1'
                ) { ?>
                    <br>
                    <img src="<?php echo $qr_code; ?>" style="height: 100px;">
                <?php } ?>


            </td>
            <td style="border: 0;  text-align: right" width="62%"><div id="logo" style="font-size:18px">
                    <img id="image" src="<?php echo APP_URL; ?>/storage/system/<?php echo $config[
    'logo_default'
]; ?>" alt="logo" /> <br> <br>
                    <?php echo $config['CompanyName']; ?> <br>
                    <?php echo $config['caddress']; ?>
                    <?php if (!empty($config['vat_number'])) {
                        echo '<br> <strong>' .
                            sp_config_or_default(
                                'label_tax_number',
                                $_L['Vat number']
                            ) .
                            ':</strong> ' .
                            $config['vat_number'];
                    } ?>
                </div></td>
        </tr>



    </table>

    <hr>
    <div style="clear:both"></div>

    <div id="customer">

        <table id="meta">
            <tr>
                <td rowspan="5" style="border: 1px solid white;

                <?php if ($config['rtl'] == 1) { ?>

                        border-left: 1px solid black; text-align: right

                <?php } else { ?>

                        border-right: 1px solid black; text-align: left
                <?php } ?>

                        " width="62%">

                    <?php if (isset($d['title']) && $d['title'] != '') {
                        echo '<h4>' .
                            $d['title'] .
                            '</h4>
                    <br>';
                    } ?>

                    <?php if (
                        $config['invoice_receipt_number'] == '1' &&
                        $d['receipt_number'] != ''
                    ) {
                        echo '<h4>' .
                            $_L['Receipt Number'] .
                            ': ' .
                            $d['receipt_number'] .
                            '</h4>
                    <br>';
                    } ?>

                    <strong><?php echo $_L['Invoiced To']; ?></strong> <br>

                    <?php if ($a['company'] != '') { ?>
                        <?php echo $a['company']; ?> <br>

                        <?php if (
                            $company &&
                            $config['show_business_number'] == '1' && $company->business_number != ''
                        ) {
                            echo $config['label_business_number'] .
                                ': ' .
                                $company->business_number .
                                ' <br>';
                        } ?>
                        <?php echo $_L['ATTN']; ?>: <?php echo $a[
    'account'
]; ?> <br>
                    <?php } else { ?>
                        <?php echo $d['account']; ?> <br>
                    <?php } ?>

                    <?php echo getContactFormattedAddress($config, $a); ?> <br>
                    <?php
                    if ($a['phone'] != '') {
                        echo $_L['Phone'] . ': ' . $a['phone'] . ' <br>';
                    }

                    if ($a['fax'] != '' && $config['fax_field'] != '0') {
                        echo $_L['Fax'] . ': ' . $a['fax'] . ' <br>';
                    }

                    if ($a['email'] != '') {
                        echo 'Email: ' . $a['email'] . ' <br>';
                    }
                    foreach ($cf as $cfs) {
                        echo $cfs['fieldname'] .
                            ': ' .
                            get_custom_field_value($cfs['id'], $a['id']) .
                            ' <br>';
                    }
                    ?></td>
                <td class="meta-head"><?php echo $_L['INVOICE']; ?> #</td>
                <td><?php echo $d['invoicenum'] . $dispid; ?></td>
            </tr>
            <tr>

                <td class="meta-head"><?php echo $_L['Status']; ?></td>
                <td><?php echo ib_lan_get_line($d['status']); ?></td>
            </tr>
            <tr>

                <td class="meta-head"><?php echo $_L['Invoice Date']; ?></td>
                <td><?php echo date(
                    $config['df'],
                    strtotime($d['date'])
                ); ?></td>
            </tr>
            <tr>

                <td class="meta-head"><?php echo $_L['Due Date']; ?></td>
                <td><?php echo date(
                    $config['df'],
                    strtotime($d['duedate'])
                ); ?></td>
            </tr>

            <?php if ($d['credit'] != '0.00') { ?>

                <tr>

                    <td class="meta-head"><?php echo $_L['Amount Due']; ?></td>
                    <td><div class="due"><?php echo ib_money_format(
                        $i_due,
                        $config,
                        $d['currency_symbol']
                    ); ?></div></td>
                </tr>

            <?php } else { ?>
                <tr>

                    <td class="meta-head"><?php echo $_L['Amount Due']; ?></td>

                    <td><div class="due"><?php echo formatCurrency(
                        $i_due,
                        $d['currency_iso_code']
                    ); ?></div></td>
                </tr>
            <?php } ?>

        </table>

    </div>

    <?php if (isset($extraHtml) && $extraHtml != '') {
        echo $extraHtml;
    } ?>
    <?php if ($quote) { ?>
        <hr>
        <div>

            <h4><?php echo $_L['Quote'] . ': ' . $quote->id; ?></h4>

            <?php echo $quote->proposal; ?>
        </div>
        <hr>
    <?php } ?>

    <table id="items">

        <tr>
            <th><?php echo $_L['Item']; ?></th>

            <?php if ($config['tax_system'] == 'India') {
                $col_span = $d['is_same_state'] == 1 ? 6 : 5; ?>

                <th>HSN / SAC</th>

                <?php
            } else {
                $col_span = 2;
            } ?>

            <th align="right"><?php echo $_L['Price']; ?></th>


            <th align="right"><?php if (
                $d['show_quantity_as'] == '' ||
                $d['show_quantity_as'] == '1'
            ) {
                echo $_L['Qty'];
            } else {
                echo $d['show_quantity_as'];
            } ?></th>
            <?php if ($config['tax_system'] == 'India') { ?>

                <th align="right">Taxable value</th>

                <?php if ($d['is_same_state'] == 1) { ?>
                    <th align="right">CGST</th>
                    <th align="right">SGST/UTGST</th>
                <?php } else { ?>

                    <th align="right">IGST</th>

                <?php } ?>



            <?php } ?>
            <th align="right"><?php echo $_L['Total']; ?></th>

        </tr>



        <?php foreach ($items as $item) {
            $cols = '';

            $item_total = $item['total'];

            if ($config['tax_system'] == 'India') {
                if ($d['is_same_state'] == 1) {
                    $tax_html =
                        '<td align="right"><span class="price">
' .
                        round($item['tax_rate'] / 2, 2) .
                        '%  <br>
' .
                        formatCurrency(
                            gstIndiaSplitTaxValue(
                                $item['total'],
                                $item['tax_rate']
                            ),
                            $d['currency_iso_code']
                        ) .
                        '
</span></td>
            <td align="right"><span class="price">
            ' .
                        round($item['tax_rate'] / 2, 2) .
                        '% <br>
            ' .
                        formatCurrency(
                            gstIndiaSplitTaxValue(
                                $item['total'],
                                $item['tax_rate']
                            ),
                            $d['currency_iso_code']
                        ) .
                        '</span></td>';
                } else {
                    $total_tax_rate =
                        ($item['tax_rate'] * ($item['qty'] * $item['amount'])) /
                        100;

                    $tax_html =
                        '<td align="right"><span class="price">
' .
                        round($item['tax_rate'], 2) .
                        '% <br> ' .
                        formatCurrency(
                            $total_tax_rate,
                            $d['currency_iso_code']
                        ) .
                        '</span>
</td>
            ';
                }

                $taxable_value_html = '';

                if ($item['discount_amount'] != '0.00') {
                    $taxable_value_html =
                        __('Total') .
                        ': ' .
                        formatCurrency(
                            $item['amount'] * $item['qty'],
                            $d['currency_iso_code']
                        ) .
                        ' <br>' .
                        __('Discount') .
                        ': ' .
                        formatCurrency(
                            $item['discount_amount'],
                            $d['currency_iso_code']
                        ) .
                        ' <br>' .
                        __('Taxable amount') .
                        ': ' .
                        formatCurrency(
                            $item['amount'] * $item['qty'] -
                                $item['discount_amount'],
                            $d['currency_iso_code']
                        );
                } else {
                    $taxable_value_html = formatCurrency(
                        $item['amount'] * $item['qty'],
                        $d['currency_iso_code']
                    );
                }

                echo '  <tr class="item-row">
            <td class="description">' .
                    $item['description'] .
                    '</td>
            <td>' .
                    $item['tax_code'] .
                    '</td>
            <td align="right">' .
                    formatCurrency($item['amount'], $d['currency_iso_code']) .
                    '</td>
            <td align="right">' .
                    numberFormatUsingCurrency(
                        $item['qty'],
                        $d['currency_iso_code']
                    ) .
                    '</td>
            <td align="right"><span class="price">' .
                    $taxable_value_html .
                    '</span></td>
            
             ' .
                    $tax_html .
                    '
            <td align="right"><span class="price">' .
                    formatCurrency(
                        $item['total'] +
                            $item['taxamount'] +
                            $item['discount_amount'],
                        $d['currency_iso_code']
                    ) .
                    '</span></td>
        </tr>';
            } else {
                echo '  <tr class="item-row">
            <td class="description">' .
                    $item['description'] .
                    '</td>
            <td align="right">' .
                    formatCurrency($item['amount'], $d['currency_iso_code']) .
                    '</td>
            <td align="right">' .
                    numberFormatUsingCurrency(
                        $item['qty'],
                        $d['currency_iso_code']
                    ) .
                    '</td>
            <td align="right"><span class="price">' .
                    formatCurrency($item_total, $d['currency_iso_code']) .
                    '</span></td>
        </tr>';
            }
        } ?>


        <tr>
            <td class="blank"> </td>
            <td colspan="<?php echo $col_span; ?>" class="total-line"><?php echo $_L[
    'Sub Total'
]; ?></td>
            <td class="total-value" <?php if ($config['rtl'] != 1) { ?>
                style="text-align: right;"
            <?php } ?>><div id="subtotal"><?php echo formatCurrency(
    $d['subtotal'],
    $d['currency_iso_code']
); ?></div></td>
        </tr>
        <?php if ($d['discount'] != '0.00') { ?>
            <tr>
                <td class="blank"> </td>
                <td colspan="<?php echo $col_span; ?>" class="total-line" style="<?php if (
    $config['rtl'] == 1
) { ?>

                        text-align: left;

                <?php } else { ?>

                        text-align: right;
                <?php } ?>"><?php echo $_L['Discount']; ?>


                </td>
                <td class="total-value" style="text-align: right;"><div><?php echo formatCurrency(
                    $d['discount'],
                    $d['currency_iso_code']
                ); ?></div></td>
            </tr>
        <?php } ?>
        <?php if ($d['tax'] != '0.00') { ?>
            <tr>

                <td class="blank"> </td>
                <td colspan="<?php echo $col_span; ?>" class="total-line" style="text-align: right;"><?php if (
    $config['tax_system'] == 'India'
) {
    echo 'GST';
} else {
    echo $_L['TAX'];
} ?></td>
                <td class="total-value" style="text-align: right;"><div id="total"><?php echo formatCurrency(
                    $d['tax'],
                    $d['currency_iso_code']
                ); ?></div></td>
            </tr>
        <?php } ?>

        <?php if ($d['credit'] != '0.00') { ?>
            <tr>
                <td class="blank"> </td>
                <td colspan="<?php echo $col_span; ?>" class="total-line"><?php echo $_L[
    'Invoice Total'
]; ?></td>
                <td class="total-value"><div class="due"><?php echo formatCurrency(
                    $d['total'],
                    $d['currency_iso_code']
                ); ?></div></td>
            </tr>
            <tr>
                <td class="blank"> </td>
                <td colspan="<?php echo $col_span; ?>" class="total-line"><?php echo $_L[
    'Total Paid'
]; ?></td>
                <td class="total-value"><div class="due"><?php echo formatCurrency(
                    $d['credit'],
                    $d['currency_iso_code']
                ); ?></div></td>
            </tr>
            <tr>
                <td class="blank"> </td>
                <td colspan="<?php echo $col_span; ?>" class="total-line balance"><?php echo $_L[
    'Amount Due'
]; ?></td>
                <td class="total-value balance"><div class="due"><?php echo formatCurrency(
                    $i_due,
                    $d['currency_iso_code']
                ); ?></div></td>
            </tr>
        <?php } else { ?>
            <tr>
                <td class="blank"> </td>
                <td colspan="<?php echo $col_span; ?>" class="total-line balance" style="<?php if (
    $config['rtl'] == 1
) { ?>

                        text-align: left;

                <?php } else { ?>

                        text-align: right;
                <?php } ?>"><?php echo $_L['Grand Total']; ?></td>
                <td class="total-value balance"
                    <?php if ($config['rtl'] != 1) { ?>
                        style="text-align: right;"
                    <?php } ?>
                ><div class="due"><?php echo formatCurrency(
                    $d['total'],
                    $d['currency_iso_code']
                ); ?></div></td>
            </tr>
        <?php } ?>

    </table>

    <!--    related transactions -->

    <?php if ($trs_c != '') { ?>
        <br>
        <h4><?php echo $_L['Related Transactions']; ?>: </h4>
        <table id="related_transactions" style="width: 100%">

            <tr>
                <th align="left" width="20%"><?php echo $_L['Date']; ?></th>
                <th align="left"><?php echo $_L['Account']; ?></th>
                <th width="50%" align="left"><?php echo $_L[
                    'Description'
                ]; ?></th>
                <th align="right"><?php echo $_L['Amount']; ?></th>

            </tr>



            <?php foreach ($trs as $tr) {
                echo '  <tr class="item-row">


            <td align="left">' .
                    date($config['df'], strtotime($tr['date'])) .
                    '</td>
            <td align="left">' .
                    $tr['account'] .
                    '</td>
            <td align="left">' .
                    $tr['description'] .
                    '</td>
            <td align="right"><span class="price">' .
                    formatCurrency($tr['amount'], $d['currency_iso_code']) .
                    '</span></td>
        </tr>';
            } ?>


        </table>
    <?php } ?>

    <!--    end related transactions -->

    <?php if ($d['notes'] != '') { ?>
        <div id="terms">
            <h5><?php echo $_L['Terms']; ?></h5>
            <?php echo $d['notes']; ?>
        </div>
    <?php } ?>

    <?php if (!empty($invoice_append_footer)) { ?>
        <br>

        <?php echo $invoice_append_footer; ?>
    <?php } ?>



</div>

</body>

</html>
