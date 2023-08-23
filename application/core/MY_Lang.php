<?php
defined('BASEPATH') OR exit('Nenhum acesso direto ao script � permitido');



class MY_Lang extends CI_Lang
{


	protected $CI = NULL;

	protected $db_loaded = array();
	protected $db_lang_count = FALSE;


	public function __construct()
	{
		parent::__construct();
	}


	public function load($langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
		$result = parent::load($langfile, $idiom, $return, $add_suffix, $alt_path);

		if (empty($idiom) OR ! preg_match('/^[a-z_-]+$/i', $idiom))
		{
			$config =& get_config();
			$idiom = empty($config['language']) ? 'english' : $config['language'];
		}

		$lang = $this->load_from_db($langfile, $idiom);

		if (is_array($result) && $return === TRUE) {
			$return_value = array_merge($result, $lang);
			return $return_value;
		}

		$this->language = array_merge($this->language, $lang);
		return TRUE;
	}


	public function load_from_db($set, $idiom)
	{
		$lang = [];
		$table = 'lang';

		$this->set_instance();

		if ( ! isset($this->CI->db)) {
			return $lang;
		}

		if ( ! $this->CI->db->table_exists($table)) {
			return $lang;
		}

		if ($this->db_lang_count === FALSE) {
			$sql = "SELECT COUNT(id) AS `total` FROM {$table}";
			$query = $this->CI->db->query($sql);
			$row = $query->row();
			$this->db_lang_count = $row->total;
		}

		if ($this->db_lang_count == 0) {
			return $lang;
		}

		if (isset($this->db_loaded[$idiom]) && $this->db_loaded[$idiom] === $set) {
			return $lang;
		}

		$sql = "SELECT `key`, `text` FROM `{$table}`
				WHERE `language` = ?
				AND `set` = ?";

		$query = $this->CI->db->query($sql, [ $idiom, $set ]);
		if ($query->num_rows() === 0) {
			return $lang;
		}

		$result = $query->result();

		foreach ($result as $row) {
			$lang[$row->key] = $row->text;
		}

		$this->db_loaded[$idiom] = $set;

		return $lang;
	}


	private function set_instance()
	{
		if ( ! $this->CI) {
			$this->CI =& get_instance();
		}
	}

}
