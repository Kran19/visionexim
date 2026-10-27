<?php
/**
 * submit-inquiry.php
 * Handles AJAX POST from the Product Enquiry modal on product.php
 * Returns JSON: { success: bool, message: string }
 */
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request.';
    echo json_encode($response);
    exit;
}

$name     = trim($_POST['name']     ?? '');
$email    = trim($_POST['email']    ?? '');
$phone    = trim($_POST['phone']    ?? '');
$company  = trim($_POST['company']  ?? '');
$country  = trim($_POST['country']  ?? '');
$quantity = trim($_POST['quantity'] ?? '');
$subject  = trim($_POST['subject']  ?? '');
$message  = trim($_POST['message']  ?? '');

if (empty($name) || empty($email)) {
    $response['message'] = 'Name and email are required.';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Please enter a valid email address.';
    echo json_encode($response);
    exit;
}

require_once __DIR__ . '/admin/includes/db.php';

// Get existing columns
$existing_cols = [];
$res = $conn->query("DESCRIBE inquiries");
if ($res) {
    while ($col = $res->fetch_assoc()) {
        $existing_cols[] = $col['Field'];
    }
}

// Always-present fields
$fields = ['contact_name', 'email', 'phone', 'requested_product', 'message', 'source', 'status'];
$values = [$name, $email, $phone, $subject, $message, 'website', 'new'];
$types  = 'sssssss';

// Add quantity if column exists
if (in_array('quantity', $existing_cols)) {
    $fields[] = 'quantity';
    $values[] = $quantity;
    $types   .= 's';
}

// Add company_name if column exists
if (in_array('company_name', $existing_cols)) {
    $fields[] = 'company_name';
    $values[] = $company;
    $types   .= 's';
}

// Add country_name if column exists
if (in_array('country_name', $existing_cols)) {
    $fields[] = 'country_name';
    $values[] = $country;
    $types   .= 's';
}

// Add created_at if column exists
if (in_array('created_at', $existing_cols)) {
    $fields[] = 'created_at';
    $values[] = date('Y-m-d H:i:s');
    $types   .= 's';
}

$placeholders = implode(', ', array_fill(0, count($fields), '?'));
$field_list   = implode(', ', $fields);

$stmt = $conn->prepare("INSERT INTO inquiries ($field_list) VALUES ($placeholders)");

if (!$stmt) {
    error_log('Inquiry Insert Error: ' . $conn->error);
    $response['message'] = 'An error occurred while submitting your inquiry. Please try again later.';
    echo json_encode($response);
    exit;
}

$stmt->bind_param($types, ...$values);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Thank you! Your enquiry has been received. We will get back to you shortly.';
} else {
    error_log('Inquiry Execute Error: ' . $stmt->error);
    $response['message'] = 'An error occurred while submitting your inquiry. Please try again later.';
}

$stmt->close();
echo json_encode($response);
