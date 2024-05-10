<?php

$ui->assign('selected_navigation', 'notes');
$ui->assign('_title', 'Notes' . ' - ' . $config['CompanyName']);
$ui->assign('_st', 'Notes');
$action = $routes[2];

switch ($action) {
    case 'make':
        // Find Invoice

        $id = $routes[3];
        $d = ORM::for_table('sys_invoices')->find_one($id);
        if (!$d) {
            exit('Invoice Not Found');
        }

        $token = $routes[4];
        $token = str_replace('token_', '', $token);
        $vtoken = $d['vtoken'];
        if ($token != $vtoken) {
            echo 'Sorry Token does not match!';
            exit();
        }

        $i_credit = $d['credit'];
        $i_due = '0.00';
        $i_total = $d['total'];

        $amount = $i_total - $i_credit;
        $invoiceid = $d['id'];
        $vtoken = $d['vtoken'];
        $ptoken = $d['ptoken'];
        
        

        //get user details

        $u = ORM::for_table('crm_accounts')->find_one($d['userid']);
        

        // find pag

        $g = PaymentGateway::where('processor', 'elavon')->first();

        

        //$pref = getSharedPreferences('elavon_payment', $d->id, 'link');

        //if ($pref) {
            //header('location: ' . $pref->value);
        //} else {
                  
                            
                $inv_no = $d->invoicenum .$d->cn;
                setSharedPreferences('elavon_payment_'.$inv_no,0,'key',$id.'_'.$token.'_'.$ptoken);
                $description = 'Invoice No :'.$inv_no;
                $salestax = 0;
                $merchantID = $g->value;
                $merchantUserID = $g->c1;
                $merchantPIN = $g->c2; 
                $baseurl = ($g->mode == 'Sandbox') ? 'https://api.demo.convergepay.com' : 'https://api.convergepay.com';                
                            
                $country_iso = '';
                if  (strtolower($u['country']) == 'canada') {
                    $country_iso = 'CAN';
                }
                if  (strtolower($u['country']) == 'united states') {
                    $country_iso = 'USA';
                }
                if  (strtolower($u['country']) == 'pakistan') {
                    $country_iso = 'PAK';
                }
                $post_data = [];

                $post_data['ssl_merchant_id'] = $merchantID;
                $post_data['ssl_user_id'] = $merchantUserID;
                $post_data['ssl_pin'] = $merchantPIN;
                $post_data['ssl_transaction_type'] = 'sale';
                $post_data['ssl_invoice_number'] = $inv_no;
                $post_data['ssl_description'] = $description;
                $post_data['ssl_salestax'] = $salestax;
                $post_data['ssl_amount'] = $amount;           
                $post_data['ssl_first_name'] = $u['account'];
                $post_data['ssl_avs_address'] = $u['address'];
                $post_data['ssl_city'] = $u['city'];
                $post_data['ssl_state'] = $u['state'];
                $post_data['ssl_avs_zip'] = $u['zip'];                
                $post_data['ssl_country'] = $country_iso;                
                $post_data['ssl_email'] = $u['email'];
                $post_data['ssl_phone'] = $u['phone'];   
                $post_data['ssl_company'] = $u['company'];
                $post_data['ssl_get_token'] = 'Y';
                $post_data['ssl_add_token'] = 'Y';                
                
                /*echo "<pre>";
                print_r($post_data);
                exit;*/
                
                
                
                                                
                
//                $post_data['ssl_callback_url'] = U . "client/ipay_success/$id/token_$ptoken/";
//                $post_data['ssl_transaction_currency'] = $d['currency_iso_code'];
                
                
                $post_query = http_build_query($post_data);
                
                $url = $baseurl.'/hosted-payments/transaction_token';
                
                $ch = curl_init();    // initialize curl handle
                curl_setopt($ch, CURLOPT_URL,$url); // set POST target URL
                curl_setopt($ch,CURLOPT_POST, true); // set POST method
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                
                curl_setopt($ch,CURLOPT_POSTFIELDS,$post_query);
                $result = curl_exec($ch); // run the curl to post to Converge
                

                if ($result === false) {
                    echo 'Curl error message: '.curl_error($ch).'<br>';
                    echo 'Curl error code: '.curl_errno($ch);
                    exit;
                } else {
                  $httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                 if ($httpstatus == 200) {            
                     $sessiontoken = urlencode($result);
                  
                  $payurl = $baseurl."/hosted-payments?ssl_txn_auth_token=$sessiontoken";

                

                header('location: ' . $payurl);
                 }
//            }
            
        }

        break;
    case 'callback':        
        $myFile = "elavon_res.log";
        $phpObj = json_encode($_POST, true);
        file_put_contents($myFile,$phpObj);
        $inv_no = $_POST['ssl_invoice_number'];     
        $pref = getSharedPreferences('elavon_payment_'.$inv_no,0, 'key');           
        list($inv_id, $token, $ptoken) = explode('_',$pref->value);  
        setSharedPreferences('elavon_payment',$inv_id,'resp',json_encode($_POST));
        if ($_POST['ssl_result_message'] == 'APPROVAL') r2(U . 'client/ipay_success/' . $inv_id . '/token_' . $ptoken);      
        else  r2(U . 'client/iview/' . $inv_id . '/' . $token, 'e', 'Payment Failed - '.$_POST['errorName']);         

        break;        
    case 'fail':  
        $inv_no = $_POST['ssl_invoice_number'];
        $pref = getSharedPreferences('elavon_payment_'.$inv_no,0, 'key');
        list($inv_id, $token,$ptoken) = explode('_',$pref->value);
        r2(U . 'client/iview/' . $inv_id . '/' . $token, 'e', 'Payment Failed - '.$_POST['errorName']);         
        
        
        break;
    case 'submitted':
        $inv_id = route(3);
        $token = route(4);

        r2(
            U . 'client/iview/' . $inv_id . '/' . $token,
            'e',
            'Payment Success.'
        );

        break;

    case 'failed':
        // redirect to original page

        $inv_id = route(3);
        $token = route(4);

        r2(U . 'client/iview/' . $inv_id . '/' . $token, 'e', 'Payment Failed');

        break;

    default:
        echo 'action not defined';
}
