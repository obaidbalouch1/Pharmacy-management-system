<?php
// Settings Helper Functions

function get_setting($conn, $key, $default = '') {
    $key = mysqli_real_escape_string($conn, $key);
    $query = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key = '$key' LIMIT 1");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        return $row['setting_value'];
    }
    
    return $default;
}

function get_all_settings($conn) {
    $settings = [];
    $query = mysqli_query($conn, "SELECT setting_key, setting_value FROM settings");
    
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    // Default values
    $defaults = [
        'store_name' => 'AsadsPharma',
        'store_address' => '123 Medical Street',
        'store_phone' => '+92 334 0540325',
        'store_email' => 'info@pharmacy.com',
        'store_gst' => '29XXXXX1234X1ZX',
        'store_tagline' => 'Management System',
        'receipt_footer' => 'Thank you for your business!'
    ];
    
    foreach ($defaults as $key => $value) {
        if (!isset($settings[$key])) {
            $settings[$key] = $value;
        }
    }
    
    return $settings;
}
?>
