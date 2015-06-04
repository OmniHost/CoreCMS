<?php

class ModelToolImage extends \Core\Model {

    public function resize($filename, $maxwidth = 0, $maxheight = 0) {

        if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
            return;
        }

        $info = pathinfo($filename);

        $extension = $info['extension'];

        $old_image = $filename;
        $new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int) $maxwidth . 'x' . (int) $maxheight . 'proport.' . $extension;

        if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
            $path = '';

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!file_exists(DIR_IMAGE . $path)) {
                    @mkdir(DIR_IMAGE . $path, 0777);
                }
            }

            list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $old_image);

            if (!$maxwidth) {
                $maxwidth = $width_orig;
            }
            if (!$maxheight) {
                $maxheight = $height_orig;
            }
            $widthRatio = $maxwidth / $width_orig;
            $heightRatio = $maxheight / $height_orig;

            // Ratio used for calculating new image dimensions.
            $ratio = min($widthRatio, $heightRatio);

            // Calculate new image dimensions.
            $newWidth = (int) $width_orig * $ratio;
            $newHeight = (int) $height_orig * $ratio;



            if ($width_orig != $newWidth || $height_orig != $newHeight) {
                $image = new \Core\Image(DIR_IMAGE . $old_image);
                $image->resize($newWidth, $newHeight, '');

                $image->save(DIR_IMAGE . $new_image);
            } else {
                copy(DIR_IMAGE . $old_image, DIR_IMAGE . $new_image);
            }
        }

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            return HTTPS_CATALOG . 'img/' . $new_image;
        } else {
            return HTTP_CATALOG . 'img/' . $new_image;
        }
    }

    public function resizeExact($filename, $width, $height) {
        if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
            return;
        }

        $info = pathinfo($filename);

        $extension = $info['extension'];

        $old_image = $filename;
        $new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

        if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
            $path = '';

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!file_exists(DIR_IMAGE . $path)) {
                    @mkdir(DIR_IMAGE . $path, 0777);
                }
            }

            $image = new \Core\Image(DIR_IMAGE . $old_image);
            $image->resize($width, $height);
            $image->save(DIR_IMAGE . $new_image);
        }

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            return HTTPS_CATALOG . 'img/' . $new_image;
        } else {
            return HTTP_CATALOG . 'img/' . $new_image;
        }
    }

}

?>