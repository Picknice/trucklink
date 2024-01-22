<?php
class ImageController {
    public static function uploadImage($image) {
        global $PATH_UPLOAD;

        $name = ImageController::setName($image['type']);
        $path = $PATH_UPLOAD . $name;
        $upload = move_uploaded_file($image['tmp_name'], $path);
        if (!$upload) return NULL;

        return $name;
    }

    public static function setName($type) {
        return time() . random_int(1000, 9999) . "." . ImageController::getTypeImg($type);
    }
    
    public static function getTypeImg($type) {
        return array_pop(explode('/', $type));
    }

    public static function deletePhoto($name) {
        global $PATH_UPLOAD;

        if (!$name) return;

        $path = $PATH_UPLOAD . $name;

        if (!file_exists($path)) return;

        return unlink($path);
    }

    public static function updatePhoto($photo_new, $photo_old) {
        ImageController::deletePhoto($photo_old);
        return ImageController::uploadImage($photo_new);
    }

    public static function checkTypePhoto($type) {
        global $ALLOWED_IMAGE_TYPES;

        return array_search($type, $ALLOWED_IMAGE_TYPES);
    }
}