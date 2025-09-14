<?php
if (!defined('ABSPATH')) exit;

function bes_delivery_tab(){
    $saved = get_option('bes_delivery_scheduler', []);
    $delivery = wp_parse_args($saved, [
        'enabled' => true,
        'time_slots' => [],
        'blackout_dates' => [],
    ]);

    // Default 5 Time Slots
    if(empty($delivery['time_slots'])) {
        $delivery['time_slots'] = [
            '9 AM - 12 PM',
            '12 PM - 3 PM',
            '3 PM - 6 PM',
            '6 PM - 9 PM',
            '9 PM - 11 PM',
        ];
    }

    // Default 5 Blackout Dates
    if(empty($delivery['blackout_dates'])) {
        $delivery['blackout_dates'] = [];
        for($i=1;$i<=5;$i++){
            $delivery['blackout_dates'][] = date('Y-m-d', strtotime("+$i day"));
        }
    }

    settings_fields('bes_delivery_group');
    do_settings_sections('bes_delivery_group');

    echo '<h2>Delivery Scheduler</h2>';
    echo '<div class="bes-delivery-card">';

    // Enable Scheduler
    echo '<label class="switch">';
    echo '<input type="checkbox" name="bes_delivery_scheduler[enabled]" '.checked($delivery['enabled'],true,false).'>';
    echo '<span class="slider round"></span> Enable Delivery Scheduler';
    echo '</label>';

    // Time Slots Repeater
    echo '<h4>Available Time Slots</h4>';
    echo '<div id="bes-time-slots">';
    foreach($delivery['time_slots'] as $slot){
        echo '<div class="bes-repeater-item"><input type="text" name="bes_delivery_scheduler[time_slots][]" value="'.esc_attr($slot).'" placeholder="e.g. 9 AM - 12 PM"><button class="button remove-item">Remove</button></div>';
    }
    echo '</div>';
    echo '<button class="button" id="add-slot">Add Time Slot</button>';
    echo '<button class="button" id="reset-slot">Reset Default 5</button>';

    // Blackout Dates Repeater
    echo '<h4>Blackout Dates</h4>';
    echo '<div id="bes-blackout-dates">';
    foreach($delivery['blackout_dates'] as $date){
        echo '<div class="bes-repeater-item"><input type="text" class="date-picker" name="bes_delivery_scheduler[blackout_dates][]" value="'.esc_attr($date).'" placeholder="YYYY-MM-DD"><button class="button remove-item">Remove</button></div>';
    }
    echo '</div>';
    echo '<button class="button" id="add-date">Add Blackout Date</button>';
    echo '<button class="button" id="reset-date">Reset Default 5</button>';

    echo '</div>';

    submit_button('Save Delivery Settings');
    ?>

    <style>
        .bes-delivery-card { border:1px solid #ddd; padding:15px; border-radius:6px; background:#fdfdfd; max-width:650px; }
        .bes-repeater-item { display:flex; gap:10px; margin-bottom:5px; align-items:center; }
        .bes-repeater-item input { padding:6px; width:200px; }
        .switch { position: relative; display: inline-block; width:40px; height:20px; margin-bottom:15px; }
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
            // Date picker
            $('.date-picker').datepicker({ dateFormat: 'yy-mm-dd' });

            // Add/Remove Time Slot
            $('#add-slot').click(function(e){ e.preventDefault();
                $('#bes-time-slots').append('<div class="bes-repeater-item"><input type="text" name="bes_delivery_scheduler[time_slots][]" placeholder="e.g. 9 AM - 12 PM"><button class="button remove-item">Remove</button></div>');
            });
            $('#reset-slot').click(function(e){ e.preventDefault();
                let defaults = ['9 AM - 12 PM','12 PM - 3 PM','3 PM - 6 PM','6 PM - 9 PM','9 PM - 11 PM'];
                $('#bes-time-slots').html('');
                defaults.forEach(function(s){ $('#bes-time-slots').append('<div class="bes-repeater-item"><input type="text" name="bes_delivery_scheduler[time_slots][]" value="'+s+'"><button class="button remove-item">Remove</button></div>'); });
            });

            // Add/Remove Blackout Date
            $('#add-date').click(function(e){ e.preventDefault();
                $('#bes-blackout-dates').append('<div class="bes-repeater-item"><input type="text" class="date-picker" name="bes_delivery_scheduler[blackout_dates][]" placeholder="YYYY-MM-DD"><button class="button remove-item">Remove</button></div>');
                $('.date-picker').datepicker({ dateFormat: 'yy-mm-dd' });
            });
            $('#reset-date').click(function(e){ e.preventDefault();
                let today = new Date(); let dates=[];
                for(let i=1;i<=5;i++){ let d=new Date(); d.setDate(today.getDate()+i); dates.push(d.toISOString().split('T')[0]); }
                $('#bes-blackout-dates').html('');
                dates.forEach(function(d){ $('#bes-blackout-dates').append('<div class="bes-repeater-item"><input type="text" class="date-picker" name="bes_delivery_scheduler[blackout_dates][]" value="'+d+'"><button class="button remove-item">Remove</button></div>'); });
                $('.date-picker').datepicker({ dateFormat: 'yy-mm-dd' });
            });

            // Remove any item
            $(document).on('click','.remove-item',function(e){ e.preventDefault(); $(this).parent().remove(); });
        });
    </script>

    <?php
}

// Admin notice
add_action('admin_notices', function() {
    if(isset($_GET['settings-updated']) && $_GET['settings-updated']==='true' && isset($_GET['tab']) && $_GET['tab']==='delivery'){
        echo '<div class="notice notice-success is-dismissible"><p>Delivery settings saved successfully!</p></div>';
    }
});
