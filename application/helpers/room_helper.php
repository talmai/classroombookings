<?php

defined('BASEPATH') OR exit('Nenhum acesso direto ao script � permitido');

function room_photo_url($room)
{
	if ( ! $room) return false;
	if ( ! strlen($room->photo)) return false;

	$photo_path = "uploads/{$room->photo}";

	if ( ! is_file(FCPATH . $photo_path)) return false;

	return base_url($photo_path);
}
