<?php
$response = array();

// Include necessary files
require_once 'db/db_connect.php';
require_once 'functions.php';

// Get the input request parameters
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true); // convert JSON into array

// Check for mandatory parameters
$mandatoryParameters = [
    'code', 'serial_number', 'production_date', 'compliance_with_drawings_special_requests',
    'external_cleaning_surface_quality', 'internal_cleaning_surface_quality',
    'scales_of_pipe_connections', 'compatibility_of_internal_elements', 
    'checking_the_scale_from_the_head', 'checking_the_cover_o_ring_screw_nut',
    'filter_foot_cleaning', 'screw_checking_suitable_for_filter_foot',
    'checking_the_labels', 'internal_fiber_lamination', 'result', 'notes', 'number',
    'check_time', 'checker', 'check_date', 'video_link', 'client'
];

if (array_diff($mandatoryParameters, array_keys($input))) {
    $response["status"] = 2;
    $response["message"] = "Missing mandatory parameters";
} else {
    $columnNames = implode(", ", array_keys($input));
    $placeholder = rtrim(str_repeat('?, ', count($input)), ', ');

    $query = "INSERT INTO quality_control_forms ($columnNames) VALUES ($placeholder)";

    $stmt = $dbConnection->prepare($query);

    if ($stmt) {
        $bindParams = array_values($input);

        $paramTypes = str_repeat('s', count($bindParams));
        $stmt->bind_param($paramTypes, ...$bindParams);

        if ($stmt->execute()) {
            $response["status"] = 0;
            $response["message"] = "Form data inserted successfully";
        } else {
            $response["status"] = 1;
            $response["message"] = "Error in SQL statement: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $response["status"] = 1;
        $response["message"] = "Error in SQL statement preparation: " . $dbConnection->error;
    }
}

// Display the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>