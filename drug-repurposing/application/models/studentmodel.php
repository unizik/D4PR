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
class studentmodel  extends MY_Model{
    //put your code here
    protected $_table_name = "";
    protected $_primary_key = '';
    protected $_order_by = '';



    function  __construct() {
        parent::__construct();
    }

    function verifystudentmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(
       'regist' =>  array(
                'field' => 'regnumber',
                'label' => 'Reg Number',
                'rules' => 'trim|required'
                )
            );
    }

    function addstudentmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(       
            'reg' =>  array(
                'field' => 'regnumber',
                'label' => 'Reg Number',
                'rules' => 'trim|required'
                ),
            'facultyid' =>  array(
                'field' => 'facultyid',
                'label' => 'Faculty Name',
                'rules' => 'trim|required'
                ),
            'department' =>  array(
                'field' => 'department',
                'label' => 'Department',
                'rules' => 'trim|required'
                ),
            'level' =>  array(
                'field' => 'level',
                'label' => 'Level',
                'rules' => 'trim|required'
                ),
            'lastname' =>  array(
                'field' => 'lastname',
                'label' => 'Last Name',
                'rules' => 'trim|required'
                ),
            'firstname' =>  array(
                'field' => 'firstname',
                'label' => 'First Name',
                'rules' => 'trim|required'
                ),
            'middlename' =>  array(
                'field' => 'middlename',
                'label' => 'Middle Name',
                'rules' => 'trim|required'
                ),
            'gender' =>  array(
                'field' => 'gender',
                'label' => 'Gender',
                'rules' => 'trim|required'
                ),
            'nationality' =>  array(
                'field' => 'nationality',
                'label' => 'Nationality',
                'rules' => 'trim|required'
                ),
            'bankname' =>  array(
                'field' => 'bankname',
                'label' => 'Bank Name',
                'rules' => 'trim|required'
                ),
            'accountnumber' =>  array(
                'field' => 'accountnumber',
                'label' => 'Account Number',
                'rules' => 'trim|required'
                ),
            'sortcode' =>  array(
                'field' => 'sortcode',
                'label' => 'Sort Code',
                'rules' => 'trim|required'
                ),

            'bloodgroup' =>  array(
                'field' => 'bloodgroup',
                'label' => 'Blood Group',
                'rules' => 'trim|required'
                ),
            'phonenumber' =>  array(
                'field' => 'phonenumber',
                'label' => 'Phone Number',
                'rules' => 'trim|required'
                )
            );
    }

    function updatestudentmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(

            'facultyid' =>  array(
                'field' => 'facultyid',
                'label' => 'Faculty Name',
                'rules' => 'trim|required'
                ),
            'department' =>  array(
                'field' => 'department',
                'label' => 'Department',
                'rules' => 'trim|required'
                ),
            'level' =>  array(
                'field' => 'level',
                'label' => 'Level',
                'rules' => 'trim|required'
                ),
            'lastname' =>  array(
                'field' => 'lastname',
                'label' => 'Last Name',
                'rules' => 'trim|required'
                ),
            'firstname' =>  array(
                'field' => 'firstname',
                'label' => 'First Name',
                'rules' => 'trim|required'
                ),
            'middlename' =>  array(
                'field' => 'middlename',
                'label' => 'Middle Name',
                'rules' => 'trim|required'
                ),
            'nationality' =>  array(
                'field' => 'nationality',
                'label' => 'Nationality',
                'rules' => 'trim|required'
                ),
            'bankname' =>  array(
                'field' => 'bankname',
                'label' => 'Bank Name',
                'rules' => 'trim|required'
                ),
            'accountnumber' =>  array(
                'field' => 'accountnumber',
                'label' => 'Account Number',
                'rules' => 'trim|required'
                ),
            'sortcode' =>  array(
                'field' => 'sortcode',
                'label' => 'Sort Code',
                'rules' => 'trim|required'
                ),

            'bloodgroup' =>  array(
                'field' => 'bloodgroup',
                'label' => 'Blood Group',
                'rules' => 'trim|required'
                ),
            'phonenumber' =>  array(
                'field' => 'phonenumber',
                'label' => 'Phone Number',
                'rules' => 'trim|required'
                )
            );
    }
    
    
}
?>
