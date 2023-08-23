<?php

namespace app\components\bookings;

defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');


use \DateTime;
use \DateInterval;
use \DatePeriod;

use app\components\Calendar;
use app\components\bookings\grid\Controls;
use app\components\bookings\grid\Header;
use app\components\bookings\grid\Table;
use app\components\bookings\exceptions\DateException;


class Grid
{

	// CI instance
	private $CI;

	// Context instance
	private $context;


	public function __construct(Context $context)
	{
		$this->CI =& get_instance();

		$this->CI->load->model([
			'sessions_model',
			'dates_model',
			'users_model',
			'rooms_model',
			'access_control_model',
			'periods_model',
			'weeks_model',
		]);

		$this->context = $context;

		$this->controls = new Controls($context);
		$this->header = new Header($context);
		$this->table = new Table($context);
	}


	public function render()
	{
		$controls = $this->controls->render();
		$header = $this->header->render();
		
		$this->CI->lang->load('bookings');

		$body = $this->render_body();
		$footer = $this->render_footer();
		$legend = $this->render_legend();

		$form_attrs = [
			'up-layer' => 'new modal',
			'up-size' => 'large',
			'up-target' => '.bookings-create',
			'up-dismissable' => 'button key',
		];
		$form_hidden = ['step' => 'selection'];
		$form_open = form_open($this->context->base_uri . '/create/multi', $form_attrs, $form_hidden);
		$form_close = form_close();

		if ($this->CI->userauth->is_level(ADMINISTRATOR)) {
		} else {
			$out = "{$controls}\n{$header}\n{$body}\n{$footer}\n{$legend}\n";
		}
			$out = "{$controls}\n{$header}\n{$form_open}\n{$body}\n{$footer}\n{$form_close}\n{$legend}\n";

		return "<div id='bookings_grid' up-hungry>{$out}</div>";
	}


	/**
	 * Render the main content area.
	 *
	 * If an exception is present in the context, the error is displayed instead of the table.
	 *
	 */
	public function render_body()
	{
		// Check for any errors and render it instead of the table.
		if ($this->context->exception) {
			return msgbox('error', $this->context->exception->getMessage());
		}

		return $this->table->render();
	}


	/**
	 * Render the footer. This includes the legend/key, and recurring bookings controls.
	 *
	 */
	public function render_footer()
	{
		// if ( ! $this->CI->userauth->is_level(ADMINISTRATOR)) {
		// 	return '';
		// }

		if ($this->context->exception) {
			return '';
		}

		$input = form_checkbox([
			'name' => 'multi_select',
			'id' => 'multi_select',
			'value' => '1',
			'checked' => false,
			'up-switch' => '.multi-select-content',
		]);
		$input_label = form_label($input . $this->CI->lang->line('Enablemulsel'), 'multi_select', ['class' => 'ni', 'style' => 'display: inline-block;margin-bottom:8px']);

		$check_block = "<div class='block b-50' style='text-align:right'>{$input_label}</div>";

		$submit_button = "<button type='submit' style='margin-left:32px' class='multi-select-content'>".$this->CI->lang->line('')."...</button>";
		$button_block = "<div class='block b-50'>{$submit_button}</div>";

		return "<div class='cssform block-group' style='margin-top:32px;'>{$check_block}{$button_block}</div>";
	}


	/**
	 * Render table legend.
	 *
	 */
	public function render_legend()
	{
		$cells = [];

		$legend = lang('bookingslegendlegend');
		$cells[] = "<td width='25%' align='right' valign='middle'><strong>{$legend}:</strong></td>";

		$label = lang('bookingslegendfree');
		$class = 'bookings-grid-slot booking-status-available';
		$cells[] = "<td class='{$class}' width='25%' align='center'><span class='bookings-grid-button'>{$label}</div></td>";

		$label = lang('bookingslegendstatic');
		$class = 'bookings-grid-slot booking-status-booked booking-status-booked-recurring';
		$cells[] = "<td class='{$class}' width='25%' align='center'><span class='bookings-grid-button'>{$label}</div></td>";

		$label = lang('bookingslegendstaff');
		$class = 'bookings-grid-slot booking-status-booked booking-status-booked-single';
		$cells[] = "<td class='{$class}' width='25%' align='center'><span class='bookings-grid-button'>{$label}</div></td>";

		$cells_str = implode("\n", $cells);

		$table = "<table border='0' cellpadding='4' cellspacing='4' class='bookings-grid is-legend' align='center' style='border:1px solid #000;border-width:0px 0px;margin:16px auto;'><tr>{$cells_str}</tr></table>";

		return $table;
	}

}
