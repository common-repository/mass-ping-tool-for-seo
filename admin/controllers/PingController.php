<?php

namespace Pagup\MassPingTool\Controllers;

use  Pagup\MassPingTool\Traits\Helpers ;
class PingController
{
    use  Helpers ;
    public  $log = array() ;
    public  $post_title ;
    public  $post_url ;
    /**
     * Modified version of WordPress "generic_ping" function.
     * @param int $post_id The post ID.
     */
    public function send_ping( int $post_id = 0 )
    {
        if ( $post_id == 0 ) {
            return;
        }
        $post = get_post( $post_id );
        $mass_ping_log = get_option( 'mass_ping_log' );
        $log = ( !empty($mass_ping_log) ? $mass_ping_log : [] );
        
        if ( isset( $post ) ) {
            // Perform pingback
            delete_post_meta( $post_id, '_pingme' );
            pingback( $post->post_content, $post_id );
            // Perform enclosure
            delete_post_meta( $post_id, '_encloseme' );
            do_enclose( $post->post_content, $post_id );
            // Perform trackback
            delete_post_meta( $post_id, '_trackbackme' );
            do_trackbacks( $post_id );
            $services = $this->services();
            foreach ( $services as $service ) {
                $service = trim( $service );
                
                if ( '' !== $service ) {
                    $this->weblog_ping( $post_id, $service );
                    $this->save_log( $post_id, $this->log );
                }
            
            }
        }
        
        return $post_id;
    }
    
    /**
     * Modified version of WordPress weblog_ping function.
     * @param string $server Host of blog to connect to.
     * @param string $path Path to send the ping.
     */
    public function weblog_ping( $post_id, $server = '', $path = '' )
    {
        include_once ABSPATH . WPINC . '/class-IXR.php';
        include_once ABSPATH . WPINC . '/class-wp-http-ixr-client.php';
        // Using a timeout of 3 seconds should be enough to cover slow servers.
        $client = new \WP_HTTP_IXR_Client( $server, ( !strlen( trim( $path ) ) || '/' === $path ? false : $path ) );
        $client->timeout = 3;
        $client->useragent .= ' -- WordPress/' . get_bloginfo( 'version' );
        // When set to true, this outputs debug messages by itself.
        $client->debug = false;
        // Data for $client->query
        $post_title = get_the_title( $post_id );
        $post_title = ( $post_title != "" ? $post_title : get_option( 'blogname' ) );
        $home = trailingslashit( home_url() );
        $post_url = get_permalink( $post_id );
        $post_url = ( $post_url != false ? $post_url : $home );
        $rss_url = get_bloginfo( 'rss2_url' );
        
        if ( $client->query(
            'weblogUpdates.extendedPing',
            $post_title,
            $post_url,
            $rss_url
        ) ) {
            $this->log = [
                'server'  => $server,
                'message' => "Successfully pinged",
                'time'    => time(),
                'status'  => 'success',
            ];
        } elseif ( $client->query( 'weblogUpdates.ping', $post_title, $post_url ) ) {
            $this->log = [
                'server'  => $server,
                'message' => "Successfully pinged",
                'time'    => time(),
                'status'  => 'success',
            ];
        } else {
            $this->log = [
                'server'  => $server,
                'message' => "Ping failed. Error: {$client->error->message}",
                'time'    => time(),
                'status'  => 'failed',
            ];
        }
    
    }
    
    public function api( $url )
    {
        $response = wp_remote_get( $url );
        $output = wp_remote_retrieve_body( $response );
        return json_decode( $output );
    }
    
    public function check_services()
    {
        $api = $this->api( 'https://agenceseo.ca/wp-json/mass-ping/v1/services' );
        $last_synced = get_option( 'mpt-last-update' );
        
        if ( isset( $last_synced ) && !empty($last_synced) ) {
            if ( $api->updated_at > $last_synced ) {
                update_option( 'mpt-update-require', true );
            }
        } else {
            update_option( 'mpt-ping-services', $api );
            update_option( "mpt-last-update", time() );
            update_option( 'mpt-update-require', false );
        }
    
    }

}