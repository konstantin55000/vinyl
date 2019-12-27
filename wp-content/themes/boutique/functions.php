<?php
/**
 * Boutique engine room
 *
 * @package boutique
 */

/**
 * Set the theme version number as a global variable
 */
$theme				= wp_get_theme( 'boutique' );
$boutique_version	= $theme['Version'];

$theme				= wp_get_theme( 'storefront' );
$storefront_version	= $theme['Version'];

/**
 * Load the individual classes required by this theme
 */
require_once( 'inc/class-boutique.php' );
require_once( 'inc/class-boutique-customizer.php' );
require_once( 'inc/class-boutique-template.php' );
require_once( 'inc/class-boutique-integrations.php' );
 
//if(is_page('checkout')){
//add_action('wp_enqueue_scripts', 'custom_checkout');
//}

function custom_checkout(){
    wp_enqueue_script('custom-checkout', get_template_directory_uri() . '/js/checkout/form-checkout-custom.js');
    //var_dump(get_template_directory_uri() . '/js/checkout/form-checkout-custom.js');
}

 function custom_checkout_enqueue_script() {
    wp_deregister_script('wc-checkout');
    wp_register_script('wc-checkout', get_bloginfo( 'stylesheet_directory' ). '/js/checkout/checkout.js' , array( 'jquery' ), WC_VERSION, TRUE);
    wp_enqueue_script('wc-checkout');
}
	
add_action( 'wp_enqueue_scripts', 'custom_checkout_enqueue_script' );

function remove_footer_admin () {
 
echo '© Vinyl Evolution 2015';
 
}
 
add_filter('admin_footer_text', 'remove_footer_admin', 100);