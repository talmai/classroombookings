<?php
defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');

use app\components\bookings\Context;
use app\components\bookings\Grid;
use app\components\bookings\agent\SingleAgent;
use app\components\bookings\agent\MultiAgent;
use app\components\bookings\agent\UpdateAgent;
use app\components\bookings\exceptions\AgentException;


class Bookings extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->require_logged_in();
		
		$this->lang->load('bookings');

		if ($this->userauth->is_level(TEACHER) && setting('maintenance_mode')) {
			$this->data['title'] = $this->lang->line('BookingsTitle');
			$this->data['showtitle'] = '';
			$this->data['body'] = '';
			$this->render();
			$this->output->_display();
			exit();
		}

		$this->load->model('bookings_model');
		$this->load->model('multi_booking_model');
		$this->load->helper('booking');
	}


	/**
	 * Main bookings page.
	 *
	 * Nearly everything handled through bookings Grid and Context components.
	 *
	 */
	public function index()
	{
		$context = new Context();

		$context->autofill([
			'base_uri' => $this->uri->segment(1),
		]);

		if ( !($this->lang->line('BookingsTitle')) ) {
			$this->lang->load('bookings');
		}
		
		$grid = new Grid($context);

		$message = $this->session->flashdata('bookings');

		$this->data['title'] = $this->lang->line('BookingsTitle');
		$this->data['showtitle'] = '';
		$this->data['body'] = $message . $grid->render();

		$arr = $context->toArray();
		$json = json_encode($arr, JSON_PRETTY_PRINT);
		// $this->data['body'] .= "<pre>{$json}</pre>";

		return $this->render();
	}


	/**
	 * View details for single booking.
	 *
	 * This is designed to be shown in a sidebar panel.
	 *
	 */
	public function view($booking_id)
	{
		$include = [
			'repeat',
			'session',
			'period',
			'week',
			'room',
			'user',
			'department',
			'repeat',
		];

		$booking = $this->bookings_model->include($include)->get($booking_id);

		$this->data['booking'] = $booking;
		$this->data['current_user'] = $this->userauth->user;

		$msg = $this->session->flashdata('bookings');

		if ($booking) {
			$this->load->library('table');
			$this->load->helper('room');
			$body = $msg . $this->load->view('bookings/view', $this->data, TRUE);
		} else {
			$body = msgbox('error', $this->lang->line('ErrorBooking1') );
		}

		$this->data['title'] = $this->lang->line('Bookingdetails');
		$this->data['showtitle'] = '';
		$this->data['body'] = '<div class="bookings-view">' . $body . '</div>';

		return $this->render();
	}


	/**
	 * View details for single booking. Like /view/ but a smaller/more minimal view.
	 *
	 */
	public function card($booking_id)
	{
		$include = [
			'repeat',
			'session',
			'period',
			'week',
			'room',
			'user',
			'department',
			'repeat',
		];

		$booking = $this->bookings_model->include($include)->get($booking_id);

		$this->data['booking'] = $booking;
		$this->data['current_user'] = $this->userauth->user;

		if ($booking) {
			$this->load->library('table');
			$this->load->helper('room');
			$body = $this->load->view('bookings/card', $this->data, TRUE);
		} else {
			$body = msgbox('error', $this->lang->line('ErrorBooking1'));
		}

		$this->data['title'] = '';
		$this->data['showtitle'] = '';
		$this->data['body'] = '<div class="bookings-card">' . $body . '</div>';

		return $this->render();
	}


	/**
	 * View all bookings in series.
	 * This is accessed from viewing details of one booking in a series.
	 *
	 */
	public function view_series($booking_id)
	{
		// Get booking to highlight it in the list
		$include = [ 'period' ];
		$booking = $this->bookings_model->include($include)->get($booking_id);

		$this->data['booking'] = $booking;

		if ($booking && $booking->repeat_id) {
			$this->data['all_bookings'] = $this->bookings_model->find_by_repeat($booking->repeat_id);
			$this->load->library('table');
			$this->load->helper('room');
			$body = $this->load->view('bookings/view_series', $this->data, TRUE);
		} else {
			$body = msgbox('error', $this->lang->line('ErrorBooking2') );
		}

		$this->data['title'] = $this->lang->line('Bookingsinseries');
		$this->data['showtitle'] = '';
		$this->data['body'] = '<div class="bookings-view">' . $body . '</div>';

		return $this->render();
	}


	/**
	 * Handle creation of a new booking.
	 *
	 * 'Type' should be supplied as the first URI param, with other relevant data coming in via query string.
	 *
	 * @param string $type		Type of booking [single|multi]
	 *
	 */
	public function create($type)
	{
		$this->data['title'] = $this->lang->line('Createbooking');

		if ($this->input->get('params')) {
			$_SESSION['return_uri'] = 'bookings?' . $this->input->get('params');
		}

		$classes = [
			'single' => SingleAgent::class,
			'multi' => MultiAgent::class,
		];

		$class = array_key_exists($type, $classes)
			? $classes[$type]
			: NULL;

		if ( ! $type) {
			$this->data['view'] = msgbox('error', $this->lang->line('ErrorBooking3') );
			$this->data['body'] = $this->load->view('bookings/create', $this->data, TRUE);
			return $this->render();
		}

		try {
			$agent = $class::create();
			$agent->load();
			$agent->process();
			$this->data['view'] = $agent->render();
		} catch (AgentException $e) {
			$this->data['view'] = msgbox('error', $e->getMessage());
		}

		// Finished - redirect back
		//
		if ($agent->is_success()) {

			$this->session->set_flashdata('bookings', msgbox('info', $agent->message));

			$uri = isset($_SESSION['return_uri'])
				? $_SESSION['return_uri']
				: 'bookings';

			unset($_SESSION['return_uri']);
			redirect($uri);
			return;
		}

		if ($agent->title) {
			$this->data['title'] = $agent->title;
		}

		$this->data['body'] = $this->load->view('bookings/create', $this->data, TRUE);

		return $this->render();
	}


	/**
	 * Edit a booking.
	 *
	 * The fields that can be changed will  differ depending on some factors:
	 *
	 *  - single booking (period + room + department + user + notes)
	 *  - recurring booking single instance (period + room + department + user + notes)
	 *  - recurring booking single instance + others (department + user + notes)
	 *  - recurring booking all instances (department + user + notes)
	 *
	 */
	public function edit($booking_id)
	{
		$this->data['title'] = $this->lang->line('Editbooking');

		if ($this->input->get('params')) {
			$_SESSION['return_uri'] = 'bookings?' . $this->input->get('params');
		}

		$_GET['booking_id'] = $booking_id;

		try {
			$agent = UpdateAgent::create();
			$agent->load();
			$agent->process();
			$this->data['view'] = $agent->render();
		} catch (AgentException $e) {
			$this->data['view'] = msgbox('error', $e->getMessage());
		}

		// Finished - redirect back
		//
		if ($agent->is_success()) {

			$this->session->set_flashdata('bookings', msgbox('info', $agent->message));

			$uri = isset($_SESSION['return_uri'])
				? $_SESSION['return_uri']
				: 'bookings';

			unset($_SESSION['return_uri']);
			redirect($uri);
			return;
		}

		if ($agent->title) {
			$this->data['title'] = $agent->title;
		}

		$this->data['body'] = $this->load->view('bookings/edit', $this->data, TRUE);

		return $this->render();
	}


	/**
	 * Handle cancellation of existing booking.
	 *
	 * On viewing, shows different content depending on booking type (single / recurring).
	 * For recurring bookings, options will be presented for selected instance, all future instances, or all instances.
	 * For single bookings, just a confirmation.
	 *
	 * On form submission, the requested action is carried out.
	 *
	 */
	public function cancel($booking_id)
	{
		if ($this->input->get('params')) {
			$return_uri = 'bookings?' . $this->input->get('params');
			$_SESSION['return_uri'] = $return_uri;
			$this->data['return_uri'] = $return_uri;
		}

		$booking = $this->bookings_model->include(['room'])->get($booking_id);

		$this->data['booking'] = $booking;
		$this->data['current_user'] = $this->userauth->user;

		switch (TRUE) {

			case ($booking === FALSE):
				$body = msgbox('error', $this->lang->line('ErrorBooking1') );
				break;

			case (booking_cancelable($booking) === FALSE):
				$body = msgbox('error', $this->lang->line('Bookingnoteditable'));
				break;

		}

		if ($cancel_type = $this->input->post('cancel')) {

			$error = msgbox('error', $this->lang->line('ErrorCancelingBooking'));

			switch ($cancel_type) {

				case '1':
					$res = $this->bookings_model->cancel_single($booking_id);
					$success = msgbox('info', $this->lang->line('OkCancelingBooking') );
					break;

				case 'future':
					$res = $this->bookings_model->cancel_future($booking_id);
					$success = msgbox('info', $this->lang->line('OkCancelingAllBookings1') );
					break;

				case 'all':
					$res = $this->bookings_model->cancel_all($booking_id);
					$success = msgbox('info', $this->lang->line('OkCancelingAllBookings2'));
					break;

				default:
					$res = FALSE;
					$error = msgbox('error', $this->lang->line('Invalidcanceltype') );
			}

			$msg = ($res) ? $success : $error;
			$this->session->set_flashdata('bookings', $msg);

			$uri = isset($_SESSION['return_uri'])
				? $_SESSION['return_uri']
				: 'bookings';

			unset($_SESSION['return_uri']);
			return redirect($uri);
		}
	}


	public function change_session()
	{
		$session_id = $this->input->post('session_id');

		$params_str = $this->input->post('params');
		parse_str($params_str, $params_data);

		if ( ! $session_id) {

			unset($params_data['date']);
			unset($_SESSION['current_session_id']);

		} else {

			$this->load->model('sessions_model');
			if ($this->userauth->is_level(ADMINISTRATOR)) {
				$session = $this->sessions_model->get($session_id);
			} else {
				$session = $this->sessions_model->get_available_session($session_id);
			}

			if ($session) {
				$_SESSION['current_session_id'] = $session->session_id;
			} else {
				$this->session->set_flashdata('bookings', msgbox('error', $this->lang->line('SessionNotAvai') ));
			}
		}

		if (isset($params_data['date'])) {
			unset($params_data['date']);
		}

		$params = http_build_query($params_data);
		$return_uri = 'bookings?' . $params;
		return redirect($return_uri);
	}

}
