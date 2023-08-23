<?php

namespace app\components\bookings\exceptions;


class SettingsException extends \RuntimeException
{


	public static function forDisplayType()
	{
		//return new static("The 'Display Type' setting has not been set.");
		return new static("A configuração 'Tipo de visualização' não foi configurada.");
	}


	public static function forColumns()
	{
		//return new static("The 'Display Columns' setting has not been set.");
		return new static("A configuração 'Colunas de visualização' não foi configurada.");
	}


	public static function forNoRooms()
	{
		//return new static("There are no rooms available.");
		return new static("Não há salas/espaços disponíveis.");
	}


}
