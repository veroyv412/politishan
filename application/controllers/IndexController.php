<?php
date_default_timezone_set('America/Buenos_Aires');

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->config = Zend_Registry::get('twitterConfig');

        $this->view->setEncoding('UTF-8');
        $this->view->doctype('XHTML1_STRICT');
        $this->view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
        $this->view->headTitle("Politishan");

        $this->view->headScript()->appendFile('/js/jquery.js');

        //We dont need this in the testing environment now, because we need to do the rest of the appi
        //Database is loaded with information
        //App_Process::storePolitishiansToDatabaseForFirstTime();
		//App_Process::loadTwitterData();
        //App_Process::loadKloutData();
        //App_Process::getTweetsByPolitishan('CFKArgentina');
        //App_Process::getReservedWordsList();
        //App_Process::doFullProcess();
    }

    public function indexAction()
    {}


}

