<?php
class ControllerFooter extends \Core\Controller {
	public function index() {
	
            $this->template = 'footer.phtml';
		$this->render();
	}
}
?>