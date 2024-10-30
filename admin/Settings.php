<?php
namespace Pagup\MassPingTool;

use Pagup\MassPingTool\Core\Asset;

class Settings {

    public function __construct()
    {

        $settings = new \Pagup\MassPingTool\Controllers\SettingsController;
        $metabox = new \Pagup\MassPingTool\Controllers\MetaboxController;

        // Add settings page
        add_action( 'admin_menu', array( &$settings, 'add_settings' ) );

        // Add metabox to psot
        add_action( 'add_meta_boxes', array(&$metabox, 'add_metabox') );

        // Add setting link to plugin page
        $plugin_base = MPT_PLUGIN_BASE;
        add_filter( "plugin_action_links_{$plugin_base}", array( &$this, 'setting_link' ) );
        
        // Add styles and scripts
        add_action( 'admin_enqueue_scripts', array( &$this, 'assets') );

        

        $metabox = new \Pagup\MassPingTool\Controllers\MetaboxController;
        add_action('save_post', array(&$metabox, 'save_post_data'), 20, 3);
        add_action('woocommerce_product_import_inserted_product_object', array(&$metabox, 'on_imported_product'), 20, 2);
        add_action('delete_post', array(&$metabox, 'on_delete_post'), 20, 2);
        add_action('transition_post_status', array(&$metabox, 'on_post_status_change'), 20, 3);

        
        // add_action('mass_ping', array(&$ping, 'test_hook'), 10, 1);
        // global $post_id;
        // do_action('mass_ping', $post_id);

        
    }

    public function setting_link( $links ) {

        array_unshift( $links, '<a href="/wp-admin/admin.php?page='.MPT_NAME.'">Settings</a>' );

        return $links;
    }

    public function assets() {

        Asset::style_remote('mpt__font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');
        Asset::style('mpt__flexboxgrid', 'flexboxgrid.min.css');
        Asset::style('mpt__styles', 'app.css');

        Asset::script('mpt__script', 'app.js');
    
    }
}

$settings = new Settings;