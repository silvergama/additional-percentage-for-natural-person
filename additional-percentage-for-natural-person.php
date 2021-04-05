<?php

/**
 * Plugin Name: Additional percentage for natural person
 * Plugin URI: https://www.wpmobilemenu.com/
 * Description: Adds percentage for users not logged in or logged in as a natural person.
 * Author: Silver Gama
 * Version: 1.0.2
 * Author URI: https://github.com/silvergama/
 * Tested up to: 4.9
 * Text Domain: additional-percentage-for-natural-person
 * Domain Path: /languages/
 * GitHub Plugin URI: https://github.com/silvergama/additional-percentage-for-natural-person
 * License: GPLv2
 */
add_action( 'plugins_loaded', 'apfnp_load_textdomain'); 
function apfnp_load_textdomain() 
    {
        load_plugin_textdomain('additional-percentage-for-natural-person', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages');
    }


add_filter('woocommerce_get_price', 'apfnp_custom_price', 10, 2);

function apfnp_custom_price($price, $product) {
    if(is_user_logged_in() && apfnp_is_company()) return $price;
    
    $percentage = get_option( 'apfnp_additional_percentage_for_natural_person', '' );
    $additional = apfnp_calc_percentage($percentage, $price);
    return $price + $additional;
    
}

function apfnp_calc_percentage($percent, $price) {
    return ($percent / 100) * $price;
}

function apfnp_is_company() {
    $user_id = get_current_user_id();
    return get_user_meta( $user_id, 'billing_cnpj', true );
}

add_filter('admin_init', 'apfnp_additional_percentage_for_natural_person'); 

function apfnp_additional_percentage_for_natural_person() { 

    $args = array(
        'type' => 'string', 
        'sanitize_callback' => 'sanitize_text_field',
        'default' => NULL,
    );

    register_setting( 
        'general', 
        'apfnp_additional_percentage_for_natural_person',
        $args
    );
    
    add_settings_field( 
        'apfnp_additional_percentage_for_natural_person', 
        __('Additional percentage for natural person', 'additional-percentage-for-natural-person'), 
        'sg_callback_apfnp_additional_percentage_for_natural_person', 
        'general', 
        'default',
        [
            'label_for' => 'apfnp_additional_percentage_for_natural_person'
        ]
    );

} 

function sg_callback_apfnp_additional_percentage_for_natural_person() {
    $apfnp_additional_percentage_for_natural_person = get_option( 'apfnp_additional_percentage_for_natural_person', '' ); 
    echo '<input id="apfnp_additional_percentage_for_natural_person" type="number" min="0" max="100" name="apfnp_additional_percentage_for_natural_person" value="' . $apfnp_additional_percentage_for_natural_person . '" /> %'; 
}
