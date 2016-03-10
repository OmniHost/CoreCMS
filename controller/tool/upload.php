<?php

class ControllerToolUpload extends \Core\Controller {

    public function index() {
        $this->load->language('tool/upload');

        $json = array();

        if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
            // Sanitize the filename
            $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

            // Validate the filename length
            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
                $json['error'] = $this->language->get('error_filename');
            }

            // Allowed file extension types
            $allowed = array();

            $extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

            $filetypes = explode("\n", $extension_allowed);

            foreach ($filetypes as $filetype) {
                $allowed[] = trim($filetype);
            }

            if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
                $json['error'] = $this->language->get('error_filetype');
            }

            // Allowed file mime types
            $allowed = array();

            $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

            $filetypes = explode("\n", $mime_allowed);

            foreach ($filetypes as $filetype) {
                $allowed[] = trim($filetype);
            }

            if (!in_array($this->request->files['file']['type'], $allowed)) {
                $json['error'] = $this->language->get('error_filetype');
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($this->request->files['file']['tmp_name']);

            if (preg_match('/\<\?php/i', $content)) {
                $json['error'] = $this->language->get('error_filetype');
            }

            // Return any upload error
            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
            }
        } else {
            $json['error'] = $this->language->get('error_upload');
        }

        if (!$json) {
            $file = $filename . '.' . md5(mt_rand());

            move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);

            // Hide the uploaded file name so people can not link to it directly.
            $this->load->model('tool/upload');

            $json['code'] = $this->model_tool_upload->addUpload($filename, $file);

            $json['success'] = $this->language->get('text_upload');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function newsletter() {
        $this->load->language('tool/upload');
        $this->load->model('tool/image');
        $width = !empty($this->request->post['width']) ? $this->request->post['width'] : '0';
        $height = !empty($this->request->post['height']) ? $this->request->post['height'] : '0';

        $json = array();

        if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
            // Sanitize the filename
            $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

            $filename = filenameslug($filename);
            $filename = substr($filename, -60);

            // Validate the filename length
            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
                $json['error'] = $this->language->get('error_filename');
            }

            // Allowed file extension types
            $allowed = array(
                'jpg',
                'jpeg',
                'gif',
                'png'
            );

            if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
                $json['error'] = $this->language->get('error_filetype');
            }

            // Allowed file mime types
            $allowed = array(
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/x-png',
                'image/gif'
            );

            if (!in_array($this->request->files['file']['type'], $allowed)) {
                $json['error'] = $this->language->get('error_filetype');
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($this->request->files['file']['tmp_name']);

            if (preg_match('/\<\?php/i', $content)) {
                $json['error'] = $this->language->get('error_filetype');
            }

            // Return any upload error
            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
            }
        } else {
            $json['error'] = $this->language->get('error_upload');
        }


        if (!is_dir(DIR_IMAGE . 'newsletters')) {
            mkdir(DIR_IMAGE . 'newsletters');
        }

        if (!$json) {
            $file = 'newsletters/'.  md5(mt_rand()) . $filename ;

            move_uploaded_file($this->request->files['file']['tmp_name'], DIR_IMAGE . $file);

            $json['url'] = $this->config->get('config_url') . 'img/' . $file;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        //  $this->not_found();
    }

}
