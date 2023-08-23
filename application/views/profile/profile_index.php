<?php
if(!$this->lang->line('profileedit')){
	$this->lang->load('profile');
}

echo $this->session->flashdata('saved');

echo iconbar(array(
	array('profile/edit', $this->lang->line('profileedit'), 'user_edit.png'),
));
?>

<?php if($myroom){ ?>
<h3><?php echo $this->lang->line('staffbookings1'); ?></h3>
<ul>
<?php
foreach($myroom as $booking){
	$string = '<li>'.$this->lang->line('profilebookings1').'</li>';
	if($booking->notes){ $booking->notes = '('.$booking->notes.')'; }
	if(!$booking->displayname){ $booking->displayname = $booking->username; }
	echo sprintf($string, html_escape($booking->name), date("d/m/Y", strtotime($booking->date)), html_escape($booking->displayname), html_escape($booking->periodname), html_escape($booking->notes));
}
?>
</ul>
<?php } ?>



<?php if($mybookings){ ?>
<h3><?php echo $this->lang->line('Mybookings'); ?></h3>
<ul>
<?php
foreach($mybookings as $booking){
	$string = '<li>'.$this->lang->line('profilebookings1').'</li>';
	$notes = '';
	if($booking->notes){ $notes = '('. $booking->notes.')'; }
	echo sprintf($string, html_escape($booking->name), date("d/m/Y", strtotime($booking->date)), html_escape($booking->periodname), html_escape($notes));
}
?>
</ul>
<?php } ?>


<h3><?php echo $this->lang->line('Mytotalbookings'); ?></h3>
<ul>
	<li><?php echo $this->lang->line('Numtotalbks').': '.$total['all'] ?></li>
	<li><?php echo echo $this->lang->line('Numtotalbksyr').': '.$total['yeartodate'] ?></li>
	<li><?php echo echo echo $this->lang->line('Numactivebks').': '.$total['active'] ?></li>
</ul>
