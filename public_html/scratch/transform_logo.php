<?php
function rgb2hsl($r, $g, $b) {
    $r /= 255;
    $g /= 255;
    $b /= 255;
    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $l = ($max + $min) / 2;
    if ($max == $min) {
        $h = $s = 0;
    } else {
        $d = $max - $min;
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
        switch ($max) {
            case $r:
                $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                break;
            case $g:
                $h = ($b - $r) / $d + 2;
                break;
            case $b:
                $h = ($r - $g) / $d + 4;
                break;
        }
        $h /= 6;
    }
    return [$h * 360, $s * 100, $l * 100];
}

function hue2rgb($p, $q, $t) {
    if ($t < 0) $t += 1;
    if ($t > 1) $t -= 1;
    if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
    if ($t < 1/2) return $q;
    if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
    return $p;
}

function hsl2rgb($h, $s, $l) {
    $h /= 360;
    $s /= 100;
    $l /= 100;
    if ($s == 0) {
        $r = $g = $b = $l;
    } else {
        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;
        $r = hue2rgb($p, $q, $h + 1/3);
        $g = hue2rgb($p, $q, $h);
        $b = hue2rgb($p, $q, $h - 1/3);
    }
    return [round($r * 255), round($g * 255), round($b * 255)];
}

$img_path = __DIR__ . '/../images/certificates/vision logo color (1).png';
$im = imagecreatefrompng($img_path);
if (!$im) {
    die("Failed to load image");
}

// Preserve transparency
imagealphablending($im, false);
imagesavealpha($im, true);

$width = imagesx($im);
$height = imagesy($im);

for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $a = ($rgb >> 24) & 0x7F;
        
        // Skip fully transparent pixels
        if ($a >= 127) continue;
        
        // Convert to HSL
        list($h, $s, $l) = rgb2hsl($r, $g, $b);
        
        // Check if the color is in the blue/teal hue range (e.g. 150 to 240 degrees)
        if ($h >= 140 && $h <= 245 && $s > 10) {
            // Shift hue to red (359.6 degrees)
            $h_new = 359.6;
            
            // Convert back to RGB
            list($r_new, $g_new, $b_new) = hsl2rgb($h_new, $s, $l);
            
            // Allocate color and set pixel
            $new_color = imagecolorallocatealpha($im, $r_new, $g_new, $b_new, $a);
            imagesetpixel($im, $x, $y, $new_color);
        }
    }
}

// Save the result
$out_path = __DIR__ . '/logo_red_test.png';
if (imagepng($im, $out_path)) {
    echo "Successfully saved transformed logo to scratch/logo_red_test.png\n";
} else {
    echo "Failed to save image\n";
}
imagedestroy($im);
