<?php
if (!defined('ABSPATH')) exit;

function bcaw_media_check_tab() {
    $uploads = get_posts([
        'post_type'      => 'attachment',
        'posts_per_page' => 50,
        'post_status'    => 'inherit',
    ]);
    ?>

    <h2><?php _e('Media Manager', 'bes-media'); ?></h2>
    <p><?php _e('Edit media information directly from this table (auto-save via AJAX).', 'bes-media'); ?></p>

    <!-- Filter by type -->
    <label for="media-type-filter"><?php _e('Filter by Type:', 'bes-media'); ?></label>
    <select id="media-type-filter">
        <option value=""><?php _e('All', 'bes-media'); ?></option>
        <option value="image"><?php _e('Image', 'bes-media'); ?></option>
        <option value="video"><?php _e('Video', 'bes-media'); ?></option>
    </select>

    <table id="media-check-table" class="widefat striped">
        <thead>
            <tr>
                <th><?php _e('Preview', 'bes-media'); ?></th>
                <th><?php _e('File Name', 'bes-media'); ?></th>
                <th><?php _e('Type', 'bes-media'); ?></th>
                <th><?php _e('Width', 'bes-media'); ?></th>
                <th><?php _e('Height', 'bes-media'); ?></th>
                <th><?php _e('Size (KB)', 'bes-media'); ?></th>
                <th><?php _e('Title', 'bes-media'); ?></th>
                <th><?php _e('Description', 'bes-media'); ?></th>
                <th><?php _e('Alt Text', 'bes-media'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($uploads)) : ?>
                <?php foreach ($uploads as $upload) : 
                    $file_path = get_attached_file($upload->ID);
                    $url       = wp_get_attachment_url($upload->ID);
                    $filetype  = wp_check_filetype($url)['ext'] ?? '';
                    $filesize  = file_exists($file_path) ? filesize($file_path)/1024 : 0; // KB
                    $width     = $height = '-';
                    $type_class = '';

                    if (in_array(strtolower($filetype), ['jpg','jpeg','png','gif'])) {
                        $type_class = 'image';
                        if (file_exists($file_path)) {
                            $size = @getimagesize($file_path);
                            if ($size) {
                                $width = $size[0];
                                $height = $size[1];
                            }
                        }
                    } elseif (in_array(strtolower($filetype), ['mp4','mov','avi','webm'])) {
                        $type_class = 'video';
                    }

                    $title = get_the_title($upload->ID);
                    $desc = get_post_field('post_content', $upload->ID);
                    $alt = get_post_meta($upload->ID, '_wp_attachment_image_alt', true);
                ?>
                    <tr class="<?php echo esc_attr($type_class); ?>" data-id="<?php echo esc_attr($upload->ID); ?>">
                        <td>
                            <?php if ($type_class === 'image') : ?>
                                <img src="<?php echo esc_url($url); ?>" style="width:50px; height:50px; object-fit:cover;" class="media-preview" data-src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($alt); ?>">
                            <?php elseif ($type_class === 'video') : ?>
                                <span style="font-size:24px;">🎬</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($upload->post_title); ?></td>
                        <td><?php echo esc_html($filetype); ?></td>
                        <td><?php echo esc_html($width); ?></td>
                        <td><?php echo esc_html($height); ?></td>
                        <td><?php echo round($filesize, 2); ?></td>
                        <td><input type="text" class="media-edit" name="title" value="<?php echo esc_attr($title); ?>" placeholder="<?php _e('Enter Title', 'bes-media'); ?>"></td>
                        <td><input type="text" class="media-edit" name="desc" value="<?php echo esc_attr($desc); ?>" placeholder="<?php _e('Enter Description', 'bes-media'); ?>"></td>
                        <td><input type="text" class="media-edit" name="alt" value="<?php echo esc_attr($alt); ?>" placeholder="<?php _e('Enter Alt Text', 'bes-media'); ?>"></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="9"><?php _e('No recent media found.', 'bes-media'); ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Preview Modal -->
    <div id="media-preview-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:9999;">
        <span id="modal-close" style="position:absolute; top:20px; right:30px; font-size:30px; color:#fff; cursor:pointer;">&times;</span>
        <img id="modal-image" src="" style="max-width:90%; max-height:90%; border:5px solid #fff;">
    </div>

    <!-- DataTables & Scripts -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
    jQuery(document).ready(function($){
        var table = $('#media-check-table').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            order: [[1,'asc']]
        });

        // Filter by type
        $('#media-type-filter').on('change', function(){
            var val = $(this).val();
            table.rows().every(function(){
                var row = this.node();
                if(!val || $(row).hasClass(val)) $(row).show();
                else $(row).hide();
            });
        });

        // Image preview
        $('.media-preview').on('click', function(){
            $('#modal-image').attr('src', $(this).data('src'));
            $('#media-preview-modal').fadeIn();
        });

        $('#modal-close, #media-preview-modal').on('click', function(){
            $('#media-preview-modal').fadeOut();
        });

        // AJAX save on blur
        $('.media-edit').on('blur', function(){
            var row = $(this).closest('tr');
            var id = row.data('id');
            var field = $(this).attr('name');
            var value = $(this).val();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bes_update_media',
                    id: id,
                    field: field,
                    value: value,
                    _wpnonce: '<?php echo wp_create_nonce("bes_media_nonce"); ?>'
                },
                success: function(resp){
                    console.log(resp);
                    alert('<?php _e("Media updated successfully!", "bes-media"); ?>');
                },
                error: function(){
                    alert('<?php _e("Failed to update media.", "bes-media"); ?>');
                }
            });
        });
    });
    </script>

    <style>
        .widefat th, .widefat td { padding: 8px; text-align: left; vertical-align: middle; }
        .dataTables_wrapper .dataTables_filter { float:right; margin-bottom:10px; }
        .dataTables_wrapper .dataTables_paginate { float:right; margin-top:10px; }
        .media-preview { cursor:pointer; border:2px solid #ccc; border-radius:4px; }
        #media-preview-modal { display:flex; }
    </style>

<?php
}

// -------------------- AJAX Handler --------------------
add_action('wp_ajax_bes_update_media', function(){
    check_ajax_referer('bes_media_nonce');

    $id = intval($_POST['id']);
    $field = sanitize_text_field($_POST['field']);
    $value = sanitize_text_field($_POST['value']);

    if($field === 'title') {
        wp_update_post(['ID'=>$id,'post_title'=>$value]);
    } elseif($field === 'desc') {
        wp_update_post(['ID'=>$id,'post_content'=>$value]);
    } elseif($field === 'alt') {
        update_post_meta($id,'_wp_attachment_image_alt',$value);
    }

    wp_send_json_success(__('Updated!', 'bes-media'));
});
