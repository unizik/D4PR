<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsersModel
 *
 * @author TOCHUKWU
 */
class UsersModel extends MY_Model {
    //put your code here

    protected $_table_name = "tbl_user";
    protected $_primary_key = 'userid';
    protected $_order_by = 'id DESC';
    public $_rules = array();

    function  __construct() {
        parent::__construct();
    }

    function addusermodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(
       'firstname' =>  array(
                'field' => 'firstname',
                'label' => 'First Name',
                'rules' => 'trim|required'
                ),
            'lastname' =>  array(
                'field' => 'lastname',
                'label' => 'Last Name',
                'rules' => 'trim|required'
                ),
            'username' =>  array(
                'field' => 'username',
                'label' => 'User Name',
                'rules' => 'trim|required'
                ),
            'password' =>  array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required'
                ),
            'email' =>  array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required'
                ),
            'phonenum' =>  array(
                'field' => 'phonenumber',
                'label' => 'Phone Number',
                'rules' => 'trim|required'
                ),
            'access' =>  array(
                'field' => 'accesslevel',
                'label' => 'Access Level',
                'rules' => 'trim|required'
                )
            );
    }

     function uploadsignaturemodel($tablename,$prikey,$order){

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
}
?>
