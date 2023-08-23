<?php
defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');

class Login extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
	}


	function index()
	{
		if(!$this->lang->line('Access')){
			$this->lang->load('custom');
		}

		$this->data['title'] = $this->lang->line('Access'); //'Acesso';

		$this->data['message'] = '';
		if (setting('login_message_enabled')) {
			$this->data['message'] = html_escape(setting('login_message_text'));
		}

		$logo = 'uploads/' . setting('logo');

		$columns = array(
			'c1' => array(
				'width' => '60%',
				'content' => $this->load->view('login/login_index', $this->data, TRUE),
			),
			'c2' => array(
				'width' => '40%',
				'content' => '',
			),
		);

		if (strlen(setting('logo')) && file_exists(FCPATH . $logo)) {
			$columns['c2']['content'] = img($logo, FALSE, 'style="max-width:100%;max-height:300px;width:auto;display:block"');
		} else {
			$columns['c2']['content'] = '';
		}

		$title = "<h2>".$this->lang->line('AccessTitle')."</h2>"; //$this->data['title']."</h2>";
		$subtitle = "<p>".$this->lang->line('AccessDescription')."</p>";
		$body = $this->load->view('columns', $columns, TRUE);

		$this->data['body'] = $title . $subtitle . $body;
		log_message('debug', 'Login.php::index()');

		return $this->render();
	}


	function submit()
	{
		log_message('debug', 'login submit');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		// Run validation
		if ($this->form_validation->run() == FALSE) {
	  		// Validation failed, load login page again
			return $this->index();
		}

		// Form validation for length etc. passed, now see if the credentials are OK in the DB
		// Post values
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		// Now see if we can login
		if ($this->userauth->log_in($username, $password)) {
			// Success! Redirect to control panel
			redirect('');
		} else {
			$this->session->set_flashdata('auth', msgbox('error', 'Login ou senha incorretos'));
			return $this->index();
		}
	}


}
