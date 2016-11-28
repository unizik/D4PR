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
class Setupmodel  extends MY_Model{
    //put your code here
    protected $_table_name = "";
    protected $_primary_key = '';
    protected $_order_by = '';



    function  __construct() {
        parent::__construct();
    }

    function addlocationmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(
       'state' =>  array(
                'field' => 'state',
                'label' => 'state',
                'rules' => 'trim|required'
                ),
            'location' =>  array(
                'field' => 'location',
                'label' => 'location',
                'rules' => 'trim|required'
                )
            );
    }
    
    function addspe1deadlinesetup($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(

             'uploadstart' =>  array(
                'field' => 'uploadstart',
                'label' => 'SPE1 Submission Start',
                'rules' => 'trim|required'
                ),
            'uploadend' =>  array(
                'field' => 'uploadend',
                'label' => 'SPE1 Submission End',
                'rules' => 'trim|required'
                ),



            );
    }
    
}
?>
