<?php

class Shop_CT_Validator {
	public static function is_valid_id($id) {
		return is_numeric($id) && 0 < $id && $id == (int)$id;
	}
}