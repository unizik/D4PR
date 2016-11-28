<?php

class MY_Controller extends CI_Controller{
    
    public $data = array();

    function __construct() {
        parent::__construct();

        //$school_config = $this->db->get('tbl_school_config')->row();

       // if(count($school_config)){
           //$this->data['schoolconfig'] =  $school_config;
        //}else{
            //redirect to the Installation Page
       // }

		//ini_set('memory_limit', "512M");

        $this->data['page_level_scripts'] = '';
        $this->data['page_level_styles'] = '';

        //initialize the userlogin session detail
       // $this->data['login_session'] = '';

        $this->data['menu'] = '';
    }

}

