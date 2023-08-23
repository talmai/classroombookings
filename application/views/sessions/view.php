<?php

$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";

$css = $calendar->get_css();
echo "<style type='text/css'>{$css}</style>";

$dateFormat = setting('date_format_long', 'crbs');
if($this->lang->get_idioma()=="english"){
	$start = $session->date_start ? $session->date_start->format($dateFormat) : '';
	$end = $session->date_end ? $session->date_end->format($dateFormat) : ''; // Sunday 1st January 2023
}else{
	$start = $session->date_start ? $this->lang->diaTraduzido( $session->date_start, 'EEEE, d MMMM yyyy' ) : '';
	$end = $session->date_end ? $this->lang->diaTraduzido( $session->date_end, 'EEEE, d MMMM yyyy' ) : '';
	// Domingo 1 Janeiro 2023 = 'EEEE d MMMM YYYY'
}
echo "<p><strong>Data inicial: </strong>{$start}</p>";
echo "<p><strong>Data final:</strong> {$end}</p>";

if ( ! empty($weeks)) {
	$this->load->view('sessions/view_apply_week', [
		'weeks' => $weeks,
		'session' => $session,
	]);
}

echo "<br><p>Clique nas datas em cada calendário para alternar o Horário Semanal daquela semana.</p><br>";

echo form_open(current_url(), [], ['session_id' => $session->session_id]);

echo $calendar->generate_full_session(['column_class' => 'b-50']);

$this->load->view('partials/submit', array(
	'submit' => array('Save', tab_index()),
));

echo form_close();
