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
class Registration extends User_Controller{
    //put your code here

    function  __construct() {
        parent::__construct();
        $this->load->model('registrationmodel');
       //  $this->_set_nav_menus();
       
    }

   
    /**
     * this is a default method
     * this loads the session page
     * it passes the subview page to the layout_main page
     */
     public function addregistration() {
         //echo "am here";exit;
         if($this->session->userdata("user_isloggedin")){
        $value = 0;
        $this->_basicregistrationinitialize();
        $this->data['active'] = "faculty";
       // $validationRules = $this->facultymodel->_rules;

        //$this->form_validation->set_rules($validationRules);
    
        //get list of created faculty
        //$faculty_data = $this->registrationmodel->get_all();

        //save it in this data so that it can be displayed in the table
        //$this->data['createdfaculty'] = $faculty_data;
        //if ($this->form_validation->run() == true) {
            $userid = $this->registrationmodel->generate_unique_id();
            $now = 'now()';
            $data = $this->input->post();
           // print_r($data);exit;
            //if the faculty id isset then update else insert
            if (($data['userid']) != '') {
               
                $value = $this->registrationmodel->save_update(array(
                            'facultyname' => $data['facultyname']), $data['facultyid']);
                
            } else {
               
                $this->db->set('datecreated', $now, false);
                $this->db->set('datemodified', $now, false);
                $insertdata = array(
                    'userid' => $userid ,
                    'firstname' => $data['firstname'],
                    'lastname' => $data['lastname'],
                    'middlename' => $data['middlename'],
                    'personalemail' => $data['personalemail'],
                    'institution' => $data['institution'],
                    'orcidid' => $data['orcidid'],
                    'fieldofstudy' => $data['fieldofstudy'],
                    'areaofspecialization' => $data['areaofspecialization'],
                    'phonenumber' => $data['phonenumber'],
                    'address' => $data['address'],
                    'federatedemail' => $data['federatedemail'],
                    'accesslevel' => "user"
                                                            
                );

                $this->db->insert('tbl_user', $insertdata);
                $value = $this->db->insert_id();                             
            
            }
            if ($value != 0) {
                $this->data['userprofile'] = $this->_getUserProfile($userid);
                $this->session->set_flashdata('success', 'Changes successful');
                
                //redirect(site_url('user/faculty/addfaculty/'));
            } else {
                $this->session->set_flashdata('error', 'An Error Occurred - Kindly retry Operation');
                redirect(site_url('login'));
            }
            //reset sessionid
            $this->data['facultyid'] = '';
        }

        $this->load->view('template/layout_main', $this->data);
        
    }


    /**
     *
     * @param <type> $sessionid
     * this method updates session based on id
     */
    public function updatefaculty($facultyid = null) {
        if($this->session->userdata("user_isloggedin")){
        $this->_basicfacultyinitialize();
        $this->data['facultyid'] = $facultyid;
        $this->data['active'] = "faculty";
        //get list of created faculty
        $faculty_data = $this->facultymodel->get_all();
        $this->data['createdfaculty'] = $faculty_data;

        //get the selected faculty based on the facultyid
        $onefacultydata = $this->facultymodel->get_where(array('facultyid' => $facultyid), true);
        // $valid_user = $this->termmodel->get_all();
        $this->data['faculty'] = $onefacultydata;


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
    public function deletefaculty($facultyid = null) {
        //intialize tbl_faculty table database parameter
        if($this->session->userdata("user_isloggedin")){
        $this->_basicfacultyinitialize();
$this->data['active'] = "faculty";
        $deleterecord = $this->facultymodel->delete($facultyid);

        $facultydata = $this->facultymodel->get_all();
        $this->data['createdfaculty'] = $facultydata;


        if ($deleterecord) {
            $this->session->set_flashdata('success', 'Delete successful');
            redirect(site_url('administrator/faculty/addfaculty/' ));
        } else {
            $this->session->set_flashdata('error', 'An Error Occurred During Delete - Kindly retry Operation');
            redirect(site_url('administrator/faculty/addfaculty/'));
        }

        $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }


     public function _basicregistrationinitialize() {

         $this->data['subview'] = 'users/dashboard_page';         
         $this->data['userid'] = '';
        //initialize the sessionmodel with database parameter
        $this->registrationmodel->addregistrationmodel('tbl_user', 'userid', 'id DESC');
    }

    public function _getUserProfile($userid){       
        $data = '';
       $query =  $this->db->get_where('tbl_user', array('userid' => $userid));      
       if($query->num_rows() > 0){                  
       foreach($query->result() as $row){
           $data = $row;
        }
       }
       return  $data;
    }
    
    
}
?>
