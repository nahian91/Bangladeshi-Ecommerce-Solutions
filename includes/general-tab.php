<?php
if (!defined('ABSPATH')) exit;

function bes_general_tab() {

    // Default settings
    $defaults = [
        'checkout' => 1,
        'cart' => 1,
        'delivery' => 1,
        'package' => 1,
        'marketing' => 1,
        'whatsapp' => 1,
        'district' => 1,
        'thankyou' => 1,
        'reports' => 1,
        'system' => 1,
        'media_check' => 1,
        'product' => 1,
        'shop' => 1
    ];

    // Get saved settings
    $general = get_option('bes_general_settings', $defaults);
    $general = wp_parse_args($general, $defaults);

    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

    echo '<form method="post" action="options.php">';
    settings_fields('bes_general_group');
    do_settings_sections('bes_general_group');

    echo '<div class="bes-general-card">';
    echo '<h2>Enable/Disable Plugin Tabs</h2>';
    echo '<p>Control which plugin tabs appear in the admin interface.</p>';

    // All tabs including Product & Shop
    $tabs = [
        'checkout' => 'Checkout',
        'cart' => 'Cart',
        'delivery' => 'Delivery',
        'package' => 'Package/Invoice',
        'marketing' => 'Marketing',
        'whatsapp' => 'WhatsApp',
        'district' => 'District/Upazilla',
        'thankyou' => 'Thank You Messages',
        'reports' => 'Reports',
        'system' => 'System Info',
        'media_check' => 'Image/Video Check',
        'product' => 'Product',
        'shop' => 'Shop'
    ];

    foreach ($tabs as $key => $label) {
        $checked = !empty($general[$key]) ? 1 : 0;
        echo '<div class="bes-general-field">';
        echo '<label class="switch">';
        echo '<input type="hidden" name="bes_general_settings['.$key.']" value="0">';
        echo '<input type="checkbox" name="bes_general_settings['.$key.']" value="1" '.checked($checked,1,false).'>';
        echo '<span class="slider round"></span> '.$label;
        echo '</label></div>';
    }

    submit_button('Save General Settings');
    echo '</div></form>';
    ?>

    <style>
    .bes-general-card {
        border:1px solid #ddd;
        border-radius:8px;
        padding:20px;
        max-width:700px;
        background:#f9f9f9;
        box-shadow:0 2px 8px rgba(0,0,0,0.05);
    }
    .bes-general-card h2 {margin-top:0; margin-bottom:15px;}
    .bes-general-card p {margin-bottom:20px; color:#555;}
    .bes-general-field {
        margin-bottom:15px;
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

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
    jQuery(document).ready(function($){
        var activeTab = '<?php echo $active_tab; ?>';
        $('.bes-general-card input[type=checkbox]').on('change', function(){
            var tabKey = $(this).attr('name').match(/\[(.*?)\]/)[1];
            var tabLink = $('.nav-tab-wrapper a[href*="tab='+tabKey+'"]');

            // Show tab if checked
            if($(this).is(':checked')){
                if(tabLink.length === 0){
                    var tabLabel = $(this).closest('label').text().trim();
                    var newTab = $('<a class="nav-tab" href="?page=bes-settings&tab='+tabKey+'">'+tabLabel+'</a>');
                    $('.nav-tab-wrapper').append(newTab);
                }
            } else {
                // Hide tab if unchecked and not active
                if(tabKey !== activeTab){
                    tabLink.remove();
                }
            }
        });
    });
    </script>
<?php
}
