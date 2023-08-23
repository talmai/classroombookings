<?php defined('BASEPATH') OR exit('Nenhum acesso direto ao script é permitido');

/*use app\components\Calendar;*/

class Menu_model extends CI_Model
{


	/**
	 * For header/footer in page.
	 *
	 */
	public function global()
	{
		$is_admin = $this->userauth->is_level(ADMINISTRATOR);
		
		$items = [];

		if ( ! $this->userauth->logged_in()) {
			return $items;
		}

		$items[] = [
			'label' => $this->lang->line('Bookings'),
			'url' => site_url('bookings'),
			'icon' => 'school_manage_bookings.png',
		];

		if ($is_admin) {
			$items[] = [
				'label' => $this->lang->line('management'),//'Setup',
				'url' => site_url('setup'),
				'icon' => 'school_manage_settings.png',
			];
		}

		$items[] = [
			'label' => $this->lang->line('Account'),//'Account',
			'url' => site_url('profile/edit'),
			'icon' => ($is_admin) ? 'user_administrator.png' : 'user_teacher.png',
		];

		$items[] = [
			'label' => $this->lang->line('Logout'),//'Log out',
			'url' => site_url('logout'),
			'icon' => 'logout.png',
		];

		return $items;
	}


	public function setup_school()
	{
		$items = [];

		if ( ! $this->userauth->is_level(ADMINISTRATOR)) {
			return $items;
		}

		$items[] = [
			'label' => $this->lang->line('intitutiondetails'),
			'icon' => 'school_manage_details.png',
			'url' => site_url('school'),
		];

		$items[] = [
			'label' => $this->lang->line('Periods'),
			'icon' => 'school_manage_times.png',
			'url' => site_url('periods'),
		];

		$items[] = [
			'label' => $this->lang->line('timetableweeks'),
			'icon' => 'school_manage_weeks.png',
			'url' => site_url('weeks'),
		];

		$items[] = [
			'label' => $this->lang->line('Sessions'),
			'icon' => 'calendar_view_month.png',
			'url' => site_url('sessions'),
		];

		$items[] = [
			'label' => $this->lang->line('Rooms'),
			'icon' => 'school_manage_rooms.png',
			'url' => site_url('rooms'),
		];

		$items[] = [
			'label' => $this->lang->line('Departments'),
			'icon' => 'school_manage_departments.png',
			'url' => site_url('departments'),
		];

		return $items;
	}


	public function setup_manage()
	{
		$items = [];

		if ( ! $this->userauth->is_level(ADMINISTRATOR)) {
			return $items;
		}

		$items[] = [
			'label' => $this->lang->line('Users'),
			'icon' => 'school_manage_users.png',
			'url' => site_url('users'),
		];

		$items[] = [
			'label' => $this->lang->line('Settings'),
			'icon' => 'school_manage_settings.png',
			'url' => site_url('settings/general'),
		];

		$items[] = [
			'label' => $this->lang->line('Authentication'),
			'icon' => 'lock.png',
			'url' => site_url('settings/authentication/ldap'),
		];

		return $items;
	}
	
		public function setup_reports()
	{
		$items = [];

		if ( ! $this->userauth->is_level(ADMINISTRATOR)) {
			return $items;
		}

		$items[] = [
			'label' => $this->lang->line('GeneralReport'),
			'icon' => 'school_manage_rooms.png',
			'url' => site_url('reports'),
		];

		return $items;
	}

}
