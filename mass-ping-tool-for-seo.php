<?php

/*
* Plugin Name: Mass Ping Tool for SEO
* Description: Mass Ping Tool for SEO
* Author: Pagup
* Version: 1.0.3
* Author URI: https://pagup.ca/
* Text Domain: mass-ping-tool-for-seo
* Domain Path: /languages/
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'mpt__fs' ) ) {
    mpt__fs()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( 'mpt__fs' ) ) {
        if ( !defined( 'MPT_NAME' ) ) {
            define( 'MPT_NAME', "mass-ping-tool-for-seo" );
        }
        if ( !defined( 'MPT_PLUGIN_BASE' ) ) {
            define( 'MPT_PLUGIN_BASE', plugin_basename( __FILE__ ) );
        }
        if ( !defined( 'MPT_PLUGIN_DIR' ) ) {
            define( 'MPT_PLUGIN_DIR', plugins_url( '', __FILE__ ) );
        }
        if ( !defined( 'MPT_LOG_TABLE' ) ) {
            define( 'MPT_LOG_TABLE', $GLOBALS['wpdb']->prefix . "mass_ping_log" );
        }
        require_once 'vendor/autoload.php';
        // Create a helper function for easy SDK access.
        function mpt__fs()
        {
            global  $mpt__fs ;
            
            if ( !isset( $mpt__fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';
                $mpt__fs = fs_dynamic_init( array(
                    'id'             => '10411',
                    'slug'           => 'mass-ping-tool-for-seo',
                    'premium_slug'   => 'mass-ping-tool-for-seo-pro',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_6b82778df5754f6fd1a36ce562456',
                    'is_premium'     => false,
                    'premium_suffix' => 'Pro',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                    'days'               => 7,
                    'is_require_payment' => true,
                ),
                    'menu'           => array(
                    'slug'       => 'mass-ping-tool-for-seo',
                    'first-path' => 'admin.php?page=mass-ping-tool-for-seo',
                    'support'    => false,
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $mpt__fs;
        }
        
        // Init Freemius.
        mpt__fs();
        // Signal that SDK was initiated.
        do_action( 'mpt__fs_loaded' );
        function mpt__fs_settings_url()
        {
            return admin_url( 'admin.php?page=' . MPT_NAME );
        }
        
        mpt__fs()->add_filter( 'connect_url', 'mpt__fs_settings_url' );
        mpt__fs()->add_filter( 'after_skip_url', 'mpt__fs_settings_url' );
        mpt__fs()->add_filter( 'after_connect_url', 'mpt__fs_settings_url' );
        mpt__fs()->add_filter( 'after_pending_connect_url', 'mpt__fs_settings_url' );
        function mpt__fs_custom_icon()
        {
            return dirname( __FILE__ ) . '/admin/assets/icon.jpg';
        }
        
        mpt__fs()->add_filter( 'plugin_icon', 'mpt__fs_custom_icon' );
        // freemius opt-in
        function mpt__fs_custom_connect_message(
            $message,
            $user_first_name,
            $product_title,
            $user_login,
            $site_link,
            $freemius_link
        )
        {
            $break = "<br><br>";
            $more_plugins = '<p><a target="_blank" href="https://wordpress.org/plugins/meta-tags-for-seo/">Meta Tags for SEO</a>, <a target="_blank" href="https://wordpress.org/plugins/mass-ping-tool-for-seo/">Auto internal links for SEO</a>, <a target="_blank" href="https://wordpress.org/plugins/bulk-image-alt-text-with-yoast/">Bulk auto image Alt Text</a>, <a target="_blank" href="https://wordpress.org/plugins/bulk-image-title-attribute/">Bulk auto image Title Tag</a>, <a target="_blank" href="https://wordpress.org/plugins/mobilook/">Mobile view</a>, <a target="_blank" href="https://wordpress.org/plugins/better-robots-txt/">Wordpress Better-Robots.txt</a>, <a target="_blank" href="https://wordpress.org/plugins/wp-google-street-view/">Wp Google Street View</a>, <a target="_blank" href="https://wordpress.org/plugins/vidseo/">VidSeo</a>, ...</p>';
            return sprintf( esc_html__( 'Hey %1$s, %2$s Click on Allow & Continue to boost your website using our Mass Ping Tool plugin for SEO. You have no idea how much this plugin will simplify your life. %2$s Never miss an important update -- opt-in to our security and feature updates notifications. %2$s See you on the other side. Thanks', 'mass-ping-tool-for-seo' ), $user_first_name, $break ) . $more_plugins;
        }
        
        mpt__fs()->add_filter(
            'connect_message',
            'mpt__fs_custom_connect_message',
            10,
            6
        );
    }
    
    class MassPingforSEO
    {
        function __construct()
        {
            $database = new \Pagup\MassPingTool\Controllers\DBController();
            // register_activation_hook( __FILE__, array( &$database, 'migration' ) );
            register_activation_hook( __FILE__, array( &$this, 'activate' ) );
            register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
            add_action( 'plugins_loaded', array( &$database, 'db_check' ) );
            add_action( 'init', array( &$this, 'mpt__textdomain' ) );
            remove_action( 'do_pings', 'do_all_pings' );
            remove_action( "publish_post", "generic_ping" );
            $ping = new \Pagup\MassPingTool\Controllers\PingController();
            add_action(
                'mass_ping',
                array( &$ping, 'send_ping' ),
                10,
                1
            );
            add_action( 'mpt_sync', array( &$ping, 'check_services' ) );
        }
        
        public function activate()
        {
            $database = new \Pagup\MassPingTool\Controllers\DBController();
            $database->migration();
            wp_schedule_event( time(), 'daily', 'mpt_sync' );
            $options = get_option( 'mass-ping-tool-for-seo' );
            if ( !is_array( $options ) ) {
                update_option( 'mass-ping-tool-for-seo', [
                    "enable"          => 'allow',
                    "post_types"      => [ 'post', 'page' ],
                    "remove_settings" => false,
                ] );
            }
        }
        
        public function deactivate()
        {
            wp_clear_scheduled_hook( 'mpt_sync' );
            if ( \Pagup\MassPingTool\Core\Option::check( 'remove_settings' ) ) {
                delete_option( 'automatic-internal-links-for-seo' );
            }
        }
        
        function mpt__textdomain()
        {
            load_plugin_textdomain( "mass-ping-tool-for-seo", false, basename( dirname( __FILE__ ) ) . '/languages' );
        }
    
    }
    $mpt = new MassPingforSEO();
    /*-----------------------------------------
                          Settings
      ------------------------------------------*/
    if ( is_admin() ) {
        include_once 'admin/Settings.php';
    }
}

