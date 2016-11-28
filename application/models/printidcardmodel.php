<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sessionmodel
 *
 * @author TOCHUKWU
 */
class Printidcardmodel  extends MY_Model{
    //put your code here
    protected $_table_name = "";
    protected $_primary_key = '';
    protected $_order_by = '';



    function  __construct() {
        parent::__construct();
    }
    

    function printstudentidcardmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(
       'faculty' =>  array(
                'field' => 'facultyid',
                'label' => 'Faculty Name',
                'rules' => 'trim|required'
                ),
            'dept' =>  array(
                'field' => 'department',
                'label' => 'Department',
                'rules' => 'trim|required'
                ),
            'session' =>  array(
                'field' => 'session',
                'label' => 'Session',
                'rules' => 'trim|required'
                )
            );
    }

}
?>
