<div class="wrap container-fluid mpt-container">
    <div class="mpt-inner-container">
    
        <?php 
include 'inc/top.view.php';
?>

        <div class="row" style="margin: 0 0.5rem;">
            <div class="mpt-segment">
            
                <h2><?php 
echo  esc_html__( 'About Mass Ping Tool for SEO', 'mass-ping-tool-for-seo' ) ;
?></h2>
                <p><?php 
echo  __( 'Mass ping tool for SEO plugin is one of the fastest way to get indexed by Google. After pinging, Google bots, crawlers will visit your website and initiate the indexing process of your website. Get your website on the map!', 'mass-ping-tool-for-seo' ) ;
?>
                </p>

            </div>
        </div>
    
        <div class="row">
    
            <div class="col-xs-6 col-main" style="padding-right: 0.8rem; padding-left: 0.8rem;">
    
                <form method="post" class="mpt-form">
    
                    <?php 
wp_nonce_field( 'mpt__settings', 'mpt__nonce' );
?>
    
                    <div class="mpt-segment">
    
                        <div class="row">
    
                            <div class="col-xs-3">
                                <label class="mpt-label" for="mpt__enable">
                                    <strong>
                                        <?php 
echo  __( 'Enable Ping', 'mass-ping-tool-for-seo' ) ;
?>
                                    </strong>
                                    <span
                                    tooltip="<?php 
echo  __( 'This option will completely enable/disable Ping. By default, this will be set to ON.', 'mass-ping-tool-for-seo' ) ;
?>"
                                    flow="right">
                                    <i class="dashicons dashicons-editor-help"></i>
                                    </span>
                                </label>
                            </div>
        
                            <div class="col-xs-9">
        
                                <label class="mpt-toggle"><input id="enable" type="checkbox" name="enable" value="allow" <?php 
if ( $options::check( 'enable' ) ) {
    echo  'checked' ;
}
?> />
                                    <span class='mpt-toggle-slider mpt-toggle-round'></span></label>
                                &nbsp;
                                <span
                                    class="mpt-comment"><?php 
echo  __( "Disable this if you don't need Ping feature", 'mass-ping-tool-for-seo' ) ;
?></span>
        
                            </div>
    
                        </div>

                        <div class="row" style="margin-top: 20px;">
    
                            <div class="col-xs-3">
                                <label class="mpt-label" for="mpt__enable">
                                    <strong>
                                        <?php 
echo  __( 'Post Types', 'mass-ping-tool-for-seo' ) ;
?>
                                    </strong>
                                    <span
                                    tooltip="<?php 
echo  __( 'Select post types for content pinging. Post, Pages are selected by default.', 'mass-ping-tool-for-seo' ) ;
?>"
                                    flow="right">
                                    <i class="dashicons dashicons-editor-help"></i>
                                    </span>
                                </label>
                            </div>
        
                            <div class="col-xs-9">
        
                            <?php 
foreach ( $post_types as $label => $post_type ) {
    ?>

                            <div class="mpt-checkbox">
                                <input id="<?php 
    echo  esc_html( "mpt-" . $post_type ) ;
    ?>" type="checkbox"
                                    
                                    <?php 
    
    if ( !mpt__fs()->can_use_premium_code__premium_only() && $post_type == 'product' ) {
        echo  'disabled' ;
    } else {
        echo  "name='post_types[]' value=" . esc_html( $post_type ) ;
    }
    
    ?>
                                    <?php 
    if ( $options->check( 'post_types' ) && in_array( $post_type, $options->get( 'post_types' ) ) ) {
        echo  "checked" ;
    }
    ?> />

                                <label
                                    for="<?php 
    echo  esc_html( "mpt-" . $post_type ) ;
    ?>"><?php 
    echo  esc_html( $label ) . (( !mpt__fs()->can_use_premium_code__premium_only() && $post_type == 'product' ? " (PRO only)" : '' )) ;
    ?></label>
                            </div>

                            <?php 
}
?>

                            
        
                            </div>
    
                        </div>

                        <?php 

if ( class_exists( 'WooCommerce' ) && !mpt__fs()->can_use_premium_code__premium_only() ) {
    ?>
                            <div class="mpt-alert mpt-info" style="margin-bottom: 0">
                                <?php 
    echo  sprintf( wp_kses( __( '<a href="%s">Get Pro version</a> to enable Woocommerce Products', "mass-ping-tool-for-seo" ), array(
        'a' => array(
        'href'   => array(),
        'target' => array(),
    ),
    ) ), esc_url( "admin.php?page=mass-ping-tool-for-seo-pricing" ) ) ;
    ?>
                            </div>
                        <?php 
}

