<?php

defined('BASEPATH') OR exit('Nenhum acesso direto ao script � permitido');


function field($validation, $database = NULL, $last = ''){
	$value = (isset($validation)) ? $validation : ( (isset($database)) ? $database : $last);
	return $value;
}




function iconbar($items = array(), $active = false) {

	$html = "<p class='iconbar'>";
	$i = 1;
	$max = count($items);

	foreach ($items as $item) {

		$attrs = '';

		if (isset($item['link'])) {
			extract($item);
		} else {
			list($link, $name, $icon) = $item;
		}

		if (is_array($attrs)) {
			$attrs = _stringify_attributes($attrs);
		}

		$class = ($link == $active)
			? 'active'
			: '';

		$img = img("assets/images/ui/{$icon}", FALSE, "alt='{$name}' align='top' hspace='0' border='0'");

		$label = anchor($link, "{$img} {$name}", "class='{$class}' {$attrs}");
		$label = str_replace(site_url('#'), '#', $label);

		$html .= $label;

		if ($i < $max) {
			$html .= img("assets/images/sep.gif", FALSE, "alt='|' align='top' hspace='0' border='0' style='margin:0px 3px;'");
		}

		$i++;
	}

	$html .= "</p>";

	return $html;
}


function tab_index($reset = NULL)
{
	static $_tab_index;

	if ( ! strlen($_tab_index) || $_tab_index === 0)
	{
		$_tab_index = 0;
	}
	else
	{
		$_tab_index++;
	}

	return $_tab_index;
}




function msgbox($type = 'error', $content = '', $escape = TRUE)
{
	if ($escape)
	{
		$content = html_escape($content);
	}

	$html = "<p class='msgbox {$type}'>{$content}</p>";
	return $html;
}


/*
function icon($name, $attributes = array())
{
	$CI =& get_instance();
	return $CI->feather->get($name, $attributes, FALSE);
}
*/
