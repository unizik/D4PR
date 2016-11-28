<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of session
 *
 * @author TOCHUKWU
 */
class session extends Admin_Controller{
    //put your code here
    public $sessionstatus = '';
    function  __construct() {
        parent::__construct();
        $this->load->model('sessionmodel');
        $this->sessionstatus = array('activated'=>'Activate', 'deactivated'=>'Deactivate');

       //  $this->_set_nav_menus();
    }

   

    /**
     * this is a default method
     * this loads the session page
     * it passes the subview page to the layout_main page
     */
     public function addsession() {
         if($this->session->userdata("admin_isloggedin")){
        $value = 0;
        $this->_basicsessioninitialize();

        $validationRules = $this->sessionmodel->_rules;

        $this->form_validation->set_rules($validationRules);
        $this->data['active'] = "session";
        //get list of created term group
        $session_data = $this->sessionmodel->get_all();

        //save it in this data so that it can be displayed in the table
        $this->data['createdsession'] = $session_data;
      if ($this->form_validation->run() == true) {
            //after posting form below code is executed
            $now = 'now()';
            $data = $this->sessionmodel->array_from_post(array('sessionname','sessionstatus','sessionid'));

           if ($data['sessionstatus'] == 'activated') {
                    //check if another session is activated
                    $result = $this->_getactivatedsession($data['sessionname']);
                    //if another is, then abort operation
                    if ($result->num_rows() > 0) {
                        foreach($result->result() as $row){

                            $value = $this->sessionmodel->save_update(array(
                                 'sessionstatus' => 'deactivated'), $row->sessionid);
                        }
                       // $this->session->set_flashdata('error', 'Another session is already activated, You can only activated one session');
                        //redirect(site_url('Administrator/session/addsession/' . $this->data['schoolconfig']->schoolid));
                    }
                }
           $sesionid = $this->sessionmodel->generate_unique_id();
            //if the session id isset then update else insert
            if (($data['sessionid']) != '') {

                //check if sessionname already exits
            $othersession = $this->_getothersession($data['sessionid']);
                if($othersession->num_rows() > 0 ){
                
                foreach($othersession->result() as $row){
                   // echo $data['sessionname'].' = '.$row->sessionname;
                   if($row->sessionname == $data['sessionname']) {
                       $this->session->set_flashdata('error', 'A session with '.$data['sessionname']. ' Already Exit, Try another');
                redirect(site_url('Administrator/session/addsession/'));
                   }
                }//exit;

            }

                $value = $this->sessionmodel->save_update(array(
                            'sessionname' => $data['sessionname'],'sessionstatus' => $data['sessionstatus']), $data['sessionid']);
            } else {

                 //check if sessionname already exits
            $sessionadded = $this->_getsession($data['sessionname']);
                if($sessionadded->num_rows() > 0 ){
              $this->session->set_flashdata('error', 'A session with '.$data['sessionname']. ' Already Exit, Try another');
                redirect(site_url('Administrator/session/addsession/'));
            }
            
                $this->db->set('datecreated', $now, false);
                $this->db->set('datemodified', $now, false);
                $insertdata = array(
                    'sessionid' => $sesionid,
                    'sessionname' => $data['sessionname'],
                    'sessionstatus' => $data['sessionstatus'],
                );

                $this->db->insert('tbl_session', $insertdata);
                $value = $this->db->insert_id();

               // $this->_initializemasterplacementtable();

               //insert an initial value for masterlist and placementlist number
                $insertdata = array(
                    'mpid' => $this->sessionmodel->generate_unique_id(),
                    'sessionname' => $data['sessionname'],
                    'sessionid' =>$sesionid,
                    'masterlistnumber' => '0',
                    'placementnumber' => '0',
                );

                $this->db->insert('tbl_master_placement_number', $insertdata);
                $value = $this->db->insert_id();
            }
            if ($value != 0) {
                $this->session->set_flashdata('success', 'Changes successful');
                redirect(site_url('administrator/session/addsession/'));
            } else {
                $this->session->set_flashdata('error', 'An Error Occurred During Update - Kindly retry Operation');
                redirect(site_url('Administrator/session/addsession/'));
            }
            //reset sessionid
            $this->data['sessionid'] = '';
        }

        $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }


    /**
     *
     * @param <type> $sessionid
     * this method updates session based on id
     */
    public function updatesession($sessionid = null) {
        if($this->session->userdata("admin_isloggedin")){
        $this->_basicsessioninitialize();
        $this->data['sessionid'] = $sessionid;
        $this->data['active'] = "session";
        //get list of created session
        $session_data = $this->sessionmodel->get_all();
        $this->data['createdsession'] = $session_data;

        //get the selected termgroupdata based on the termgroupid
        $onesessiondata = $this->sessionmodel->get_where(array('sessionid' => $sessionid), true);
        // $valid_user = $this->termmodel->get_all();
        $this->data['session'] = $onesessiondata;


        $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

     /**
     *
     * @param <type> $termgroupid
     * this method deletes termgroup based on id
     */
    public function deletesession($sessionid = null) {
        //intialize tbl_session table database parameter
        if($this->session->userdata("admin_isloggedin")){
        $this->_basicsessioninitialize();

        $deleterecord = $this->sessionmodel->delete($sessionid);

        $sessiondata = $this->sessionmodel->get_all();
        $this->data['createdsession'] = $sessiondata;
        $this->data['active'] = "session";

        if ($deleterecord) {
            $this->session->set_flashdata('success', 'Delete successful');
            redirect(site_url('administrator/session/addsession/' ));
        } else {
            $this->session->set_flashdata('error', 'An Error Occurred During Delete - Kindly retry Operation');
            redirect(site_url('Administrator/session/addsession/'));
        }

        $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }


     public function _basicsessioninitialize() {

         $this->data['subview'] = 'Administrator/session_page';
         $this->data['pageheading'] = 'Create Session';
        $this->data['sessionid'] = '';


        //initialize the sessionmodel with database parameter
        $this->sessionmodel->addsessionmodel('tbl_session', 'sessionid', 'id DESC');
    }
    public function _initializemasterplacementtable() {

        //initialize the sessionmodel with database parameter
        $this->sessionmodel->initializemasterplacementtable('tbl_master_placement_number', 'mpid', 'id DESC');
    }

    public function _getactivatedsession($activesession){
        $this->db->select();
        $this->db->where('sessionname !=', $activesession);
        $this->db->where('sessionstatus =', 'activated');
        $query = $this->db->get('tbl_session');

        return $query;
    }

    public function _getsession($session){
        $this->db->select('sessionname');
        $this->db->where('sessionname =', $session);
        $query = $this->db->get('tbl_session');

        return $query;
    }

    public function _getothersession($sessionid){
        $this->db->select('sessionname');
        $this->db->where('sessionid !=', $sessionid);
        $query = $this->db->get('tbl_session');

        return $query;
    }
}
?>
