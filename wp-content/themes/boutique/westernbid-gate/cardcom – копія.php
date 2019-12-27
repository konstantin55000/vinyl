<?php

class EffectiveWritingCardComPayment
{
    public function __construct($order)
    {
        $payment_gateways = WC_Payment_Gateways::instance();
        $cardcom_payment_gateway = $payment_gateways->payment_gateways()['cardcom'];
        
        $this->base_url = 'https://secure.cardcom.solutions/Interface/Direct2.aspx';
//        $this->terminal_number = $cardcom_payment_gateway->terminalnumber;
//        $this->user_name = $cardcom_payment_gateway->username;
          
        $this->terminal_number ='1000';
        $this->user_name =  'barak9611';
        
        $this->order = $order;
    }

    public function charge_credit_card($payment_details)
    {
        $vars = array(
            'TerminalNumber' => '1000', //$this->TERMINAL_NUMBER,
            'UserName' => 'barak9611',  //$this->USER_NAME,
            'Sum' => $this->order->get_total_sum(),
            'Coinid' => '1',
            'Identitynumber' => $payment_details['identity_number'],
            'CardValidityMonth' => $payment_details['CC_MM'],
            'CardValidityYear' => '20'.$payment_details['CC_YY'],
            'CardNumber' => $payment_details['CC_Number'],
            'Cvv' => $payment_details['CC_CVV'],
            'NumOfPayments' => '1',
            'codepage' => '65001', #UNICODE
        );

         
        # urlencode the information
        $urlencoded = http_build_query($vars);
        //var_dump($urlencoded);
      
        #init curl connection
        if (function_exists('curl_init')) {
            $CR = curl_init();
            curl_setopt($CR, CURLOPT_URL, $this->base_url);
            curl_setopt($CR, CURLOPT_POST, 1);
            curl_setopt($CR, CURLOPT_FAILONERROR, true);
            curl_setopt($CR, CURLOPT_POSTFIELDS, $urlencoded);
            curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);

            #actual curl execution perfom
            $result = curl_exec($CR);            
            $error = curl_error($CR);
           
            if (!empty($error)) {
                error_log(print_r($result));
                error_log(print_r($message));
                error_log(print_r($error));
                var_dump($result,$error);
                //die;
                return;
            }
            curl_close($CR);
        }

        $resultArray = array();
        parse_str($result, $resultArray); # ResponseCode={0}&Description={1}&InternalDealNumber={2}&InvoiceResponse.ResponseCode={3}&InvoiceResponse.Description={4}&InvoiceResponse.InvoiceNumber={5}&InvoiceResponse.InvoiceType={6}
        
        if (isset($resultArray['ResponseCode']) && $resultArray['ResponseCode'] == '0') # was charged OK!
        {
            // print_r($resultArray);
            // print_r(json_encode(['InternalDealNumber' => $resultArray['InternalDealNumber']]));
            // die;
            $this->order->set_order_completed();
           // var_dump($this->order);
      
            return true;
        } else # some error , unable to charge toekn // log for informtation
        {
            return false;
        }
    }
}
