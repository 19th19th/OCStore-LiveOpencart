<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		if ($this->user->isLogged() && isset($this->request->get['user_token']) && ($this->request->get['user_token'] == $this->session->data['user_token'])) {
			$data['text_version'] = sprintf($this->language->get('text_version'), VERSION);

			$php_version = phpversion();
			$ioncube_version = function_exists('ioncube_loader_version') ? ioncube_loader_version() : 'Not installed';

			$data['extra_version'] = ' | PHP ' . $php_version . ' | IonCube ' . $ioncube_version;
		} else {
			$data['text_version'] = '';
			$data['extra_version'] = '';
		}

		return $this->load->view('common/footer', $data);
	}
}
