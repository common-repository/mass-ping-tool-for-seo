<?php
namespace Pagup\MassPingTool\Controllers;

use Pagup\MassPingTool\Core\Option;
use Pagup\MassPingTool\Core\Plugin;
use Pagup\MassPingTool\Traits\Helpers;

class MetaboxController
{
    use Helpers;

    public $private_to_publish = false;

    public function add_metabox() {

        add_meta_box(
            'mpt__ping_log', // id, used as the html id att
            __( 'Mass Ping Log (last 10 entries)' ), // meta box title
            array( &$this, 'metabox' ), // callback function, spits out the content
            'post', // post type or page. This adds to posts only
            'normal', // context, where on the screen
            'high' // priority, where should this go in the context
        );
    }

    public function metabox ( $post ) {

        global $wpdb;
        $log_table = MPT_LOG_TABLE;
        $log = $wpdb->get_row( "SELECT * FROM $log_table WHERE post_id = $post->ID" );

        if ( $log !== NULL ) {
            
            $logs = maybe_unserialize($log->data);
            $logs = array_slice($logs, -10, 10, true);
            $logs = array_reverse($logs);

            $data = [
                'logs' => $logs,
                'updated_at' => $log->updated_at
            ];

        } else {
            $data = [];
        }

        return Plugin::view('metabox', $data);
        
    }

    public function save_post_data($post_id, $post, $update)
    {
        // SKIP IF DOING_AUTOSAVE OR USER DON'T HAVE EDIT_PAGE PERMISSIONS
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
        if ( !current_user_can( 'edit_page', $post_id ) ) { return; }

        $services = $this->services();

        // Return if Pinging disabled, Status is Draft, Title is empty or Ping sites list is empty.
        if ( $post->post_status == 'draft' || empty($post->post_title) || empty($services) ) return;

        // Return if post type is not selected.
        $allowed_post_types = Option::check('post_types') ? Option::get('post_types') : [];
        if ( !in_array( get_post_type($post_id), $allowed_post_types ) ) { return; }

        // Return if ping is disabled from settings page
        $ping_enable = Option::check('enable') ? true : false;
        if ( !$ping_enable ) {
            $log = [ "message" => 'Ping is disabled by administrator', "time" => time() ];
            $this->save_log($post_id, $log);
            return;
        }

        // On publish, Ping it.
        if ( isset( $_POST['publish'] ) ) {

            // Checking post status because WordPress creates post with "inehrit" status first and then post with "publish" status. Which ping twice & generates log twice
            if (get_post_status($post_id) === 'publish') {

                // If post date is bigger than current time then it's a scheduled post
                if ( isset($post->post_date) && $post->post_date > current_time('mysql') ) {

                    $log = [ "message" => "Scheduled post: This will be pinged at {$post->post_date}" , "time" => time()];
                    $this->schedule_event($post_id, $post->post_date, $log);
                    return;
                    
                }

                else {

                    // Ping it.
                    wp_schedule_single_event( time(), 'mass_ping', array($post_id), true);
                    return;
    
                }

            }

        }

        elseif ( $post->post_status === 'trash') {
            
            wp_unschedule_event(strtotime($post->post_date), 'mass_ping', array($post_id));
            $log = [ "message" => 'Post trashed', "time" => time() ];
            $this->save_log($post_id, $log);
            return;

        }

        // Not pinging, If post is an update
        elseif ( $update && $this->private_to_publish == false && $post->post_date <= current_time('mysql') ) {
            
            $log = [ "message" => 'Post edited: Not pinging', "time" => time() ];
            $this->save_log($post_id, $log);
            return;

        }

        // If post is an update but got scheduled, Ping it.
        if ( $update && $post->post_date > current_time('mysql') ) {

            $data = [ "message" => "Scheduled post: This will be pinged at {$post->post_date}", "time" => time() ];
            $this->schedule_event($post_id, $post->post_date, $data);
            return;

            // $last_time = 1653158057;
            // $time = intval(strtotime(current_time('mysql'))) - 1653158057;
            // exit( var_dump( $this->excessive_ping() ) );
        }

        // If post is an update but status changed from private to publish, Ping it.
        elseif ( $update && $this->private_to_publish ) {

            // If post date is bigger than current time then it's a scheduled post
            if ( $post->post_date > current_time('mysql') ) {

                $log = [ "message" => "Status changed from Private to Publish. Scheduled post: This will be pinged at {$post->post_date}" ];
                $this->schedule_event($post_id, $post->post_date, $log);
                return;
            }
            
            else {

                $log = [ "message" => 'Status changed from Private to Publish. This will be pinged', "time" => time() ];
                $this->save_log($post_id, $log, time());
                wp_schedule_single_event( time(), 'mass_ping', array($post_id), true);
                return;

            }

        }

    }

    public function on_imported_product($product, $data){
        //$product is a WC_Product
        //$data is an array of data pulled from the CSV
        // var_dump($product);
        // return;

        $log = [ "message" => 'Product imported: Not pinging', "time" => time() ];
        $this->save_log($product->id, $log);
        // return;
    
    }

    public function on_post_status_change($new_status, $old_status)
    {
        if ($old_status == "private" && $new_status == "publish") {
            $this->private_to_publish = true;
        }
    }

    public function on_delete_post($post_id, $post)
    {
        global $wpdb;

        $log_table = MPT_LOG_TABLE;

        wp_unschedule_event(strtotime($post->post_date), 'mass_ping', array($post_id));

        $wpdb->delete(
            $log_table, 
            array(
                'post_id' => $post_id
            )
        );
    }

}