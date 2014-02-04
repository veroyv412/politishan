<?php
class LogController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
    	$this->view->lines = $this->read_file(APPLICATION_PATH . '/logs/politishan.log', 2000);
    }
	
	private function read_file($file, $lines) {
	    //global $fsize;
	    $handle = fopen($file, "r");
	    $linecounter = $lines;
	    $pos = -2;
	    $beginning = false;
	    $text = array();
	    while ($linecounter > 0) {
	        $t = " ";
	        while ($t != "\n") {
	            if(fseek($handle, $pos, SEEK_END) == -1) {
	                $beginning = true; 
	                break; 
	            }
	            $t = fgetc($handle);
	            $pos --;
	        }
	        $linecounter --;
	        if ($beginning) {
	            rewind($handle);
	        }
	        $text[$lines-$linecounter-1] = fgets($handle);
	        if ($beginning) break;
	    }
	    fclose ($handle);
	    return array_reverse($text);
	}
}
