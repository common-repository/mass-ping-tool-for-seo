<?php
namespace Pagup\MassPingTool\Traits;

trait Helpers {
    /**
     * Save log to database on save_post or using forced ping option.
     * @param int $post_id The Post ID.
     * @param array $data Data need to be saved in log array.
     */
    public function save_log(int $post_id, array $data, int $time = 0)
    {
        
        global $wpdb;

        $log_table = MPT_LOG_TABLE;

        $item = $wpdb->get_row( "SELECT * FROM $log_table WHERE post_id = $post_id" );

        $log_data = [
            'post_id' => $post_id,
            'title' => get_the_title( $post_id ),
            'time' => $time,
            'data' =>  maybe_serialize( [ $data ] ),
        ];

        if ( $item === NULL ) {

            $result = $wpdb->insert( 
                $log_table, 
                $log_data,
                array( '%d', '%s', '%s', '%s' )
            );
        
        } else {

            $item_data = (is_serialized($item->data)) ? unserialize($item->data) : [];
            array_push($item_data, $data);
            $item->data = maybe_serialize($item_data);

            $update_data = [
                'title' => get_the_title( $post_id ),
                'time' => $time,
                'data' => $item->data
            ];
            
            $wpdb->update( 
                $log_table, 
                $update_data,
                array( 
                    'post_id' => $post_id
                ),
                array( '%s', '%s', '%s' ),
                array( '%d' )
            );
        }
    }

    /**
     * Schedule single event and save log for mass ping
     * @param int $post_id Post ID.
     * @param mix $time unix time stamp or datetime
     * @param array $log Log which need to be saved.
     */
    public function schedule_event($post_id, $time, $log = [])
    {
        $unixtimestamp = is_string($time) ? strtotime($time) : $time;

        wp_schedule_single_event( $unixtimestamp, 'mass_ping', array($post_id));

        if ( !empty($log) ) {
            $this->save_log($post_id, $log, $unixtimestamp);
        }

    }

    /**
     * Merge default services and mass ping services list
     * @return array list of all services
     */
    public function services()
	{
		$services = get_option( 'ping_sites' );
        $services = explode( "\n", $services );

        $mpt_services = get_option( 'mpt-ping-services' );
        $mpt_services = (isset($mpt_services->data) && !empty($mpt_services->data)) ? $mpt_services->data : [];
        
        foreach ($services as $service) {
            if ( '' !== $service && !in_array($service, $mpt_services) ) {
                array_push($mpt_services, $service);
            }
        }

		return $mpt_services;
	}

    public function excessive_ping()
    {
        $current_time = intval( strtotime(current_time('mysql')) );
        $last_ping_time = $current_time - 540;

        $do_ping = $current_time - $last_ping_time;
        $time_limit = 11 * 60;

        $ping_limit = 4;
        $pinged_so_far = 3;

        if ($ping_limit > $pinged_so_far) {
            
            if ( $time_limit >= $do_ping  ) {
                // return "Yes. We have " . ($time_limit - $do_ping) . " seconds left";
                return false;
            } else {
                // return "No. Time limit is over";
                return true;
            }

        } else {
            // return "You've reached pinging limit";
            return true;
        }
    }

    public function cpts( $excludes = [] ) {
        // All CPTs.
        $post_types = get_post_types( array(
            'public'   => true,
        ), 'objects' );
        // remove Excluded CPTs from All CPTs.
        foreach($excludes as $exclude)
        unset($post_types[$exclude]);

        $types = [];
        foreach ( $post_types as $post_type ) {
            $label = get_post_type_labels( $post_type );
            $types[$label->name] = $post_type->name;
        }

        return $types;
    }

    public function in_log($id, $array) {
        foreach ($array as $key => $val) {
            if ($val['post_id'] === $id) {
                return $key;
            }
        }
        return null;
    }
}