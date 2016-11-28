<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of welcome
 *
 * @author TOCHUKWU
 */
class welcome extends MY_Controller {
    //put your code here
    public function  __construct() {
        parent::__construct();

    }

   public function index(){
      // $this->data['subview'] = 'users/register';
         
       // $this->load->view('template/layout_main', $this->data);
       $this->load->view('welcome_page');
   }
}
?>
