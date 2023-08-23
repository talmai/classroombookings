<?php

echo $this->session->flashdata('saved');

if ( !($this->lang->line('Reports')) ) {
	$this->lang->load('custom'); 
}

echo iconbar([
	['rooms/add_field', $this->lang->line('AddField'), 'add.png'],
]);

$sort_cols = ["Name", "Type", "Options", "None"];

?>

<?php

// echo $iconbar;
