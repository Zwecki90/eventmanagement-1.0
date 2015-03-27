<?php
	function get_site_host($s, $use_forwarded_host=false)
	{
		$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
		$port = $s['SERVER_PORT'];
		$port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
		$host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
		$host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
		return $host;
	}

	function url_origin($s, $use_forwarded_host=false)
	{
		$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
		$sp = strtolower($s['SERVER_PROTOCOL']);
		$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
		return $protocol . '://' . get_site_host($s, $use_forwarded_host);
	}
	
	function full_url($s, $use_forwarded_host=false)
	{
		return url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
	}
	
	function checktime($hour, $min, $sec=0)
	{
		if ($hour < 0 || $hour > 23 || !is_numeric($hour))
		{
			return false;
		}
		if ($min < 0 || $min > 59 || !is_numeric($min))
		{
			return false;
		}
		if ($sec < 0 || $sec > 59 || !is_numeric($sec))
		{
			return false;
		}
		return true;
	} 