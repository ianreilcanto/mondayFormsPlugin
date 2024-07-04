<?php

class MondayAPI {
    private $apiKey;
    private $boardId;
    private $viewId;
    private $apiUrl;

    public function __construct($apiKey, $boardId, $viewId) {
        $this->apiKey = $apiKey;
        $this->boardId = $boardId;
        $this->viewId = $viewId;
        $this->apiUrl = "https://api.monday.com/v2";
    }

    private function sendRequest($query) {
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        
        $data = json_encode(['query' => $query]);
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $this->apiKey
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Execute cURL session and fetch response
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }

    public function getColumns() {
        $query = 'query {
            boards (ids: ' . $this->boardId . ') {
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

        $data = $this->sendRequest($query);
        return $data['data']['boards'][0]['columns'];
    }

    public function createFormItem($itemName, $viewId, $columnValues) {
        $columnValuesJson = json_encode($columnValues);

        $query = 'mutation {
            create_item(
                board_id: ' . $this->boardId . ',
                item_name: "' . $itemName . '",
                position_relative_method: before_at,
                relative_to: ' . $viewId . ',
                column_values: \'' . $columnValuesJson . '\'
            ) {
                id
            }
        }';

        return $this->sendRequest($query);
    }
}
