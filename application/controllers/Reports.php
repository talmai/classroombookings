<?php
defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');

class Reports extends MY_Controller {


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		$this->require_auth_level(ADMINISTRATOR);
		
		$this->lang->load('reports');

		$this->load->library('pagination');
		$this->load->model('crud_model');
		$this->load->model('reports_model');
	}

	function index($page = NULL)
	{
		$pagination_config = array(
			'base_url' => site_url('reports/index'),
			'total_rows' => $this->crud_model->Count('reports'),
			'per_page' => 25,
			'full_tag_open' => '<p class="pagination">',
			'full_tag_close' => '</p>',
		);

		$this->pagination->initialize($pagination_config);

		$this->data['pagelinks'] = $this->pagination->create_links();

		$this->data['title'] = $this->lang->line('ReportsTitle');
		$this->data['showtitle'] = $this->data['title'];
		$this->data['body'] = $this->load->view('reports/index', $this->data, TRUE);

		return $this->render();
	}
}
