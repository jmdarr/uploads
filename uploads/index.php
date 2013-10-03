<?php

// Imagine - Image Directory Indexer
// Written by: Jeremy Darr, 2011

// Options
///////////////////////////////////////////////////
$allowed_types = array("jpg","jpeg","png","bmp","gif");
$max_dimension = 200;
$thumb_dir="thumbs";

// Code
///////////////////////////////////////////////////
$dir_list = scandir(getcwd());
$counter=0;
if(!file_exists($thumb_dir)) {
    mkdir($thumb_dir, 0755) or die("Thumb directory creation failed!");
}
else if(file_exists($thumb_dir) && !is_dir($thumb_dir)) {
    die("File has thumbnail directory folder - please rename $thumb_dir.");
}

?>
<html><head></head>
<body>
<table width='80%' border='1' align='center'>

<?php

foreach($dir_list as $file) {
    $file_info = pathinfo($file);
    if(isset($file_info['extension']) && in_array($file_info['extension'], $allowed_types)) {
        
        $thumb = "$thumb_dir/$file";
        if(!file_exists($thumb)) {
            $image = new Imagick($file);
            $height = $image->getImageHeight();
            $width = $image->getImageWidth();

            if($image->getImageFormat() == "GIF" || $image->getImageFormat() == "gif") {
                $image = $image->coalesceImages();

                do {
                    if($height >= $width) { $image->resizeImage(0, $max_dimension, Imagick::FILTER_BOX, 1); }
                    else { $image->resizeImage($max_dimension, 0, Imagick::FILTER_BOX, 1); }
                } while($image->nextImage());

                $image = $image->deconstructImages();
                $image->writeImages($thumb, true);
            }
            else {
                if($height >= $width) { $image->resizeImage(0, $max_dimension, Imagick::FILTER_BOX, 1); }
                else { $image->resizeImage($max_dimension, 0, Imagick::FILTER_BOX, 1); }
                $image->writeImage($thumb);
            }
        }

        if($counter == 0) { echo "<tr>\n"; }
        echo "<td align='center'><a href='".$file."'><img src='".$thumb."'></td>\n";
        $counter++;
        if($counter == 4) { echo "</tr>\n"; $counter = 0; }
    }
}

?>

</table>
</html>
