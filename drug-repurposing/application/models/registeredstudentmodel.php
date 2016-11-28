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
class registeredstudentmodel  extends MY_Model{
    //put your code here
    protected $_table_name = "";
    protected $_primary_key = '';
    protected $_order_by = '';



    function  __construct() {
        parent::__construct();
    }

    function changepasswordmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(
       'currentpassword' =>  array(
                'field' => 'currentpassword',
                'label' => 'current password',
                'rules' => 'trim|required'
                ),
            'newpassword' =>  array(
                'field' => 'newpassword',
                'label' => 'new password',
                'rules' => 'trim|required'
                ),
            'confirmpassword' =>  array(
                'field' => 'confirmpassword',
                'label' => 'confirm password',
                'rules' => 'trim|required'
                )
            );
    }
    
    function activateaccountmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(
       'pin' =>  array(
                'field' => 'pin',
                'label' => 'Pin',
                'rules' => 'trim|required'
                ),
            'serial' =>  array(
                'field' => 'serial',
                'label' => 'Serial',
                'rules' => 'trim|required'
                ),
            'sessionid' =>  array(
                'field' => 'sessionid',
                'label' => 'Session',
                'rules' => 'trim|required'
                )
            );
    }

    function uploadspemodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(       
            'org' =>  array(
                'field' => 'placeofattachment',
                'label' => 'Organisation/Instutions Name',
                'rules' => 'trim|required'
                ),
            'employerpayment' =>  array(
                'field' => 'employerpayment',
                'label' => 'Amount Paid by Employer',
                'rules' => 'trim|required'
                ),
            'add' =>  array(
                'field' => 'Addressofattachment',
                'label' => 'Address',
                'rules' => 'trim|required'
                ),
            'email' =>  array(
                'field' => 'employeremail',
                'label' => 'Employer Email',
                'rules' => 'trim|required'
                ),
            'phone' =>  array(
                'field' => 'employerphonenumber',
                'label' => 'Employer Phone Number',
                'rules' => 'trim|required'
                ),
            'state' =>  array(
                'field' => 'state',
                'label' => 'State',
                'rules' => 'trim|required'
                ),
            'country' =>  array(
                'field' => 'country',
                'label' => 'Country',
                'rules' => 'trim|required'
                ),
            'locationid' =>  array(
                'field' => 'locationid',
                'label' => 'Location',
                'rules' => 'trim|required'
                ),
            'session' =>  array(
                'field' => 'session',
                'label' => 'Session',
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
