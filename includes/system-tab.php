<?php
if(!defined('ABSPATH')) exit;

function bes_system_info_tab() {
    global $wpdb;

    // ----------------- Server & Site Info -----------------
    $system_info = [
        'WordPress Version'   => get_bloginfo('version'),
        'WooCommerce Version' => defined('WC_VERSION') ? WC_VERSION : 'Not Installed',
        'PHP Version'         => phpversion(),
        'MySQL Version'       => $wpdb->db_version(),
        'Memory Limit'        => WP_MEMORY_LIMIT,
        'Max Upload Size'     => wp_max_upload_size(),
        'Debug Mode'          => (defined('WP_DEBUG') && WP_DEBUG) ? 'Enabled' : 'Disabled',
        'Active Theme'        => wp_get_theme()->get('Name').' '.wp_get_theme()->get('Version'),
        'Site URL'            => get_site_url(),
    ];

    // ----------------- Active Plugins -----------------
    $active_plugins = get_option('active_plugins', []);
    
    // ----------------- Logs -----------------
    $plugin_log_file = WP_CONTENT_DIR . '/bes-logs/plugin.log';
    $logs = file_exists($plugin_log_file) ? file_get_contents($plugin_log_file) : 'No plugin logs found.';
    
    ?>
    <div class="wrap">
        <h1>BES System Info / Debug</h1>

        <!-- Copyable Report Button -->
        <button id="bes-copy-report" class="button button-primary">Copy Report</button>
        <textarea id="bes-system-report" style="width:100%;height:400px;display:none;"><?php

        // Prepare text report
        echo "----- System Info -----\n";
        foreach($system_info as $key=>$value){
            echo $key . ": " . $value . "\n";
        }

        echo "\n----- Active Plugins -----\n";
        foreach($active_plugins as $plugin){
            $plugin_data = get_plugin_data(WP_PLUGIN_DIR.'/'.$plugin);
            echo $plugin_data['Name'] . " - " . $plugin_data['Version'] . "\n";
        }

        echo "\n----- BES Plugin Logs -----\n";
        echo $logs;
        ?></textarea>

        <!-- Table Display -->
        <h2>Server & Site Info</h2>
        <table class="widefat striped">
            <?php foreach($system_info as $key=>$value): ?>
            <tr>
                <th><?php echo esc_html($key); ?></th>
                <td><?php echo esc_html($value); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Active Plugins</h2>
        <table class="widefat striped">
            <tr><th>Plugin Name</th><th>Version</th></tr>
            <?php foreach($active_plugins as $plugin):
                $plugin_data = get_plugin_data(WP_PLUGIN_DIR.'/'.$plugin); ?>
            <tr>
                <td><?php echo esc_html($plugin_data['Name']); ?></td>
                <td><?php echo esc_html($plugin_data['Version']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>BES Plugin Logs</h2>
        <pre style="background:#f1f1f1;padding:10px;max-height:300px;overflow:auto;"><?php echo esc_html($logs); ?></pre>
    </div>

    <script>
    jQuery(document).ready(function($){
        $('#bes-copy-report').click(function(){
            var copyText = document.getElementById("bes-system-report");
            copyText.style.display = 'block';
            copyText.select();
            document.execCommand("copy");
            copyText.style.display = 'none';
            alert("System report copied to clipboard!");
        });
    });
    </script>
    <?php
}
