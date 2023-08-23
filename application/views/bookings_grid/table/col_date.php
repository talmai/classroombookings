<?php
$classes = [
	'bookings-grid-header-cell',
	'bookings-grid-header-cell-day',
];

if ($date->date == $today->format('Y-m-d')) {
	$classes[] = 'bookings-grid-header-cell-is-today';
}

?>

<th class="<?= implode(' ', $classes) ?>" width="<?= $width ?>">
	<strong>
		<?php
		echo isset($day_names[$date->weekday])
			? $day_names[$date->weekday]
			: '';
		?>
	</strong>
	<?php
	$date_fmt = setting('date_format_weekday');
	if (strlen($date_fmt)) {
		$dt = datetime_from_string($date->date);
		
		$format =  $dt->format($date_fmt); // $this->localize( $dt->format($date_fmt) ); //
		echo "<br>";
		echo "<span style='font-size: 90%'>{$format}</span>";
	}
	?>
</th>
