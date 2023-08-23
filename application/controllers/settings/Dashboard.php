<?php
defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');

class Dashboard extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->require_auth_level(ADMINISTRATOR);
		redirect('settings/general');
	}


	public function index()
	{
	}



}
