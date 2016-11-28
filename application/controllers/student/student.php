<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dashboard
 *
 * @author TOCHUKWU
 */
class Student extends Student_Controller {

    //put your code here
    public $countries = '';
    public $states = '';

    function __construct() {
        parent::__construct();
        $this->load->model('registeredstudentmodel');
        session_start();
        $this->level = array('100' => '100', '200' => '200', '300' => '300', '400' => '400', '500' => '500', '600' => '600');
        $this->gender = array('Male' => 'Male', 'Female' => 'Female');
        $this->countries = $this->_getcountries();
        $this->states = $this->_getstates();
    }

    /**
     * this is a default method
     * that loads once the login attempt
     * is successful
     * it passes the subview page to the layout_main page
     */
    public function index() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("student_isloggedin")) {
            $this->data['subview'] = 'student/dashboard_page';

            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function activateaccount() {
        if ($this->session->userdata("student_isloggedin")) {
            //if ($regnumber == null)
            //  show_error('Student not Recognized - Contact Admin if Issue Persists');
            //echo 'am here';exit;
            $this->data['active'] = "account";
            $this->_basicaccountinitialize();

            $activesession = '';
            //get session
            $this->data['sessiondata'] = $this->_getactivatedsession();

            if ($this->data['sessiondata']) {
                foreach ($this->data['sessiondata'] as $row) {
                    $activesessionid = $row->sessionid;
                }
            } else {
                $this->session->set_flashdata('error', 'No Activated Session, Please Call  Siwes Unit, If Problem Persists ');
                redirect(site_url('student/dashboard'));
            }

            $validationRules = $this->registeredstudentmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                $now = 'now()';
                $data = $this->registeredstudentmodel->array_from_post(array('pin', 'serial', 'sessionid'));

                $pinserialvalid = $this->_verifypinserial($data['pin'], $data['serial']);
                if (!$pinserialvalid) {

                    $this->session->set_flashdata('error', 'Invalid Pin or Serial');
                    redirect(site_url('student/student/activateaccount'));
                }

                $pinserialstatus = $this->_verifypinserialstatus($data['pin'], $data['serial']);
                if (!$pinserialstatus) {

                    $this->session->set_flashdata('error', ' Pin and Serial Number has been Used');
                    redirect(site_url('student/student/activateaccount'));
                }
                // send update to database
                //$isUpdate = 0;
                $this->db->set('datemodified', $now, false);
                $updatedata = array(
                    'status' => '1',
                    'regnumber' => $this->session->userdata('user_name'),
                    'sessionid' => $activesessionid
                );

                $this->db->set($updatedata);
                $this->db->where('pin', $data['pin']);
                $this->db->where('serial', $data['serial']);
                $value = $this->db->update('tbl_pins_serials');
                //$isUpdate = $this->registeredstudentmodel->save_update(array(,'regnumber' => $this->session->userdata('user_name'),), $this->session->userdata('user_name'));
                if ($value) {
                    $this->session->set_flashdata('success', 'Account Activation Successfull.');
                    redirect(site_url('student/student/activateaccount'));
                } else {
                    $this->session->set_flashdata('error', 'Account Activation NOT Successfull - Kindly retry Operation');
                    redirect(site_url('student/student/activateaccount'));
                }

                //}
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    /**
     * this is a score configuration method
     * used to create the number of forms for
     * the CAs and Exam
     */
    public function changepassword() {
        if ($this->session->userdata("student_isloggedin")) {
            //if ($regnumber == null)
            //  show_error('Student not Recognized - Contact Admin if Issue Persists');
            //echo 'am here';exit;
            $this->data['active'] = "password";
            $this->_basicpasswordinitialize();
            //$validationRules = $this->schoolconfigmodel->_rules;
            //$this->form_validation->set_rules($validationRules);
            // if($this->form_validation->run() == true){
            //after posting form below code is executed
            $validationRules = $this->registeredstudentmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                $data = $this->registeredstudentmodel->array_from_post(array('currentpassword', 'newpassword', 'confirmpassword'));

                $onestudentdata = $this->registeredstudentmodel->get_where(array('regnumber' => $this->session->userdata('user_name')), true);
                //print_r($onestudentdata);exit;
                if ($onestudentdata->password == md5($data['currentpassword'])) {
                    $isUpdate = $this->registeredstudentmodel->save_update(array('password' => md5($data['newpassword'])), $this->session->userdata('user_name'));
                } else {
                    $this->session->set_flashdata('error', 'Invalid Current password - Kindly Enter Another');
                    redirect(site_url('student/student/changepassword'));
                }
                // send update to database
                //$isUpdate = $this->schoolconfigmodel->save_update( $data, $schoolid);

                if ($isUpdate) {
                    $this->session->set_flashdata('success', 'Password Change updated');
                    redirect(site_url('student/student/changepassword'));
                } else {
                    $this->session->set_flashdata('error', 'An Error Occurred During Update - Kindly retry Operation');
                    redirect(site_url('student/student/changepassword'));
                }

                //}
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function uploadspe() {
        if ($this->session->userdata("student_isloggedin")) {
            //if ($regnumber == null)
            //  show_error('Student not Recognized - Contact Admin if Issue Persists');
            $value = 0;
            $biodatainfo=0;
            $this->_basicuploadspeinitialize();
            $activesession = '';
            $activesessionid = '';
            $validationRules = $this->registeredstudentmodel->_rules;
            $this->data['active'] = "uploadspe";
            $this->form_validation->set_rules($validationRules);

            //get session
            $this->data['sessiondata'] = $this->_getactivatedsession();

            $this->data['location'] = $this->_getlocation();

            if ($this->data['sessiondata']) {
                foreach ($this->data['sessiondata'] as $row) {
                    $activesession = $row->sessionname;
                    $activesessionid = $row->sessionid;
                }
            } else {
                $this->session->set_flashdata('error', 'No Activated Session, Please Call  Siwes Unit, If Problem Persists ');
                redirect(site_url('student/dashboard'), 'refresh');
            }

            $activationstatus = $this->_checkifaccountisactivated($this->session->userdata('user_name'), $activesessionid);
            if ($activationstatus) {
                
            } else {
                $this->session->set_flashdata('error', 'Account INACTIVE for this SESSSION, Purchase and USE the Scratch card from the SIWES Unit to ACTIVATE your Account ');
                redirect(site_url('student/dashboard'), 'refresh');
            }

             //check if student has filled biodata form
            $query = $this->db->get_where('tbl_student', array('regnumber' => $this->session->userdata('user_name')));
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $biodatainfo = $row;
                    //print_r($biodatainfo);exit;
                }
            }
            if($biodatainfo){
            if ($biodatainfo->bankname==''  && $biodatainfo->accountnumber=='' && $biodatainfo->sortcode=='') {
               $this->session->set_flashdata('error', 'Please Fill your BIODATA First and then Upload SPE1 Form ');
                redirect(site_url('student/dashboard'), 'refresh'); 
            }
            }
           // print_r($biodatainfo);exit;
            //check if student was mobilized
            $studentinfo = $this->_checkifstudentismobilised($this->session->userdata('user_name'), $activesessionid);
            if ($studentinfo) {
                
            } else {
                $this->session->set_flashdata('error', 'Sorry You were not Mobilized for this Session, Please Call  Siwes Unit, If Problem Persists ');
                redirect(site_url('student/dashboard'));
            }

            $spe1uploadduration = $this->_getspe1uploadduration();
            //check  the time for course registration
            $time = strtotime("now");
            if (strtotime($spe1uploadduration->uploadstart) > $time) {
                $this->session->set_flashdata('error', 'Uploading SPE1 Form  starts on ' . date('r', strtotime($spe1uploadduration->uploadstart)));
                redirect(site_url('student/dashboard/'), 'refresh');
            }

            if (strtotime($spe1uploadduration->uploadend) < $time) {
                $this->session->set_flashdata('error', 'Uploading SPE1 Form  Closed on ' . date('r', strtotime($spe1uploadduration->uploadend)));
                redirect(site_url('student/dashboard/'), 'refresh');
            }


            $onestudentdata = $this->registeredstudentmodel->get_where(array('regnumber' => $this->session->userdata('user_name'), 'sessionid' => $activesessionid), true);
            if ($onestudentdata)
                $this->data['student'] = $onestudentdata;

            if ($this->form_validation->run() == true) {
                $now = 'now()';


                $data = $this->registeredstudentmodel->array_from_post(array('placeofattachment', 'employerpayment', 'Addressofattachment', 'employeremail',
                    'employerphonenumber', 'state', 'country', 'locationid', 'session'));

                $logo_name = $this->_handlespeformupload($this->session->userdata('user_name'));
                if ($logo_name != null) {
                    $data['spe1formimg'] = $logo_name;
                }

                //update data if 
                if ($this->data['student'] != '') {

                    $this->db->update('tbl_placement_list', $data, array('regnumber' => $this->session->userdata('user_name'), 'sessionid' => $activesessionid));
                    $this->session->set_flashdata('success', 'Form Upload Update successful');
                    redirect(site_url('student/student/uploadspe'));
                } else {

                    //$placementlistno = 0;
                    $newplacementlistno = 0;
                    $placementno = $this->_getplacementnumber($activesessionid);
                    // print_r($placementno);exit;
                    if ($placementno) {

                        //increment plcementno
                        $newplacementlistno = intval($placementno->placementnumber) + 1;


                        $this->db->set('datecreated', $now, false);
                        $this->db->set('datemodified', $now, false);
                        $insertdata = array(
                            'placementlistid' => $this->registeredstudentmodel->generate_unique_id(),
                            'studentid' => $this->session->userdata("student_id"),
                            'regnumber' => $this->session->userdata('user_name'),
                            'placementlistnumber' => $newplacementlistno,
                            'placeofattachment' => $data['placeofattachment'],
                            'employerpayment' => $data['employerpayment'],
                            'Addressofattachment' => $data['Addressofattachment'],
                            'employeremail' => $data['employeremail'],
                            'employerphonenumber' => $data['employerphonenumber'],
                            'state' => $data['state'],
                            'country' => $data['country'],
                            'locationid' => $data['locationid'],
                            'session' => $data['session'],
                            'sessionid'=> $activesessionid,
                            'spe1formimg' => $data['spe1formimg']
                        );

                        $this->db->insert('tbl_placement_list', $insertdata);
                        $value = $this->db->insert_id();
                    }
                    if ($value != 0) {
                        $this->db->where('sessionid', $activesessionid);
                        $this->db->update('tbl_master_placement_number', array(
                            'placementnumber' => $newplacementlistno));
                        $_SESSION['printpreviewdata'] = $data;
                        $this->session->set_flashdata('success', 'Form Upload successful');
                        redirect(site_url('student/student/uploadspe'));
                    } else {
                        $this->session->set_flashdata('error', 'An Error Occurred During Upload - Kindly retry Operation');
                        redirect(site_url('student/student/uploadspe'));
                    }
                }
            }
        }
        $this->load->view('template/layout_main', $this->data);
    }

    public function addstudent() {
        if ($this->session->userdata("student_isloggedin")) {

            //echo $this->verifystudent;exit;
            $value = 0;
            $this->_basicstudentinitialize();

            $this->data['active'] = "biodata";

            $validationRules = $this->registeredstudentmodel->_rules;

            //get session
            $this->data['sessiondata'] = $this->_getactivatedsession();

            if ($this->data['sessiondata']) {
                foreach ($this->data['sessiondata'] as $row) {
                    //$activesession = $row->sessionname;
                    $activesessionid = $row->sessionid;
                }
            }

            $activationstatus = $this->_checkifaccountisactivated($this->session->userdata('user_name'), $activesessionid);
            if ($activationstatus) {
                
            } else {
                $this->session->set_flashdata('error', 'Account INACTIVE for this SESSSION, Purchase and USE the Scratch card from the SIWES Unit to ACTIVATE your Account ');
                redirect(site_url('student/dashboard'));
            }

            $this->form_validation->set_rules($validationRules);
            //$onestudentdata = '';
            //get faculty
            $this->data['facultydata'] = $this->_getfaculty();
            //get department
            $this->data['departmentdata'] = $this->_getdepartment();

            $query = $this->db->get_where('tbl_student', array('regnumber' => $this->session->userdata('user_name')));
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $this->data['onestudentdata'] = $row;
                }

                //$this->session->set_userdata('verifystudent', true);
                //$this->session->set_userdata('studentinfo', $onestudentdata);
            } //else {
            //   $this->verifystudent = '';
            // }

            if ($this->form_validation->run() == true) {
                //after posting form below code is executed
                //generate the unique student id
                //$uniquestudentid = $this->registeredstudentmodel->generate_unique_id();
                $now = 'now()';
                $data = $this->registeredstudentmodel->array_from_post(array('studentid', 'studentimg', 'regnumber', 'facultyid', 'department',
                    'level', 'lastname', 'firstname', 'middlename', 'gender', 'nationality', 'bankname', 'accountnumber', 'sortcode', 'bloodgroup', 'phonenumber'));


                //if ($this->session->userdata('verifystudent')) {
                //    $uniquestudentid = $data['studentid'];
                // }
                $logo_name = $this->_handlepassportUpload($data['regnumber']);
                if ($logo_name != null) {
                    //echo 'am here';exit;
                    $data['studentimg'] = $logo_name;
                } else {
                    $data['studentimg'] = $data['regnumber'] . '.jpg';
                }
                //if the student already exit then update else insert
                // if ($this->session->userdata('verifystudent')) {
                $value = $this->registeredstudentmodel->save_update(array(
                    'studentimg' => $data['studentimg'],
                    'facultyid' => $data['facultyid'],
                    'department' => $data['department'],
                    'level' => $data['level'],
                    'lastname' => $data['lastname'],
                    'firstname' => $data['firstname'],
                    'middlename' => $data['middlename'],
                    'gender' => $data['gender'],
                    'nationality' => $data['nationality'],
                    'bankname' => $data['bankname'],
                    'accountnumber' => $data['accountnumber'],
                    'sortcode' => $data['sortcode'],
                    'bloodgroup' => $data['bloodgroup'],
                    'phonenumber' => $data['phonenumber']), $data['regnumber']);
                //$value = $this->studentmodel->save_update($data, $data['studentid']);
                // $this->session->set_userdata('verifystudent', false);
                //$this->session->unset_userdata('studentinfo');
                //} 
                /*                 * *
                  else {

                  $query = $this->db->get_where('tbl_student', array('regnumber' => $data['regnumber']));
                  if ($query->num_rows() > 0) {
                  $this->session->set_flashdata('error', 'A Student with'. $data['regnumber'].' Already exits, Please Check the Reg number and Retry');
                  redirect(site_url('users/student'));
                  }
                  $this->db->set('datecreated', $now, false);
                  $this->db->set('datemodified', $now, false);
                  $insertdata = array(
                  'studentid' => $uniquestudentid,
                  'studentimg' => $data['studentimg'],
                  'regnumber' => $data['regnumber'],
                  'password' => md5($data['regnumber']),
                  'facultyid' => $data['facultyid'],
                  'department' => $data['department'],
                  'level' => $data['level'],
                  'lastname' => $data['lastname'],
                  'firstname' => $data['firstname'],
                  'middlename' => $data['middlename'],
                  'gender' => $data['gender'],
                  'nationality' => $data['nationality'],
                  'bankname' => $data['bankname'],
                  'accountnumber' => $data['accountnumber'],
                  'sortcode' => $data['sortcode'],
                  'bloodgroup' => $data['bloodgroup'],
                  'phonenumber' => $data['phonenumber']
                  );

                  $this->db->insert('tbl_student', $insertdata);
                  $value = $this->db->insert_id();

                  //insert into the masterlist table

                  $this->db->set('datecreated', $now, false);
                  $this->db->set('datemodified', $now, false);
                  $insertdata = array(
                  'masterlistid' => $this->studentmodel->generate_unique_id(),
                  'masterlistnumber' => '',
                  'studentid' => $uniquestudentid,
                  'regnumber' => $data['regnumber'],
                  'session' => '',
                  'startmonth' => '',
                  'endmonth' => '',
                  'attachmentyear' => '',
                  'remark' => '',
                  'mobilizationstatus' => 'not mobilized'
                  );

                  $this->db->insert('tbl_master_list', $insertdata);
                  $value = $this->db->insert_id();
                  }
                 * * */
                if ($value != 0) {
                    $this->session->set_flashdata('success', 'Changes successful');
                    redirect(site_url('student/student/addstudent/'));
                    $_SESSION['printpreviewdata'] = $data;
                    //$this->data['subview'] = 'student/preview_page';
                    //$this->data['previewdata'] = $data;
                } else {
                    $this->session->set_flashdata('error', 'An Error Occurred During Update - Kindly retry Operation');
                    redirect(site_url('student/student/addstudent/'));
                }
                //reset sessionid
                $this->data['studentid'] = '';
            }

            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function previewbiodata() {
        if ($this->session->userdata("student_isloggedin")) {

            //echo $this->verifystudent;exit;
            $value = 0;
            $this->_basicbiodatapreviewinitialize();

            $query = $this->db->get_where('tbl_student', array('regnumber' => $this->session->userdata('user_name')));
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $this->data['previewdata'] = $row;
                }

                //$this->session->set_userdata('verifystudent', true);
                //$this->session->set_userdata('studentinfo', $onestudentdata);
            } //else {
            //   $this->verifystudent = '';
            // }

            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function previewspe() {
        if ($this->session->userdata("student_isloggedin")) {

            //echo $this->verifystudent;exit;
            $value = 0;
            $this->_basicspepreviewinitialize();

            $this->data['sessiondata'] = $this->_getactivatedsession();

            $this->data['location'] = $this->_getlocation();

            if ($this->data['sessiondata']) {
                foreach ($this->data['sessiondata'] as $row) {
                    //  $activesession = $row->sessionname;
                    $activesessionid = $row->sessionid;
                }
            }

            $this->data['previewdata'] = $this->_getplacementdata($this->session->userdata('user_name'), $activesessionid);


            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function _basicbiodatapreviewinitialize() {

        $this->data['subview'] = 'student/preview_studentbiodata_page';
        $this->data['pageheading'] = 'Print Preview Form';
        $this->data['studentid'] = '';


        //page level scripts
        //$this->data['page_level_scripts'] = '<script src="' . base_url('resources/assets/js/bootstrap-fileupload.js') . '"></script>';
        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/css/bootstrap-fileupload.min.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        //$this->registeredstudentmodel->addstudentmodel('tbl_student', 'regnumber', 'id DESC');
    }

    public function _basicspepreviewinitialize() {

        $this->data['subview'] = 'student/preview_studentspe_page';
        $this->data['pageheading'] = 'Print Preview Form';
        $this->data['studentid'] = '';


        //page level scripts
        //$this->data['page_level_scripts'] = '<script src="' . base_url('resources/assets/js/bootstrap-fileupload.js') . '"></script>';
        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/css/bootstrap-fileupload.min.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->registeredstudentmodel->uploadspemodel('tbl_placement_list', 'regnumber', 'id DESC');
    }

    public function _basicstudentinitialize() {

        $this->data['subview'] = 'student/create_student_page';
        $this->data['pageheading'] = 'Enrol Student';
        $this->data['studentid'] = '';


        //page level scripts
        $this->data['page_level_scripts'] = '<script src="' . base_url('resources/assets/js/bootstrap-fileupload.js') . '"></script>';
        $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/css/bootstrap-fileupload.min.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->registeredstudentmodel->addstudentmodel('tbl_student', 'regnumber', 'id DESC');
    }

    public function _getactivatedsession() {
        $sessiondata = 0;
        $query = $this->db->get_where('tbl_session', array('sessionstatus' => 'activated'));
        if ($query->num_rows() > 0)
            $sessiondata = $query->result();

        return $sessiondata;
    }

    public function _getlocation() {
        $location = 0;
        $query = $this->db->get('tbl_location');
        if ($query->num_rows() > 0)
            $location = $query->result();

        return $location;
    }

    public function _checkifstudentismobilised($regnumber, $activesessionid) {
        $studentdata = 0;
        $query = $this->db->get_where('tbl_master_list', array('regnumber' => $regnumber, 'sessionid' => $activesessionid, 'mobilizationstatus' => 'mobilized'));
        if ($query->num_rows() > 0)
            $studentdata = $query->result();

        return $studentdata;
    }

    public function _checkifaccountisactivated($regnumber, $activesessionid) {
        $studentdata = 0;
        $query = $this->db->get_where('tbl_pins_serials', array('regnumber' => $regnumber, 'sessionid' => $activesessionid));
        if ($query->num_rows() > 0)
            $studentdata = $query->result();

        return $studentdata;
    }

    public function _verifypinserial($pin, $serial) {
        $studentdata = 0;
        $this->db->select();
        $this->db->where('pin =', $pin);
        $this->db->where('serial =', $serial);
        $query = $this->db->get('tbl_pins_serials');
        if ($query->num_rows() > 0)
            $studentdata = $query->result();

        return $studentdata;
    }

    public function _verifypinserialstatus($pin, $serial) {
        $studentdata = 0;
        $this->db->select();
        $this->db->where('pin =', $pin);
        $this->db->where('serial =', $serial);
        $this->db->where('status =', '0');
        $this->db->where('regnumber =', '0');
        $query = $this->db->get('tbl_pins_serials');
        if ($query->num_rows() > 0)
            $studentdata = $query->result();

        return $studentdata;
    }

    public function _getplacementnumber($session) {
        $placement = 0;
        $query = $this->db->get_where('tbl_master_placement_number', array('sessionid' => $session));
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $placement = $row;
            }
        }
        return $placement;
    }

    public function _getspe1uploadduration() {
        $data = '';
        $this->db->select();
        //$this->db->where('schoolid =', $schoolid);
        $query = $this->db->get('tbl_spe1_upload_duration');

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {

                $data = $row;
            }
        }

        return $data;
    }

    public function _handlespeformupload($filename) {
        if (!empty($_FILES['spe1formimg']['name'])) {

            $config = array(
                'upload_path' => './resources/speform/',
                'allowed_types' => 'jpg|png|jpeg|gif',
                'max_size' => '1500',
                'overwrite' => TRUE,
                'file_name' => $filename . '.jpg',
                'remove_spaces' => TRUE
            );
            $this->load->library('upload', $config, 'logo_object');

            if (!$this->logo_object->do_upload('spe1formimg')) {
                $this->session->set_flashdata('error', $this->logo_object->display_errors());
                redirect(site_url('student/student/changepassword'));
            } else {
                $upload_data = $this->logo_object->data();
                return $upload_data['file_name'];
            }
        } else {
            //echo 'am null';exit;
            return null;
        }
    }

    public function _basicpasswordinitialize() {

        $this->data['subview'] = 'student/changepassword_page';
        $this->data['pageheading'] = 'Change Password';
        $this->data['regnumber'] = '';

        //page level scripts
        //
        //initialize the sessionmodel with database parameter
        $this->registeredstudentmodel->changepasswordmodel('tbl_student', 'regnumber', 'id DESC');
    }

    public function _basicaccountinitialize() {

        $this->data['subview'] = 'student/activate_account_page';
        $this->data['pageheading'] = 'Activate Account';
        $this->data['regnumber'] = '';

        //page level scripts
        //
        //initialize the sessionmodel with database parameter
        $this->registeredstudentmodel->activateaccountmodel('tbl_pins_serials', 'pin', 'id DESC');
    }

    public function _basicuploadspeinitialize() {

        $this->data['subview'] = 'student/uploadspe_page';
        $this->data['pageheading'] = 'Upload SPE1 Form';
        $this->data['student'] = '';


        //page level scripts
        $this->data['page_level_scripts'] = '<script src="' . base_url('resources/assets/js/bootstrap-fileupload.js') . '"></script>';
        $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/css/bootstrap-fileupload.min.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->registeredstudentmodel->uploadspemodel('tbl_placement_list', 'regnumber', 'id DESC');
    }

    public function _getfaculty() {
        $facultydata = 0;
        $query = $this->db->get('tbl_faculty');
        if ($query->num_rows() > 0)
            $facultydata = $query->result();

        return $facultydata;
    }

    public function _getdepartment() {
        $departmentdata = 0;
        $query = $this->db->get('tbl_department');
        if ($query->num_rows() > 0)
            $departmentdata = $query->result();

        return $departmentdata;
    }

    function _getplacementdata($regnumber, $sessionid) {
        $data = '';
        $this->db->select();
        $this->db->from('tbl_student');
        $this->db->join('tbl_placement_list', 'tbl_student.regnumber = tbl_placement_list.regnumber', 'inner');
        $this->db->join('tbl_faculty', 'tbl_student.facultyid = tbl_faculty.facultyid', 'inner');
        $this->db->join('tbl_session', 'tbl_placement_list.sessionid = tbl_session.sessionid', 'inner');
//$this->db->where('tbl_student.department', $innerrow->departmentname);
        $this->db->where('tbl_placement_list.sessionid', $sessionid);
        $this->db->where('tbl_placement_list.regnumber', $regnumber);
//$this->db->order_by('tbl_student.regnumber asc');
        $query = $this->db->get();
//echo $this->db->last_query();exit;
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $data = $row;
                //$index = $index + 1;
            }
        }

        return $data;
    }

    public function _handlepassportUpload($filename) {
        if (!empty($_FILES['studentimg']['name'])) {

            $config = array(
                'upload_path' => './resources/studentimg/',
                'allowed_types' => 'jpg|png|jpeg|gif',
                'max_size' => '1500',
                'overwrite' => TRUE,
                'file_name' => $filename . '.jpg',
                'remove_spaces' => TRUE
            );
            $this->load->library('upload', $config, 'logo_object');

            if (!$this->logo_object->do_upload('studentimg')) {
                $this->session->set_flashdata('error', $this->logo_object->display_errors());
                redirect(site_url('users/student/addstudent/'));
            } else {
                $upload_data = $this->logo_object->data();
                return $upload_data['file_name'];
            }
        } else {
            //echo 'am null';exit;
            return null;
        }
    }

    public function _getcountries() {
        $countries = array(
            "AF" => "Afghanistan",
            "AL" => "Albania",
            "DZ" => "Algeria",
            "AS" => "American Samoa",
            "AD" => "Andorra",
            "AO" => "Angola",
            "AI" => "Anguilla",
            "AQ" => "Antarctica",
            "AG" => "Antigua And Barbuda",
            "AR" => "Argentina",
            "AM" => "Armenia",
            "AW" => "Aruba",
            "AU" => "Australia",
            "AT" => "Austria",
            "AZ" => "Azerbaijan",
            "BS" => "Bahamas",
            "BH" => "Bahrain",
            "BD" => "Bangladesh",
            "BB" => "Barbados",
            "BY" => "Belarus",
            "BE" => "Belgium",
            "BZ" => "Belize",
            "BJ" => "Benin",
            "BM" => "Bermuda",
            "BT" => "Bhutan",
            "BO" => "Bolivia",
            "BA" => "Bosnia And Herzegowina",
            "BW" => "Botswana",
            "BV" => "Bouvet Island",
            "BR" => "Brazil",
            "IO" => "British Indian Ocean Territory",
            "BN" => "Brunei Darussalam",
            "BG" => "Bulgaria",
            "BF" => "Burkina Faso",
            "BI" => "Burundi",
            "KH" => "Cambodia",
            "CM" => "Cameroon",
            "CA" => "Canada",
            "CV" => "Cape Verde",
            "KY" => "Cayman Islands",
            "CF" => "Central African Republic",
            "TD" => "Chad",
            "CL" => "Chile",
            "CN" => "China",
            "CX" => "Christmas Island",
            "CC" => "Cocos (Keeling) Islands",
            "CO" => "Colombia",
            "KM" => "Comoros",
            "CG" => "Congo",
            "CD" => "Congo, The Democratic Republic Of The",
            "CK" => "Cook Islands",
            "CR" => "Costa Rica",
            "CI" => "Cote D'Ivoire",
            "HR" => "Croatia (Local Name: Hrvatska)",
            "CU" => "Cuba",
            "CY" => "Cyprus",
            "CZ" => "Czech Republic",
            "DK" => "Denmark",
            "DJ" => "Djibouti",
            "DM" => "Dominica",
            "DO" => "Dominican Republic",
            "TP" => "East Timor",
            "EC" => "Ecuador",
            "EG" => "Egypt",
            "SV" => "El Salvador",
            "GQ" => "Equatorial Guinea",
            "ER" => "Eritrea",
            "EE" => "Estonia",
            "ET" => "Ethiopia",
            "FK" => "Falkland Islands (Malvinas)",
            "FO" => "Faroe Islands",
            "FJ" => "Fiji",
            "FI" => "Finland",
            "FR" => "France",
            "FX" => "France, Metropolitan",
            "GF" => "French Guiana",
            "PF" => "French Polynesia",
            "TF" => "French Southern Territories",
            "GA" => "Gabon",
            "GM" => "Gambia",
            "GE" => "Georgia",
            "DE" => "Germany",
            "GH" => "Ghana",
            "GI" => "Gibraltar",
            "GR" => "Greece",
            "GL" => "Greenland",
            "GD" => "Grenada",
            "GP" => "Guadeloupe",
            "GU" => "Guam",
            "GT" => "Guatemala",
            "GN" => "Guinea",
            "GW" => "Guinea-Bissau",
            "GY" => "Guyana",
            "HT" => "Haiti",
            "HM" => "Heard And Mc Donald Islands",
            "VA" => "Holy See (Vatican City State)",
            "HN" => "Honduras",
            "HK" => "Hong Kong",
            "HU" => "Hungary",
            "IS" => "Iceland",
            "IN" => "India",
            "ID" => "Indonesia",
            "IR" => "Iran (Islamic Republic Of)",
            "IQ" => "Iraq",
            "IE" => "Ireland",
            "IL" => "Israel",
            "IT" => "Italy",
            "JM" => "Jamaica",
            "JP" => "Japan",
            "JO" => "Jordan",
            "KZ" => "Kazakhstan",
            "KE" => "Kenya",
            "KI" => "Kiribati",
            "KP" => "Korea, Democratic People's Republic Of",
            "KR" => "Korea, Republic Of",
            "KW" => "Kuwait",
            "KG" => "Kyrgyzstan",
            "LA" => "Lao People's Democratic Republic",
            "LV" => "Latvia",
            "LB" => "Lebanon",
            "LS" => "Lesotho",
            "LR" => "Liberia",
            "LY" => "Libyan Arab Jamahiriya",
            "LI" => "Liechtenstein",
            "LT" => "Lithuania",
            "LU" => "Luxembourg",
            "MO" => "Macau",
            "MK" => "Macedonia, Former Yugoslav Republic Of",
            "MG" => "Madagascar",
            "MW" => "Malawi",
            "MY" => "Malaysia",
            "MV" => "Maldives",
            "ML" => "Mali",
            "MT" => "Malta",
            "MH" => "Marshall Islands",
            "MQ" => "Martinique",
            "MR" => "Mauritania",
            "MU" => "Mauritius",
            "YT" => "Mayotte",
            "MX" => "Mexico",
            "FM" => "Micronesia, Federated States Of",
            "MD" => "Moldova, Republic Of",
            "MC" => "Monaco",
            "MN" => "Mongolia",
            "MS" => "Montserrat",
            "MA" => "Morocco",
            "MZ" => "Mozambique",
            "MM" => "Myanmar",
            "NA" => "Namibia",
            "NR" => "Nauru",
            "NP" => "Nepal",
            "NL" => "Netherlands",
            "AN" => "Netherlands Antilles",
            "NC" => "New Caledonia",
            "NZ" => "New Zealand",
            "NI" => "Nicaragua",
            "NE" => "Niger",
            "NG" => "Nigerian",
            "NU" => "Niue",
            "NF" => "Norfolk Island",
            "MP" => "Northern Mariana Islands",
            "NO" => "Norway",
            "OM" => "Oman",
            "PK" => "Pakistan",
            "PW" => "Palau",
            "PA" => "Panama",
            "PG" => "Papua New Guinea",
            "PY" => "Paraguay",
            "PE" => "Peru",
            "PH" => "Philippines",
            "PN" => "Pitcairn",
            "PL" => "Poland",
            "PT" => "Portugal",
            "PR" => "Puerto Rico",
            "QA" => "Qatar",
            "RE" => "Reunion",
            "RO" => "Romania",
            "RU" => "Russian Federation",
            "RW" => "Rwanda",
            "KN" => "Saint Kitts And Nevis",
            "LC" => "Saint Lucia",
            "VC" => "Saint Vincent And The Grenadines",
            "WS" => "Samoa",
            "SM" => "San Marino",
            "ST" => "Sao Tome And Principe",
            "SA" => "Saudi Arabia",
            "SN" => "Senegal",
            "SC" => "Seychelles",
            "SL" => "Sierra Leone",
            "SG" => "Singapore",
            "SK" => "Slovakia (Slovak Republic)",
            "SI" => "Slovenia",
            "SB" => "Solomon Islands",
            "SO" => "Somalia",
            "ZA" => "South Africa",
            "GS" => "South Georgia, South Sandwich Islands",
            "ES" => "Spain",
            "LK" => "Sri Lanka",
            "SH" => "St. Helena",
            "PM" => "St. Pierre And Miquelon",
            "SD" => "Sudan",
            "SR" => "Suriname",
            "SJ" => "Svalbard And Jan Mayen Islands",
            "SZ" => "Swaziland",
            "SE" => "Sweden",
            "CH" => "Switzerland",
            "SY" => "Syrian Arab Republic",
            "TW" => "Taiwan",
            "TJ" => "Tajikistan",
            "TZ" => "Tanzania, United Republic Of",
            "TH" => "Thailand",
            "TG" => "Togo",
            "TK" => "Tokelau",
            "TO" => "Tonga",
            "TT" => "Trinidad And Tobago",
            "TN" => "Tunisia",
            "TR" => "Turkey",
            "TM" => "Turkmenistan",
            "TC" => "Turks And Caicos Islands",
            "TV" => "Tuvalu",
            "UG" => "Uganda",
            "UA" => "Ukraine",
            "AE" => "United Arab Emirates",
            "UK" => "United Kingdom",
            "US" => "United States",
            "UM" => "United States Minor Outlying Islands",
            "UY" => "Uruguay",
            "UZ" => "Uzbekistan",
            "VU" => "Vanuatu",
            "VE" => "Venezuela",
            "VN" => "Viet Nam",
            "VG" => "Virgin Islands (British)",
            "VI" => "Virgin Islands (U.S.)",
            "WF" => "Wallis And Futuna Islands",
            "EH" => "Western Sahara",
            "YE" => "Yemen",
            "YU" => "Yugoslavia",
            "ZM" => "Zambia",
            "ZW" => "Zimbabwe");

        return $countries;
    }

    public function _getstates() {
        $states = array(
            '1' => 'Abia',
            '2' => 'Adamawa',
            '3' => 'Akwa Ibom',
            '4' => 'Anambra',
            '5' => 'Bauchi',
            '6' => 'Bayelsa',
            '7' => 'Benue',
            '8' => 'Borno',
            '9' => 'Cross River',
            '10' => 'Delta',
            '11' => 'Ebonyi',
            '12' => 'Edo',
            '13' => 'Ekiti',
            '14' => 'Enugu',
            '15' => 'FCT',
            '16' => 'Gombe',
            '17' => 'Imo',
            '18' => 'Jigawa',
            '19' => 'Kaduna',
            '20' => 'Kano',
            '21' => 'Katsina',
            '22' => 'Kebbi',
            '23' => 'Kogi',
            '24' => 'Kwara',
            '25' => 'Lagos',
            '26' => 'Nassarawa',
            '27' => 'Niger',
            '28' => 'Ogun',
            '29' => 'Ondo',
            '30' => 'Osun',
            '31' => 'Oyo',
            '32' => 'Plateau',
            '33' => 'Rivers',
            '34' => 'Sokoto',
            '35' => 'Taraba',
            '36' => 'Yobe',
            '37' => 'Zamfara'
        );
        return $states;
    }

}

?>
