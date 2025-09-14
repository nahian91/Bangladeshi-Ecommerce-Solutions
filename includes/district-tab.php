<?php
if (!defined('ABSPATH')) exit;

function bes_district_tab(){
    // Get saved JSON or initialize
    $json_data = get_option('bes_district_settings_json', '[]');
    $districts = json_decode($json_data, true);

    if(!is_array($districts)) $districts = [];

    // Ensure 5 default rows
    for($i=0; $i<5; $i++){
        if(!isset($districts[$i])){
            $districts[$i] = ['district'=>'','upazillas'=>[''],'postcode'=>''];
        }
    }

    echo '<h2>District & Upazilla Auto-Fill</h2>';
    echo '<p>Add districts, upazillas (comma-separated) and postcode. Add more rows if needed.</p>';

    echo '<div id="bes-district-repeater">';
    foreach($districts as $index => $d){
        $district = esc_attr($d['district'] ?? '');
        $upazillas = esc_attr(implode(',', $d['upazillas'] ?? []));
        $postcode = esc_attr($d['postcode'] ?? '');
        echo '<div class="bes-district-row" style="margin-bottom:10px;">';
        echo '<input type="text" name="bes_district_settings['.$index.'][district]" value="'.$district.'" placeholder="District" style="width:150px;"> ';
        echo '<input type="text" name="bes_district_settings['.$index.'][upazillas]" value="'.$upazillas.'" placeholder="Upazillas (comma-separated)" style="width:200px;"> ';
        echo '<input type="text" name="bes_district_settings['.$index.'][postcode]" value="'.$postcode.'" placeholder="Postcode" style="width:100px;"> ';
        echo '<button class="button remove-row">Remove</button>';
        echo '</div>';
    }
    echo '</div>';
    echo '<button class="button" id="add-district-row">Add Row</button>';

    submit_button('Save District Settings');

    ?>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        jQuery(document).ready(function($){
            var rowIndex = <?php echo count($districts); ?>;

            $('#add-district-row').click(function(e){
                e.preventDefault();
                var html = '<div class="bes-district-row" style="margin-bottom:10px;">' +
                           '<input type="text" name="bes_district_settings['+rowIndex+'][district]" placeholder="District" style="width:150px;"> ' +
                           '<input type="text" name="bes_district_settings['+rowIndex+'][upazillas]" placeholder="Upazillas (comma-separated)" style="width:200px;"> ' +
                           '<input type="text" name="bes_district_settings['+rowIndex+'][postcode]" placeholder="Postcode" style="width:100px;"> ' +
                           '<button class="button remove-row">Remove</button>' +
                           '</div>';
                $('#bes-district-repeater').append(html);
                rowIndex++;
            });

            $(document).on('click','.remove-row', function(e){
                e.preventDefault();
                $(this).closest('.bes-district-row').remove();
            });
        });
    </script>

    <style>
        #bes-district-repeater input { padding:4px; margin-right:5px; }
        #bes-district-repeater .button { vertical-align:middle; }
    </style>
    <?php
}

// Save JSON on update
add_action('admin_init', function(){
    if(isset($_POST['bes_district_settings'])){
        $data = $_POST['bes_district_settings'];
        $districts = [];
        foreach($data as $row){
            if(empty($row['district'])) continue;
            $districts[] = [
                'district' => sanitize_text_field($row['district']),
                'upazillas' => array_map('trim', explode(',', sanitize_text_field($row['upazillas'] ?? ''))),
                'postcode' => sanitize_text_field($row['postcode'] ?? ''),
            ];
        }
        update_option('bes_district_settings_json', wp_json_encode($districts));
    }
});
