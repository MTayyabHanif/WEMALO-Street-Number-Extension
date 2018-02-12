<?php
/**
* Plugin Name: WEMALO Street Number Extension
* Plugin URI: https://www.trainsane.shop/
* Description: An extension for WEMALO to add Street Number on checkout page of woocommerce.
* Version: 1.0
* Author: Tayyab Hanif
* Author URI: https://tayyabhanif.me/
* Developer: Tayyab Hanif
* Developer URI: https://tayyabhanif.me/
* Text Domain: wemalo-street-number-extension
*
* WC requires at least: 3.0
* WC tested up to: 3.3.1
*
* Copyright: © 2009-2018 WooCommerce.
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}







/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    add_filter( 'woocommerce_default_address_fields', 'woo_new_default_address_fields' );

    function woo_new_default_address_fields( $fields ) {

        $fields['street_number'] = array(

            'label' => __( 'Street number', 'woocommerce' ),
            'type' => 'text',
            'required'  => true,
            'class' => array( 'form-row-wide street_number', 'update_totals_on_change' ),

        );

        $order = array(
            "first_name",
            "last_name",
            "company",
            "country",
            "address_1",
            "street_number",
            "address_2",
            "city",
            "state",
            "postcode"
        );

        foreach($order as $field) {

            $ordered_fields[$field] = $fields[$field];

        }

        $fields = $ordered_fields;

        return $fields;

    }

    add_filter( 'woocommerce_order_formatted_billing_address' , 'woo_custom_order_formatted_billing_address', 10, 2 );
    add_filter( 'wc_get_account_formatted_address' , 'woo_custom_order_formatted_billing_address', 10, 2 );

    function woo_custom_order_formatted_billing_address( $address, $WC_Order ) {

        $address = array(
            'first_name'    => $WC_Order->billing_first_name,
            'last_name'     => $WC_Order->billing_last_name,
            'company'       => $WC_Order->billing_company,
            'address_1'     => $WC_Order->billing_address_1,
            'street_number' => $WC_Order->billing_street_number,
            'address_2'     => $WC_Order->billing_address_2,
            'city'          => $WC_Order->billing_city,
            'state'         => $WC_Order->billing_state,
            'postcode'      => $WC_Order->billing_postcode,
            'country'       => $WC_Order->billing_country
        );

        return $address;

    }

    add_filter( 'woocommerce_formatted_address_replacements', function( $replacements, $args ){

        $replacements['{street_number}'] = $args['street_number'];
        return $replacements;

    }, 10, 2 );

    add_filter( 'woocommerce_localisation_address_formats' , 'woo_includes_address_formats', 10, 1);

    function woo_includes_address_formats($address_formats) {

        $address_formats['ES'] = "{name}\n{company}\n{address_1} (Street number: {street_number})\n{address_2}\n{postcode} {city}\n{state}\n{country}";
        $address_formats['default'] = "{name}\n{company}\n{address_1} (Street number: {street_number})\n{address_2}\n{city}\n{state}\n{postcode}\n{country}";

        return $address_formats;

    }

    add_filter( 'woocommerce_my_account_my_address_formatted_address', 'custom_my_account_my_address_formatted_address', 10, 3 );
    function custom_my_account_my_address_formatted_address( $fields, $customer_id, $type ) {
        // if ( $type == 'billing' ) {
            $fields['street_number'] = get_user_meta($customer_id, "billing_street_number")[0];
        // }
        return $fields;
    }

}
