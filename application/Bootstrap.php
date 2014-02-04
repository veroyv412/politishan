<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    protected function _initTwitterConfig()
    {
        $twitterConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/politishans.ini', 'production');
        Zend_Registry::set('twitterConfig',$twitterConfig);
    }

    protected function _initKloutConfig()
    {
        $kloutConfig = $this->getOption('klout');
        Zend_Registry::set('kloutConfig',$kloutConfig);
    }

    protected function _initConfig()
    {
        $config = $this->getOption('app');
        Zend_Registry::set('config',$config);
    }

    protected function _initReservedWords()
    {
        $string = file_get_contents(APPLICATION_PATH . '/configs/reservedWords.json');
        $reservedWordsConfig=json_decode($string);

        Zend_Registry::set('reservedWordsConfig',$reservedWordsConfig);
    }

}

