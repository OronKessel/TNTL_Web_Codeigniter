<?php

defined('BASEPATH') OR exit('No direct script access allowed');
include('stripe/init.php');
class Stripe {
    
    public $secretKey = "sk_test_QUZ3FBU1l2itHpPPrre52IIi";
    public function testCheckout()
    {   
        $myCard =  array(
            'number' => '4242424242424242',
            'exp_month' => '09',
            'exp_year'=>'2030'
        );
        return $this->checkOut($myCard['number'],$myCard['exp_month'],$myCard['exp_year'],'222',1000);
    }
    public function testRefund()
    {
        $id = "ch_1AUFMxGyC32uFSEc0cqwtUzs";        
        echo $this->refund($id);
    }
    
    public function checkOut($cardNumber,$expMonth,$expYear,$cv,$amount)
    {
        \Stripe\Stripe::setApiKey($this->secretKey);
        $myCard =  array(
            'number' => $cardNumber,
            'exp_month' => $expMonth,
            'exp_year'=>$expYear,
            'cvc'=>$cv
        );
        try {
            $charge = \Stripe\Charge::create(array('card'=>$myCard,'amount' => $amount, 'currency' => 'usd' ));        
            if ($charge->status == "succeeded")
            {            
                return $charge->id;
            }
            else return false;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    public function refund($transactionId)
    {        
        \Stripe\Stripe::setApiKey($this->secretKey);
        try {
            $re = \Stripe\Refund::create(array(
                    "charge" => $transactionId
                  ));        
            if ($re->status == "succeeded")
            {            
                return $re->id;
            }
            else return false;
        } catch (Exception $ex) {
            return false;
        }
    }

}
