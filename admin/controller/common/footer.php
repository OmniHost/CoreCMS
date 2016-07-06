<?php
/**
 * CoreCMS - Bootstrap Based PHP 5 CMS
 * @name Administration Common footer
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2016 Craig smith
 * @link        http://www.omnihost.co.nz
 * @license     http://www.omnihost.co.nz/cms-license
 * @version     1.8.0
 * @package     CoreCMS
 *  @visibility private
 */
 

class ControllerCommonFooter extends \Core\Controller {

    public function index() {

        $this->template = 'common/footer.phtml';
        $this->data['token'] = $this->session->data['token'];
        $this->render();
    }

}
