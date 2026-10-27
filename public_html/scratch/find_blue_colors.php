<?php
$img_path = __DIR__ . '/../images/certificates/vision logo color (1).png';
$im = imagecreatefrompng($img_path);
$width = imagesx($im);
$height = imagesy($im);

$blue_counts = [];
for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $a = ($rgb >> 24) & 0x7F;
        
        // Skip fully transparent
        if ($a >= 127) continue;
        
        // Filter for blue
        if ($b > $r && $b > $g && $b > 50) {
            $hex = sprintf("#%02x%02x%02x", $r, $g, $b);
            if (!isset($blue_counts[$hex])) {
                $blue_counts[$hex] = 0;
            }
            $blue_counts[$hex]++;
        }
    }
}

arsort($blue_counts);
echo "Top 30 blue colors:\n";
$i = 0;
foreach ($blue_counts as $hex => $count) {
    echo "$hex: $count\n";
    if (++$i >= 30) break;
}
