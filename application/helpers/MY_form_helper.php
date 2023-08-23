<?php
defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');


/**
 * Colour picker input (huebee JS)
 *
 */
function form_colour_picker($options = [])
{
	$defaults = [];
	$options = array_merge($defaults, $options);

	$options['class'] = isset($options['class']) ? $options['class'] . ' color-input' : 'color-input';
	$options['data-huebee'] = html_escape(json_encode(['notation' => 'hex']));

	return form_input($options);
}
