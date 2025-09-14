<?php
if (!defined('ABSPATH')) exit;

function bes_product_tab() {
    // Default settings
    $defaults = [
        'show_sku' => 1,
        'show_stock' => 1,
        'show_categories' => 1,
        'show_tags' => 0,
        'disable_price' => 0,
        'disable_add_to_cart' => 0,
        'disable_variations' => 0,
        'show_reviews' => 1,
        'custom_label' => '' // Custom text label
    ];

    $settings = wp_parse_args(get_option('bes_product_settings', []), $defaults);

    echo '<h2>Product Settings</h2>';
    echo '<p>Control product page elements and visibility.</p>';
    echo '<form method="post" action="options.php">';
    settings_fields('bes_product_group');

    echo '<div class="bes-product-card">';

    // Checkbox options
    $checkboxes = [
        'show_sku' => 'Show SKU',
        'show_stock' => 'Show Stock Status',
        'show_categories' => 'Show Categories',
        'show_tags' => 'Show Tags',
        'disable_price' => 'Hide Product Price',
        'disable_add_to_cart' => 'Disable Add to Cart Button',
        'disable_variations' => 'Disable Product Variations',
        'show_reviews' => 'Show Reviews'
    ];

    foreach ($checkboxes as $key => $label) {
        $val = !empty($settings[$key]) ? 1 : 0;
        echo '<div class="bes-product-field">';
        echo '<label class="switch">';
        echo '<input type="hidden" name="bes_product_settings['.$key.']" value="0">';
        echo '<input type="checkbox" name="bes_product_settings['.$key.']" value="1" '.checked($val,1,false).'>';
        echo '<span class="slider round"></span> '.$label;
        echo '</label></div>';
    }

    // Custom text label
    echo '<div class="bes-product-field" style="margin-top:10px;">';
    echo '<label>Custom Label/Text for Product Page:</label><br>';
    echo '<input type="text" name="bes_product_settings[custom_label]" value="'.esc_attr($settings['custom_label']).'" style="width:100%; max-width:400px; padding:5px;">';
    echo '</div>';

    submit_button('Save Product Settings');
    echo '</div></form>';

    ?>
    <style>
    .bes-product-card {
        border:1px solid #ddd;
        border-radius:8px;
        padding:20px;
        max-width:700px;
        background:#f9f9f9;
        box-shadow:0 2px 8px rgba(0,0,0,0.05);
    }
    .bes-product-card h2 {margin-top:0; margin-bottom:10px;}
    .bes-product-card p {margin-bottom:15px; color:#555;}
    .bes-product-field {
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
