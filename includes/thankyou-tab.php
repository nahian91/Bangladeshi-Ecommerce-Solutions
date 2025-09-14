<?php
if (!defined('ABSPATH')) exit;

function bes_thankyou_tab(){
    echo '<h2>Custom Thank You Messages</h2>';
    echo '<p>Set different thank you messages after checkout.</p>';
    echo '<textarea name="bes_thankyou_settings[message]" rows="5" style="width:400px;">'.esc_textarea(get_option('bes_thankyou_settings')['message']??'').'</textarea>';

    submit_button('Save Thank You Settings');
}
