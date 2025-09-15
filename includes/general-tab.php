<?php
function bes_general_tab() {

    $defaults = [
        'checkout' => 1, 'cart' => 1, 'delivery' => 1,
        'package' => 1, 'marketing' => 1, 'whatsapp' => 1,
        'district' => 1, 'thankyou' => 1, 'reports' => 1,
        'system' => 1, 'media_check' => 1, 'product' => 1, 'shop' => 1
    ];

    $general = get_option('bes_general_settings', $defaults);
    $general = wp_parse_args($general, $defaults);

    $tabs = [
        'checkout' => ['label'=>'Checkout', 'icon'=>'🛒', 'color'=>'#f39c12'],
        'cart' => ['label'=>'Cart', 'icon'=>'🛍️', 'color'=>'#e74c3c'],
        'delivery' => ['label'=>'Delivery', 'icon'=>'🚚', 'color'=>'#27ae60'],
        'package' => ['label'=>'Package/Invoice', 'icon'=>'📦', 'color'=>'#2980b9'],
        'marketing' => ['label'=>'Marketing', 'icon'=>'📢', 'color'=>'#9b59b6'],
        'whatsapp' => ['label'=>'WhatsApp', 'icon'=>'💬', 'color'=>'#25d366'],
        'district' => ['label'=>'District/Upazilla', 'icon'=>'🏘️', 'color'=>'#d35400'],
        'thankyou' => ['label'=>'Thank You Messages', 'icon'=>'🙏', 'color'=>'#1abc9c'],
        'reports' => ['label'=>'Reports', 'icon'=>'📊', 'color'=>'#c0392b'],
        'system' => ['label'=>'System Info', 'icon'=>'💻', 'color'=>'#34495e'],
        'media_check' => ['label'=>'Image/Video Check', 'icon'=>'🖼️', 'color'=>'#16a085'],
        'product' => ['label'=>'Product', 'icon'=>'🏷️', 'color'=>'#8e44ad'],
        'shop' => ['label'=>'Shop', 'icon'=>'🏪', 'color'=>'#d35400']
    ];
    ?>

    <form method="post" action="options.php">
        <?php settings_fields('bes_general_group'); ?>
        <?php do_settings_sections('bes_general_group'); ?>

        <h2 class="bes-page-title">Enable/Disable Plugin Tabs</h2>
        <div class="bes-tab-grid">
            <?php foreach ($tabs as $key => $tab): 
                $checked = !empty($general[$key]) ? 1 : 0; 
            ?>
                <div class="bes-tab-card" style="border-top:4px solid <?php echo esc_attr($tab['color']); ?>;">
                    <label class="bes-tab-label">
                        <div class="bes-tab-icon"><?php echo $tab['icon']; ?></div>
                        <div class="bes-tab-text"><?php echo esc_html($tab['label']); ?></div>
                        <input type="hidden" name="bes_general_settings[<?php echo $key; ?>]" value="0">
                        <input type="checkbox" name="bes_general_settings[<?php echo $key; ?>]" value="1" <?php checked($checked,1); ?>>
                        <span class="bes-slider"></span>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <?php submit_button('Save Settings', 'primary', 'bes-save-btn'); ?>
    </form>
<?php
}
