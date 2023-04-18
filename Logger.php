<?php

class Logger
{
	public function __construct() {
		// $this->var = $var;
	}

	private function logger($level, $info)
	{
		$now = DateTime::createFromFormat('U.u', microtime(true));
		$now = $now->setTimeZone(new DateTimeZone(date_default_timezone_get()));
		$dt = $now->format("Y-m-d H:i:s.u");
		echo "\n[$level] $dt: ".$info;
	}

	/** Information. */
	public function log($info)
	{
		$this->logger('INFO', $info);
	}
	/** Warning. */
	public function warn($info)
	{
		$this->logger('WARNING', $info);
	}
	/** Error. */
	public function error($info)
	{
		$this->logger('ERROR', $info);
	}
}
