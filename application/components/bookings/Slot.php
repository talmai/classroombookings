<?php

namespace app\components\bookings;

defined('BASEPATH') OR exit('Nenhum acesso direto ao script � permitido');


use \DateTime;
use \DateInterval;
use \DatePeriod;

use app\components\Calendar;


/**
 * Represents the status/details for an individual timeslot: date + period + room
 *
 */
class Slot
{


	const STATUS_AVAILABLE = 'available';
	const STATUS_UNAVAILABLE = 'unavailable';
	const STATUS_BOOKED = 'booked';

	const BOOKED_SINGLE = 'single';
	const BOOKED_RECURRING = 'recurring';

	const UNAVAILABLE_HOLIDAY = 'holiday';
	const UNAVAILABLE_DATE_RANGE = 'date_range';
	const UNAVAILABLE_PERIOD = 'period';
	const UNAVAILABLE_LIMIT = 'limit';
	const UNAVAILABLE_UNKNOWN = 'unknown';


	// CI instance
	private $CI;


	// Context instance
	private $context;

	private $date;
	private $period;
	private $room;

	private $key;

	private $status;
	private $reason;
	private $label;
	private $view_data = [];



	public function __construct(Context $context, $date, $period, $room)
	{
		$this->CI =& get_instance();

		$this->context = $context;

		$this->date = $date;
		$this->period = $period;
		$this->room = $room;
		$this->booking = FALSE;

		$this->init();
	}


	private function init()
	{
		$this->datetime = datetime_from_string($this->date->date);
		$this->key = self::generate_key($this->date->date, $this->period->period_id, $this->room->room_id);

		if (array_key_exists($this->key, $this->context->bookings)) {
			$this->booking = $this->context->bookings[ $this->key ];
		}

		if(!$this->CI->lang->line('Holiday')){
			$this->CI->lang->load('custom');
		}

		$this->set_status();
	}


	/**
	 * Check various pieces of data to determine if this slot is bookable or not.
	 *
	 * Uses various cues, including:
	 * 	- holidays
	 * 	- period time/day
	 * 	- existing bookings
	 * 	- user's permissions
	 *
	 */
	private function set_status()
	{
		// Default
		// $this->status = self::STATUS_UNAVAILABLE;
		// $this->reason = self::UNAVAILABLE_LIMIT;

		// Check for holiday
		//
		if ($this->date->holiday_id) {

			$this->status = self::STATUS_UNAVAILABLE;
			$this->reason = self::UNAVAILABLE_HOLIDAY;

			$holiday_id = $this->date->holiday_id;
			$holiday = isset($this->context->holidays[$holiday_id])
				? $this->context->holidays[$holiday_id]
				: FALSE;

			$date_fmt = 'd/m/Y';

			$this->label = $holiday
				? sprintf( $this->CI->lang->line('Holiday').": %s<br>(%s - %s)",
		          	$holiday->name,
		          	$holiday->date_start->format($date_fmt),
		          	$holiday->date_end->format($date_fmt)
		          	)
				: 'Holiday';

			return;
		}

		// Check if period is valid.
		//
		$period_key = sprintf('day_%d', $this->date->weekday);
		if ($this->period->{$period_key} != 1) {

			$day_names = Calendar::get_day_names();
			$day_name = $day_names["{$this->date->weekday}"];

			$this->status = self::STATUS_UNAVAILABLE;
			$this->reason = self::UNAVAILABLE_PERIOD;

			$this->label = sprintf('%s não disponível em %s.', $this->period->name, $day_name);

			return;
		}

		// Check for bookings
		//
		if ($this->booking) {

			$this->status = self::STATUS_BOOKED;
			$this->reason = ($this->booking->repeat_id)
				? self::BOOKED_RECURRING
				: self::BOOKED_SINGLE;

			$this->view_data = [
				'booking' => $this->booking,
			];

			return;

		}

		// Check permissions/quotas
		$start_date = ($this->context->session->is_current == '1')
			? date('Y-m-d')
			: $this->context->session->date_start->format('Y-m-d');
			
		// TODO: Ibam: Adicionar restrição para dias no futuro para reservas
		// 1- criar campo para edição, 2- verificar incluindo estes dias... 
		// 3- exibir msg de erro apropriada 'msgBlockedDays'
		$booking_permitted = $this->CI->userauth->can_create_booking($this->date->date, $start_date);

		if ($booking_permitted->result === FALSE) {

			$this->status = self::STATUS_UNAVAILABLE;
			$this->reason = self::UNAVAILABLE_LIMIT;
			$this->extended = $booking_permitted;

			switch (FALSE) {

				case $booking_permitted->date_in_range:
					$advance = (int) abs(setting('bia'));
					$this->view_data = ['extended' => 'future'];
					$this->label = sprintf( $this->CI->lang->line('msgDaysinFuture'), $advance);
					break;

				case $booking_permitted->is_future_date:
					$this->view_data = ['extended' => 'past'];
					$this->label = $this->CI->lang->line('msgPassedDays');
					break;

				case $booking_permitted->in_quota:
					$max = (int) abs(setting('num_max_bookings'));
					$this->view_data = ['extended' => 'quota'];
					$this->label = sprintf( $this->CI->lang->line('ErrorMaxBookings'), $max);
					break;

				default:
					$this->label = 'Unknown';
			}

			return;
		}

		$this->status = self::STATUS_AVAILABLE;

		// $this->status = self::STATUS_UNAVAILABLE;
		// $this->reason = self::UNAVAILABLE_UNKNOWN;
	}


	/**
	 * Generate the unique key for this slot.
	 *
	 */
	public static function generate_key($date_ymd, $period_id, $room_id)
	{
		return sprintf('%s.P%d.R%d', $date_ymd, $period_id, $room_id);
	}


	public function __get($name)
	{
		return $this->{$name};
	}



}
