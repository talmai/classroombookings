<?php

namespace app\components\bookings\agent;

defined('BASEPATH') OR exit('Nenhum acesso direto ao script � permitido');


use app\components\bookings\exceptions\AgentException;
use app\components\bookings\Slot;
use \Bookings_model;


/**
 * Agent handles the creation/editing/cancellation of bookings.
 *
 */
class SingleAgent extends BaseAgent
{


	// Agent type
	const TYPE = 'single';

	// For single
	protected $period;
	protected $room;
	protected $department;
	protected $date_info;
	protected $datetime;
	protected $week;
	protected $recurring_dates;



	/**
	 * Initialise the Agent with some values.
	 *
	 * Depending on the type of booking, these will be retrieved from different places.
	 *
	 */
	public function load()
	{
		$this->view = 'bookings/create/single';
		
		if ( !($this->CI->lang->line('msgConfirmBooking')) ) {
			$this->CI->lang->load('custom');
		}

		$period_id = $this->CI->input->post_get('period_id');
		if (strlen($period_id)) $this->period = $this->CI->periods_model->Get($period_id);
		if ( ! $this->period) throw AgentException::forNoPeriod();

		$room_id = $this->CI->input->post_get('room_id');
		if (strlen($room_id)) $this->room = $this->CI->rooms_model->get_bookable_rooms($this->user->user_id, $room_id);
		if ( ! $this->room) throw AgentException::forNoRoom();

		$date = $this->CI->input->post_get('date');
		$this->date_info = $this->CI->dates_model->get_by_date($date);
		if ( ! $this->date_info) throw AgentException::forInvalidDate();

		$this->week = $this->CI->weeks_model->get($this->date_info->week_id);
		if ( ! $this->week) throw AgentException::forNoWeek();

		$this->datetime = datetime_from_string($this->date_info->date);

		$department_id = $this->user->department_id;
		if ($this->is_admin && $this->CI->input->post('department_id')) {
			$department_id = $this->CI->input->post('department_id');
		}
		if (strlen($department_id)) $this->department = $this->CI->departments_model->Get($department_id);

		$this->session = $this->CI->sessions_model->get_by_date($this->datetime);
		if ( ! $this->session) throw AgentException::forNoSession();

		// List of dates that a recurring booking can begin/end on.
		$this->recurring_dates = $this->CI->dates_model->get_recurring_dates($this->session->session_id, $this->date_info->week_id, $this->date_info->weekday);
		
	}


	/**
	 * Main vars to ensure are in the view.
	 *
	 */
	public function get_view_data()
	{
		$vars = [

			'recurring_dates' => $this->recurring_dates,

			'room' => $this->room,
			'period' => $this->period,
			'department' => $this->department,
			'date_info' => $this->date_info,
			'datetime' => $this->datetime,
			'week' => $this->week,

		];

		return $vars;
	}


	/**
	 * Process the input and state.
	 *
	 * This should return bool TRUE for success of whole process.
	 * Returning FALSE keeps the process active.
	 *
	 */
	public function process()
	{
		$action = $this->CI->input->post('action');
		if ( ! $action) return;

		switch ($action) {

			// Single booking: validate / create.
			case 'create':
				return $this->create_single_booking();
				break;

			case 'preview_recurring':
				return $this->preview_single_recurring();
				break;

			case 'create_recurring':
				return $this->create_single_recurring();
				break;
		}
	}


	/**
	 * Create a single booking.
	 *
	 */
	private function create_single_booking()
	{
		$rules = [
			['field' => 'date', 'label' => $this->CI->lang->line('Date'), 'rules' => 'required|valid_date'],
			['field' => 'period_id', 'label' => $this->CI->lang->line('Period'), 'rules' => 'required|integer'],
			['field' => 'room_id', 'label' => $this->CI->lang->line('Room'), 'rules' => 'required|integer'],
			['field' => 'notes', 'label' => $this->CI->lang->line('Notes'), 'rules' => 'max_length[255]'],
		];

		$this->CI->load->library('form_validation');
		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() == FALSE) {
			$this->message = $this->CI->lang->line('ErrorInvalidValues');
			return FALSE;
		}

		$booking_data = [
			'date' => $this->datetime->format('Y-m-d'),
			'notes' => $this->CI->input->post('notes'),
			'period_id' => $this->period->period_id,
			'room_id' => $this->room->room_id,
			'department_id' => $this->department ? $this->department->department_id : null,
			'session_id' => $this->session->session_id,
			'user_id' => $this->user->user_id,
		];


		if ($this->is_admin) {
			// Allow user to be changed
			$post_user_id = $this->CI->input->post('user_id');
			$booking_data['user_id'] = strlen($post_user_id) ? $post_user_id : null;
		}

		$booking_id = $this->CI->bookings_model->create($booking_data);

		if ($booking_id) {
			$this->success = TRUE;
			$this->message = $this->CI->lang->line('msgConfirmBooking');
			return TRUE;
		}

		$err = $this->CI->bookings_model->get_error();

		$this->message = ($err)
			? $err
			: $this->CI->lang->line('ErrorCreatingBooking');

