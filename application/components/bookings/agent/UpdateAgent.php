<?php

namespace app\components\bookings\agent;

defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');


use app\components\bookings\exceptions\AgentException;
use app\components\bookings\Slot;
use \Bookings_model;


/**
 * Agent handles the editing of a booking.
 *
 */
class UpdateAgent extends BaseAgent
{


	// Agent type
	const TYPE = 'update';

	// Features that can be changed
	const FEATURE_DATE = 'date';
	const FEATURE_PERIOD = 'period';
	const FEATURE_ROOM = 'room';
	const FEATURE_DEPARTMENT = 'department';
	const FEATURE_USER = 'user';
	const FEATURE_NOTES = 'notes';

	// Edit modes
	const EDIT_ONE = '1';
	const EDIT_FUTURE = 'future';
	const EDIT_ALL = 'all';


	// Booking being edited
	private $booking;

	// Edit mode
	private $edit_mode;

	// Features
	private $features = [];


	/**
	 * Initialise the Agent with some values.
	 *
	 * Depending on the type of booking, these will be retrieved from different places.
	 *
	 */
	public function load()
	{
		// Load booking that is being edited
		$booking_id = $this->CI->input->post_get('booking_id');
		$includes = [
			'week',
			'period',
			'room',
			'department',
			'user',
		];
		if (strlen($booking_id)) $this->booking = $this->CI->bookings_model->include($includes)->get($booking_id);
		if ( ! $this->booking) throw AgentException::forNoBooking();

		// Get session of booking
		$this->session = $this->CI->sessions_model->get($this->booking->session_id);
		if ( ! $this->session) throw AgentException::forNoSession();

		// Load custom localization
		if(!$this->CI->lang->line('Rooms')){
			$this->CI->lang->load('custom');
		}
		// Get edit mode.
		// This flag helps determine what fields can be edited (important for recurring bookings selection)
		// Options are single, future, or all.
		$this->edit_mode = $this->CI->input->post_get('edit')
			? $this->CI->input->post_get('edit')
			: self::EDIT_ONE;

		// Determine what aspects can be changed.
		$default_feature = ($this->is_admin) ? TRUE : FALSE;

		$this->features = [
			self::FEATURE_DATE => $default_feature,
			self::FEATURE_PERIOD => $default_feature,
			self::FEATURE_ROOM => $default_feature,
			self::FEATURE_DEPARTMENT => $default_feature,
			self::FEATURE_USER => $default_feature,
			self::FEATURE_NOTES => $default_feature,
		];

		// Booking owners can change the notes
		if ($this->booking->user_id == $this->user->user_id) {
			$this->features[self::FEATURE_NOTES] = TRUE;
		}

		// If a recurring booking future or all is being edited, then it can't be moved.
		if ($this->booking->repeat_id) {
			if (in_array($this->edit_mode, [self::EDIT_FUTURE, self::EDIT_ALL])) {
				$this->features[self::FEATURE_DATE] = FALSE;
				$this->features[self::FEATURE_PERIOD] = FALSE;
				$this->features[self::FEATURE_ROOM] = FALSE;
			}
		}

		$this->handle_edit();
	}


	private function handle_edit()
	{
		$this->view = 'bookings/edit/form';
		$this->title = $this->CI->lang->line('Editbooking');

		if ($this->CI->input->post()) {
			$this->process_edit_booking();
		}
	}


	/**
	 * Main vars to ensure are in the view.
	 *
	 */
	public function get_view_data()
	{
		$vars = [

			'booking' => $this->booking,
			'features' => $this->features,
			'edit_mode' => $this->edit_mode,

		];

		return $vars;
	}


	/**
	 * Edit a booking
	 *
	 */
	private function process_edit_booking()
	{
		$rules = $this->get_validation_rules($this->booking->booking_id);
		$this->CI->load->library('form_validation');
		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() == FALSE) {
			$this->message = $this->CI->lang->line('ErrorInvalidValues');
			return FALSE;
		}

		// Build data array with values that can be updated.
		// These are passed directly to db->update(), so it should only include
		// the fields that are permitted according to the edit mode.

		$booking_data = [];

		if ($this->features[self::FEATURE_DATE]) {
			$booking_data['date'] = $this->CI->input->post('booking_date');
		}

		if ($this->features[self::FEATURE_PERIOD]) {
			$booking_data['period_id'] = $this->CI->input->post('period_id');
		}

		if ($this->features[self::FEATURE_ROOM]) {
			$booking_data['room_id'] = $this->CI->input->post('room_id');
		}

		if ($this->features[self::FEATURE_DEPARTMENT]) {
			$booking_data['department_id'] = $this->CI->input->post('department_id');
		}

		if ($this->features[self::FEATURE_USER]) {
			$booking_data['user_id'] = $this->CI->input->post('user_id');
		}

		if ($this->features[self::FEATURE_NOTES]) {
			$booking_data['notes'] = $this->CI->input->post('notes');
		}

		$update = $this->CI->bookings_model->update($this->booking->booking_id, $booking_data, $this->edit_mode);

		if ($update) {

			$msgs = [
				self::EDIT_ONE => $this->CI->lang->line('msgBookingEdit1'),
				self::EDIT_FUTURE => $this->CI->lang->line('msgBookingEditFuture'),
				self::EDIT_ALL => $this->CI->lang->line('msgBookingEditAll'),
			];

			$this->message = $msgs[$this->edit_mode];
			$this->success = TRUE;

			return TRUE;
		}

		$err = $this->CI->bookings_model->get_error();

		$this->message = ($err)
			? $err
			: $this->CI->lang->line('ErrorCreatingBooking');

		return FALSE;
	}


	private function get_validation_rules($booking_id)
	{
		$rules = [];

		if ($this->features[self::FEATURE_DATE]) {
			$rules[] = ['field' => 'booking_date', 'label' => $this->CI->lang->line('Date'), 'rules' => sprintf('required|valid_date|no_conflict[%d]', $booking_id)];
		}

		if ($this->features[self::FEATURE_PERIOD]) {
			$rules[] = ['field' => 'period_id', 'label' => $this->CI->lang->line('Period'), 'rules' => 'required|integer'];
		}

		if ($this->features[self::FEATURE_ROOM]) {
			$rules[] = ['field' => 'room_id', 'label' => $this->CI->lang->line('Room'), 'rules' => 'required|integer'];
		}

		if ($this->features[self::FEATURE_DEPARTMENT]) {
			$rules[] = ['field' => 'department_id', 'label' => $this->CI->lang->line('Department'), 'rules' => 'integer'];
		}

		if ($this->features[self::FEATURE_USER]) {
			$rules[] = ['field' => 'user_id', 'label' => $this->lang->CI->line('User'), 'rules' => 'integer'];
		}

		if ($this->features[self::FEATURE_NOTES]) {
			$rules[] = ['field' => 'notes', 'label' => $this->lang->CI->line('Notes'), 'rules' => 'max_length[255]'];
		}

		return $rules;
	}


}
