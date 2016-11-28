<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dashboard
 *
 * @author TOCHUKWU
 */
class Dashboard extends User_Controller{
    //put your code here

    function  __construct() {
        parent::__construct();
        //$this->load->model('schoolconfigmodel');
        // $this->_set_nav_menus();
    }

    /**
     * this is a default method
     * that loads once the login attempt
     * is successful
     * it passes the subview page to the layout_main page
     */
     public function index() {
    // echo $this->data['login_session'].'dash';exit;
        if($this->session->userdata("user_isloggedin")){
         $this->data['subview'] = 'users/dashboard_page';
         $this->data['active'] = "dashboard";
        $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
           redirect(site_url('login'));
        }
        
    }
       
}
?>
