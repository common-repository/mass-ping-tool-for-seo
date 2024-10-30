<div>
    <table style="width: 100%;">
        <?php if (isset($logs) && !empty($logs)) { foreach ($logs as $log) { ?>
            
        <tr style="background-color: #f9f9f9; margin-bottom: 3px; font-size: 11px; color: #333; clear: both">
            <td style="width: 85%; padding: 5px;">
            <?php echo esc_html__($log['message']); ?>
            </td>
            <td style="width: 15%; padding: 5px;">
            <?php 
            $time = (array_key_exists('time', $log)) ? date("M j, Y, g:i a", $log['time']) : "";
            echo esc_html__($time);
            
        ?> 
            </td>
        </tr>
        
        
        <?php } } else { echo "No ping logs available"; } ?>
        </table>
</div>