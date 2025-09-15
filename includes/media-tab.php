<?php
if (!defined('ABSPATH')) exit;

function bes_media_check_tab() {
    $uploads = get_posts([
        'post_type'      => 'attachment',
        'posts_per_page' => 50,
        'post_status'    => 'inherit',
    ]);
    ?>

    <h2>Image & Video Size Check</h2>
    <p>Check uploaded media for recommended sizes and limits.</p>

    <table class="widefat striped">
        <thead>
            <tr>
                <th>File Name</th>
                <th>Type</th>
                <th>Width</th>
                <th>Height</th>
                <th>Size (KB)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($uploads)) : ?>
                <?php foreach ($uploads as $upload) : 
                    $file_path = get_attached_file($upload->ID);
                    $url       = wp_get_attachment_url($upload->ID);
                    $filetype  = wp_check_filetype($url)['ext'] ?? '';
                    $filesize  = file_exists($file_path) ? filesize($file_path)/1024 : 0; // KB
                    $status    = 'OK';
                    $width     = $height = '-';

                    if (in_array(strtolower($filetype), ['jpg','jpeg','png','gif'])) {
                        if (file_exists($file_path)) {
                            $size = @getimagesize($file_path);
                            if ($size) {
                                $width = $size[0];
                                $height = $size[1];
                                if ($width > 2000 || $height > 2000) $status = 'Too Large';
                            }
                        }
                    } elseif (in_array(strtolower($filetype), ['mp4','mov','avi','webm'])) {
                        if ($filesize > 51200) $status = 'Too Large'; // >50MB
                    }
                ?>
                    <tr>
                        <td><?php echo esc_html($upload->post_title); ?></td>
                        <td><?php echo esc_html($filetype); ?></td>
                        <td><?php echo esc_html($width); ?></td>
                        <td><?php echo esc_html($height); ?></td>
                        <td><?php echo round($filesize, 2); ?></td>
                        <td><?php echo esc_html($status); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="6">No recent media found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <style>
        .widefat th, .widefat td { padding: 8px; text-align: left; }
        .widefat td { vertical-align: middle; }
    </style>

<?php
}
