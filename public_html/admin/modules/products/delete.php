<?php
/**
 * Vision Exim — Delete Product Action
 */
include '../../includes/auth.php';
include '../../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
        exit;
    }

    // Fetch the product image path to delete it from disk
    $stmt = $conn->prepare("SELECT image, gallery_images FROM products WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $prod = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($prod) {
        // Delete main featured image from disk
        if (!empty($prod['image']) && file_exists('../../../' . $prod['image'])) {
            @unlink('../../../' . $prod['image']);
        }
        
        // Delete gallery images from disk
        if (!empty($prod['gallery_images'])) {
            $gallery = json_decode($prod['gallery_images'], true);
            if (is_array($gallery)) {
                foreach ($gallery as $img) {
                    if (file_exists('../../../' . $img)) {
                        @unlink('../../../' . $img);
                    }
                }
            }
        }
    }

    // Perform deletion
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
