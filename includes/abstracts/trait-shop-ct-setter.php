<?php

trait Shop_CT_Setter {
	protected function set($row) {
		foreach ( $row as $column => $value ) {
			$method_name = 'set_' . $column;

			if (method_exists($this, $method_name)) {
				try {
					call_user_func(array($this, $method_name), $value);
				} catch (Exception $e) {
					var_dump($e); // todo: make readable.
				}
			} else {
				$failed_columns[] = $column;
			}
		}

		return isset($failed_columns) ? $failed_columns : true;
	}
}