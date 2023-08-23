<?php

$block_class = 'b-33';

$blocks = [];

$blocks[] = [
	'title' => $this->lang->line('Allbookings'),
	'figure' => $totals['all'],
];

$blocks[] = [
	'title' => $this->lang->line('Bookingsthissession'),
	'figure' => $totals['session'],
];

$blocks[] = [
	'title' => $this->lang->line('Activebookings'),
	'figure' => $totals['active'],
];

// total bookings ever
// total in current session
// current active bookings

// if max_active:
// maximum bookings allowed
// number permitted (max - active)

$max_active_bookings = (int) abs(setting('num_max_bookings'));
if ($max_active_bookings > 0) {

	$block_class = 'b-20';
	$num_active = (int) $totals['active'];

	$blocks[] = [
		'title' => $this->lang->line('Maximumactivebookings'),
		'figure' => $max_active_bookings,
	];

	$blocks[] = [
		'title' => $this->lang->line('Bookingsyoucanmake'),
		'figure' => ($max_active_bookings - $num_active),
	];

}


// echo "<div class='block-group has-spacing'>";

echo "<div style='margin-bottom:48px'>";

foreach ($blocks as $block) {

	$figure = number_format($block['figure']);
	$figure_html = "<dt>{$figure}</dt>";

	$title = html_escape($block['title']);
	$title_html = "<dd>{$title}</dd>";

	$block_content = "<div class='stat-item'><dl>{$title_html}{$figure_html}</dl></div>";

	echo $block_content;

	// echo "<div class='block {$block_class}'>{$block_content}</div>";

}

echo "</div>";
