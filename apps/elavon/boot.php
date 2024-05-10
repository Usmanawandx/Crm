<?php
Event::bind('settings/pg_conf/label', function () {
    $arg_list = func_get_args();

    if ($arg_list[0][0] == 'elavon') {
        global $ui;

        $label = [];

        $label['value'] = 'Merchant ID';
        $label['c1'] = 'User ID';
        $label['c2'] = 'PIN';
        $label['c3'] = '';
        $label['c4'] = '';
        $label['c5'] = '';
        $label['mode'] = true;

        $ui->assign('label', $label);
    }
});


Event::bind('client/ipay/pg', function () {
    $arg_list = func_get_args();    
    if ($arg_list[0][0] == 'elavon') {
        $id = $arg_list[0][1];
        $token = $arg_list[0][2];

        r2(U . "elavon/payments/make/$id/$token/");
    }
});

//refund 

Event::bind('invoices', function () {
	global $routes;
	$action = $routes['1'];
	if ($action == 'set-status'){
		$status = $_REQUEST['status'];		
		if ($status == 'Cancelled'){
			$invoice_id = $_REQUEST['invoice_id'];
			$invoice = Invoice::find($invoice_id);
            $pref = getSharedPreferences('elavon_payment',$invoice_id, 'resp');   
            $refund_status = getSharedPreferences('elavon_payment',$invoice_id, 'refund_status'); 

            if (!empty($pref->value)&&(empty($refund_status))){
                $respar = json_decode($pref->value, true);
                $ssl_txn_id = $respar['ssl_txn_id'];
                $res_json = elavon_refund($ssl_txn_id) ;
                setSharedPreferences('elavon_payment',$invoice_id,'refund',$res_json);
                $res_ar = json_decode($res_json, true);
                
                if (array_key_exists("errorCode",$res_ar)){
                    _msglog('e', 'Invoice elavon refund fail - '.$res_ar['errorMessage']);
                    exit;
                }
                else {
                    setSharedPreferences('elavon_payment',$invoice_id,'refund_status','Y');
                    _msglog('s', 'Elavon Refund Successful');
                }
            }                        
            
			//error_log("Inv - ".$invoice_id.' - '.$status.'ttl= '.$invoice->total );		
		}
		
	}		
    
});

function elavon_refund($ssl_txn_id){
    $g = PaymentGateway::where('processor', 'elavon')->first();

    $xmldata = '<txn>
    <ssl_merchant_id>'.$g->value.'</ssl_merchant_id>
    <ssl_user_id>'.$g->c1.'</ssl_user_id>
    <ssl_pin>'.$g->c2.'</ssl_pin>
    <ssl_transaction_type>ccreturn</ssl_transaction_type>
    <ssl_txn_id>'.$ssl_txn_id.'</ssl_txn_id>
</txn>';
    
    $baseurl = ($g->mode == 'Sandbox') ? 'https://api.demo.convergepay.com' : 'https://api.convergepay.com';  
    $url = $baseurl.'/VirtualMerchantDemo/processxml.do';                
    $ch = curl_init();    // initialize curl handle
    curl_setopt($ch, CURLOPT_URL,$url); // set POST target URL    
    curl_setopt($ch,CURLOPT_POST, true); // set POST method
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                
    curl_setopt($ch,CURLOPT_POSTFIELDS,"xmldata=" . $xmldata);
    $result = curl_exec($ch); // run the curl to post to Converge
    curl_close($ch);    
    $res_json = json_encode(simplexml_load_string($result), true);        
    return $res_json;
   

}


