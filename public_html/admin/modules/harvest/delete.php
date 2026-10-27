<?php
/**
 * Delete a harvest calendar record via AJAX POST.
 * Expects: POST { id: int }
 * Returns: JSON { success: bool, message: string }
 */
header('Content-Type: application/json');
include '../../includes/auth.php';
include '../../includes/db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    $response['message'] = 'Invalid crop ID.';
    echo json_encode($response);
    exit;
}

// Delete the record
$stmt = $conn->prepare("DELETE FROM harvest_calendar WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Crop deleted successfully.';
    } else {
        $response['message'] = 'Record not found.';
    }
} else {
    $response['message'] = 'Database error: ' . $conn->error;
}

$stmt->close();
echo json_encode($response);
