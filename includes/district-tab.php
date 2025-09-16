<?php
if (!defined('ABSPATH')) exit;

/**
 * 1️⃣ Prefilled District & Upazilla Settings
 */
function bes_district_tab() {
    // Prefilled districts with all upazillas per district
    $districts = [
        ['district'=>'Dhaka','upazillas'=>['Dhaka Sadar','Narayanganj Sadar','Munshiganj Sadar','Narsingdi Sadar','Gazipur Sadar','Manikganj Sadar','Tangail Sadar','Kishoreganj Sadar'],'postcode'=>'1000-2399'],
        ['district'=>'Mymensingh','upazillas'=>['Jamalpur Sadar','Sherpur Sadar','Mymensingh Sadar','Netrokona Sadar'],'postcode'=>'2000-2499'],
        ['district'=>'Sylhet','upazillas'=>['Sunamganj Sadar','Sylhet Sadar','Moulvibazar Sadar','Habiganj Sadar'],'postcode'=>'3000-3399'],
        ['district'=>'Chattogram','upazillas'=>['Chattogram Sadar','Khagrachhari Sadar','Rangamati Sadar','Bandarban Sadar','Cox\'s Bazar Sadar'],'postcode'=>'4000-4799'],
        ['district'=>'Rangpur','upazillas'=>['Panchagarh Sadar','Thakurgaon Sadar','Dinajpur Sadar','Nilphamari Sadar','Rangpur Sadar','Lalmonirhat Sadar','Kurigram Sadar','Gaibandha Sadar'],'postcode'=>'5000-5799'],
        ['district'=>'Rajshahi','upazillas'=>['Bogura Sadar','Joypurhat Sadar','Rajshahi Sadar','Chapai Nawabganj Sadar','Natore Sadar','Naogaon Sadar','Pabna Sadar','Sirajganj Sadar'],'postcode'=>'5800-6799'],
        ['district'=>'Barishal','upazillas'=>['Barishal Sadar','Bhola Sadar','Jhalokati Sadar','Pirojpur Sadar','Patuakhali Sadar','Barguna Sadar'],'postcode'=>'8200-8799'],
    ];

    $json_data = get_option('bes_district_settings_json', '');
    if (empty($json_data)) {
        update_option('bes_district_settings_json', wp_json_encode($districts));
    }

    // Admin form display
    ?>
    <h2>District & Upazilla Auto-Fill</h2>
    <div id="bes-district-repeater">
        <?php foreach ($districts as $index => $d): ?>
            <div class="bes-district-row" style="margin-bottom:10px;">
                <input type="text" name="bes_district_settings[<?php echo $index; ?>][district]" value="<?php echo esc_attr($d['district']); ?>" placeholder="District">
                <input type="text" name="bes_district_settings[<?php echo $index; ?>][upazillas]" value="<?php echo esc_attr(implode(',', $d['upazillas'])); ?>" placeholder="Upazillas (comma-separated)">
                <input type="text" name="bes_district_settings[<?php echo $index; ?>][postcode]" value="<?php echo esc_attr($d['postcode']); ?>" placeholder="Postcode">
            </div>
        <?php endforeach; ?>
    </div>
    <?php submit_button('Save District Settings'); ?>
    <?php
}

// Save district settings
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
 * 2️⃣ Checkout Fields
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

    $fields['billing']['billing_postcode'] = [
        'type' => 'text',
        'label' => 'Postcode',
        'required' => true,
        'class' => ['form-row-wide'],
        'id' => 'billing_postcode',
    ];

    return $fields;
});

/**
 * 3️⃣ Frontend JS: Auto-fill Upazilla + Postcode
 */
add_action('wp_footer', function(){
    $districts = json_decode(get_option('bes_district_settings_json', '[]'), true);
    if (!$districts) return;

    // Group upazillas by district
    $DIST = [];
    foreach($districts as $d){
        $DIST[$d['district']] = [
            'upazillas' => $d['upazillas'],
            'postcode' => $d['postcode']
        ];
    }
    ?>
    <script>
    jQuery(document).ready(function($){
        const DISTRICTS = <?php echo wp_json_encode($DIST); ?>;

        function populateUpazilla(district){
            const $upazilla = $('#billing_upazilla');
            $upazilla.empty();
            $upazilla.append('<option value="">Select Upazilla</option>');

            if(DISTRICTS[district]){
                DISTRICTS[district].upazillas.forEach(u => {
                    $upazilla.append(`<option value="${u}">${u}</option>`);
                });
                $('#billing_postcode').val(DISTRICTS[district].postcode);
            } else {
                $('#billing_postcode').val('');
            }
        }

        // Init on load
        populateUpazilla($('#billing_district').val());

        // On change
        $(document).on('change', '#billing_district', function(){
            populateUpazilla($(this).val());
        });

        // WooCommerce AJAX updates
        $('body').on('updated_checkout', function(){
            populateUpazilla($('#billing_district').val());
        });
    });
    </script>
    <?php
});
