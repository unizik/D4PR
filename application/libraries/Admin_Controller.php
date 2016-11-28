<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin_Controller
 *
 * @author Maxwell
 */
class Admin_Controller extends MY_Controller{
    
    function __construct() {
        parent::__construct();
		
        //$this->data['menu'] = 'layout_admin_menu';
        
        if(!$this->session->userdata("admin_isloggedin")){
            redirect(site_url('login/logout'));
        }

    }

}
