<?php
if (!defined('ABSPATH')) exit;

function bes_checkout_tab() {
    // Get saved settings or defaults
    $checkout = get_option('bes_checkout_fields', []);
    $titles = get_option('bes_checkout_titles', [
        'page_title' => 'Checkout',
        'billing_title' => 'Billing Details',
        'shipping_title' => 'Shipping Details',
        'order_notes_title' => 'Order Notes',
        'place_order_button' => 'Place Order',
    ]);
    $additional = get_option('bes_checkout_additional', [
        'show_terms' => true,
        'terms_text' => 'I have read and agree to the terms and conditions.',
        'show_login_reminder' => true,
        'custom_css' => ''
    ]);

    // Ensure defaults
    $titles = wp_parse_args($titles, [
        'page_title' => 'Checkout',
        'billing_title' => 'Billing Details',
        'shipping_title' => 'Shipping Details',
        'order_notes_title' => 'Order Notes',
        'place_order_button' => 'Place Order',
    ]);
    $additional = wp_parse_args($additional, [
        'show_terms' => true,
        'terms_text' => 'I have read and agree to the terms and conditions.',
        'show_login_reminder' => true,
        'custom_css' => ''
    ]);

    settings_fields('bes_checkout_group');
    do_settings_sections('bes_checkout_group');

    echo '<h2>Checkout Fields & Titles</h2>';
    echo '<p>Manage fields, titles, and checkout page settings.</p>';

    echo '<div class="bes-checkout-wrapper">';

    // ======= Checkout Fields =======
    echo '<div class="bes-section">';
    echo '<h3>Fields Settings</h3>';
    echo '<p>Click a card to expand. Drag to reorder fields.</p>';
    echo '<div id="bes-checkout-fields">';
    foreach ($checkout as $group_name => $group_fields) {
        echo '<div class="bes-field-card">';
        echo '<div class="bes-card-header"><strong>'.esc_html($group_name).'</strong> <span class="dashicons dashicons-arrow-down"></span></div>';
        echo '<div class="bes-card-body"><ul class="bes-fields-group" data-group="'.esc_attr($group_name).'">';
        foreach ($group_fields as $key => $field) {
            $enabled = $field['enabled'] ?? true;
            $label = $field['label'] ?? '';
            $required = $field['required'] ?? true;
            $placeholder = $field['placeholder'] ?? '';
            $description = $field['description'] ?? '';

            echo '<li class="bes-field-item">';
            echo '<label class="switch">';
            echo '<input type="checkbox" name="bes_checkout_fields['.$group_name.']['.$key.'][enabled]" '.checked($enabled,true,false).'>';
            echo '<span class="slider round"></span></label>';
            echo '<input type="text" name="bes_checkout_fields['.$group_name.']['.$key.'][label]" value="'.esc_attr($label).'" placeholder="Label" style="flex:1;">';
            echo '<label><input type="checkbox" name="bes_checkout_fields['.$group_name.']['.$key.'][required]" '.checked($required,true,false).'> Required</label>';
            echo '<input type="text" name="bes_checkout_fields['.$group_name.']['.$key.'][placeholder]" value="'.esc_attr($placeholder).'" placeholder="Placeholder">';
            echo '<input type="text" name="bes_checkout_fields['.$group_name.']['.$key.'][description]" value="'.esc_attr($description).'" placeholder="Description">';
            echo '<span class="dashicons dashicons-move"></span>';
            echo '</li>';
        }
        echo '</ul></div></div>';
    }
    echo '</div>'; // end #bes-checkout-fields
    echo '</div>'; // end section

    // ======= Titles Settings =======
    echo '<div class="bes-section">';
    echo '<h3>Checkout Page Titles</h3>';
    echo '<div class="bes-title-field">';
    foreach ($titles as $key => $value) {
        $label = ucwords(str_replace('_',' ',$key));
        echo '<label>'.$label.': <input type="text" name="bes_checkout_titles['.$key.']" value="'.esc_attr($value).'" class="bes-title-input" data-preview="'.$key.'"></label><br>';
    }
    echo '<div class="bes-preview"><p><strong>Live Preview:</strong></p>';
    echo '<h2 id="preview_page_title">'.esc_html($titles['page_title']).'</h2>';
    echo '<h3 id="preview_billing_title">'.esc_html($titles['billing_title']).'</h3>';
    echo '<h3 id="preview_shipping_title">'.esc_html($titles['shipping_title']).'</h3>';
    echo '<h3 id="preview_order_notes_title">'.esc_html($titles['order_notes_title']).'</h3>';
    echo '<button class="button button-primary" id="preview_place_order">'.esc_html($titles['place_order_button']).'</button>';
    echo '</div></div>';
    echo '</div>';

    // ======= Additional Settings =======
    echo '<div class="bes-section">';
    echo '<h3>Additional Settings</h3>';
    echo '<label class="switch">';
    echo '<input type="checkbox" name="bes_checkout_additional[show_terms]" '.checked($additional['show_terms'],true,false).'>';
    echo '<span class="slider round"></span> Show Terms & Conditions</label><br>';
    echo '<textarea name="bes_checkout_additional[terms_text]" placeholder="Terms & Conditions text" style="width:100%;height:60px;">'.esc_textarea($additional['terms_text']).'</textarea><br>';
    echo '<label class="switch">';
    echo '<input type="checkbox" name="bes_checkout_additional[show_login_reminder]" '.checked($additional['show_login_reminder'],true,false).'>';
    echo '<span class="slider round"></span> Show Login Reminder</label><br>';
    echo '<label>Custom CSS (Optional):</label><br>';
    echo '<textarea name="bes_checkout_additional[custom_css]" placeholder="Custom CSS" style="width:100%;height:80px;">'.esc_textarea($additional['custom_css']).'</textarea>';
    echo '</div>';

    submit_button('Save Checkout Settings');

    echo '</div>'; // end wrapper
    ?>
    <style>
        .bes-checkout-wrapper { max-width:800px; }
        .bes-section { border:1px solid #ddd; padding:15px; margin-bottom:15px; border-radius:5px; background:#fafafa; }
        .bes-card-header { background:#f1f1f1; padding:10px; cursor:pointer; display:flex; justify-content:space-between; align-items:center; }
        .bes-card-body { display:none; padding:10px; }
        .bes-fields-group { list-style:none; margin:0; padding:0; }
        .bes-fields-group li { margin:5px 0; padding:8px; background:#fff; display:flex; flex-wrap:wrap; align-items:center; gap:5px; cursor:move; border:1px solid #eee; border-radius:4px; }
        .bes-fields-group li input[type=text]{ min-width:120px; padding:4px; }
        .bes-title-field label { display:block; margin-bottom:8px; }
        .bes-preview { border:1px dashed #ccc; padding:10px; margin-top:10px; background:#fff; }
        .switch { position: relative; display: inline-block; width:40px; height:20px; }
        .switch input { display:none; }
        .slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#ccc; transition:.4s; border-radius:20px; }
        .slider:before { position:absolute; content:""; height:16px; width:16px; left:2px; bottom:2px; background:white; transition:.4s; border-radius:50%; }
        input:checked + .slider { background:#4caf50; }
        input:checked + .slider:before { transform:translateX(20px); }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.14.0/jquery-ui.min.js"></script>
    <script>
        jQuery(document).ready(function($){
            // Expand/Collapse
            $('.bes-card-header').click(function(){
                $(this).next('.bes-card-body').slideToggle();
                $(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');
            });

            // Sortable Fields
            $('.bes-fields-group').sortable({ placeholder: "ui-state-highlight" });

            // Live Preview Titles
            $('.bes-title-input').on('input', function(){
                var preview_id = 'preview_'+$(this).data('preview');
                $('#'+preview_id).text($(this).val());
            });
        });
    </script>
<?php
}

// Admin notice for Checkout Settings Saved
add_action('admin_notices', function(){
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
        echo '<div class="notice notice-success is-dismissible"><p>Checkout settings saved successfully!</p></div>';
    }
});
