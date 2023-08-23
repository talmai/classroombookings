<?php
defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');

class Access_control extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_auth_level(ADMINISTRATOR);
		if(!$this->lang->line('Rooms')){
			$this->lang->load('custom');
		}

		$this->load->model('access_control_model');
		$this->load->model('departments_model');
		$this->load->model('rooms_model');
		$this->load->model('users_model');

		$this->data['showtitle'] = $this->lang->line('Rooms');

		$this->data['rooms_icons'] = [
			['rooms', 'Rooms', 'school_manage_rooms.png'],
			['rooms/fields', $this->lang->line('CustomFields'), 'room_fields.png'],
			['access_control', $this->lang->line('AccessControl'), 'key.png'],
		];
	}


	public function index()
	{
		if(!$this->lang->line('Rooms')){
			$this->lang->load('custom');
		}
				
		$filter_keys = ['actor', 'department_id', 'room_id'];
		$filter = [];
		foreach ($filter_keys as $k) {
			if ($v = $this->input->get($k)) {
				$filter[$k] = $v;
			}
		}

		$this->data['filter'] = $filter;

		$this->data['items'] = $this->access_control_model->get_all_items($filter);
		$this->data['grouped_items'] = $this->access_control_model->group_items($this->data['items']);

		$this->data['departments'] = $this->departments_model->Get(NULL, NULL, NULL);
		$this->data['rooms'] = $this->rooms_model->Get();

		$this->data['title'] = $this->lang->line('Rooms');

		$icons = iconbar($this->data['rooms_icons'], 'access_control');
		$body = $this->load->view('access_control/index', $this->data, TRUE);

		$this->data['body'] = $icons . $body;

		return $this->render();
	}


	public function add($view = 'form')
	{
		$views = ['form', 'link'];
		if ( ! in_array($view, $views)) {
			show_404();
		}

		$this->data['title'] = $this->lang->line('AddEntry');

		$this->data['departments'] = $this->departments_model->Get(NULL, NULL, NULL);
		$this->data['rooms'] = $this->rooms_model->Get();

		$body = $this->load->view("access_control/add_{$view}", $this->data, TRUE);

		if ($this->input->get_request_header('X-Up-Target')) {
			echo $body;
			return;
		}

		$icons = iconbar($this->data['rooms_icons'], 'access_control');
		$title = "<h2>{$this->data['title']}</h2>";

		$this->data['body'] = $icons . $title . $body;

		return $this->render();
	}


	public function save()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_message('is_unique', $this->lang->line('msgEntryExists'));

		$this->form_validation->set_rules('target', $this->lang->line('Targettype'), 'required|exact_length[1]');
		$this->form_validation->set_rules('target_id', $this->lang->line('RoomID'), 'required|integer');
		$this->form_validation->set_rules('actor',  $this->lang->line('Actor'), 'required|exact_length[1]');

		switch ($this->input->post('actor')) {
			case 'D':
				$this->form_validation->set_rules('department_id',  $this->lang->line('Department'), 'required|integer');
			break;
		}

		$data = array(
			'target' => $this->input->post('target'),
			'target_id' => $this->input->post('target_id'),
			'actor' => $this->input->post('actor'),
			'actor_id' => NULL,
			'permission' => Access_control_model::ACCESS_VIEW,
		);

		switch ($data['actor']) {
			case 'D':
				$data['actor_id'] = $this->input->post('department_id');
			break;
		}

		$reference = $this->access_control_model->get_reference($data);
		$_POST['reference'] = $reference;

		$this->form_validation->set_rules('reference', 'Entry', 'is_unique[access_control.reference]');

		if ($this->form_validation->run() == FALSE) {
			return $this->add();
		}

		$entry_id = $this->access_control_model->add_entry($data);

		if ($entry_id) {
			$line = sprintf($this->lang->line('crbs_action_added'), 'Entry');
			$flashmsg = msgbox('info', $line);
		} else {
			$line = sprintf($this->lang->line('crbs_action_dberror'), 'adding');
			$flashmsg = msgbox('error', $line);
		}

		$this->session->set_flashdata('saved', $flashmsg);

		$this->output->set_header('X-Up-Method: GET');
		$this->output->set_header('X-Up-Location: ' . site_url('access_control'));

		redirect('access_control');
	}



	/**
	 * Controller function to delete an entry
	 *
	 */
	function delete($id = NULL)
	{
		if ($id) {
			$this->access_control_model->delete_entry($id);
			$flashmsg = msgbox('info', $this->lang->line('crbs_action_deleted'));
			$this->session->set_flashdata('saved', $flashmsg);
		} else {
			$flashmsg = msgbox('error', "No ID supplied");
			$this->session->set_flashdata('saved', $flashmsg);
		}

		redirect('access_control');
	}




}
