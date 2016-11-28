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
class sessionmodel  extends MY_Model{
    //put your code here
    protected $_table_name = "";
    protected $_primary_key = '';
    protected $_order_by = '';



    function  __construct() {
        parent::__construct();
    }

    function addsessionmodel($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        $this->_rules = array(
       'session' =>  array(
                'field' => 'sessionname',
                'label' => 'Session',
                'rules' => 'trim|required'
                ),
            'activated' =>  array(
                'field' => 'sessionstatus',
                'label' => 'Session Status',
                'rules' => 'trim|required'
                )
            );
    }
    
    function initializemasterplacementtable($tablename,$prikey,$order){

        $this->_table_name = $tablename;
      $this->_primary_key = $prikey;
     $this->_order_by = $order;
        
     
        }
}
?>
