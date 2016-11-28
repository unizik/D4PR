<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of student
 *
 * @author TOCHUKWU
 */
class student extends User_Controller {

    //put your code here
    public $level = '';
    public $countries = '';
    public $verifystudent = '';

    function __construct() {
        parent::__construct();
        $this->load->model('studentmodel');
        $this->level = array('100' => '100', '200' => '200', '300' => '300', '400' => '400', '500' => '500', '600' => '600');
         $this->gender = array('Male' => 'Male', 'Female' => 'Female');
        $this->countries = $this->_getcountries();
//  $this->_set_nav_menus();
    }


    public function updatepassport() {
        //initialize the verifystudent session
        if ($this->session->userdata("user_isloggedin")) {
        $this->_basicstudentinitialize();
            $query = $this->db->get('tbl_student');
            if( $query->num_rows() > 0){
                foreach($query->result() as $row){
$value = $this->studentmodel->save_update(array(
                                'studentimg' => $row->regnumber.'.jpg'),
                                    $row->studentid);
}
            }
            $this->data['subview'] = 'users/verify_student_page';
            $this->data['pageheading'] = 'Enrol Student';


            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }
    public function index() {
        //initialize the verifystudent session
        $this->session->set_userdata('verifystudent', false);
        $this->session->set_userdata('studentinfo', '');
        if ($this->session->userdata("user_isloggedin")) {

            $this->data['subview'] = 'users/verify_student_page';
            $this->data['pageheading'] = 'Enrol Student';


            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function verifystudent() {
        if ($this->session->userdata("user_isloggedin")) {
            $this->_basicverifyinitialize();

            $validationRules = $this->studentmodel->_rules;

            $this->form_validation->set_rules($validationRules);

            if ($this->form_validation->run() == true) {
                //get faculty
                $this->data['facultydata'] = $this->_getfaculty();
                //get department
                $this->data['departmentdata'] = $this->_getdepartment();
                $this->data['subview'] = 'users/create_student_page';
                $data = $this->studentmodel->array_from_post(array('regnumber'));
                //get list of created student
                $query = $this->db->get_where('tbl_student', array('regnumber' => $data['regnumber']));
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $row) {
                        $onestudentdata = $row;
                    }

                    $this->session->set_userdata('verifystudent', true);
                    $this->session->set_userdata('studentinfo', $onestudentdata);
                } else {
                    $this->verifystudent = '';
                }
            }

            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    /**
     * this is a default method
     * this loads the student page
     * it passes the subview page to the layout_main page
     */
    public function addstudent() {
        if ($this->session->userdata("user_isloggedin")) {

            //echo $this->verifystudent;exit;
            $value = 0;
            $this->_basicstudentinitialize();

            $validationRules = $this->studentmodel->_rules;
            $this->data['active'] = "enroll";
            $this->form_validation->set_rules($validationRules);

            //get faculty
            $this->data['facultydata'] = $this->_getfaculty();
            //get department
            $this->data['departmentdata'] = $this->_getdepartment();
            //get list of created student
            // $query = $this->db->get('tbl_student', 300);
            //$student_data = $query->result();
            //save it in this data so that it can be displayed in the table
            // $this->data['createdstudent'] = $student_data;
            if ($this->form_validation->run() == true) {
                //after posting form below code is executed
                //generate the unique student id
                $uniquestudentid = $this->studentmodel->generate_unique_id();
                $now = 'now()';
                $data = $this->studentmodel->array_from_post(array('studentid', 'studentimg', 'regnumber', 'facultyid', 'department',
                            'level', 'lastname', 'firstname', 'middlename','gender', 'nationality', 'bankname', 'accountnumber', 'sortcode', 'bloodgroup', 'phonenumber'));


                if ($this->session->userdata('verifystudent')) {
                    $uniquestudentid = $data['studentid'];
                }
                $logo_name = $this->_handlepassportUpload($data['regnumber']);
                if ($logo_name != null) {
                    //echo 'am here';exit;
                    $data['studentimg'] = $logo_name;
                } else {
                    $data['studentimg'] = $data['regnumber'] . '.jpg';
                }
                //if the student already exit then update else insert
                if ($this->session->userdata('verifystudent')) {
                    $value = $this->studentmodel->save_update(array(
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
                                'phonenumber' => $data['phonenumber']),
                                    $data['regnumber']);
                    //$value = $this->studentmodel->save_update($data, $data['studentid']);
                    $this->session->set_userdata('verifystudent', false);
                    //$this->session->unset_userdata('studentinfo');
                } else {

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
                if ($value != 0) {
                    $this->session->set_flashdata('success', 'Changes successful');
                    redirect(site_url('users/student/verifystudent/'));
                } else {
                    $this->session->set_flashdata('error', 'An Error Occurred During Update - Kindly retry Operation');
                    redirect(site_url('users/student/verifystudent/'));
                }
                //reset sessionid
                $this->data['studentid'] = '';
            }

            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function viewstudent() {
        if ($this->session->userdata("user_isloggedin")) {

            $this->data['subview'] = 'users/view_student_page';
            $this->data['pageheading'] = 'Enroled Student';
$this->data['active'] = "enroll";
            //   $this->data['page_level_scripts'] = '<script src="' . base_url('resources/assets/js/bootstrap-fileupload.js') . '"></script>';
            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

            //get faculty
            $this->data['facultydata'] = $this->_getfaculty();
            //get department
            $this->data['departmentdata'] = $this->_getdepartment();
            //get list of created student
            $query = $this->db->get('tbl_student', 300);
            $student_data = $query->result();

            //save it in this data so that it can be displayed in the table
            $this->data['createdstudent'] = $student_data;

            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function searchstudent() {
        if ($this->session->userdata("user_isloggedin")) {
            $searchparameters = array();
            $this->data['subview'] = 'users/view_student_page';
            $this->data['pageheading'] = 'Enroled Student';

            //   $this->data['page_level_scripts'] = '<script src="' . base_url('resources/assets/js/bootstrap-fileupload.js') . '"></script>';
            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
$this->data['active'] = "enroll";
            //get faculty
            $this->data['facultydata'] = $this->_getfaculty();
            //get department
            $this->data['departmentdata'] = $this->_getdepartment();

            $data = $this->studentmodel->array_from_post(array('facultyid', 'department', 'bankname'));

            if ($data['facultyid'] != '') {
                $searchparameters['facultyid'] = $data['facultyid'];
            }
            if ($data['department'] != '') {
                $searchparameters['department'] = $data['department'];
            }
            if ($data['bankname'] != '') {
                $searchparameters['bankname'] = $data['bankname'];
            }
            //get list of created student
            $query = $this->db->get_where('tbl_student', $searchparameters);
            $student_data = $query->result();

            //save it in this data so that it can be displayed in the table
            $this->data['createdstudent'] = $student_data;

            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    /**
     *
     * @param <type> $studentid
     * this method updates session based on id
     */
    public function updatestudent($regnumber = null) {
        if ($this->session->userdata("user_isloggedin")) {

            if ($regnumber != null) {
                $this-> _basicupdatestudentinitialize();
                $this->data['regnumber'] = $regnumber;
$this->data['active'] = "enroll";
                $validationRules = $this->studentmodel->_rules;

            $this->form_validation->set_rules($validationRules);
                
                //get faculty
                $this->data['facultydata'] = $this->_getfaculty();
                //get department
                $this->data['departmentdata'] = $this->_getdepartment();


                //get the selected faculty based on the facultyid
                $query = $this->db->get_where('tbl_student', array('regnumber' => $regnumber));
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $row) {
                        $onestudentdata = $row;
                    }
                }

                // $valid_user = $this->termmodel->get_all();
                $this->data['student'] = $onestudentdata;

                if ($this->form_validation->run() == true) {
                //after posting form below code is executed
                //generate the unique student id
                //$uniquestudentid = $this->studentmodel->generate_unique_id();
                $now = 'now()';
                //$postdata = $this->input->post();
                $data = $this->studentmodel->array_from_post(array( 'facultyid', 'department',
                            'level', 'lastname', 'firstname', 'middlename', 'nationality', 'bankname', 'accountnumber', 'sortcode', 'bloodgroup', 'phonenumber'));


                    $uniquestudentid = $regnumber;

                $logo_name = $this->_handlepassportUpload($this->data['student']->regnumber);
                if ($logo_name != null) {
                    //echo 'am here';exit;
                    $data['studentimg'] = $logo_name;
                }
                //if the student already exit then update else insert

                    $value = $this->studentmodel->save_update($data,$regnumber);

                    if ($value != 0) {
                    $this->session->set_flashdata('success', 'Update successful');
                    redirect(site_url('users/student/viewstudent/'));
                } else {
                    $this->session->set_flashdata('error', 'An Error Occurred During Update - Kindly retry Operation');
                    redirect(site_url('users/student/viewstudent/'));
                }
                }
                $this->load->view('template/layout_main', $this->data);
            } else {
                redirect(site_url('users/student/viewstudent/'));
            }
        } else {
            redirect(site_url('login'));
        }
    }

    public function _basicverifyinitialize() {

        $this->data['subview'] = 'users/verify_student_page';
        $this->data['pageheading'] = 'Enrol Student';
        // $this->verifystudent = '';
        $this->data['studentid'] = '';

        //page level scripts
        $this->data['page_level_scripts'] = '<script src="' . base_url('resources/assets/js/bootstrap-fileupload.js') . '"></script>';
        $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/css/bootstrap-fileupload.min.css') . '" rel="stylesheet">';

        //initialize the sessionmodel with database parameter
        $this->studentmodel->verifystudentmodel('tbl_student', 'regnumber', 'id DESC');
    }

    public function _basicstudentinitialize() {

        $this->data['subview'] = 'users/create_student_page';
        $this->data['pageheading'] = 'Enrol Student';
        $this->data['studentid'] = '';


        //page level scripts
        $this->data['page_level_scripts'] = '<script src="' . base_url('resources/assets/js/bootstrap-fileupload.js') . '"></script>';
        $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/css/bootstrap-fileupload.min.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->studentmodel->addstudentmodel('tbl_student', 'regnumber', 'id DESC');
    }

    public function _basicupdatestudentinitialize() {

       $this->data['subview'] = 'users/update_student_page';
                $this->data['pageheading'] = 'Update Student';
                //page level scripts
                $this->data['page_level_scripts'] = '<script src="' . base_url('resources/assets/js/bootstrap-fileupload.js') . '"></script>';
                $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/css/bootstrap-fileupload.min.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->studentmodel->updatestudentmodel('tbl_student', 'regnumber', 'id DESC');
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
        $ARR_COUNTRIES = array(
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

        return $ARR_COUNTRIES;
    }

}

?>
