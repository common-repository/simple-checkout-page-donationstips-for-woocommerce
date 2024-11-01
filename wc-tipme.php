<?php

/*
 Plugin Name: Simple checkout page donations/tips for WooCommerce
 Plugin URI: https://profiles.wordpress.org/rynald0s
 Description: This plugin lets you add some doantion options to the checkout page.
 Author: Rynaldo Stoltz
 Author URI: http:rynaldo.com
 Version: 1.3
 License: GPLv3 or later License
 URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

function wc_tipme_section( $sections ) {
    $sections['wc_tipme_section'] = __( 'Simple donations/tips for WooCommerce', 'woocommerce' );
    return $sections;
}

add_filter( 'woocommerce_get_sections_products', 'wc_tipme_section' );


function wc_tipme_settings( $settings, $current_section ) {

    if ( 'wc_tipme_section' === $current_section ) {

        $wc_tipme_settings[] = array( 'title' => __( 'Tips', 'woocommerce' ), 'type' => 'title', 'id' => 'wc_tipme' );

        $wc_tipme_settings[] = array(
                'name'     => __( 'Enable ', 'woocommerce' ),
                'type' => 'checkbox',
                'desc'     => __( 'Enable the plugin', 'woocommerce' ),
                'id'       => 'wc_tipme_enable',
                'desc_tip' => true
            );

        $wc_tipme_settings[] = array(
                'name'     => __( 'Label ', 'woocommerce' ),
                'type' => 'text',
                'desc'     => __( 'Add your own label, such as "Want to give me a tip?"', 'woocommerce' ),
                'id'       => 'wc_tipme_label',
            );

        $wc_tipme_settings[] = array(
                'title'    => __( 'Tip option 1', 'woocommerce' ),
                'desc' => __( 'Add your preferred tip amount here', 'woocommerce' ),
                'id'       => 'wc_tipme_option_1',
                'type'     => 'text',
                'placeholder' => '2',
                'css'      => 'min-width:250px;',
            );

        $wc_tipme_settings[] = array(
                'title'    => __( 'Tip option 2', 'woocommerce' ),
                'desc' => __( 'Add your preferred tip amount here', 'woocommerce' ),
                'id'       => 'wc_tipme_option_2',
                'type'     => 'text',
                'placeholder' => '5',
                'css'      => 'min-width:350px;',
            );

        $wc_tipme_settings[] = array(
                'title'    => __( 'Tip option 3', 'woocommerce' ),
                'desc' => __( 'Add your preferred tip amount here', 'woocommerce' ),
                'id'       => 'wc_tipme_option_3',
                'type'     => 'text',
                'placeholder' => '10',
                'css'      => 'min-width:350px;',
            );

        $wc_tipme_settings[] = array( 'type' => 'sectionend', 'id' => 'wc_tipme' );
        return $wc_tipme_settings;
} else {
        return $settings;
    }

}

if( get_option('wc_tipme_enable', true )=='yes') {
  
add_action( 'woocommerce_review_order_before_payment', 'wc_tipme_radio_choice' );
  
function wc_tipme_radio_choice() {
     
   $chosen = WC()->session->get( 'radio_chosen' );
   $chosen = empty( $chosen ) ? WC()->checkout->get_value( 'radio_choice' ) : $chosen;
   $chosen = empty( $chosen ) ? '0' : $chosen;
   $chosen = '0';
        
   $args = array(
   'type' => 'radio',
   'class' => array( 'form-row-wide', 'update_totals_on_change' ),
   'options' => array( get_option('wc_tipme_option_1') => ('$') . get_option('wc_tipme_option_1'), get_option('wc_tipme_option_2') => ('$') . get_option('wc_tipme_option_2'), get_option('wc_tipme_option_3') => ('$') . get_option('wc_tipme_option_3'), '0' => 'No thanks',),
   'default' => $chosen
   );
     
   echo '<div id="checkout-radio">';
   echo '<h4>' . get_option('wc_tipme_label') . '</h4>';
   woocommerce_form_field( 'radio_choice', $args, $chosen );
   echo '</div>';
     
}

add_action( 'woocommerce_cart_calculate_fees', 'wc_tipme_checkout_radio_choice_fee', 20, 1 );
  
function wc_tipme_checkout_radio_choice_fee( $cart ) {
   
   if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
    
   $radio = WC()->session->get( 'radio_chosen' );
     
   if ( $radio ) {
      $cart->add_fee( 'Tip', $radio );
   }
   
}

add_action( 'woocommerce_checkout_update_order_review', 'wc_tipme_checkout_radio_choice_set_session' );
  
function wc_tipme_checkout_radio_choice_set_session( $posted_data ) {
    parse_str( $posted_data, $output );
    if ( isset( $output['radio_choice'] ) ){
        WC()->session->set( 'radio_chosen', $output['radio_choice'] );
        }
    }
}

function wc_tipme_adding_styles() {
wp_register_style('tipme', plugins_url('/assets/css/tipme.css', __FILE__));
wp_enqueue_style('tipme');
}

add_action( 'wp_enqueue_scripts', 'wc_tipme_adding_styles' );  
add_filter( 'woocommerce_get_settings_products','wc_tipme_settings', 10, 2 );
}
