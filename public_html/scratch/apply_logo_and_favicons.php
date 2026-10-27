<?php
$original_logo = __DIR__ . '/../images/certificates/vision logo color (1).png';
$backup_logo   = __DIR__ . '/../images/certificates/vision logo color (1)_backup.png';
$red_logo_src  = __DIR__ . '/logo_red_test.png';

if (!file_exists($red_logo_src)) {
    die("Red logo source not found in scratch directory. Please run transform_logo.php first.");
}

// 1. Backup original logo
if (!file_exists($backup_logo)) {
    if (copy($original_logo, $backup_logo)) {
        echo "✅ Original logo backed up to images/certificates/vision logo color (1)_backup.png\n";
    } else {
        die("❌ Failed to backup original logo");
    }
} else {
    echo "ℹ️ Backup of original logo already exists.\n";
}

// 2. Overwrite with red logo
if (copy($red_logo_src, $original_logo)) {
    echo "✅ Main logo updated with the reddish version.\n";
} else {
    die("❌ Failed to update main logo");
}

// Helper to resize and save as PNG
function resize_png($src_path, $dest_path, $w, $h) {
    $src = imagecreatefrompng($src_path);
    if (!$src) return false;
    
    $dest = imagecreatetruecolor($w, $h);
    if (!$dest) {
        imagedestroy($src);
        return false;
    }
    
    // Preserve transparency
    imagealphablending($dest, false);
    imagesavealpha($dest, true);
    
    $src_w = imagesx($src);
    $src_h = imagesy($src);
    
    if (imagecopyresampled($dest, $src, 0, 0, 0, 0, $w, $h, $src_w, $src_h)) {
        $result = imagepng($dest, $dest_path);
        imagedestroy($src);
        imagedestroy($dest);
        return $result;
    }
    
    imagedestroy($src);
    imagedestroy($dest);
    return false;
}

$favicons_dir = __DIR__ . '/../images/favicons';
if (!is_dir($favicons_dir)) {
    mkdir($favicons_dir, 0755, true);
}

// 3. Generate properly sized favicons from the new red logo
$targets = [
    'favicon-96x96.png'     => [96, 96],
    'apple-touch-icon.png'  => [180, 180],
    'favicon.png'           => [32, 32],
    'favicon.ico'           => [48, 48]
];

foreach ($targets as $filename => $sizes) {
    $dest = $favicons_dir . '/' . $filename;
    $backup = $favicons_dir . '/' . $filename . '_backup';
    
    // Backup old favicon if it exists and hasn't been backed up yet
    if (file_exists($dest) && !file_exists($backup)) {
        copy($dest, $backup);
        echo "✅ Backed up old favicon: $filename\n";
    }
    
    // Generate new resized favicon
    if (resize_png($original_logo, $dest, $sizes[0], $sizes[1])) {
        echo "✅ Generated resized favicon: $filename ($sizes[0]x$sizes[1])\n";
    } else {
        echo "❌ Failed to generate favicon: $filename\n";
    }
}

echo "\nDone!\n";
