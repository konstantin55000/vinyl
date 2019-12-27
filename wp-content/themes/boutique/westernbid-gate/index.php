<?php
/*
Plugin Name: Payment Gate Westernbid
Plugin URI:  http://link to your plugin homepage
Description: Westernbid Paypal Gate for Vinyl Evolution
Version:     1.0
Author:      Author
License:     GPL2 etc 
*/
//error_reporting(E_ALL & ~E_NOTICE);

if ( ! defined( 'ABSPATH' ) ) {
   exit; // Exit if accessed directly
}
  
  
require 'order.php';
require 'cardcom.php'; 
add_filter( 'woocommerce_payment_gateways', 'woo_add_gateway_class' );
function woo_add_gateway_class( $gateways ) {
	$gateways[] = 'WC_VinylEvo_Gateway';  
	return $gateways;
} 

add_action('plugins_loaded', 'init_custom_gateway_class');

//init_custom_gateway_class();
function init_custom_gateway_class(){

    class WC_VinylEvo_Gateway extends WC_Payment_Gateway {

       public $domain;
      
        /**
         * Constructor for the gateway.
         */
        public function __construct() {

            $this->domain = 'custom_payment';
      
            $this->id                 = 'custom';
            $this->icon               = apply_filters('woocommerce_custom_gateway_icon', '');
            $this->has_fields         = false;
            $this->method_title       = __( 'Custom', $this->domain );
            $this->method_description = __( 'Allows payments with custom gateway.', $this->domain );

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title        = $this->get_option( 'title' );
            $this->description  = $this->get_option( 'description' );
            $this->instructions = $this->get_option( 'instructions', $this->description );
            $this->order_status = $this->get_option( 'order_status', 'completed' );

            // Actions
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );

            // Customer Emails
            add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
        
            
        }
        
   
 
 
  
        function display_verification_id_in_customer_order( $order ) {
        // compatibility with WC +3
        $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

        echo '<p class="verification-id"><strong>'.__('Verification ID', 'woocommerce') . ':</strong> ' . get_post_meta( $order_id, '_billing_vid', true ) .'</p>';
    }
        
        //add_action( 'woocommerce_order_details_after_customer_details', 'display_verification_id_in_customer_order', 10 );
        
        /**
         * Initialise Gateway Settings Form Fields.
         */
        public function init_form_fields() {

            $this->form_fields = array(
                'enabled' => array(
                    'title'   => __( 'Enable/Disable', $this->domain ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable Custom Payment', $this->domain ),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title'       => __( 'Title', $this->domain ),
                    'type'        => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', $this->domain ),
                    'default'     => __( 'Custom Payment', $this->domain ),
                    'desc_tip'    => true,
                ),
                'order_status' => array(
                    'title'       => __( 'Order Status', $this->domain ),
                    'type'        => 'select',
                    'class'       => 'wc-enhanced-select',
                    'description' => __( 'Choose whether status you wish after checkout.', $this->domain ),
                    'default'     => 'wc-completed',
                    'desc_tip'    => true,
                    'options'     => wc_get_order_statuses()
                ),
                'description' => array(
                    'title'       => __( 'Description', $this->domain ),
                    'type'        => 'textarea',
                    'description' => __('Vinyl Evolution Gate', $this->domain),
                    'default'     => __('Vinyl Evolution Gate', $this->domain),
                    'desc_tip'    => true,
                ),
                'instructions' => array(
                    'title'       => __( 'Instructions', $this->domain ),
                    'type'        => 'textarea',
                    'description' => __( 'Instructions that will be added to the thank you page and emails.', $this->domain ),
                    'default'     => '',
                    'desc_tip'    => true,
                ),
            );
        }

        /**
         * Output for the order received page.
         */
        public function thankyou_page() {
            if ( $this->instructions )
                echo wpautop( wptexturize( $this->instructions ) );
        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
            if ( $this->instructions && ! $sent_to_admin && 'custom' === $order->payment_method && $order->has_status( 'on-hold' ) ) {
                echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
            }
        }

       

        public function payment_fields() {
            if ( $description = $this->get_description() ) {
                echo wpautop( wptexturize( $description ) );
            }

            if ( $this->supports( 'tokenization' ) && is_checkout() ) {
                $this->tokenization_script();
                $this->saved_payment_methods();
                $this->form();
                $this->save_payment_method_checkbox();
            } else {
                $this->form();
            }
        }
    
        /**
         * Output field name HTML
         *
         * Gateways which support tokenization do not require names - we don't want the data to post to the server.
         *
         * @since  2.6.0
         * @param  string $name Field name.
         * @return string
         */
        public function field_name( $name ) {
            return $this->supports( 'tokenization' ) ? '' : ' name="' . esc_attr( $this->id . '-' . $name ) . '" ';
        }

        // Outputting the hidden field in checkout page

      
        public function form() {
            $wb = new WC_VinylEvo_Gateway();
            wp_enqueue_script( 'wc-checkout-form' );
    
            global $order_id;
            global $woocommerce;
            
           // do_action( 'woocommerce_checkout_form_start', $this->id ); 
            $actual_link = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

            $wb_login = "best_wool";
            $secret = "G#gcV#/";
            $amount = $woocommerce->cart->cart_contents_total;
            $wb_hash = $wb_login.$secret.$amount.$order_id;                    
            $wb_hash = md5($wb_hash); 
                
            $items = $woocommerce->cart->get_cart();
            $count = 1;
            
            echo '<input type="hidden" name="amount" value="'.$amount.'">';
             echo '<input type="hidden" name="wb_hash" id="wb_hash" value="init">';
            echo '<input type="hidden" id="return_url" name="return" value="https://vinyl-evolution.com/order_recieved/?order_id="'.$order_id.'">';
            echo '<input type="hidden"  id="cancel_url" name="cancel_return" value="https://vinyl-evolution.com/order_canceled/?order_id='.$order_id.'">';
            echo '<input type="hidden"  id="notify_url" name="notify_url" value="https://vinyl-evolution.com/order_recieved/?order_id='.$order_id.'">';
            echo '<input type="hidden" name="invoice" id="invoice" value="'.$order_id.'">';

        foreach($items as $item => $values) { 
            $product_id = $values['data']->get_id();
            $_product =  wc_get_product( $values['data']->get_id());  
            $price = get_post_meta($values['product_id'] , '_price', true);
             
            $product_total =  $price * $quantity;            
            $product_total = number_format((float)$product_total, 2, '.', '');
                 
            echo '<input type="hidden" name="amount_'.$count.'" value="'.$product_total.'">';
            echo '<input type="hidden" name="item_name_'.$count.'" value="'.$_product->get_title().'">';
            echo '<input type="hidden" name="item_number_'.$count.'" value="'.$product_id.'">'; //.$_product->get_sku()
            echo '<input type="hidden" name="quantity_'.$count.'" value="'.$values['quantity'].'">';
            echo '<input type="hidden" name="url_'.$count.'" value="'.get_permalink($product_id).'">';
            echo '<input type="hidden" name="description_'.$count.'" value="'.$_product->get_title().'">'; //$_product->get_short_description()

            
            $count++;
        }
        
            echo '<input type="hidden" name="shipping" value="0">';
            echo '<input type="hidden" name="currency_code" value="USD">';     
               
            //$this->id 
             
            $str = '<form action="https://shop.westernbid.info" method="post">
              
                <input type="hidden" name="charset" value="utf-8">
                
                <input type="hidden" name="invoice" value="xx1111">
                <input type="hidden" name="email" value="user@mail.com">
                <input type="hidden" name="phone" value="75986897445">
                <input type="hidden" name="first_name" value="User Firstname">
                <input type="hidden" name="last_name" value="User Last name">
                <input type="hidden" name="address1" value="User address">
                <input type="hidden" name="address2" value="">
                <input type="hidden" name="country" value="User Country Code">
                <input type="hidden" name="city" value="User city">
                <input type="hidden" name="state" value="User state">
                <input type="hidden" name="zip" value="User zip">
                <input type="hidden" name="item_name" value="Order name">
                 
                <input type="hidden" name="shipping" value="0">
                <input type="hidden" name="currency_code" value="USD"> 
               
                <input type="hidden" name="url_1" value="http://site.com/item_8654"> 
                
                <input type="hidden" name="item_name_2" value="Sample Product 2">
                <input type="hidden" name="item_number_2" value="xf-346621">
                <input type="hidden" name="url_2" value="http://site.com/item_8264">
                <input type="hidden" name="description_2" value="This is description of item 2">
                <input type="hidden" name="amount_2" value="20">
                <input type="hidden" name="quantity_2" value="1">
                
                <input type="hidden" name="return" value="http://website.com/return?order_id=1111">
                <input type="hidden" name="cancel_return" value="http://website.com/cancel?order_id=1111">
                <input type="hidden" name="notify_url" value="http://website.com/notify?order_id=1111">'; 
                  
             
                 // do_action( 'woocommerce_checkout_form_end', $this->id );  
        }
    
    

           public function process_payment( $order_id ) {

           global $woocommerce;

           $payment_details = $_REQUEST;           
           $payment_details['order_id']  = $order_id;
           $payment_details['invoice']  = $order_id;
               
           $order = wc_get_order( $order_id );
           $expiry  = $_REQUEST['billing_card_expiry'];
           $date_arr =   explode(" / ", $expiry); 
          
           $wb_login = "best_wool";
           $secret = "G#gcV#/";
          
           $amount = $woocommerce->cart->cart_contents_total;
           $wb_hash_raw = $wb_login.$secret.$amount.$order_id;  
               
           $wb_hash = md5($wb_hash_raw);
           $payment_details['wb_hash'] =  $wb_hash ;
                // $payment_details['wb_hash_raw'] = $wb_hash_raw;
     
           $returnUrl = str_replace('writer','vinyl-evolution.com', $this->get_return_url( $order ));
           $payment_details['return'] =   $returnUrl;           
               
           $payment_details['cancel_return'] = $returnUrl;  //$this->get_return_url( $order ); //'https://vinyl-evolution.com/order_canceled/?order_id='.$order_id;  //wc_get_checkout_url(); //'https://vinyl-evolution.com/order_canceled/?order_id='.$order_id; //wc_get_checkout_url()
           $payment_details['notify_url'] =  $returnUrl; // $this->get_return_url( $order ); // 'https://vinyl-evolution.com/notify/?order_id='.$order_id; 
            // str_replace('writer','vinyl-evolution.com');
          
            // $payment_details['first_name']  = $_REQUEST['billing_first_name'];    
            // $payment_details['last_name'] = $_REQUEST['billing_last_name'];
            // $payment_details['CC_Number'] = $_REQUEST['billing_cardnumber'];  
            // $payment_details['CC_MM'] = $date_arr[0];
            // $payment_details['CC_YY'] = $date_arr[1];
            // $payment_details['CC_CVV'] = $_REQUEST['billing_code'];
            // $payment_details['phone'] = $_REQUEST['billing_phone'];
            // $payment_details['identity_number'] = $_REQUEST['billing_identity_number'];
 
            
            // if(!values_are_valid($payment_details)) {
            //    echo json_encode($payment_details);
            //    die;
            // };

//           $order = new EffectiveWritingOrder($payment_details, $this->get_return_url( $order ));       
//       
//           $cardcom = new EffectiveWritingCardComPayment($order);
//           $result = $cardcom->charge_credit_card($payment_details);  
//           echo $result;
//           exit;
               
            $result = array(
                
                'result'    =>  'success',
                'order_id'  => $order_id, 
                'complete_url' => $returnUrl,
                'wb_hash' => $wb_hash,
                'wb_hash_raw' => $wb_hash_raw
            );
          
           // if ($result['success'] == false) {
                //$order->set_order_failed(); 
           echo json_encode($result); 
           die;
               
//            }else {
//                
////                    $order->set_order_completed();      
////                    //$order->reduce_order_stock(); 
////                   //  WC()->cart->empty_cart();                    
////                    $data = array(
////                         'result'    => 'success',
////                         'redirect'  => $this->get_return_url( $order ),
////                         'price' => $order->get_total_sum(),
////                         'wc_order_id' => $order->get_order_id()  
////                     
////                     );
////                    // echo json_encode($data);  
////                    // die;    
////                   return $data;  
//          }
  
             
       }
  
}
 

//add_action( 'woocommerce_checkout_process', 'validate_extra_checkout_fields' );
//function validate_extra_checkout_fields()
// {   
//     $fields = get_extra_checkout_fields();
//     if($fields){ 
//         foreach($fields as $field_key => $fieldData){
 

//             if(isset($fieldData['required']) && $fieldData['required'] && (!isset($_REQUEST[$field_key]) || empty($_REQUEST[$field_key])) ){
//                 wc_add_notice( 'Card field  - ' . $fieldData['label'] . ' is required', 'error' );
//             }
//         }
//     }
// }

//add_action('woocommerce_checkout_process', 'validate_card');

// function validate_card() { 

//     $cardnumber = $_REQUEST['billing_cardnumber'];
//     $ccPattMasterCard = "/(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}/"; // прозапас
//     // $ccVisaCard = "^4[0-9]{12}(?:[0-9]{3})?$";  
//     var_dump('billing_cardnumber', $cardnumber);
//     exit;
//     //$ccPattern = "^/(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14})/$";
//     if (preg_match($ccPattMasterCard, $cardnumber) == false ){
//         wc_add_notice( __( 'Card field - Please check cardnumber' ), 'error' );
//     };
  
// }
 

//add_action('woocommerce_checkout_process', 'validate_phone');

function validate_phone() {
    $billing_phone = $_REQUEST['billing_phone']; //filter_input($_REQUEST['billing_phone'], 'billing_phone');
    //var_dump($billing_phone);
    
    if (strlen(trim(preg_replace('/^[6789]\d{9}$/', '', $billing_phone))) > 0) {
        wc_add_notice(__('Invalid <strong>Phone Number</strong>, please check your input.'), 'error');
    }
}

//add_action('woocommerce_checkout_process', 'validate_expiry');

 

function my_custom_checkout_field_process() {
     
    // if ( ! $_REQUEST['billing_cardnumber'] )
    //     wc_add_notice( __( 'Please enter valid card number.' ), 'error' );
    // if ( ! $_REQUEST['billing_code'] )
    // wc_add_notice( __( 'Please enter valid  CVC. ' ), 'error' );

    // if ( ! $_REQUEST['billing_card_expiry'] )
    // wc_add_notice( __( 'Please enter valid card expiry MM / YY.' ), 'error' );
}


 
}
add_action( 'woocommerce_checkout_update_order_meta', 'custom_payment_update_order_meta' );
function custom_payment_update_order_meta( $order_id ) {
    if($_REQUEST['payment_method'] != 'custom')
        return; 
    // update_post_meta( $order_id, 'billing_holdername', $_REQUEST['billing_holdername'] );
    // update_post_meta( $order_id, 'billing_holderlastname', $_REQUEST['billing_holderlastname'] );
    // update_post_meta( $order_id, 'billing_cardnumber', $_REQUEST['billing_cardnumber'] );
    // update_post_meta( $order_id, 'billing_cardmonth', $_REQUEST['billing_cardmonth'] );
    // update_post_meta( $order_id, 'billing_cardyear', $_REQUEST['billing_cardyear'] );
    // update_post_meta( $order_id, 'billing_code', $_REQUEST['billing_code'] );
    update_post_meta( $order_id, 'billing_phone', $_REQUEST['billing_phone'] ); 
}

