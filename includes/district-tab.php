<?php
if (!defined('ABSPATH')) exit;

/**
 * ===============================
 * 1️⃣ District & Upazilla Admin
 * ===============================
 */
function bes_district_tab() {
    $json_data = get_option('bes_district_settings_json', '[]');
    $districts = json_decode($json_data, true);
    if (!is_array($districts)) $districts = [];

    for ($i = 0; $i < 5; $i++) {
        if (!isset($districts[$i])) {
            $districts[$i] = ['district' => '', 'upazillas' => [''], 'postcode' => ''];
        }
    }
    ?>
    <h2>District & Upazilla Auto-Fill</h2>
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

    <script>
    jQuery(document).ready(function($){
        $('#add-district-row').click(function(e){
            e.preventDefault();
            let index = $('#bes-district-repeater .bes-district-row').length;
            $('#bes-district-repeater').append(`
                <div class="bes-district-row">
                    <input type="text" name="bes_district_settings[${index}][district]" placeholder="District">
                    <input type="text" name="bes_district_settings[${index}][upazillas]" placeholder="Upazillas (comma-separated)">
                    <input type="text" name="bes_district_settings[${index}][postcode]" placeholder="Postcode">
                    <button class="button remove-row">Remove</button>
                </div>
            `);
        });

        $(document).on('click', '.remove-row', function(e){
            e.preventDefault();
            $(this).parent().remove();
        });
    });
    </script>
<?php
}

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

/**
 * ===============================
 * 2️⃣ Add District & Upazilla to Checkout
 * ===============================
 */
add_filter('woocommerce_checkout_fields', function($fields){
    $districts = json_decode(get_option('bes_district_settings_json', '[]'), true);
    $district_options = ['' => 'Select District'];
    if ($districts) {
        foreach($districts as $d){
            $district_options[$d['district']] = $d['district'];
        }
    }

    $fields['billing']['billing_district'] = [
        'type' => 'select',
        'label' => 'District',
        'required' => true,
        'class' => ['form-row-first'],
        'id' => 'billing_district',
        'options' => $district_options
    ];

    $fields['billing']['billing_upazilla'] = [
        'type' => 'select',
        'label' => 'Upazilla',
        'required' => true,
        'class' => ['form-row-last'],
        'id' => 'billing_upazilla',
        'options' => ['' => 'Select Upazilla']
    ];

    return $fields;
});

/**
 * ===============================
 * 3️⃣ Frontend JS: Auto-fill Upazilla
 * ===============================
 */
add_action('wp_footer', function(){
    $districts = json_decode(get_option('bes_district_settings_json', '[]'), true);
    if (!$districts) return;
    ?>
    <script>
    jQuery(document).ready(function($){
        const DISTRICTS = <?php echo wp_json_encode($districts); ?>;

        function populateUpazilla(district){
            let upazillas = [''];
            DISTRICTS.forEach(d => {
                if(d.district === district){
                    upazillas = d.upazillas;
                }
            });
            const $upazilla = $('#billing_upazilla');
            $upazilla.empty();
            $upazilla.append(`<option value="">Select Upazilla</option>`);
            upazillas.forEach(u => {
                $upazilla.append(`<option value="${u}">${u}</option>`);
            });
        }

        function initUpazillaDropdown(){
            populateUpazilla($('#billing_district').val());
        }

        // On change
        $(document).on('change', '#billing_district', function(){
            populateUpazilla($(this).val());
        });

        // WooCommerce updates checkout via AJAX
        $('body').on('updated_checkout', function(){
            initUpazillaDropdown();
        });

        // Init on page load
        initUpazillaDropdown();
    });
    </script>
    <?php
});
