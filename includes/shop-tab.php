<?php
if (!defined('ABSPATH')) exit;

function bes_shop_tab() {
    // Default settings
    $defaults = [
        'show_featured' => 1,
        'show_sale_badge' => 1,
        'show_add_to_cart' => 1,
        'show_whatsapp_button' => 1,
        'hide_prices' => 0,
        'disable_shop_page' => 0,
        'hide_categories' => 0,
        'hide_tags' => 0,
        'custom_label' => ''
    ];

    $settings = wp_parse_args(get_option('bes_shop_settings', []), $defaults);

    echo '<h2>Shop Settings</h2>';
    echo '<p>Control shop page layout and product visibility.</p>';
    echo '<form method="post" action="options.php">';
    settings_fields('bes_shop_group');

    echo '<div class="bes-shop-card">';

    // Checkbox options
    $checkboxes = [
        'show_featured' => 'Show Featured Products',
        'show_sale_badge' => 'Show Sale Badge',
        'show_add_to_cart' => 'Show Add to Cart Buttons',
        'show_whatsapp_button' => 'Show WhatsApp Button',
        'hide_prices' => 'Hide Product Prices',
        'disable_shop_page' => 'Disable Shop Page',
        'hide_categories' => 'Hide Categories',
        'hide_tags' => 'Hide Tags'
    ];

    foreach ($checkboxes as $key => $label) {
        $val = !empty($settings[$key]) ? 1 : 0;
        echo '<div class="bes-shop-field">';
        echo '<label class="switch">';
        echo '<input type="hidden" name="bes_shop_settings['.$key.']" value="0">';
        echo '<input type="checkbox" name="bes_shop_settings['.$key.']" value="1" '.checked($val,1,false).'>';
        echo '<span class="slider round"></span> '.$label;
        echo '</label></div>';
    }

    // Custom text label
    echo '<div class="bes-shop-field" style="margin-top:10px;">';
    echo '<label>Custom Label/Text for Shop Page:</label><br>';
    echo '<input type="text" name="bes_shop_settings[custom_label]" value="'.esc_attr($settings['custom_label']).'" style="width:100%; max-width:400px; padding:5px;">';
    echo '</div>';

    submit_button('Save Shop Settings');
    echo '</div></form>';

    ?>
    <style>
    .bes-shop-card {
        border:1px solid #ddd;
        border-radius:8px;
        padding:20px;
        max-width:700px;
        background:#f9f9f9;
        box-shadow:0 2px 8px rgba(0,0,0,0.05);
    }
    .bes-shop-card h2 {margin-top:0; margin-bottom:10px;}
    .bes-shop-card p {margin-bottom:15px; color:#555;}
    .bes-shop-field {
        margin-bottom:12px;
        display:flex;
        align-items:center;
    }
    .switch {
        position: relative;
        display: inline-block;
        width:50px;
        height:24px;
        margin-right:10px;
    }
    .switch input {display:none;}
    .slider {
        position:absolute;
        cursor:pointer;
        top:0; left:0; right:0; bottom:0;
        background:#ccc;
        transition:.4s;
        border-radius:34px;
    }
    .slider:before {
        position:absolute;
        content:"";
        height:18px;
        width:18px;
        left:3px;
        bottom:3px;
        background:white;
        transition:.4s;
        border-radius:50%;
    }
    input:checked + .slider {background:#4caf50;}
    input:checked + .slider:before {transform:translateX(26px);}
    </style>
    <?php
}
