<?php
if (!defined('ABSPATH')) exit;

function bes_marketing_tab(){
    echo '<h2>Marketing Settings</h2>';
    echo '<p>Setup banners, campaigns, or promotional messages.</p>';
    echo '<textarea name="bes_marketing_settings[campaign]" rows="5" style="width:400px;">'.esc_textarea(get_option('bes_marketing_settings')['campaign']??'').'</textarea>';

    submit_button('Save Marketing Settings');
}
