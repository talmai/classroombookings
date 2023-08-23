<?php
defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');

class Setup extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->require_auth_level(ADMINISTRATOR);
	}


	public function index()
	{
		if(!$this->lang->line('Setup')){
			$this->lang->load('custom');
		}
		
		$data = [
			'school_menu' => $this->menu_model->setup_school(),
			'manage_menu' => $this->menu_model->setup_manage(),
			'reports_menu' => $this->menu_model->setup_reports(), // Ibam: Added
		];

		$this->data['title'] = $this->lang->line('Setup');
		$this->data['body'] = '';
		$this->data['body'] .= $this->session->flashdata('auth');
		$this->data['body'] .= $this->load->view('setup/index', $data, TRUE);

		return $this->render();
	}



}