?>
    
                    </div>

                    <div class="mpt-segment">

                    <?php 

if ( get_option( 'mpt-update-require' ) != false ) {
    ?>
                        <div class="mpt-alert mpt-success" style="margin: 0 0 25px;">
                            <?php 
    echo  esc_html( 'Services list update is available. ', 'mass-ping-tool-for-seo' ) . esc_html( $get_pro ) . " auto update" ;
    ?>
                        </div>
                    <?php 
}

?>

                    <div class="row">
                            <div class="col-xs-3">
                                <label class="mpt-label" for="mpt__id">
                                    <strong>
                                        <?php 
echo  __( 'Ping Services', 'mass-ping-tool-for-seo' ) ;
?>
                                    </strong>
                                    <span
                                    tooltip="<?php 
echo  __( 'Our ping list (provided by API) is updated on a regular basis for better performances. Make sure to click on Update Services List button to get updated list.', 'mass-ping-tool-for-seo' ) ;
?>"
                                    flow="right">
                                    <i class="dashicons dashicons-editor-help"></i>
                                    </span>
                                </label>
                            </div>
    
                            <div class="col-xs-9">
                                
                                <div class="row">
                                    <div class="col-xs">
                                        <div class="mpt-btn" style="font-size: 12px; <?php 
echo  "background-color: #29ce76;" ;
?> cursor: default;">Free Mode <?php 
if ( isset( $services->free_services ) && !empty($services->free_services) ) {
    echo  "( {$services->free_services} Services)" ;
}
?></div>
                                    </div>
                                    <div class="col-xs">
                                        <div class="mpt-btn" style="font-size: 12px; <?php 
echo  "background-color: #d1d5db;" ;
?> cursor: default;">Pro Mode (<?php 
echo  esc_html( $pro_count ) ;
?> Services)</div>
                                    </div>
                                </div>

                                <label class="mpt-btn" style="margin-top: 15px;" >
                                    <input type="submit" name="update_services" value="update_services" style="display: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; display: inline-block;" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                    </svg> <span style="font-size: 22px;"><?php 
echo  __( 'Update Services List', 'mass-ping-tool-for-seo' ) ;
?></span>
                                </label>

                                <?php 

if ( get_option( 'mpt-last-update' ) !== false ) {
    ?>
                                <div style="text-align: center; margin-top: 5px;">
                                    <?php 
    echo  __( 'Last Synced: ', 'mass-ping-tool-for-seo' ) . date( "F j, Y, g:i a", get_option( 'mpt-last-update' ) ) ;
    ?>
                                </div>
                                <?php 
}

?>

                            </div>
    
                        </div>

                        <?php 
?>

                        <div class="mpt-alert mpt-info" style="margin-top: 15px;">
                            <span class="closebtn">&times;</span>
                            <?php 
echo  sprintf( wp_kses( __( '<a href="%s">Get Pro version</a> to enable', 'mass-ping-tool-for-seo' ), array(
    'a' => array(
    'href'   => array(),
    'target' => array(),
),
) ), esc_url( 'admin.php?page=' . MPT_NAME . '-pricing' ) ) . " " . __( 'Pro Mode', 'mass-ping-tool-for-seo' ) . " with <strong>" . esc_html( $pro_count ) . "</strong> services" ;
?>
                        </div>

                        <?php 
?>
                        
                    </div>

                    <div class="mpt-segment ping-limit">

                        <div class="row">
    
                            <div class="col-xs-3">
                                <label class="mpt-label" for="mpt__limit">
                                    <strong>
                                        <?php 
echo  __( 'Limit Ping Rate', 'mass-ping-tool-for-seo' ) ;
?>
                                    </strong>
                                    <span
                                    tooltip="<?php 
echo  __( 'Avoid unnecessary ping (each time that you edit a post). Save your blog from getting tagged as ping spammer.', 'mass-ping-tool-for-seo' ) ;
?>"
                                    flow="right">
                                    <i class="dashicons dashicons-editor-help"></i>
                                    </span>
                                </label>
                            </div>

                            <?php 
