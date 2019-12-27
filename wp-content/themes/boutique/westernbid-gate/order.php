<?php
class EffectiveWritingOrder {
    private $product;
    private $order;
    private $qantity;
    private $returnUrl;

    public function __construct($payment_details, $return_url) {
        $this->set_product($payment_details['product_id']);
        $this->create_order($payment_details);
        $this->returnUrl = $return_url;
        if(isset($payment_details['coupon_code']) && $this->coupon_is_valid($payment_details['coupon_code'])) {
            $this->apply_coupon($payment_details['coupon_code']);
        } 
    }

    public function get_total_sum() {
        return $this->order->get_total();
    }
    public function get_checkout_order_received_url() {   
        return $this->returnUrl;
    }
    public function set_order_completed() {
        $this->order->update_status('completed');
    }
    
    public function set_order_failed() {
        $this->order->update_status('failed');
    }

    public function get_order_id() {
        return $this->order->get_id();        
    }

    private function set_product($product_id) {
        $this->product = wc_get_product($product_id);
    }

    private function set_quantity($quantity) {
        $this->quantity = $quantity;
    }
    
    private function coupon_is_valid($coupon_code) {
        $coupon = wc_get_coupon_id_by_code($coupon_code);
        if($coupon) {
            return true;
        }

        return false;
    }

    private function apply_coupon($coupon_code) {
        $this->order->apply_coupon($coupon_code);     
    }

    private function create_order($payment_details) {
        $address = array(
            'first_name' => $payment_details['first_name'],
            'last_name'  => $payment_details['last_name'],
            'phone'      => $payment_details['phone'],
        );

        $this->order = wc_create_order();
        $this->order->add_product( $this->product, $this->quantity);
        $this->order->set_address( $address, 'billing' );
        $this->order->calculate_totals();
    }
}

 function values_are_valid($values) { 
    
     if(!isset($values['first_name']) || empty($values['first_name'])) return false;
     if(!isset($values['last_name']) || empty($values['last_name'])) return false;
     if(!isset($values['CC_Number']) || empty($values['CC_Number'])) return false;
     if(!isset($values['CC_MM']) || empty($values['CC_MM'])) return false;
     if(!isset($values['CC_YY']) || empty($values['CC_YY'])) return false;
     if(!isset($values['CC_CVV']) || empty($values['CC_CVV'])) return false;
     if(!isset($values['phone']) || empty($values['phone'])) return false;
     if(!isset($values['identity_number']) || empty($values['identity_number'])) return false;

     
    
     return true;
 }

 