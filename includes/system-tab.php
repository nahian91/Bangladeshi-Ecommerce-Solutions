<?php
if (!defined('ABSPATH')) exit;

function bes_system_info_tab() {
    global $wpdb;

    // ----------------- Server & Site Info -----------------
    $system_info = [
        'WordPress Version'   => get_bloginfo('version'),
        'WooCommerce Version' => defined('WC_VERSION') ? WC_VERSION : 'Not Installed',
        'PHP Version'         => phpversion(),
        'MySQL Version'       => $wpdb->db_version(),
        'Memory Limit'        => WP_MEMORY_LIMIT,
        'Max Upload Size'     => size_format(wp_max_upload_size()),
        'Debug Mode'          => (defined('WP_DEBUG') && WP_DEBUG) ? 'Enabled' : 'Disabled',
        'Active Theme'        => wp_get_theme()->get('Name') . ' ' . wp_get_theme()->get('Version'),
        'Site URL'            => get_site_url(),
    ];
    // ----------------- Logs -----------------
    $plugin_log_file = WP_CONTENT_DIR . '/bes-logs/plugin.log';
    $logs = file_exists($plugin_log_file) ? file_get_contents($plugin_log_file) : 'No plugin logs found.';

    // ----------------- Report Text -----------------
    $report = "----- System Info -----\n";
    foreach ($system_info as $key => $value) {
        $report .= "$key: $value\n";
    }

    $report .= "\n----- BES Plugin Logs -----\n";
    $report .= $logs;
    ?>

    <div class="wrap">
        <h1>BES System Info / Debug</h1>

        <!-- Server & Site Info -->
        <h2>Server & Site Info</h2>
        <table class="widefat striped">
            <tbody>
            <?php foreach ($system_info as $key => $value): ?>
                <tr>
                    <th><?php echo esc_html($key); ?></th>
                    <td><?php echo esc_html($value); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    jQuery(document).ready(function($){
        // Copy report
        $('#bes-copy-report').click(function(){
            const textarea = document.getElementById('bes-system-report');
            textarea.select();
            textarea.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');
            alert('System report copied to clipboard!');
        });
    });
    </script>

    <style>
        /* Optional: Make tables more readable */
        .wrap table.widefat th {
            width: 30%;
        }
    </style>

<?php
}
