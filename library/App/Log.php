<?php
class App_Log
{
    const SEPARATOR = "| ";
    
    private $_log_file;

    public function __construct($log_file)
	{
		$this->_log_file = $log_file;
	}

    public function log($msg)
	{
		$d = date("Y-m-d H:i:s");
		$m = $d . self::SEPARATOR . str_replace("\n", ("\n" . str_repeat(" ", strlen($d)) . self::SEPARATOR), $msg);
		$this->_write($m);
	}

    protected function _write($msg = "")
	{
		try {
			@file_put_contents($this->_log_file, utf8_encode($msg) . "\n", FILE_APPEND);
		}
		catch (Exception $e) {
		}
	}
}

