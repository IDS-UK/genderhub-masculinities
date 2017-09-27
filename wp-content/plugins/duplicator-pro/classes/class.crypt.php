<?php
class DUP_PRO_Crypt
{
	public function encrypt($key, $string)
	{
		$result = '';
		for ($i = 0; $i < strlen($string); $i++)
		{
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result .= $char;
		}

		return urlencode(base64_encode($result));
	}

	function decrypt($key, $string)
	{
		$result = '';
		$string = urldecode($string);
		$string = base64_decode($string);

		for ($i = 0; $i < strlen($string); $i++)
		{
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result .= $char;
		}

		return $result;
	}

}
