<?php

use app\components\Calendar;

$this->table->set_template([
	'table_open' => '<table class="border-table" width="100%" cellpadding="6" cellspacing="0" border="0">',
]);

if ( !($this->lang->line('BookingsTitle')) ) {
	$this->lang->load('custom');
}

$date_format = setting('date_format_long', 'crbs');

// Date
//
$info[] = [
	'name' => 'date',
	'label' => $this->lang->line('Date'),
	'value' => $booking->date->format($date_format),
];

// Week
//
$info[] = [
	'name' => 'week',
	'label' => $this->lang->line('Week'),
	'value' => week_dot($booking->week, 'sm') . ' ' . html_escape($booking->week->name),
];


if ($booking->repeat_id) {
	$weekday = Calendar::get_day_name($booking->repeat->weekday);
	$info[] = [
		'name' => 'occurs',
		'label' => $this->lang->line('Occurs'),
		'value' => sprintf("%s, every %s", $booking->week->name, $weekday),
	];
} else {
	$info[] = [
		'name' => 'occurs',
		'label' => $this->lang->line('Occurs'),
		'value' => 'Once',
	];
}

// Period
//
$time_fmt = setting('time_format_period');
$time = '';
if (strlen($time_fmt)) {
	$start = date($time_fmt, strtotime($booking->period->time_start));
	$end = date($time_fmt, strtotime($booking->period->time_end));
	$time = sprintf(' (%s - %s)', $start, $end);
}
$info[] = [
	'name' => 'period',
	'label' => $this->lang->line('Period'),
	'value' => html_escape($booking->period->name . $time),
];

// User
//
$user_is_admin = $this->userauth->is_level(ADMINISTRATOR);
$user_is_booking_owner = ($booking->user_id && $booking->user_id == $current_user->user_id);

$display_user_setting = ($booking->repeat_id)
	? setting('bookings_show_user_recurring')
	: setting('bookings_show_user_single');

$show_user = ($user_is_admin || $user_is_booking_owner || $display_user_setting);

$user_label = '';
if ($booking->user) {
	$user_label = strlen($booking->user->displayname)
		? $booking->user->displayname
		: $booking->user->username;
}

$user_value = ($show_user && ! empty($booking->user))
	? html_escape($user_label)
	: '<em>'.$this->lang->line('Notavailable').'</em>';

$info[] = [
	'name' => 'user',
	'label' => $this->lang->line('Bookedby'),
	'value' => $user_value,
];

// Department
//
$department = ($booking->department)
	? $booking->department
	: ($booking->user ? $booking->user->department : false);
if ($department) {
	$info[] = [
		'name' => 'department',
		'label' => $this->lang->line('Department'),
		'value' => html_escape($department->name),
	];
}

// Notes
//
if (strlen($booking->notes)) {
	$info[] = [
		'name' => 'notes',
		'label' => $this->lang->line('Notes'),
		'value' => html_escape($booking->notes),
	];
}


foreach ($info as $row) {
	$label = ['data' => "<strong>{$row['label']}</strong>", 'width' => '40%'];
	$value = ['data' => $row['value'], 'width' => '60%'];
	$this->table->add_row($label, $value);
}

echo $this->table->generate();
