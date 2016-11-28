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
class Printidcard extends Admin_Controller {

    //put your code here
    public $sessionstatus = '';

    function __construct() {
        parent::__construct();
        $this->load->model('printidcardmodel');
       
    }

    public function index() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicprintidcardinitialize();
           // $this->session->set_userdata('uploadedfile', '');
            $this->data['active'] = "printidcard";
            //get faculty
            $this->data['facultydata'] = $this->_getfaculty();
            //get department
            $this->data['departmentdata'] = $this->_getdepartment();

            $this->data['sessiondata'] = $this->_getactivatedsession();

            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

  public function loadprintsingleidcardpage(){
       if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicprintstudentidcardinitialize();
           // $this->session->set_userdata('uploadedfile', '');
            $this->data['active'] = "printidcard";
            //get faculty
            //$this->data['facultydata'] = $this->_getfaculty();
            //get department
            //$this->data['departmentdata'] = $this->_getdepartment();

            $this->data['sessiondata'] = $this->_getactivatedsession();

            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
  }




  public function printstudentidcard() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
           // $this->_basicprintmasterlistinitialize();
                  $this->data['active'] = "printidcard";    
            //$validationRules = $this->printidcardmodel->_rules;

            //$this->form_validation->set_rules($validationRules);
            //if ($this->form_validation->run() == true) {
                //get post data
                $data = $this->input->get();

                if($data['department'] == 'Accountancy'){
                  $this->data['printidcarddata'] = $this->_getAccountancystudentdataparameter($data['session'],$data['facultyid'],$data['department']);   
                }else{
                $this->data['printidcarddata'] = $this->_getstudentdataparameter($data['session'],$data['facultyid'],$data['department']);
                }
                if ($this->data['printidcarddata'] == 0) {
                    $this->session->set_flashdata('error', 'No Student Data exit for this department in this session');
                    redirect(site_url('Administrator/printidcard'));
                }                
           // }
                //print_r($this->data['printidcarddata']);exit;
                $this->load->view('administrator/print_studentid_card_page', $this->data);
            //$this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

     public function printsinglestudentidcard() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
           // $this->_basicprintmasterlistinitialize();
            $this->data['active'] = "printidcard";
            //$validationRules = $this->printidcardmodel->_rules;

            //$this->form_validation->set_rules($validationRules);
            //if ($this->form_validation->run() == true) {
                //get post data
                $data = $this->input->get();

                $this->data['printidcarddata'] = $this->_getsinglestudentdataparameter($data['session'], $data['regnumber']);
                if ($this->data['printidcarddata'] == 0) {
                    $this->session->set_flashdata('error', 'Student with '. $data['regnumber']. ' does not exist in this session ');
                    redirect(site_url('Administrator/printidcard'));
                }
           // }
                //print_r($this->data['printidcarddata']);exit;
                $this->load->view('administrator/print_studentid_card_page', $this->data);
            //$this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }


  
    public function _getfaculty() {
        $facultydata = 0;
        $query = $this->db->get('tbl_faculty');
         if($query->num_rows() > 0)
        $facultydata = $query->result();

        return $facultydata;
    }

    public function _getdepartment() {
        $departmentdata = 0;
        $query = $this->db->get('tbl_department');
        if($query->num_rows() > 0)
        $departmentdata = $query->result();

        return $departmentdata;
    }



   

    public function _basicprintidcardinitialize() {

        $this->data['subview'] = 'Administrator/printidcard_page';
        $this->data['pageheading'] = 'Print Student ID Card';
       // $this->data['uploaddata'] = '';


        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
       /// $this->masterlistmodel->uploadmasterlistmodel('tbl_master_list', 'masterlistid', 'id DESC');
    }

     public function _basicprintstudentidcardinitialize() {
           $this->data['subview'] = 'Administrator/printidcard_student_page';
        $this->data['pageheading'] = 'Print Student ID Card';

     }
   
    

    public function _getactivatedsession() {
        $sessiondata = 0;
        $query = $this->db->get_where('tbl_session', array('sessionstatus' => 'activated'));
        if ($query->num_rows() > 0)
            $sessiondata = $query->result();

        return $sessiondata;
    }
   
     public function _getstudentdataparameter($session,$facultyid,$department) {
        $printid = 0;
        $this->db->select();
        $this->db->from('tbl_student');
        $this->db->join('tbl_master_list', 'tbl_student.regnumber = tbl_master_list.regnumber', 'inner');
        $this->db->join('tbl_faculty', 'tbl_student.facultyid = tbl_faculty.facultyid', 'inner');
        $this->db->where('tbl_master_list.sessionid', $session);
        $this->db->where('tbl_student.facultyid', $facultyid);
        $this->db->where('tbl_student.department', $department);
        $this->db->where('tbl_master_list.mobilizationstatus', 'mobilized');

        $query = $this->db->get();

        if ($query->num_rows()) {

            $printid = $query->result();
        }
		 //echo $this->db->last_query();exit;
        return $printid;
    }
    
    public function _getAccountancystudentdataparameter($session,$facultyid,$department) {
        $printid = 0;
        $this->db->select();
        $this->db->from('tbl_student');
       // $this->db->join('tbl_master_list', 'tbl_student.studentid = tbl_master_list.studentid', 'inner');
        $this->db->join('tbl_faculty', 'tbl_student.facultyid = tbl_faculty.facultyid', 'inner');
       // $this->db->where('tbl_master_list.session', $session);
        $this->db->where('tbl_student.facultyid', $facultyid);
        $this->db->where('tbl_student.department', $department);
      //  $this->db->where('tbl_master_list.mobilizationstatus', 'mobilized');

        $query = $this->db->get();

        if ($query->num_rows()) {

            $printid = $query->result();
        }
		 //echo $this->db->last_query();exit;
        return $printid;
    }

     public function _getsinglestudentdataparameter($session,$regnumber) {
        $printid = 0;
        $this->db->select();
        $this->db->from('tbl_student');
        $this->db->join('tbl_master_list', 'tbl_student.studentid = tbl_master_list.studentid', 'inner');
        $this->db->join('tbl_faculty', 'tbl_student.facultyid = tbl_faculty.facultyid', 'inner');
        $this->db->where('tbl_master_list.session', $session);
        $this->db->where('tbl_student.regnumber', $regnumber);
        $this->db->where('tbl_master_list.mobilizationstatus', 'mobilized');

        $query = $this->db->get();

        if ($query->num_rows()) {

            $printid = $query->result();
        }
        return $printid;
    }

    public function _getsession($session) {
        $this->db->select('sessionname');
        $this->db->where('sessionname =', $session);
        $query = $this->db->get('tbl_session');

        return $query;
    }

    public function _getallsessionname() {
        $session = 0;
        $this->db->select('sessionname');
        $query = $this->db->get('tbl_session');
        if ($query->num_rows() > 0) {
            $session = $query->result();
        }
        return $session;
    }

}

?>
