<?php

class DpointController extends Point_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->content = "Some nice value";
    }


}