?>

                            <div class="col-xs-2">
                            
                            <label class="mpt-toggle"><input id="limit" type="checkbox" disabled />
                            <span class='mpt-toggle-slider mpt-toggle-round'></span></label>
                            </div>

                            <div class="col-xs-7">
                                <div class="limiter">
                                    <span style="font-weight: 700">Ping <input type="text" class="mpt-input" style="width: 60px; background: #f5f5f5" disabled> time's in <input type="text" class="mpt-input" style="width: 60px; background: #f5f5f5" disabled> minutes</span>
                                </div>
                            </div>

                            <?php 
?>
    
                        </div>

                        <?php 
?>

                        <div class="mpt-alert mpt-info">
                            <span class="closebtn">&times;</span>
                            <?php 
echo  sprintf( wp_kses( __( '<a href="%s">Get Pro version</a> to enable', 'mass-ping-tool-for-seo' ), array(
    'a' => array(
    'href'   => array(),
    'target' => array(),
),
) ), esc_url( 'admin.php?page=' . MPT_NAME . '-pricing' ) ) . " " . __( 'limit ping rate feature', 'mass-ping-tool-for-seo' ) ;
?>
                        </div>

                        <?php 
?>
    
                    </div>

                    <div class="mpt-segment">

                    <div class="row">
                            <div class="col-xs-3">
                                <label class="mpt-label" for="mpt__id">
                                    <strong>
                                        <?php 
echo  __( 'Default List', 'mass-ping-tool-for-seo' ) ;
?>
                                    </strong>
                                    <span
                                    tooltip="<?php 
echo  __( 'This list is provided with the default WordPress Ping services and is additional to our own ping list service. You may also add your own services (other ping URL)', 'mass-ping-tool-for-seo' ) ;
?>"
                                    flow="right">
                                    <i class="dashicons dashicons-editor-help"></i>
                                    </span>
                                </label>
                            </div>
    
                            <div class="col-xs-9">
                                <textarea name="ping_sites" class="mpt-textarea"><?php 
echo  $ping_sites ;
?></textarea>
    
                                <p class="mpt-comment">
                                    <?php 
echo  sprintf( wp_kses( __( 'Please note that this list is synced with default Ping list in Settings > Writing. Refer <a href="%s">FAQ</a> to learn more about the list.', 'mass-ping-tool-for-seo' ), array(
    'a' => array(
    'href' => array(),
),
) ), esc_url( 'admin.php?page=mass-ping-tool-for-seo&tab=mpt-faq' ) ) ;
?>
                                </p>
    
                            </div>
    
                        </div>
                        
                    </div>

                    <div class="mpt-segment">

                        <div class="row">

                            <div class="col-xs-3">
                                <label class="mpt-label" for="remove_settings">
                                    <strong>
                                        <?php 
echo  __( 'Remove Settings', 'mass-ping-tool-for-seo' ) ;
?>
                                    </strong>
                                </label>
                            </div>

                            <div class="col-xs-2">
                                <label class="mpt-toggle"><input id="remove_settings" type="checkbox"
                                        name="remove_settings" value="allow" <?php 
if ( $options::check( 'remove_settings' ) ) {
    echo  'checked' ;
}
?> />
                                    <span class='mpt-toggle-slider mpt-toggle-round'></span></label>
                            </div>

                            <div class="col-xs-7 field">
                                <input type="submit" name="update" class="mpt-submit" value="<?php 
echo  esc_html__( 'Save Changes', 'mass-ping-tool-for-seo' ) ;
?>" />
                            </div>

                        </div>

                    </div>

                    </form>

                    <form method="post" class="mpt-form">
    
                    <?php 
wp_nonce_field( 'mpt__settings', 'mpt__nonce' );
?>

                    <div class="mpt-segment">

                        <div class="row">

                            <div class="col-xs field">
                                <input type="submit" name="pinging" class="mpt-submit" value="<?php 
echo  esc_html__( 'Ping Now (Forced)', 'mass-ping-tool-for-seo' ) ;
?>" />
                            </div>

                            <p class="mpt-comment" style="margin: auto;">
                                <?php 
echo  sprintf( wp_kses( __( 'Not recommended. Please check <a href="%s">FAQ</a> to learn more about this.', 'mass-ping-tool-for-seo' ), array(
    'a' => array(
    'href' => array(),
),
) ), esc_url( 'admin.php?page=mass-ping-tool-for-seo&tab=mpt-faq' ) ) ;
?>
                            </p>

                        </div>

                    </div>
    
                </form>
    
            </div>

            <div class="col-xs-6 col-side" style="padding-right: 0.8rem; padding-left: 0.8rem;">

                    <?php 
include "log.view.php";
?>

            </div>
    
        </div>
    </div>
    
    
</div>