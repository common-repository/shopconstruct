<?php

/**
 * Simple wrapper for users
 *
 * todo: refactor
 *
 * Class Shop_CT_Customer
 */
class Shop_CT_Customer
{
	protected $id;
	protected $nicename;
	protected $email;

	public function __construct($args = array())
	{
		if (!empty($args)) {
			foreach($args as $argKey => $argVal){
				$methodName = 'set_'.$argKey;
				if (method_exists($this,$methodName)) {
					call_user_func(array($this,$methodName), $argVal);
				}
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 *
	 * @return Shop_CT_Customer
	 */
	public function set_id( $id )
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_nicename()
	{
		return $this->nicename;
	}

	/**
	 * @param mixed $nicename
	 *
	 * @return Shop_CT_Customer
	 */
	public function set_nicename( $nicename )
	{
		$this->nicename = $nicename;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_email()
	{
		return $this->email;
	}

	/**
	 * @param mixed $email
	 *
	 * @return Shop_CT_Customer
	 */
	public function set_email( $email )
	{
		$this->email = $email;

		return $this;
	}


	public static function get_all()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'users';
		$sql = "SELECT id, user_nicename, user_email FROM " . $table_name;
		$result = $wpdb->get_results($sql);
		$a = array();

		foreach ($result as $key => $object) {
			$a[] = new self(array(
				'id' => $object->id,
				'nicename' => $object->user_nicename,
				'email' => $object->user_email,
			));
			$name_email[$object->user_nicename] = $object->user_nicename . ' (' . $object->user_email . ')';
		}

		return $name_email;
	}

	/**
	 * todo: fix this garbage
	 * @return array
	 */
	public static function get_all_for_selectbox()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'users';
		$sql = "SELECT id, user_nicename, user_email FROM " . $table_name;
		$name_email = array('Guest' => 'Guest');
		$result = $wpdb->get_results($sql);

		foreach ($result as $key => $object) {
			$name_email[$object->user_nicename] = $object->user_nicename . ' (' . $object->user_email . ')';
		}

		return $name_email;
	}
}