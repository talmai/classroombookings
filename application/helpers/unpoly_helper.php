<?php

defined('BASEPATH') OR exit('Nenhum acesso direto ao script � permitido');

function up_target()
{
	$CI =& get_instance();
	$target = $CI->input->get_request_header('x-up-target');
	return strlen($target) ? $target : FALSE;
}
