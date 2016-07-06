<?php

class ControllerStep3 extends \Core\Controller {

    private $error = array();

    public function index() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->model('install');

            $this->model_install->database($this->request->post);


            $admin_config = $front_config = '<?php ' . "\n";
            $admin_config .= "//@autoGeneratred Config \n";
            $front_config .= "//@autoGeneratred Config \n";

            $front_config .= "define('DIR_ROOT', __DIR__ . '/'); \n";
            $front_config .= "define('DIR_APPLICATION', DIR_ROOT); \n";
            $front_config .= "define('DIR_PLUGINS', DIR_ROOT . 'plugins/'); \n";
            $front_config .= "define('DIR_LANGUAGE', DIR_APPLICATION . 'language/'); \n";
            $front_config .= "define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/'); \n";
            $front_config .= "define('DIR_SYSTEM', DIR_ROOT . 'system/'); \n";
            $front_config .= "define('DIR_STORAGE', DIR_SYSTEM . 'storage/'); \n";
            $front_config .= "define('DIR_DOWNLOAD', DIR_STORAGE . 'downloads/'); \n";
            $front_config .= "define('DIR_VENDOR', DIR_ROOT . 'vendor/'); \n";
            $front_config .= "define('DIR_IMAGE', DIR_ROOT . 'img/'); \n";
            $front_config .= "define('DIR_CONFIG', DIR_APPLICATION . '_configs/'); \n";
            $front_config .= "define('DIR_CACHE', DIR_STORAGE . 'cache/'); \n";
            $front_config .= "define('DIR_LOGS', DIR_STORAGE . 'logs/'); \n";
            $front_config .= "define('DIR_UPLOAD', DIR_STORAGE . 'upload/'); \n";
            $front_config .= "define('DIR_MODIFICATION', DIR_STORAGE . 'modification/'); \n";



