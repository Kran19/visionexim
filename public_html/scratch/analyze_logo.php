<?php
$img_path = __DIR__ . '/../images/certificates/vision logo color (1).png';
if (!file_exists($img_path)) {
    die("File does not exist: " . $img_path);
}

$im = imagecreatefrompng($img_path);
if (!$im) {
    die("Failed to load image");
}

$width = imagesx($im);
$height = imagesy($im);

echo "Dimensions: $width x $height\n";

// Count colors
$colors = [];
for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $rgb = imagecolorat($im, $x, $y);
        $colors[] = $rgb;
    }
}

echo "Total pixels: " . count($colors) . "\n";
// Let's sample a few pixels or look at blue tones
$blue_pixels = 0;
foreach ($colors as $rgb) {
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    $a = ($rgb >> 24) & 0x7F;
    
    // Check for blue color: B is high, R and G are relatively low
    // Let's print some sample blue colors
    if ($b > 100 && $b > $r && $b > $g && $a < 100) {
        $blue_pixels++;
    }
}
echo "Blue pixels (rough estimate B > 100 and B > R and B > G): $blue_pixels\n";
