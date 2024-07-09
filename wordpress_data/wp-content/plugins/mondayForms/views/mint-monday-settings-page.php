<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api_key']) && isset($_POST['board_id'])) {
    $settings = array(
        'api_key' => sanitize_text_field($_POST['api_key']),
        'board_id' => sanitize_text_field($_POST['board_id']),
        'view_id' => sanitize_text_field($_POST['view_id'])
    );
    update_option('mint_modany_settings', $settings);
    echo '<div class="updated"><p>Settings saved.</p></div>';
}

// Retrieve the settings value
$settings = get_option('mint_modany_settings', array('api_key' => '', 'board_id' => '', 'view_id' => '' ));

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
                            settings_str
                        }	
  
  }
}';


// mutation {
//     create_item(
//       board_id: 6956293697, 
//       item_name: "new item", 
//       position_relative_method: before_at, 
//       relative_to: 150847288, 
//       column_values: "{\"name\":\"Sample Name\",\"status5\":{\"label\":\"Option 1\"},\"short_text__1\":\"John Doe\",\"short_text7__1\":\"http://example.com\",\"email__1\":{\"email\":\"john.doe@example.com\",\"text\":\"john.doe@example.com\"}}"
//     ) {
//       id
//     }
//   }

// $column_values = json_encode([
//     "name" => $name,
//     "status5" => ["label" => $status_label],
//     "short_text__1" => $full_name,
//     "short_text7__1" => $test_link,
//     "email__1" => ["email" => $email, "text" => $email]
// ]);

// $query = 'mutation {
//   create_item(
//     board_id: ' . $board_id . ', 
//     item_name: "' . $item_name . '", 
//     position_relative_method: ' . $position_relative_method . ', 
//     relative_to: ' . $relative_to . ', 
//     column_values: \'' . $column_values . '\'
//   ) {
//     id
//   }
// }';

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

$data = json_decode($response, true);

// Extract column values
$columns = $data['data']['boards'][0]['columns'];
$column_values = [];

foreach ($columns as $column) {

    if(isset($column['settings_str']))
        $option_value = json_decode($column['settings_str'], true);

    $options = $option_value['labels'];

    $column_values[$column['id']] = [
        'id' => $column['id'] ,
        'title' => $column['title'],
        'type' => $column['type'],
        'settings_str' => $options
    ];
}

// Display column values

echo "<pre>";
echo "<textarea>";
print_r($column_values);
echo "</textarea>";
echo "</pre>";

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
            <tr valign="top">
                <th scope="row">View ID</th>
                <td><input type="text" name="view_id" value="<?php echo esc_attr($settings['view_id']); ?>" /></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
        </p>
    </form>
</div>
