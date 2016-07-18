<?php
class ControllerStep4 extends \Core\Controller {
	public function index() {
		$this->template = 'step_4';
		$this->children = array(
			'header',
			'footer'
		);

		$this->response->setOutput($this->render());
	}
}
?>