            $front_config .= '$config' . " = array(
                'DB_DRIVER' => '" . addslashes($this->request->post['db_driver']) . "',
                'DB_HOSTNAME' => '" . addslashes($this->request->post['db_host']) . "',
                'DB_USERNAME' => '" . addslashes($this->request->post['db_user']) . "',
                'DB_PASSWORD' => '" . addslashes($this->request->post['db_password']) . "',
                'DB_DATABASE' => '" . addslashes($this->request->post['db_name']) . "',
                'DB_PREFIX' => '" . addslashes($this->request->post['db_prefix']) . "',
                'DB_LOG' => false,
                'config_url' => '" . HTTP_CORECMS . "',
                'config_ssl' => '" . HTTP_CORECMS . "',
                );";

            $admin_config .= "define('DIR_ROOT', dirname(__DIR__) . '/'); \n";
            $admin_config .= "define('DIR_APPLICATION', DIR_ROOT . 'admin/');\n";
            $admin_config .= "define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');\n";
            $admin_config .= "define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');\n";
            $admin_config .= "define('DIR_SYSTEM', DIR_ROOT . 'system/');\n";
            $admin_config .= "define('DIR_STORAGE', DIR_SYSTEM . 'storage/'); \n";
            $admin_config .= "define('DIR_DOWNLOAD', DIR_STORAGE . 'downloads/'); \n";
            $admin_config .= "define('DIR_VENDOR', DIR_ROOT . 'vendor/');\n";
            $admin_config .= "define('DIR_IMAGE', DIR_ROOT . 'img/');\n";
            $admin_config .= "define('DIR_CONFIG', DIR_ROOT . '_configs/');\n";
            $admin_config .= "define('DIR_CACHE', DIR_STORAGE . 'cache/');\n";
            $admin_config .= "define('DIR_LOGS', DIR_STORAGE . 'logs/');\n";
            $admin_config .= "define('DIR_PLUGINS', DIR_ROOT . 'plugins/');\n";
            $admin_config .= "define('DIR_UPLOAD', DIR_STORAGE . 'upload/'); \n";
            $admin_config .= "define('DIR_MODIFICATION', DIR_STORAGE . 'modification/'); \n";
            $admin_config .= '$config' . " = array(
                'DB_DRIVER' => '" . addslashes($this->request->post['db_driver']) . "',
                'DB_HOSTNAME' => '" . addslashes($this->request->post['db_host']) . "',
                'DB_USERNAME' => '" . addslashes($this->request->post['db_user']) . "',
                'DB_PASSWORD' => '" . addslashes($this->request->post['db_password']) . "',
                'DB_DATABASE' => '" . addslashes($this->request->post['db_name']) . "',
                'DB_PREFIX' => '" . addslashes($this->request->post['db_prefix']) . "',
                'DB_LOG' => false,
                'config_catalog' => '" . HTTP_CORECMS . "',
                    'config_catalog_ssl' => '" . HTTP_CORECMS . "',
                'config_url' => '" . HTTP_CORECMS . "admin/',
                'config_ssl' => '" . HTTP_CORECMS . "admin/',
                );";


            $file = fopen(DIR_ROOT . 'config.php', 'w');

            fwrite($file, $front_config);

            fclose($file);

            $filea = fopen(DIR_ROOT . 'admin/config.php', 'w');

            fwrite($filea, $admin_config);

            fclose($filea);



            $this->redirect($this->url->link('step_4'));
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['db_driver'])) {
            $this->data['error_db_driver'] = $this->error['db_driver'];
        } else {
            $this->data['error_db_driver'] = '';
        }

        if (isset($this->error['db_host'])) {
            $this->data['error_db_host'] = $this->error['db_host'];
        } else {
            $this->data['error_db_host'] = '';
        }

        if (isset($this->error['db_user'])) {
            $this->data['error_db_user'] = $this->error['db_user'];
        } else {
            $this->data['error_db_user'] = '';
        }

        if (isset($this->error['db_name'])) {
            $this->data['error_db_name'] = $this->error['db_name'];
        } else {
            $this->data['error_db_name'] = '';
        }

        if (isset($this->error['db_prefix'])) {
            $this->data['error_db_prefix'] = $this->error['db_prefix'];
        } else {
            $this->data['error_db_prefix'] = '';
        }

        if (isset($this->error['username'])) {
            $this->data['error_username'] = $this->error['username'];
        } else {
            $this->data['error_username'] = '';
        }

        if (isset($this->error['password'])) {
            $this->data['error_password'] = $this->error['password'];
        } else {
            $this->data['error_password'] = '';
        }

        if (isset($this->error['email'])) {
            $this->data['error_email'] = $this->error['email'];
        } else {
            $this->data['error_email'] = '';
        }

        $this->data['action'] = $this->url->link('step_3');

        if (isset($this->request->post['db_driver'])) {
            $this->data['db_driver'] = $this->request->post['db_driver'];
        } else {
            $this->data['db_driver'] = 'mysqli';
        }

        if (isset($this->request->post['db_host'])) {
            $this->data['db_host'] = $this->request->post['db_host'];
        } else {
            $this->data['db_host'] = 'localhost';
        }

        if (isset($this->request->post['db_user'])) {
            $this->data['db_user'] = html_entity_decode($this->request->post['db_user']);
        } else {
            $this->data['db_user'] = '';
        }

        if (isset($this->request->post['db_password'])) {
            $this->data['db_password'] = html_entity_decode($this->request->post['db_password']);
        } else {
            $this->data['db_password'] = '';
        }

        if (isset($this->request->post['db_name'])) {
            $this->data['db_name'] = html_entity_decode($this->request->post['db_name']);
        } else {
            $this->data['db_name'] = '';
        }

        if (isset($this->request->post['db_prefix'])) {
            $this->data['db_prefix'] = html_entity_decode($this->request->post['db_prefix']);
        } else {

            $this->data['db_prefix'] = generateRandomString(3) . '_';
        }

        if (isset($this->request->post['username'])) {
            $this->data['username'] = $this->request->post['username'];
        } else {
            $this->data['username'] = 'superadmin';
        }

        if (isset($this->request->post['password'])) {
            $this->data['password'] = $this->request->post['password'];
        } else {
            $this->data['password'] = '';
        }

        if (isset($this->request->post['email'])) {
            $this->data['email'] = $this->request->post['email'];
        } else {
            $this->data['email'] = '';
        }

        $this->data['back'] = $this->url->link('step_2');

        $this->template = 'step_3.tpl';
        $this->children = array(
            'header',
            'footer'
        );

        $this->response->setOutput($this->render());
    }

    private function validate() {

        if (!$this->request->post['db_host']) {
            $this->error['db_host'] = 'Host required!';
        }

        if (!$this->request->post['db_user']) {
            $this->error['db_user'] = 'User required!';
        }

        if (!$this->request->post['db_name']) {
            $this->error['db_name'] = 'Database Name required!';
        }

        if ($this->request->post['db_prefix'] && preg_match('/[^a-z0-9_]/', $this->request->post['db_prefix'])) {
            $this->error['db_prefix'] = 'DB Prefix can only contain lowercase characters in the a-z range, 0-9 and "_"!';
        }

        if ($this->request->post['db_driver'] == 'mysql') {
            if (function_exists('mysql_connect')) {
                if (!$connection = @mysql_connect($this->request->post['db_host'], $this->request->post['db_user'], $this->request->post['db_password'])) {
                    $this->error['warning'] = 'Error: Could not connect to the database please make sure the database server, username and password is correct!';
                } else {
                    if (!@mysql_select_db($this->request->post['db_name'], $connection)) {
                        $this->error['warning'] = 'Error: Database does not exist!';
                    }

                    mysql_close($connection);
                }
            } else {
                $this->error['db_driver'] = 'MySQL is not supported on your server! Try using MySQLi';
            }
        }

        if ($this->request->post['db_driver'] == 'mysqli') {
            if (function_exists('mysqli_connect')) {
                $connection = new mysqli($this->request->post['db_host'], $this->request->post['db_user'], $this->request->post['db_password'], $this->request->post['db_name']);

                if (mysqli_connect_error()) {
                    $this->error['warning'] = 'Error: Could not connect to the database please make sure the database server, username and password is correct!';
                } else {
                    $connection->close();
                }
            } else {
                $this->error['db_driver'] = 'MySQLi is not supported on your server! Try using MySQL';
            }
        }

        if (!$this->request->post['username']) {
            $this->error['username'] = 'Username required!';
        }

        if (!$this->request->post['password']) {
            $this->error['password'] = 'Password required!';
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
            $this->error['email'] = 'Invalid E-Mail!';
        }

        if (!is_writable(DIR_CONFIG . 'config.php')) {
            $this->error['warning'] = 'Error: Could not write to config.php please check you have set the correct permissions on: ' . DIR_OPENCART . 'config.php!';
        }


        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}

?>