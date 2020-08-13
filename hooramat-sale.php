<?php
/**
 * @package hooramat-sale
 * @version 1.0
 */
/*
Plugin Name: hooramat sale
Plugin URI: https://github.com/omidshj/hooramat-sale.git
Description: sale plugin for hooramat theme.
Author: hooraweb
Version: 1.0
Author URI: http://hooraweb.com
Text Domain: hooraweb
*/

include ('services.php');
include ('orders.php');
include ('shortcode.php');


function hooramat_sale_activation() {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $charset_collate = $wpdb->get_charset_collate();
	    $sql = "
            CREATE TABLE {$wpdb->prefix}hooramat_sale_groups (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                name varchar(255) NULL,
                description varchar(255) NULL,
                start datetime,
                finish datetime,
                PRIMARY KEY (id)
            ) $charset_collate;

            CREATE TABLE {$wpdb->prefix}hooramat_sale_services (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                group_id mediumint(9) NOT NULL,
                sort mediumint(9) NULL,
                name varchar(255) NULL,
                description varchar(255) NULL,
                total mediumint(9),
                price mediumint(9),
                sale mediumint(9),
                sort mediumint(9) NULL,
                media int(11) NULL,
                PRIMARY KEY (id)
            ) $charset_collate;
    
            CREATE TABLE {$wpdb->prefix}hooramat_sale_coupons (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                group_id mediumint(9) NOT NULL,
                code varchar(255) NULL,
                description varchar(255) NULL,
                total mediumint(9),
                percent mediumint(9),
                discount mediumint(9),
                PRIMARY KEY (id)
            ) $charset_collate;

            CREATE TABLE {$wpdb->prefix}hooramat_sale_orders (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                code mediumint(9) NULL,
                first_name varchar(255) NULL,
                last_name varchar(255) NULL,
                mobile varchar(255) NULL,
                area varchar(255) NULL,
                services text NULL,
                cost mediumint(9) NULL,
                payment_time datetime NULL,
                payment_code mediumint(9) NULL,
                PRIMARY KEY (id)
	        ) $charset_collate;
        ";

        dbDelta( $sql );
}
register_activation_hook( __FILE__, 'hooramat_sale_activation' );