<?php
defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');


class Logout extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();
	}


	function index()
	{
		$this->userauth->log_out();
		redirect('');
	}


}
