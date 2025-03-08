<?php

if (!defined('SITE_URL')) {
    define('SITE_URL', 'http://127.0.0.1/fern/');
}

if (!defined('UPLOAD_IMAGE_PATH')) {
    define('UPLOAD_IMAGE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Fern/images/');
}

// Backend
if (!defined('ROOMS_FOLDER')) {
    define('ROOMS_FOLDER', 'rooms/');
}

// Frontend
if (!defined('ROOMS_IMG_PATH')) {
    define('ROOMS_IMG_PATH', SITE_URL . 'Images/rooms/');
}

if (!function_exists('adminLogin')) {
    function adminLogin()
    {
        session_start();
        if (!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)) {
            echo "<script>
                    window.location.href = 'index.php';
                </script>";
            exit;
        }
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        echo "<script>
                    window.location.href = '$url';
                </script>";
        exit;
    }
}

if (!function_exists('alert')) {
    function alert($type, $msg)
    {
        $bs_class = ($type == 'success') ? 'alert-success' : 'alert-danger';
        echo <<<alert
                        <div class="alert $bs_class alert-dismissible custom-alert fade show" role="alert" >
                            <strong class='me-3'>$msg</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>       
                        </div> 
            alert;
    }
}

if (!function_exists('uploadImage')) {
    function uploadImage($image, $folder)
    {
        $valid_mime = ['image/jpeg', 'image/png', 'image/webp'];
        $img_mime = $image['type'];

        if (!in_array($img_mime, $valid_mime)) {
            return 'inv_img'; // Invalid image mime or format
        } else if (($image['size'] / (1024 * 1024)) > 2) {
            return 'inv_size'; // Invalid size of the uploaded file (2 MB limit)
        } else {
            $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $rname = 'IMAGE_' . random_int(11111, 99999) . ".$ext";
            $img_path = UPLOAD_IMAGE_PATH . $folder . $rname;

            if (move_uploaded_file($image['tmp_name'], $img_path)) {
                return $rname;
            }
            return 'upd_failed';
        }
    }
}

if (!function_exists('deleteImage')) {
    function deleteImage($image, $folder)
    {
        if (unlink(UPLOAD_IMAGE_PATH . $folder . $image)) {
            return true;
        } else {
            return false;
        }
    }
}
?>
