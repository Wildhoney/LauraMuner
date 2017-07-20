<?php

define('THUMBNAIL_IMAGE_MAX_WIDTH', 400);
define('THUMBNAIL_IMAGE_MAX_HEIGHT', 400);

function generate_image_thumbnail($source_image_path) {

    list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
    switch ($source_image_type) {
        case IMAGETYPE_GIF:
            $source_gd_image = imagecreatefromgif($source_image_path);
            break;
        case IMAGETYPE_JPEG:
            $source_gd_image = imagecreatefromjpeg($source_image_path);
            break;
        case IMAGETYPE_PNG:
            $source_gd_image = imagecreatefrompng($source_image_path);
            break;
    }
    if ($source_gd_image === false) {
        return false;
    }
    $source_aspect_ratio = $source_image_width / $source_image_height;
    $thumbnail_aspect_ratio = THUMBNAIL_IMAGE_MAX_WIDTH / THUMBNAIL_IMAGE_MAX_HEIGHT;
    if ($source_image_width <= THUMBNAIL_IMAGE_MAX_WIDTH && $source_image_height <= THUMBNAIL_IMAGE_MAX_HEIGHT) {
        $thumbnail_image_width = $source_image_width;
        $thumbnail_image_height = $source_image_height;
    } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
        $thumbnail_image_width = (int) (THUMBNAIL_IMAGE_MAX_HEIGHT * $source_aspect_ratio);
        $thumbnail_image_height = THUMBNAIL_IMAGE_MAX_HEIGHT;
    } else {
        $thumbnail_image_width = THUMBNAIL_IMAGE_MAX_WIDTH;
        $thumbnail_image_height = (int) (THUMBNAIL_IMAGE_MAX_WIDTH / $source_aspect_ratio);
    }
    $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
    imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
    imagepng($thumbnail_gd_image);
    imagedestroy($source_gd_image);
    imagedestroy($thumbnail_gd_image);

    return true;

}

$read = function($path) {

    return array_values(array_filter(scandir($path), function($file) {
        $ext = pathinfo($file)['extension'];
        return $file != '.' && $file != '..' && $ext != 'mp4' && $ext != 'json' && $ext != 'DS_Store';
    }));

};

$path  = "../images/media";
$slug  = preg_replace('/[^a-z0-9\-]/i', '', $_GET['slug']);
$dir   = join(DIRECTORY_SEPARATOR, array($path, $slug));
$image = join(DIRECTORY_SEPARATOR, array($path, $slug, $read($dir)[0]));
header('Content-Type: image/png');
echo generate_image_thumbnail($image);
