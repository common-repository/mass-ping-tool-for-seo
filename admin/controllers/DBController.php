<?php
namespace Pagup\MassPingTool\Controllers;

class DBController {

    private $table_log = MPT_LOG_TABLE;
    private $db_version = '1.0.0';

    public function migration()
    {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();

        $table_log = $this->db($this->table_log, $charset_collate);

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $table_log );

        add_option( 'mpt_db_version', $this->db_version );

        $installed_ver = get_option( "mpt_db_version" );

        if ( $installed_ver != $this->db_version ) {

            // Modify Table
            // $row = $wpdb->get_row("SELECT * FROM $this->table_log");

            // if(isset($row->url)){
            //     $wpdb->query("ALTER TABLE $this->table_log CHANGE url time bigint(20) unsigned DEFAULT 0 NOT NULL;");
            // }
            
            $row = $wpdb->get_row("SELECT * FROM $this->table_log");

            if(!isset($row->time)){
                $wpdb->query("ALTER TABLE $this->table_log time bigint(20) unsigned DEFAULT 0 NOT NULL;");
            }

            $table_log = $this->db($this->table_log);
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $table_log );

            update_option( "mpt_db_version", $this->db_version );
        }
    }

    public function db($table, $charset_collate = "")
    {
        return "CREATE TABLE $table (
            log_id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id INT UNSIGNED,
            title text,
            data longtext,
            time bigint(20) unsigned DEFAULT 0 NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (log_id)
        ) $charset_collate;";
    }

    public function db_check() {
        if ( get_site_option( 'mpt_db_version' ) != $this->db_version ) {
            $this->migration();
        }
    }

}

$database = new DBController();