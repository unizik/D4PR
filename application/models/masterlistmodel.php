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
class Masterlistmodel  extends MY_Model{
    //put your code here
    protected $_table_name = "";
    protected $_primary_key = '';
    protected $_order_by = '';



    function  __construct() {
        parent::__construct();
    }
    

    public function uploadmasterlistmodel($tablename,$prikey,$order){
         $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
     
     $this->_rules = array(
       'submitbutton' =>  array(
                'field' => 'submitbutton',
                'label' => 'submitbutton',
                'rules' => 'trim|required'
                )
         );

    }

    public function processmasterlistmodel($tablename,$prikey,$order){
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
             'startmonth' =>  array(
                'field' => 'startmonth',
                'label' => 'Month of commencement',
                'rules' => 'trim|required'
                ),
             'endmonth' =>  array(
                'field' => 'endmonth',
                'label' => 'Month of completion',
                'rules' => 'trim|required'
                ),
             'attachmentyear' =>  array(
                'field' => 'attachmentyear',
                'label' => 'Year of attachment',
                'rules' => 'trim|required'
                ),
            'session' =>  array(
                'field' => 'session',
                'label' => 'Session',
                'rules' => 'trim|required'
                )
            );
        
    }

    function initializemasterplacementtable($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        
     
        }

        function masterlistbysessionmodel($tablename,$prikey,$order){

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


    function printmasterlistmodel($tablename,$prikey,$order){

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
             *
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
