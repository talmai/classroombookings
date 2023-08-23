<?php
$messages = $this->session->flashdata('saved');
echo "<div class='messages'>{$messages}</div>";


echo iconbar([
	['sessions/add', $this->lang->line('AddSession'), 'add.png'],
]);


$sort_cols = [$this->lang->line('Name'), $this->lang->line('Startdate'), $this->lang->line('Enddate'), $this->lang->line('Current')."?", $this->lang->line('Selectable')."?"];

echo "<h3>".$this->lang->line('titleSessions')."</h3>";
$this->load->view('sessions/table', ['items' => $active, 'id' => 'sessions_active', 'sort_cols' => $sort_cols]);

if ( ! empty($past)) {
	echo "<br><br><h3>".$this->lang->line('Pastsessions')."</h3>";
	$this->load->view('sessions/table', ['items' => $past, 'id' => 'sessions_past', 'sort_cols' => $sort_cols]);
}
