<?php
defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');

class Periods_model extends CI_Model
{

	// CI instance
	private $CI;

	public $days = array();


	public function __construct()
	{
		parent::__construct();

		$this->load->model('crud_model');
		
		$this->CI =& get_instance();
		$this->CI->lang->load('calendar');

		$this->days[1] = $this->CI->lang->line('cal_monday'); //'Segunda';
		$this->days[2] = $this->CI->lang->line('cal_tuesday'); //'Terça';
		$this->days[3] = $this->CI->lang->line('cal_wednesday'); //'Quarta';
		$this->days[4] = $this->CI->lang->line('cal_thursday'); //'Quinta';
		$this->days[5] = $this->CI->lang->line('cal_friday'); //'Sexta';
		$this->days[6] = $this->CI->lang->line('cal_saturday'); //'Sábado';
		$this->days[7] = $this->CI->lang->line('cal_sunday'); //'Domingo';
	}




	function Get($period_id = NULL)
	{
		if ($period_id == NULL) {
			$rows = $this->crud_model->Get('periods', NULL, NULL, NULL, 'day_1 desc, day_2 desc, day_3 desc, day_4 desc, day_5 desc, day_6 desc, day_7 desc, time_start asc');
			return $rows;
		} else {
			$row = $this->crud_model->Get('periods', 'period_id', $period_id);
			return $row;
		}
	}


	public function arrange_by_day_num()
	{
		$data = [];
		$periods = $this->GetBookable();

		foreach ($periods as $period) {
			foreach ($this->days as $num => $name) {
				$field = "day_{$num}";
				if ($period->$field == 1) {
					$data["{$num}"][] = $period;
				}
			}
		}

		return $data;
	}


	public function GetBookable($day_num = NULL)
	{
		$out = array();
		$where = array('bookable' => '1');

		if ($day_num !== NULL && is_numeric($day_num))
		{
			$where["day_{$day_num}"] = '1';
		}

		$this->db->where($where);
		$this->db->order_by('time_start', 'ASC');
		$query = $this->db->get('periods');

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$out[$row->period_id] = $row;
			}
		}

		return $out;
	}


	function Add($data)
	{
		return $this->crud_model->Add('periods', 'period_id', $data);
	}




	function Edit($period_id, $data)
	{
		return $this->crud_model->Edit('periods', 'period_id', $period_id, $data);
	}




	/**
	 * Deletes a period with the given ID
	 *
	 * @param   int   $id   ID of period to delete
	 *
	 */
	function Delete($id)
	{
		return $this->crud_model->Delete('periods', 'period_id', $id);
	}




}
