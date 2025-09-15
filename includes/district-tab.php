<?php
if (!defined('ABSPATH')) exit;

function bes_district_tab() {
    // Get saved JSON or initialize
    $json_data = get_option('bes_district_settings_json', '[]');
    $districts = json_decode($json_data, true);
    if (!is_array($districts)) $districts = [];

    // Ensure 5 default rows
    for ($i = 0; $i < 5; $i++) {
        if (!isset($districts[$i])) {
            $districts[$i] = ['district' => '', 'upazillas' => [''], 'postcode' => ''];
        }
    }
    ?>

    <h2>District & Upazilla Auto-Fill</h2>
    <p>Add districts, upazillas (comma-separated), and postcode. Add more rows if needed.</p>

    <div id="bes-district-repeater">
        <?php foreach ($districts as $index => $d): 
            $district = esc_attr($d['district'] ?? '');
            $upazillas = esc_attr(implode(',', $d['upazillas'] ?? []));
            $postcode = esc_attr($d['postcode'] ?? '');
        ?>
            <div class="bes-district-row">
                <input type="text" name="bes_district_settings[<?php echo $index; ?>][district]" value="<?php echo $district; ?>" placeholder="District">
                <input type="text" name="bes_district_settings[<?php echo $index; ?>][upazillas]" value="<?php echo $upazillas; ?>" placeholder="Upazillas (comma-separated)">
                <input type="text" name="bes_district_settings[<?php echo $index; ?>][postcode]" value="<?php echo $postcode; ?>" placeholder="Postcode">
                <button class="button remove-row">Remove</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button class="button" id="add-district-row">Add Row</button>

    <?php submit_button('Save District Settings'); ?>
<?php
}

// Save JSON on update
add_action('admin_init', function() {
    if (isset($_POST['bes_district_settings'])) {
        $data = $_POST['bes_district_settings'];
        $districts = [];
        foreach ($data as $row) {
            if (empty($row['district'])) continue;
            $districts[] = [
                'district' => sanitize_text_field($row['district']),
                'upazillas' => array_map('trim', explode(',', sanitize_text_field($row['upazillas'] ?? ''))),
                'postcode' => sanitize_text_field($row['postcode'] ?? ''),
            ];
        }
        update_option('bes_district_settings_json', wp_json_encode($districts));
    }
});
