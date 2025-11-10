<?php

// Supabase configuration
$supabaseUrl = 'https://jmfgsgatvkzofwmbciqp.supabase.co';
$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImptZmdzZ2F0dmt6b2Z3bWJjaXFwIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTYzOTUwNzcsImV4cCI6MjA3MTk3MTA3N30.GlSDSeO4ZUTR4DZhMCe9k7DBcgnbrP8JgvVX8CNuUAo'; 
$tableName = 'YOUR_TABLE_NAME'; // Replace with your table name

// Function to perform cURL requests
function supabaseRequest($method, $url, $data = null) {
    global $supabaseKey;

    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $supabaseKey,
        'apikey: ' . $supabaseKey,
    ]);

    // Add data for POST and PUT requests
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    // Execute the request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
    } else {
        // Decode and display the response
        $data = json_decode($response, true);
        print_r($data);
    }

    // Close the cURL session
    curl_close($ch);
}

// GET request
echo "GET request:\n";
supabaseRequest('GET', $supabaseUrl . '/rest/v1/' . $tableName);

// POST request
echo "\nPOST request:\n";
$postData = [
    'column1' => 'value1',
    'column2' => 'value2',
]; // Replace with your actual data structure
supabaseRequest('POST', $supabaseUrl . '/rest/v1/' . $tableName, $postData);

// PUT request
echo "\nPUT request:\n";
$putData = [
    'column1' => 'updated_value1',
]; // Replace with the data you want to update
$specificId = 1; // Replace with the actual ID of the record to update
supabaseRequest('PUT', $supabaseUrl . '/rest/v1/' . $tableName . '?id=eq.' . $specificId, $putData);

// DELETE request
echo "\nDELETE request:\n";
$specificIdToDelete = 1; // Replace with the actual ID of the record to delete
supabaseRequest('DELETE', $supabaseUrl . '/rest/v1/' . $tableName . '?id=eq.' . $specificIdToDelete);

?>