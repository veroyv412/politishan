<?php
class AjaxController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
    }

    public function indexAction()
    {}

    /** This method orders the politician list by $field ordered by $order [ASC/DESC]
     *
     * @param <String> $field
     * @param <String> $order ASC | DESC
     */
    public function orderpoliticianAction()
    {
        $field = $this->_request->getParam('field');
        $order = $this->_request->getParam('order');

        $orderedBy = $order === "ASC" ? 1 : -1;
        $politicians = Model_Politician::fetchAll()->sort( array($field => $orderedBy) );

        $this->view->politicians = $politicians;
        $this->render('orderpolitician');
    }


}

