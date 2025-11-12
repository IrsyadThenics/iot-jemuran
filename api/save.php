<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil raw JSON
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    // Ambil field dari JSON
    $device_id    = $data['device_id']    ?? null;
    $rain_value   = $data['rain_value']   ?? null;
    $status       = $data['status']       ?? null;
    $motor_action = $data['motor_action'] ?? null;
    $created_at   = date('Y-m-d H:i:s');

    if ($device_id && $rain_value && $status && $motor_action) {
        $sql = "INSERT INTO sensor_data (device_id, rain_value, status, motor_action, created_at) 
                VALUES ('$device_id', '$rain_value', '$status', '$motor_action', '$created_at')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(["status" => "OK", "message" => "Data saved successfully"]);
        } else {
            echo json_encode(["status" => "ERROR", "message" => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(["status" => "ERROR", "message" => "Missing fields", "received" => $data]);
    }
} else {
    echo json_encode(["status" => "Invalid Request"]);
}
?>
