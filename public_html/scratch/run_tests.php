<?php
require_once __DIR__ . '/../admin/includes/db.php';

header('Content-Type: text/plain');
echo "=== Vision Exim Hierarchy Constraints Test ===\n\n";

// Function to simulate validation rules
function validate_category($id, $name, $parent_id) {
    global $conn;
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Category name is required.';
    }
    
    $parent_val = ($parent_id === 'none' || empty($parent_id)) ? null : (int)$parent_id;
    if ($parent_val !== null) {
        // 1. Own parent check
        if ($id !== null && $parent_val === $id) {
            $errors[] = 'A category cannot be selected as its own parent.';
        } else {
            // 2. Parent exist & not subcategory check
            $chk_p = $conn->prepare("SELECT parent_id FROM categories WHERE id = ?");
            $chk_p->bind_param('i', $parent_val);
            $chk_p->execute();
            $res_p = $chk_p->get_result()->fetch_assoc();
            if (!$res_p) {
                $errors[] = 'The selected parent category does not exist.';
            } elseif ($res_p['parent_id'] !== null) {
                $errors[] = 'The selected parent category is itself a subcategory. Multi-level nesting is not supported.';
            }
            $chk_p->close();

            // 3. Current category has children check
            if ($id !== null && empty($errors)) {
                $chk_c = $conn->prepare("SELECT COUNT(*) as cnt FROM categories WHERE parent_id = ?");
                $chk_c->bind_param('i', $id);
                $chk_c->execute();
                $res_c = $chk_c->get_result()->fetch_assoc();
                if ((int)$res_c['cnt'] > 0) {
                    $errors[] = 'This category has subcategories of its own and cannot be made a subcategory.';
                }
                $chk_c->close();
            }
        }
    }
    return $errors;
}

// Test case 1: Valid parent category creation
$err1 = validate_category(null, 'Spices', 'none');
echo "Test 1 (Valid Parent): " . (empty($err1) ? "PASSED" : "FAILED: " . implode(', ', $err1)) . "\n";

// Test case 2: Valid subcategory creation under parent 1 (Spices)
$err2 = validate_category(null, 'Whole Spices', 1);
echo "Test 2 (Valid Subcategory): " . (empty($err2) ? "PASSED" : "FAILED: " . implode(', ', $err2)) . "\n";

// Test case 3: Invalid - Own parent check
$err3 = validate_category(9, 'Whole Spices', 9);
echo "Test 3 (Own Parent Block): " . (!empty($err3) && in_array('A category cannot be selected as its own parent.', $err3) ? "PASSED" : "FAILED") . "\n";

// Test case 4: Invalid - Nested subcategory (selecting a subcategory as parent)
// 9 is Whole Spices, which has parent_id = 1.
$err4 = validate_category(null, 'Exotic Whole Spices', 9);
echo "Test 4 (Nested Parent Block): " . (!empty($err4) && strpos(implode(', ', $err4), 'itself a subcategory') !== false ? "PASSED" : "FAILED") . "\n";

// Test case 5: Invalid - Editing a category with children to make it a subcategory
// 1 is Spices, which has children (9 is Whole Spices).
$err5 = validate_category(1, 'Spices', 2);
echo "Test 5 (Parent with Children Block): " . (!empty($err5) && strpos(implode(', ', $err5), 'has subcategories of its own') !== false ? "PASSED" : "FAILED") . "\n";

echo "\nTests Completed.\n";
