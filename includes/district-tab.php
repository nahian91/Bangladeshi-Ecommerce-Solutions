<?php
if (!defined('ABSPATH')) exit;

/**
 * ===============================
 * 1️⃣ Prefilled District, Upazilla & Thana Settings
 * ===============================
 */
function bes_district_tab() {
    // Default districts
    $districts = [
        ['district'=>'Dhaka','upazillas'=>[
            ['name'=>'Dhaka Sadar','thanas'=>['Thana 1','Thana 2']],
            ['name'=>'Narayanganj Sadar','thanas'=>['Thana A','Thana B']],
            ['name'=>'Munshiganj Sadar','thanas'=>['Thana X','Thana Y']]
        ],'postcode'=>'1000-2399'],
        ['district'=>'Mymensingh','upazillas'=>[
            ['name'=>'Jamalpur Sadar','thanas'=>['Thana 1']],
            ['name'=>'Sherpur Sadar','thanas'=>['Thana 2']],
            ['name'=>'Mymensingh Sadar','thanas'=>['Thana 3']]
        ],'postcode'=>'2000-2499'],
        ['district'=>'Sylhet','upazillas'=>[
            ['name'=>'Sylhet Sadar','thanas'=>['Thana 1']],
            ['name'=>'Sunamganj Sadar','thanas'=>['Thana 2']],
            ['name'=>'Habiganj Sadar','thanas'=>['Thana 3']]
        ],'postcode'=>'3000-3399'],
        ['district'=>'Chattogram','upazillas'=>[
            ['name'=>'Chattogram Sadar','thanas'=>['Thana 1']],
            ['name'=>'Khagrachhari Sadar','thanas'=>['Thana 2']],
            ['name'=>'Cox\'s Bazar Sadar','thanas'=>['Thana 3']]
        ],'postcode'=>'4000-4799']
        // Add more districts as needed...
    ];

    // Load saved data
    $saved = get_option('bes_district_settings', ['enabled'=>1,'json'=>'']);
    $saved_json = !empty($saved['json']) ? json_decode($saved['json'], true) : $districts;
    $enabled = isset($saved['enabled']) ? $saved['enabled'] : 1;

    ?>
    <div class="wrap">
        <h1>District, Upazilla & Thana Settings</h1>

        <form method="post" action="">
            <?php wp_nonce_field('bes_save_district_settings','bes_district_nonce'); ?>

            <p>
                <label>
                    <strong>Enable District Feature:</strong>
                    <input type="checkbox" name="bes_enabled" value="1" <?php checked($enabled,1); ?>>
                </label>
            </p>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>District</th>
                        <th>Upazilla</th>
                        <th>Thana</th>
                        <th>Postcode</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($saved_json as $d_index => $d_row):
                    foreach($d_row['upazillas'] as $u_index => $upazilla):
                        foreach($upazilla['thanas'] as $t_index => $thana):
                ?>
                    <tr>
                        <?php if($u_index==0 && $t_index==0): ?>
                            <td rowspan="<?php echo count($d_row['upazillas']) * max(1,count($upazilla['thanas'])); ?>">
                                <input type="text" name="districts[<?php echo $d_index; ?>][district]" value="<?php echo esc_attr($d_row['district']); ?>" style="width:100%;">
                            </td>
                        <?php endif; ?>

                        <td>
                            <input type="text" name="districts[<?php echo $d_index; ?>][upazillas][<?php echo $u_index; ?>][name]" value="<?php echo esc_attr($upazilla['name']); ?>" style="width:100%;">
                        </td>

                        <td>
                            <input type="text" name="districts[<?php echo $d_index; ?>][upazillas][<?php echo $u_index; ?>][thanas][]" value="<?php echo esc_attr($thana); ?>" style="width:100%;">
                        </td>

                        <?php if($u_index==0 && $t_index==0): ?>
                            <td rowspan="<?php echo count($d_row['upazillas']) * max(1,count($upazilla['thanas'])); ?>">
                                <input type="text" name="districts[<?php echo $d_index; ?>][postcode]" value="<?php echo esc_attr($d_row['postcode']); ?>" style="width:100%;">
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php
                        endforeach;
                    endforeach;
                endforeach;
                ?>
                </tbody>
            </table>

            <br>
            <input type="submit" name="bes_save_districts" value="Save Settings" class="button button-primary">
        </form>
    </div>
    <?php
}

/**
 * ===============================
 * Save District Settings
 * ===============================
 */
