<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api_key']) && isset($_POST['board_id'])) {
    $settings = array(
        'api_key' => sanitize_text_field($_POST['api_key']),
        'board_id' => sanitize_text_field($_POST['board_id']),
    );
    update_option('mint_modany_settings', $settings);
    echo '<div class="updated"><p>Settings saved.</p></div>';
}

// Retrieve the settings value
$settings = get_option('mint_modany_settings', array('api_key' => '', 'board_id' => ''));

// Your Monday.com API key
$apiKey = 'your_api_key_here';

// Board ID from which you want to fetch form submissions
$boardId = 'your_board_id_here';

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, "https://api.monday.com/v2");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);


// GraphQL query to fetch items (form submissions) from the board
$query = 'query {
                    boards (ids: 6956293697) {
                        views(ids: 150847288 ) {
                            type
                            settings_str
                            view_specific_data_str
                            name
                            id      
                        }
    
                        columns {
                            id
                            title
                            type
                        }	
  
  }
}';

$data = json_encode(['query' => $query]);

// Set headers
$headers = [
    'Content-Type: application/json',
    'Authorization: ' . $settings['api_key']
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

// Execute cURL session and fetch response
$response = curl_exec($ch);


var_dump($response);

// Settings form
?>
<div class="wrap">
    <h1>Monday.com API Integration Settings</h1>
    <form method="post" action="">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">API Key</th>
                <td><input type="text" name="api_key" value="<?php echo esc_attr($settings['api_key']); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Board ID</th>
                <td><input type="text" name="board_id" value="<?php echo esc_attr($settings['board_id']); ?>" /></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
        </p>
    </form>
</div>
