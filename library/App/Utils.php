<?php
class App_Utils
{

    public static function log( $msg )
	{
		$logger = new Zend_Log();
		$writer = new Zend_Log_Writer_Stream('php://stderr');
		$logger->addWriter($writer);
		$logger->info($msg);
        
		return $logger;
	}


    /**
	 * @desc Becomes an array in an object
	 *
	 * @param array $array
	 * @return object
	 * @example
	 * 		$foo['bar']['pp'] = "hello";
	 * 		$bar = Umb_Utils::arrayToObject($foo);
	 * 		echo $bar->bar->pp; #echo "hello"
	 */
	public static function arrayToObject($array = array())
	{
		if (!empty($array)) {
			$data = false;
			foreach ($array as $akey => $aval) {
				$data->{trim($akey)} = is_array($aval) ? self::arrayToObject($aval) : $aval;
			}
			return $data;
		}
		return false;
	}

    public static function relativeTime( $dt,$precision=2 )
    {
        $times=array(	365*24*60*60	=> "year",
                        30*24*60*60		=> "month",
                        7*24*60*60		=> "week",
                        24*60*60		=> "day",
                        60*60			=> "hour",
                        60				=> "minute",
                        1				=> "second");

        $passed=time()-$dt;

        if($passed<5)
        {
            $output='less than 5 seconds ago';
        }
        else
        {
            $output=array();
            $exit=0;

            foreach($times as $period=>$name)
            {
                if($exit>=$precision || ($exit>0 && $period<60)) break;

                $result = floor($passed/$period);
                if($result>0)
                {
                    $output[]=$result.' '.$name.($result==1?'':'s');
                    $passed-=$result*$period;
                    $exit++;
                }
                else if($exit>0) $exit++;
            }

            $output=implode(' and ',$output).' ago';
        }

        return $output;
    }
}