add_filter( 'woocommerce_payment_gateways', 'add_custom_gateway_class' );
function add_custom_gateway_class( $methods ) {
    $methods[] = 'WC_VinylEvo_Gateway'; 
    return $methods;
}



// Create hidden checkout field type
add_filter( 'woocommerce_form_field_hidden', 'create_checkout_hidden_field_type', 5, 4 );
function create_checkout_hidden_field_type( $field, $key, $args, $value ){
    return '<input type="hidden" name="'.esc_attr($key).'" id="'.esc_attr($args['id']).'" value="'.esc_attr($args['default']).'" />';
}

// Add custom hidden billing checkout field
add_filter( 'woocommerce_checkout_fields', 'custom_billing_fields' );
function custom_billing_fields( $fields ){

    ## HERE set the value (for this hidden checkout field)
    
    $fields['billing']['first_name'] = $fields['billing']['billing_first_name'];
    $fields['billing']['first_name']['type']  = 'hidden';
   
   
    $fields['billing']['first_name']['required'] = false;
     

     
    $fields['billing']['last_name'] = $fields['billing']['billing_last_name'];
    $fields['billing']['last_name']['type']  = 'hidden';
    $fields['billing']['last_name']['required'] = false;

    $fields['billing']['address1'] = $fields['billing']['billing_address_1'];
    $fields['billing']['address1']['type']  = 'hidden';
    $fields['billing']['address1']['required'] = false;

    $fields['billing']['address2'] = $fields['billing']['billing_address_2'];
    $fields['billing']['address2']['type']  = 'hidden';
    $fields['billing']['address2']['required'] = false;

    $fields['billing']['country'] = $fields['billing']['billing_country'];
    $fields['billing']['country']['type']  = 'hidden';
    $fields['billing']['country']['required'] = false;
    
    $fields['billing']['city'] = $fields['billing']['billing_city'];
    $fields['billing']['city']['type']  = 'hidden';
    $fields['billing']['city']['required'] = false;

    $fields['billing']['zip'] = $fields['billing']['billing_postcode'];
    $fields['billing']['zip']['type']  = 'hidden';
    $fields['billing']['zip']['required'] = false;

    $fields['billing']['state'] = $fields['billing']['billing_state'];
    $fields['billing']['state']['type']  = 'hidden';
    $fields['billing']['state']['required'] = false;

    $fields['billing']['phone'] = $fields['billing']['billing_phone'];
    $fields['billing']['phone']['type']  = 'hidden';
    $fields['billing']['phone']['required'] = false;
    
    $fields['billing']['email'] = $fields['billing']['billing_email'];
    $fields['billing']['email']['type']  = 'hidden';
    $fields['billing']['email']['required'] = false;
    

    global $woocommerce;
    global $order_id;
    $cart = $woocommerce->cart;
    $wb_login = "best_wool";
    $secret = "G#gcV#/";
    $amount = $cart->cart_contents_total;
    $wb_hash = $wb_login.$secret.$amount.$order_id;                    
    $wb_hash = md5($wb_hash);
            
 
    $fields['billing']['charset'] = array(
        'type' => 'hidden', 
        'required'  => false,
        'class'     => array('form-row-wide'),
        'clear'     => true,
        'name' => 'charset',
        'default'   => 'utf-8', // The custom field value
    );
    
    $fields['billing']['item_name'] = array(
        'type' => 'hidden', 
        'required'  => false,
        'class'     => array('form-row-wide'),
        'clear'     => true,
        'name' => 'item_name',
        'default'   => 'Wall Clock', // The custom field value
    );
     
   $fields['billing']['currency_code'] = array(
        'type' => 'hidden', 
        'required'  => false,
        'class'     => array('form-row-wide'),
        'clear'     => true,
        'name' => 'charset',
        'default'   => '4217', // The custom field value
    );
    
//    $fields['billing']['wb_hash'] = array(
//        'type' => 'hidden', 
//        'required'  => false,
//        'class'     => array('form-row-wide'),
//        'clear'     => true,
//        'name' => 'wb_id',
//        'default'   => $wb_hash, // The custom field value
//    );
    
//   $fields['billing']['invoice'] = array(
//        'type' => 'hidden', 
//        'required'  => false,
//        'class'     => array('form-row-wide'),
//        'clear'     => true,
//        'name' => 'invoice',
//        'default'   => $order_id, // The custom field value
//    );

    $fields['billing']['wb_login'] = array(
        'type' => 'hidden', 
        'required'  => false,
        'class'     => array('form-row-wide'),
        'clear'     => true,
        'name' => 'wb_id',
        'default'   => $wb_login, // The custom field value
    );
    
    
   
    return $fields;
}

// Display the field value on the admin order edit page (after billing address)
// add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_custom_field_in_admin_order_meta', 10, 1 );
// function display_custom_field_in_admin_order_meta($order){
//     echo '<p><strong>'.__('Quickbook').':</strong> ' . get_post_meta( $order->get_id(), '_billing_quickbook', true ) . '</p>';
// }

 
   add_action( 'woocommerce_checkout_update_order_meta', 'save_custom_checkout_hidden_field' );
    function save_custom_checkout_hidden_field( $order_id ) {
        
        if ( ! empty( $_POST['wb_login'] ) ) {
            update_post_meta( $order_id, 'wb_login', sanitize_text_field( $_POST['wb_login'] ) );
        }
        
        if ( ! empty( $_POST['wb_hash'] ) ) {
            update_post_meta( $order_id, 'wb_hash', sanitize_text_field( $_POST['wb_hash'] ) );
        }
    }
// $wb = new WC_VinylEvo_Gateway()
// function wb_form = $wb->form;

//       add_action( 'woocommerce_after_order_notes', 'wb_form' );
 
