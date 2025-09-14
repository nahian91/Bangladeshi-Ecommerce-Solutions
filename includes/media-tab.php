<?php
if (!defined('ABSPATH')) exit;

function bes_media_check_tab(){
    ?>
    <h2>Image & Video Size Check</h2>
    <p>Check uploaded media for recommended sizes and limits.</p>

    <table class="widefat">
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
            <?php
            // Fetch recent uploads
            $uploads = get_posts([
                'post_type' => 'attachment',
                'posts_per_page' => 50,
                'post_status' => 'inherit'
            ]);

            foreach($uploads as $upload){
                $url = wp_get_attachment_url($upload->ID);
                $filetype = wp_check_filetype($url)['ext'];
                $filesize = filesize(get_attached_file($upload->ID))/1024; // KB
                $status = 'OK';

                if(in_array($filetype, ['jpg','jpeg','png','gif'])){
                    $size = getimagesize(get_attached_file($upload->ID));
                    $width = $size[0];
                    $height = $size[1];

                    // Example limit check
                    if($width>2000 || $height>2000) $status='Too Large';
                }elseif(in_array($filetype,['mp4','mov','avi','webm'])){
                    $width = $height = '-';
                    // Size limit example 50MB
                    if($filesize>51200) $status='Too Large';
                }else{
                    $width = $height = '-';
                }

                echo "<tr>
                    <td>{$upload->post_title}</td>
                    <td>{$filetype}</td>
                    <td>{$width}</td>
                    <td>{$height}</td>
                    <td>".round($filesize,2)."</td>
                    <td>{$status}</td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
    <?php
}