		return FALSE;
	}


	/**
	 * Single recurring booking marked as recurring: show preview of generated bookings.
	 *
	 */
	private function preview_single_recurring()
	{
		$rules = [];

		if ($this->CI->input->post('recurring_start') == 'session') {

			$rules[] = ['field' => 'recurring_start', 'label' => $this->CI->lang->line('Startdate'), 'rules' => 'required'];
			$recurring_start = false;

			foreach ($this->recurring_dates as $row) {
				$dt = datetime_from_string($row->date);
				if ($dt >= $this->session->date_start) {
					$recurring_start = clone $dt;
					break;
				}
			}

		} else {
			$rules[] = ['field' => 'recurring_start', 'label' => $this->CI->lang->line('Startdate'), 'rules' => 'required|valid_date'];
			$recurring_start = $this->CI->input->post('recurring_start');
		}

		if ($this->CI->input->post('recurring_end') == 'session') {

			$rules[] = ['field' => 'recurring_end', 'label' => $this->CI->lang->line('Enddate'), 'rules' => 'required'];
			$recurring_end = FALSE;

			foreach (array_reverse($this->recurring_dates) as $row) {
				$dt = datetime_from_string($row->date);
				if ($dt <= $this->session->date_end) {
					$recurring_end = clone $dt;
					break;
				}
			}

		} else {
			$rules[] = ['field' => 'recurring_end', 'label' => $this->CI->lang->line('Enddate'), 'rules' => 'required|valid_date'];
			$recurring_end = $this->CI->input->post('recurring_end');
		}

		// $this->data['recurring_start'] = $recurring_start;
		// $this->data['recurring_end'] = $recurring_end;

		$this->CI->load->library('form_validation');
		$this->CI->form_validation->set_rules($rules);

		if ($this->CI->form_validation->run() == FALSE) {
			$this->message = $this->CI->lang->line('ErrorInvalidValues');
			return FALSE;
		}

		$recurring_start = datetime_from_string($recurring_start);
		$recurring_end = datetime_from_string($recurring_end);


		$dates = [];
		$slots = [];

		// Generate a list of all recurring dates that the user
		// has requested to be filled, starting with the list of *all* possible
		// recurring dates.
		//
		foreach ($this->recurring_dates as $row) {
			if ($row->date < $recurring_start) continue;
			if ($row->date > $recurring_end) continue;
			$date_ymd = $row->date->format('Y-m-d');
			// List of dates for booking checking
			$dates[] = $date_ymd;
			// Slot for references
			$key = Slot::generate_key($date_ymd, $this->period->period_id, $this->room->room_id);
			$slots[$key]['datetime'] = $row->date;
		}

		// Get list of recurring bookings for these dates
		$existing_bookings = $this->CI->bookings_model->find_conflicts($dates, $this->period->period_id, $this->room->room_id);

		foreach ($slots as $key => $slot) {

			// List of possible actions for each slot
			$actions = [];

			if (array_key_exists($key, $existing_bookings)) {
				$actions['do_not_book'] = $this->CI->lang->line('Keepexistingbooking');
				$actions['replace'] = $this->CI->lang->line('Replaceexistingbooking');
				$slots[$key]['booking'] = $existing_bookings[$key];
			} else {
				$actions['book'] = $this->CI->lang->line('Book');
				$actions['do_not_book'] = $this->CI->lang->line('Donotbook');
			}

			$slots[$key]['actions'] = $actions;

		}

		$this->view_data['slots'] = $slots;

		$this->title = $this->CI->lang->line('Previewrecurbooks');

		$this->view = 'bookings/create/single_recurring_preview';
	}


	/**
	 * Create a single recurring booking.
	 *
	 */
	private function create_single_recurring()
	{
		$dates = $this->CI->input->post('dates');

		if (empty($dates)) {
			$this->message = $this->CI->lang->line('Nodatesselected');
			return FALSE;
		}

		$repeat_data = [
			'session_id' => $this->session->session_id,
			'period_id' => $this->period->period_id,
			'room_id' => $this->room->room_id,
			'user_id' => $this->user->user_id,
			'department_id' => $this->department ? $this->department->department_id : NULL,
			'week_id' => $this->date_info->week_id,
			'weekday' => $this->date_info->weekday,
			'status' => Bookings_model::STATUS_BOOKED,
			'notes' => $this->CI->input->post('notes'),
			'dates' => $dates,
		];

		if ($this->is_admin) {
			// Allow user to be changed
			$post_user_id = $this->CI->input->post('user_id');
			$repeat_data['user_id'] = strlen($post_user_id) ? $post_user_id : null;
		}

		// bookings_repeat_model will also handle creation of individual bookings.
		$repeat_id = $this->CI->bookings_repeat_model->create($repeat_data);

		if ( ! $repeat_id) {
			$this->message = $this->CI->lang->line('ErrorCreatingRecurBooking');
			return FALSE;
		}

		$this->success = TRUE;
		$this->message = $this->CI->lang->line('msgConfirmBooking');
		return TRUE;
	}


}