add_action('admin_init', function(){
    if(isset($_POST['bes_save_districts']) && check_admin_referer('bes_save_district_settings','bes_district_nonce')){
        $enabled = isset($_POST['bes_enabled']) ? 1 : 0;
        $districts = $_POST['districts'] ?? [];

        $save_arr = [];
        foreach($districts as $d){
            $upazillas = [];
            if(isset($d['upazillas'])){
                foreach($d['upazillas'] as $u){
                    $upazillas[] = [
                        'name'=>sanitize_text_field($u['name']),
                        'thanas'=>array_map('sanitize_text_field',$u['thanas'] ?? [])
                    ];
                }
            }

            $save_arr[] = [
                'district'=>sanitize_text_field($d['district']),
                'upazillas'=>$upazillas,
                'postcode'=>sanitize_text_field($d['postcode'])
            ];
        }

        update_option('bes_district_settings',['enabled'=>$enabled,'json'=>wp_json_encode($save_arr)]);
    }
});

/**
 * ===============================
 * WooCommerce Checkout Fields
 * ===============================
 */
add_filter('woocommerce_checkout_fields', function($fields){
    $saved = get_option('bes_district_settings', ['enabled'=>0,'json'=>'']);
    $enabled = $saved['enabled'] ?? 0;
    if(!$enabled) return $fields;

    $districts = json_decode($saved['json'], true);
    $district_options = [''=>'Select District'];
    $DIST = [];

    if($districts){
        foreach($districts as $d){
            $district_options[$d['district']] = $d['district'];
            $DIST[$d['district']] = [
                'upazillas'=>array_column($d['upazillas'],'name'),
                'thanas'=>array_column($d['upazillas'],'thanas'),
                'postcode'=>$d['postcode']
            ];
        }
    }

    $fields['billing']['billing_district'] = [
        'type'=>'select',
        'label'=>'District',
        'required'=>true,
        'class'=>['form-row-first'],
        'options'=>$district_options
    ];
    $fields['billing']['billing_upazilla'] = [
        'type'=>'select',
        'label'=>'Upazilla',
        'required'=>true,
        'class'=>['form-row-last'],
        'options'=>[''=>'Select Upazilla']
    ];
    $fields['billing']['billing_thana'] = [
        'type'=>'select',
        'label'=>'Thana',
        'required'=>true,
        'class'=>['form-row-wide'],
        'options'=>[''=>'Select Thana']
    ];
    $fields['billing']['billing_postcode'] = [
        'type'=>'text',
        'label'=>'Postcode',
        'required'=>true,
        'class'=>['form-row-wide']
    ];

    return $fields;
});

/**
 * ===============================
 * Frontend JS: Auto-fill Upazilla, Thana & Postcode
 * ===============================
 */
add_action('wp_footer', function(){
    $saved = get_option('bes_district_settings', ['enabled'=>0,'json'=>'']);
    $enabled = $saved['enabled'] ?? 0;
    if(!$enabled) return;

    $districts = json_decode($saved['json'], true);
    if(!$districts) return;

    $DIST = [];
    foreach($districts as $d){
        $DIST[$d['district']] = [
            'upazillas'=>array_column($d['upazillas'],'name'),
            'thanas'=>array_column($d['upazillas'],'thanas'),
            'postcode'=>$d['postcode']
        ];
    }
    ?>
    <script>
    jQuery(document).ready(function($){
        const DISTRICTS = <?php echo wp_json_encode($DIST); ?>;

        function populateUpazilla(district){
            const $up = $('#billing_upazilla');
            $up.empty();
            $up.append('<option value="">Select Upazilla</option>');

            const $th = $('#billing_thana');
            $th.empty();
            $th.append('<option value="">Select Thana</option>');

            if(DISTRICTS[district]){
                DISTRICTS[district].upazillas.forEach((u,i)=>{
                    $up.append(`<option value="${u}">${u}</option>`);
                });
                $('#billing_postcode').val(DISTRICTS[district].postcode);
            } else {
                $('#billing_postcode').val('');
            }
        }

        function populateThana(upazilla){
            const district = $('#billing_district').val();
            const $th = $('#billing_thana');
            $th.empty();
            $th.append('<option value="">Select Thana</option>');

            if(DISTRICTS[district]){
                const idx = DISTRICTS[district].upazillas.indexOf(upazilla);
                if(idx >= 0){
                    DISTRICTS[district].thanas[idx].forEach(t=>{
                        $th.append(`<option value="${t}">${t}</option>`);
                    });
                }
            }
        }

        populateUpazilla($('#billing_district').val());
        populateThana($('#billing_upazilla').val());

        $(document).on('change','#billing_district',function(){
            populateUpazilla($(this).val());
        });

        $(document).on('change','#billing_upazilla',function(){
            populateThana($(this).val());
        });

        $('body').on('updated_checkout',function(){
            populateUpazilla($('#billing_district').val());
            populateThana($('#billing_upazilla').val());
        });
    });
    </script>
    <?php
});
