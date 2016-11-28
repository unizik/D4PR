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
class Placementlistmodel  extends MY_Model{
    //put your code here
    protected $_table_name = "";
    protected $_primary_key = '';
    protected $_order_by = '';



    function  __construct() {
        parent::__construct();
    }
    

    
    function initializemasterplacementtable($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        
     
        }

        function placementlistbysessionmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(

            'session' =>  array(
                'field' => 'session',
                'label' => 'Session',
                'rules' => 'trim|required'
                )
            
            );
    }


    function placementlistbystatemodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(

            'session' =>  array(
                'field' => 'session',
                'label' => 'Session',
                'rules' => 'trim|required'
                ),
            'state' =>  array(
                'field' => 'state',
                'label' => 'State',
                'rules' => 'trim|required'
                )
            );
    }
    
    function sendsmsmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(

            'session' =>  array(
                'field' => 'session',
                'label' => 'Session',
                'rules' => 'trim|required'
                ),
            'receipient' =>  array(
                'field' => 'receipient',
                'label' => 'Send To',
                'rules' => 'trim|required'
                ),
            'message' =>  array(
                'field' => 'message',
                'label' => 'Message',
                'rules' => 'trim|required'
                )
            );
    }
    
    function generatepinmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(
            
            'quantity' =>  array(
                'field' => 'quantity',
                'label' => 'Quantity',
                'rules' => 'trim|required'
                )
            );
    }

    function printpinmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(
            
            'dateuploaded' =>  array(
                'field' => 'dateuploaded',
                'label' => 'Date',
                'rules' => 'trim|required'
                )
            );
    }

    function printplacementlistmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(
            /**
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
             * **
             */
            'session' =>  array(
                'field' => 'session',
                'label' => 'Session',
                'rules' => 'trim|required'
                )
            );
    }

}
?>
