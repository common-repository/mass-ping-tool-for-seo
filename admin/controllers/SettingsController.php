<?php

namespace Pagup\MassPingTool\Controllers;

use  Pagup\MassPingTool\Core\Option ;
use  Pagup\MassPingTool\Core\Plugin ;
use  Pagup\MassPingTool\Core\Request ;
use  Pagup\MassPingTool\Traits\Helpers ;
class SettingsController
{
    use  Helpers ;
    protected  $log_table = MPT_LOG_TABLE ;
    public function add_settings()
    {
        add_menu_page(
            __( 'Mass Ping Tool for SEO', 'mass-ping-tool-for-seo' ),
            __( 'Mass Ping Tool', 'mass-ping-tool-for-seo' ),
            'manage_options',
            MPT_NAME,
            array( &$this, 'page' ),
            'dashicons-rss'
        );
    }
    
    public function page()
    {
        global  $wpdb ;
        $safe = [ "allow", "settings", "faq" ];
        
        if ( isset( $_POST['update'] ) ) {
            // check if user is authorised
            if ( function_exists( 'current_user_can' ) && !current_user_can( 'manage_options' ) ) {
                die( 'Sorry, not allowed...' );
            }
            check_admin_referer( 'mpt__settings', 'mpt__nonce' );
            if ( !isset( $_POST['mpt__nonce'] ) || !wp_verify_nonce( $_POST['mpt__nonce'], 'mpt__settings' ) ) {
                die( 'Sorry, not allowed. Nonce doesn\'t verify' );
            }
            // $errors = [];
            // if (Request::numeric('limit_number') === NULL) {
            //     array_push($errors, "Limit number must a integer");
            // }
            // if (Request::numeric('limit_time') === NULL) {
            //     array_push($errors, "Limit time must a integer");
            // }
            // if (!empty($errors)) {
            //     echo  '<div class="notice notice-error is-dismissible"><p><strong>';
            //     foreach ($errors as $error) {
            //         echo "<div>".$error."</div>";
            //     }
            //     echo '</strong></p></div>';
            // }
            $options = [
                'enable'          => Request::post( 'enable', $safe ),
                'post_types'      => Request::array( $_POST['post_types'] ),
                'limit'           => Request::post( 'limit', $safe ),
                'remove_settings' => Request::post( 'remove_settings', $safe ),
            ];
            update_option( 'mass-ping-tool-for-seo', $options );
            $ping_list = sanitize_textarea_field( $_POST['ping_sites'] );
            update_option( "ping_sites", $ping_list );
            // update options
            echo  '<div class="notice notice-success is-dismissible"><p><strong>' . esc_html__( 'Settings saved.', 'mass-ping-tool-for-seo' ) . '</strong></p></div>' ;
        }
        
        $ping = new \Pagup\MassPingTool\Controllers\PingController();
        
        if ( isset( $_POST['update_services'] ) ) {
            $api = $ping->api( 'https://agenceseo.ca/wp-json/mass-ping/v1/services' );
            update_option( 'mpt-ping-services', $api );
            update_option( "mpt-last-update", time() );
            echo  '<div class="notice notice-success is-dismissible"><p><strong>' . esc_html__( 'Servers list updated successfully', 'mass-ping-tool-for-seo' ) . '</strong></p></div>' ;
        }
        
        // PING NOW
        
        if ( isset( $_POST['pinging'] ) ) {
            // check if user is authorised
            if ( function_exists( 'current_user_can' ) && !current_user_can( 'manage_options' ) ) {
                die( 'Sorry, not allowed...' );
            }
            $pingme_posts = $wpdb->get_results( "SELECT id, meta_key FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->posts}.id = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '_pingme'" );
            
            if ( !empty($pingme_posts) ) {
                $i = 0;
                foreach ( $pingme_posts as $post ) {
                    $i = $i + 60;
                    $time = time() + $i;
                    $log = [
                        "message" => "Forced Ping: This will be pinged at {$time}",
                    ];
                    $this->schedule_event( $post->id, $time, $log );
                    // wp_schedule_single_event( $time, 'mass_ping', array($post->id), true);
                }
                echo  '<div class="notice notice-success is-dismissible"><p style="font-size: 18px;"><strong>' . count( $pingme_posts ) . esc_html__( ' Post(s) are scheduled to ping (forced), each after 1 minute.', 'mass-ping-tool-for-seo' ) . '</strong></p></div>' ;
            } else {
                echo  '<div class="notice notice-success is-dismissible"><p style="font-size: 18px;"><strong>' . esc_html__( ' Nothing to ping at the moment.', 'mass-ping-tool-for-seo' ) . '</strong></p></div>' ;
            }
        
        }
        
        $ping_sites = get_option( "ping_sites" );
        $options = new Option();
        // Get logs for Ping Log Report
        $logs = $wpdb->get_results( "SELECT * FROM {$this->log_table} ORDER BY updated_at DESC", OBJECT );
        // Get Scheduled posts from Logs table
        $time = time();
        $scheduled_posts = $wpdb->get_results( "SELECT * FROM {$this->log_table} WHERE time > {$time}" );
        //Set active class for navigation tabs
        $active_tab = ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], $safe ) ? sanitize_key( $_GET['tab'] ) : 'settings' );
        $get_pro = sprintf( wp_kses( __( '<a href="%s">Get Pro version</a> to enable', 'mass-ping-tool-for-seo' ), array(
            'a' => array(
            'href'   => array(),
            'target' => array(),
        ),
        ) ), esc_url( 'admin.php?page=' . MPT_NAME . '-pricing' ) );
        $services = get_option( 'mpt-ping-services' );
        $pro_count = ( isset( $services->pro_services ) && !empty($services->pro_services) ? $services->pro_services : "" );
        $free_count = ( isset( $services->free_services ) && !empty($services->free_services) ? $services->free_services : "" );
        $post_types = $this->cpts( [ 'attachment' ] );
        // $mpt_ping_cron = array_column(_get_cron_array(), 'mass_ping');
        // print("<pre>".print_r($mpt_ping_cron, true)."</pre>");
        // print("<pre>".print_r(_get_cron_array(), true)."</pre>");
        if ( $active_tab == 'settings' ) {
            return Plugin::view( 'settings', compact(
                'active_tab',
                'ping_sites',
                'options',
                'post_types',
                'logs',
                'scheduled_posts',
                'services',
                'pro_count',
                'free_count',
                'get_pro'
            ) );
        }
        if ( $active_tab == 'faq' ) {
            return Plugin::view( "faq", compact( 'active_tab' ) );
        }
    }

}