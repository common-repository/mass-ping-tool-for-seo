<div class="mpt-segment mpt-log">
                    
    <h2 style="margin-bottom: 15px;"><?php echo esc_html__( 'Ping Log Report', 'mass-ping-tool-for-seo' ); ?></h2>
    
    <?php if (!empty($logs)) { ?>
    <div class="mpt-accordion">
        <?php foreach ($logs as $log ) { ?> 
        <div class="mpt-accordion-heading">
        <?php 
            echo esc_html( $log->title );
        ?></div>
        <div class="mpt-accordion-contents">
            <p>Updated at: <?php 
            echo esc_html( date("F j, Y, g:i a", strtotime($log->updated_at) ) );
        ?></p>

        <ul>
            <?php
                $data = ( isset($log->data) ) ? maybe_unserialize($log->data) : null;

                if (!is_null($data)) {
                    foreach ( array_reverse($data) as $item) { ?>
                    <li>
                        <span style="<?php //if (isset($item['status']) && $item['status'] == 'success' ) echo "color: green"; elseif (isset($item['status']) && $item['status'] == 'failed' ) echo "color: red"; ?>">
                        <?php
                            $server = (isset($item['server'])) ? "[Service: " . $item['server'] . "] " : "";
                            $time = (isset($item['time'])) ? " [".date("M j, Y, g:i a", $item['time'])."]" : "";
                            echo esc_html( $server . $item['message'] . $time);
                        ?>
                        </span>
                    
                    </li>
            <?php }

            }
            
            ?>
        </ul>
        </div>
        <?php } ?>
    </div>
    <?php } else { ?>
        <p>No log available at the moment</p>
    <?php } ?>

</div>
            
<div class="mpt-segment mpt-log" style="max-height: 250px;">

    <h2 style="margin-bottom: 15px;">
        <?php echo esc_html__( 'Scheduled Posts', 'mass-ping-tool-for-seo' ); ?>
    </h2> <span
        tooltip="<?php echo __('This section features posts scheduled for ping service.', 'mass-ping-tool-for-seo'); ?>"
        flow="right">
        <i class="dashicons dashicons-editor-help"></i>
        </span>

    <?php if (!empty($scheduled_posts)) { ?>

    <div class="mpt-accordion">
        <?php foreach ($scheduled_posts as $post ) { ?> 
        <div class="mpt-accordion-heading">
        <?php 
            echo esc_html( $post->title );
        ?></div>
        <div class="mpt-accordion-contents">
            <p>Will be pinged at: <?php 
            echo esc_html( date("F j, Y, g:i a", $post->time ) );
        ?></p>
        </div>
        <?php } ?>
    </div>

    <?php } else { ?>
        <p>No scheduled post to ping at the moment</p>
    <?php } ?>
        
</div>