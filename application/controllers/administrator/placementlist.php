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
$CI = & get_instance();

class Placementlist extends Admin_Controller {

    //put your code here
    public $sessionstatus = '';

    function __construct() {
        parent::__construct();
        $this->load->model('placementlistmodel');
        //$this->load->library('Excel/ExcelReader');
        $this->load->library('excel');
        session_start();

        $this->states = $this->_getstates();
//print_r($this->ExcelReader);
        $this->receipent = array('all' => 'All Students', 'mobilized' => 'Mobilized Students', 'placed' => 'Students With Placement', 'unplaced' => 'Students Without Placement');
        //  $this->_set_nav_menus();
    }

    public function index() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicuploadmasterlistinitialize();
            $this->session->set_userdata('uploadedfile', '');
            $this->data['active'] = "placementlist";
            //get faculty
            //$this->data['facultydata'] = $this->_getfaculty();
            //get department
            //$this->data['departmentdata'] = $this->_getdepartment();
            //$this->data['sessiondata'] = $this->_getactivatedsession();
            // $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }


    public function sendsms() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicsendsmsinitialize();


            $this->data['active'] = "send sms";


            // $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
            $this->data['sessiondata'] = $this->_getactivatedsession();
            $validationRules = $this->placementlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                //get post data
                $data = $this->input->post();
                $contacts = 0;
               // print_r($data);
                if ($data['receipient'] == 'all') {
                    $contacts = $this->_getallstudentcontact($data['session']);
                    
                } else if ($data['receipient'] == 'mobilized') {
                    $contacts = $this->_getmobilizedstudentcontact($data['session']);
                    
                } else if ($data['receipient'] == 'placed') {
                    $contacts = $this->_getplacedstudentcontact($data['session']);
                    
                } else if ($data['receipient'] == 'unplaced') {
                    $contacts = $this->_getunplacedstudentlist($data['session']);
                    
                }

                if (!$contacts) {
                    $this->session->set_flashdata('error', 'No Contact Exist');
                    redirect(site_url('administrator/placementlist/sendsms'));
                } else {

                    $sms_msg = "";
                    $owneremail = "td.eze@unizik.edu.ng";
                    $subacct = "COOUSIWES";
                    $subacctpwd = "mrnicholas";
                    $sender = 'SIWES';

                    $numbers = "";
                    $totalcontact = count($contacts);

                    if ($totalcontact <= 100) {
                        for ($a = 0; $a < $totalcontact; $a++) {
                            
                            if ($contacts[$a]->phonenumber != "" && strlen($contacts[$a]->phonenumber) == 11) {

                                //ceheck if its the last contact and dont add comma
                                if ($a == ($totalcontact - 1)) {
                                    $numbers = $numbers . '234' . substr($contacts[$a]->phonenumber, 1, 10);
                                } else {
                                    $numbers = $numbers . '234' . substr($contacts[$a]->phonenumber, 1, 10) . ',';
                                }
                            }
                        }
                        
                        $url = "http://www.smslive247.com/http/index.aspx?"
                                . "cmd=sendquickmsg"
                                . "&owneremail=" . UrlEncode($owneremail)
                                . "&subacct=" . UrlEncode($subacct)
                                . "&subacctpwd=" . UrlEncode($subacctpwd)
                                . "&message=" . UrlEncode($data['message'])
                                . "&sender=" . UrlEncode($sender)
                                . "&sendto=" . UrlEncode($numbers)
                                . "&msgtype=" . UrlEncode(0);
                        //echo $url;exit;
                        if ($f = @fopen($url, "r")) {
                            $answer = fgets($f, 255);
                            echo $answer;
                            if (substr($answer, 0, 1) == "+") {
                                $fatalerror = 0;
                                $sms_msg = $sms_msg . " SMS Was not Sent Successfully to " . $totalcontact . ' Contacts';
                                // echo $sms_msg;
                            } else {
                                $fatalerror = 0;
                                // $nomsgsent = $nomsgsent + 1;
                                $sms_msg = "SMS  was Sent successfully to " . $totalcontact . ' Contacts';
                                //echo $sms_msg;
                            }
                        } else {

                            $fatalerror = 1;

                            $sms_msg = "Error: URL could not be opened.";
                        }
                    } else {
                        $numof100s = intval($totalcontact / 100);
                        $remainder = $totalcontact % 100;

                        for ($b = 0; $b < $numof100s; $b++) {

                            for ($c = (100 * (intval($b))); $c < (100 * (intval($b) + 1)); $c++) {
                                if ($contacts[$c]->phonenumber != "" && strlen($contacts[$c]->phonenumber) == 11) {

                                    //ceheck if its the last contact in this batch of 100 and dont add comma
                                    if ($c == ((100 * (intval($b) + 1)) - 1)) {
                                        $numbers = $numbers . '234' . substr($contacts[$c]->phonenumber, 1, 10);
                                    } else {
                                        $numbers = $numbers . '234' . substr($contacts[$c]->phonenumber, 1, 10) . ',';
                                    }
                                }
                               
                            }
                            
                             //
                            /***
                                $url = "http://www.smslive247.com/http/index.aspx?"
                                        . "cmd=sendquickmsg"
                                        . "&owneremail=" . UrlEncode($owneremail)
                                        . "&subacct=" . UrlEncode($subacct)
                                        . "&subacctpwd=" . UrlEncode($subacctpwd)
                                        . "&message=" . UrlEncode($data['message'])
                                        . "&sender=" . UrlEncode($sender)
                                        . "&sendto=" . UrlEncode($numbers)
                                        . "&msgtype=" . UrlEncode(0);

                                if ($f = @fopen($url, "r")) {
                                    $answer = fgets($f, 255);
                                    echo $answer;
                                    if (substr($answer, 0, 1) == "+") {
                                        $fatalerror = 0;
                                        $sms_msg = $sms_msg . " SMS Was not Sent Successfully to 100 Contacts";
                                        // echo $sms_msg;
                                    } else {
                                        $fatalerror = 0;
                                        // $nomsgsent = $nomsgsent + 1;
                                        $sms_msg = "SMS  was Sent successfully to 100 Contacts";
                                        //echo $sms_msg;
                                    }
                                } else {

                                    $fatalerror = 1;

                                    $sms_msg = "Error: URL could not be opened.";
                                }
                             * 
                             */
                            echo $numbers;
                        }
                        exit;

                            //if there are remainder contacts then send
                            if ($remainder > 0) {

                            //since the last index iterated by the upper code code stops
                            //at numof100s*100 - 1 this will start at numof100s*100 to less than  numof100s*100+remainder 
                            for ($d = (100 * (intval($numof100s))); $d < (100 * (intval($numof100s) + (intval($remainder)))); $d++) {
                                if ($contacts[$d]->phonenumber != "" && strlen($contacts[$d]->phonenumber) == 11) {

                                    //ceheck if its the last contact in this batch of 100 and dont add comma
                                    if ($d == ((100 * (intval($numof100s) + (intval($remainder)))) - 1)) {
                                        $numbers = $numbers . '234' . substr($contacts[$d]->phonenumber, 1, 10);
                                    } else {
                                        $numbers = $numbers . '234' . substr($contacts[$d]->phonenumber, 1, 10) . ',';
                                    }
                                }

                                
                            }
                            
                            $url = "http://www.smslive247.com/http/index.aspx?"
                                        . "cmd=sendquickmsg"
                                        . "&owneremail=" . UrlEncode($owneremail)
                                        . "&subacct=" . UrlEncode($subacct)
                                        . "&subacctpwd=" . UrlEncode($subacctpwd)
                                        . "&message=" . UrlEncode($data['message'])
                                        . "&sender=" . UrlEncode($sender)
                                        . "&sendto=" . UrlEncode($numbers)
                                        . "&msgtype=" . UrlEncode(0);

                                if ($f = @fopen($url, "r")) {
                                    $answer = fgets($f, 255);
                                    echo $answer;
                                    if (substr($answer, 0, 1) == "+") {
                                        $fatalerror = 0;
                                        $sms_msg = $sms_msg . " SMS Was not Sent Successfully to 100 Contacts";
                                        // echo $sms_msg;
                                    } else {
                                        $fatalerror = 0;
                                        // $nomsgsent = $nomsgsent + 1;
                                        $sms_msg = "SMS  was Sent successfully.";
                                        //echo $sms_msg;
                                    }
                                } else {

                                    $fatalerror = 1;

                                    $sms_msg = "Error: URL could not be opened.";
                                }
                        }
                    }


                    $this->session->set_flashdata('error', $sms_msg);
                    redirect(site_url('administrator/placementlist/sendsms'));
                }
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function generatepin() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicgeneratepininitialize();


            $this->data['active'] = "generate pin";

            // $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
            //$this->data['sessiondata'] = $this->_getactivatedsession();
            $validationRules = $this->placementlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                $now = 'now()';
                //get post data
                $data = $this->input->post();
                $quantity = intval($data['quantity']);
                for ($x = 0; $x < $quantity; $x++) {

                    $pinserial = $this->_generate_unique_pin_serial();
                    $this->db->set('datecreated', $now, false);
                    $this->db->set('datemodified', $now, false);
                    $insertdata = array(
                        'pin' => $pinserial[0],
                        'serial' => $pinserial[1],
                        'status' => '0',
                        'regnumber' => '0',
                        'sessionid' => ''
                    );

                    $this->db->insert('tbl_pins_serials', $insertdata);
                }

                $this->session->set_flashdata('success', $quantity . ' PINs & SERIALs  successful generated');
                redirect(site_url('administrator/placementlist/generatepin/'));
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function printpin() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicprintpininitialize();


            $this->data['active'] = "generate pin";

            // $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
            //$this->data['sessiondata'] = $this->_getactivatedsession();
            $validationRules = $this->placementlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function printscratchcard() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            // $this->_basicprintmasterlistinitialize();
            $this->data['active'] = "generate pin";
            //$validationRules = $this->printidcardmodel->_rules;
            //$this->form_validation->set_rules($validationRules);
            //if ($this->form_validation->run() == true) {
            //get post data
            $data = $this->input->get();

            //$data = $this->input->post();
            $this->data['pindata'] = $this->_getpin($data['dateuploaded']);

            if (count($this->data['pindata']) == 0) {
                $this->session->set_flashdata('error', 'No Pin Data Uploaded for this Date');
                redirect(site_url('administrator/placementlist/generatepin/printpin'));
            }
            // }
            //print_r($this->data['printidcarddata']);exit;
            $this->load->view('administrator/print_scratch_card_page', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function loadexportplacementlistpage() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicplacementbysessionlistinitialize();


            $this->data['active'] = "placementlist";

            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

            $this->data['sessiondata'] = $this->_getallsessionname();
            $validationRules = $this->placementlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                //get post data
                $data = $this->input->post();

                $this->data['placementlistdata'] = $this->_getplacementlistbysession($data['session']);
                if (count($this->data['placementlistdata']) == 0) {
                    $this->session->set_flashdata('error', 'No Placementlist Data exit for this session');
                    redirect(site_url('Administrator/placementlist/loadexportplacementlistpage'));
                }
                $_SESSION['selectedsession'] = $data['session'];
                //$this->session->set_userdata('placementlistinfo', $this->data['placementlistdata']);
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function loadexportplacementbystatepage() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicplacementbystateinitialize();
            $this->data['active'] = "supervisory list";
            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

            $this->data['sessiondata'] = $this->_getallsessionname();
            $validationRules = $this->placementlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                //get post data
                $data = $this->input->post();

                $this->data['placementlistdata'] = $this->_getplacementlistbystate($data['session'], $data['state']);
                if (count($this->data['placementlistdata']) == 0) {
                    $this->session->set_flashdata('error', 'No Placementlist Data exit for this session');
                    redirect(site_url('Administrator/placementlist/loadexportplacementlistpage'));
                }
                $_SESSION['selectedsession'] = $data['session'];
                $_SESSION['selectedstate'] = $data['state'];
                //$this->session->set_userdata('placementlistinfo', $this->data['placementlistdata']);
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function loadexportunplacedstudentlistpage() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicunplacedstudentinitialize();
            $this->data['active'] = "uplaced student";

            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

            $this->data['sessiondata'] = $this->_getallsessionname();
            $validationRules = $this->placementlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                //get post data
                $data = $this->input->post();

                $this->data['placementlistdata'] = $this->_getunplacedstudentlist($data['session']);
                if (count($this->data['placementlistdata']) == 0) {
                    $this->session->set_flashdata('error', 'No List Data exit for this session');
                    redirect(site_url('Administrator/placementlist/loadexportunplacedstudentlistpage'));
                }
                $_SESSION['selectedsession'] = $data['session'];
                //$this->session->set_userdata('placementlistinfo', $this->data['placementlistdata']);
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function loadprintplacementlistpage() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicprintplacementlistinitialize();

            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

            $this->data['active'] = "placementlist";

            $this->data['sessiondata'] = $this->_getallsessionname();

            //get faculty
            // $this->data['facultydata'] = $this->_getfaculty();
            //get department
            //$this->data['departmentdata'] = $this->_getdepartment();

            $validationRules = $this->placementlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                //get post data
                $data = $this->input->post();

                $this->data['printplacementlistdata'] = $this->_getplacementlistbysession($data['session']); //, $data['facultyid'], $data['department']);
                if (count($this->data['printplacementlistdata']) == 0) {
                    $this->session->set_flashdata('error', 'No placementlist Data exit for this department in this session');
                    redirect(site_url('Administrator/placementlist/loadprintplacementlistpage'));
                }
                $_SESSION['printplacementlistinfo'] = $this->data['printplacementlistdata'];
                //$this->session->set_userdata('printplacementlistinfo', $this->data['printplacementlistdata']);
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function exportplacementlist() {
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicplacementbysessionlistinitialize();

            $this->data['active'] = "placementlist";
            $filename = 'export_' . date('Y-m-d') . '.xls'; // Define Excel (.xls) file name
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            $fac = '';
            $dept = '';

            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

            $this->data['sessiondata'] = $this->_getallsessionname();
            //prepare the placementlist data ready to be exported to excel
            $i = 0;
            $exportdata;
            $masterdata = $this->_getmasterlistbysession($_SESSION['selectedsession']);
            $placedata = $this->_getplacementlistbysession($_SESSION['selectedsession']);
            //$data = $_SESSION['placementlistinfo'];
            for ($mas = 0; $mas < count($masterdata); $mas++) {

                if (array_key_exists($masterdata[$mas]->studentid, $placedata)) {
                    if ($i == 0) {
                        echo '<table width="100%" border="0">';
                        echo '<th>';
                        echo '<td height="370" align="center" valign="top">';
                        echo '<h2>CHUKWUEMEKA ODIMEGWU OJUKWU UNIVERSITY, IGBARIAM</h2>';
                        echo '<h3>STUDENTS INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES) UNIT</h3>';
                        echo ' <h3>PLACEMENT LIST FOR ' . $placedata[$masterdata[$mas]->studentid]->session .
                        ' SIWES PROGRAMME (' . $placedata[$masterdata[$mas]->studentid]->startmonth . '-' . $placedata[$masterdata[$mas]->studentid]->endmonth .
                        ' ' . $placedata[$masterdata[$mas]->studentid]->attachmentyear . ' )</h3>';

                        echo '<br />';
                        echo '<table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse">';
                    }

                    if (($fac != $placedata[$masterdata[$mas]->studentid]->facultyname) || ($dept != $placedata[$masterdata[$mas]->studentid]->department)) {

                        echo ' <tr  align="left" style="border:solid 1px;">';
                        echo ' <td colspan="15" style="font-weight:bold"><font size="3" face="Arial, Helvetica, sans-serif"> Faculty: ' . $placedata[$masterdata[$mas]->studentid]->facultyname . '<br> Department: ' . $placedata[$masterdata[$mas]->studentid]->department . ' </font>';
                        echo ' </td> ';
                        echo ' </tr> ';
                        echo ' <tr align="left" class="fieldDarkText" style="border:solid 1px;"> ';
                        echo '   <td height="22" style="border:solid 1px;">SN.</td>';
                        echo '<td width="25%" style="border:solid 1px;">Name of Student</td>';
                        echo '<td style="border:solid 1px;">Matric NO.</td>';
                        echo '<td width="18%" style="border:solid 1px;">Course of Study/Level</td>';
                        echo '<td width="10%" style="border:solid 1px;">ML No.</td>';
                        echo '<td width="10%" style="border:solid 1px;">PL No.</td>';
                        echo ' <td width="10%" style="border:solid 1px;">Organisation where student worked.</td>';
                        echo ' <td width="18%" style="border:solid 1px;">Date of Commencement</td>';
                        echo '<td width="18%" style="border:solid 1px;">Date of Completion</td>';
                        echo '<td width="18%" style="border:solid 1px;">Amount Earned</td>';
                        echo '<td width="18%" style="border:solid 1px;">Amount Paid</td>';
                        echo '<td width="18%" style="border:solid 1px;">Bank Name</td>';
                        echo '<td width="18%" style="border:solid 1px;">Account No.</td>';
                        echo '<td width="18%" style="border:solid 1px;">Sort Code</td>';
                        echo ' <td width="18%" style="border:solid 1px;">Remarks</td>';
                        echo ' </tr>';
                    }


                    echo ' <tr align="left" style="border:solid 1px;"> ';
                    echo ' <td width="5%" height="30" style="border:solid 1px;">&nbsp;<font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($i + 1) . '</font></td> ';
                    echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($placedata[$masterdata[$mas]->studentid]->lastname) . ' ' . utf8_decode($placedata[$masterdata[$mas]->studentid]->firstname) . ' ' . utf8_decode($placedata[$masterdata[$mas]->studentid]->middlename) . '</font></td>';
                    echo '<td width="10%" style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($placedata[$masterdata[$mas]->studentid]->regnumber) . ' </font></td>';
                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;' . utf8_decode($placedata[$masterdata[$mas]->studentid]->department) . '/' . utf8_decode($placedata[$masterdata[$mas]->studentid]->level) . ' </font></td>';
                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($mas + 1) . '</font></td>';
                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;' . utf8_decode($i + 1) . '</font></td>';
                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($placedata[$masterdata[$mas]->studentid]->placeofattachment) . ', ' . strip_quotes($placedata[$masterdata[$mas]->studentid]->Addressofattachment) . ',  ' . strip_quotes($placedata[$masterdata[$mas]->studentid]->state) . '</font></td>';
                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($placedata[$masterdata[$mas]->studentid]->startmonth) . ', ' . utf8_decode($placedata[$masterdata[$mas]->studentid]->attachmentyear) . '</font></td>';
                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($placedata[$masterdata[$mas]->studentid]->endmonth) . ', ' . utf8_decode($placedata[$masterdata[$mas]->studentid]->attachmentyear) . '</font></td>';
                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;' . utf8_decode(0) . '</font></td>';
                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">0</font></td>';
                    echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($placedata[$masterdata[$mas]->studentid]->bankname) . '</font></td>';
                    echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($placedata[$masterdata[$mas]->studentid]->accountnumber) . ' </font></td>';
                    echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($placedata[$masterdata[$mas]->studentid]->sortcode) . '</font></td>';
                    echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;</font></td>';

                    echo ' </tr>';



                    $i = $i + 1;
                    $fac = $placedata[$masterdata[$mas]->studentid]->facultyname;
                    $dept = $placedata[$masterdata[$mas]->studentid]->department;
                }
            }
            echo '</table>';
            echo '</td>';
            echo ' </tr>';
            echo ' </table>';

//exit;
            //$returnvalue = $this->processexportplacementlist($exportdata);
            //if ($returnvalue) {
            unset($_SESSION['selectedsession']);

            // }
            // $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function exportplacementbystate() {
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicplacementbysessionlistinitialize();
            $this->data['active'] = "supervisory list";
            $filename = 'export_' . date('Y-m-d') . '.xls'; // Define Excel (.xls) file name
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);


            $this->data['sessiondata'] = $this->_getallsessionname();
            //prepare the placementlist data ready to be exported to excel
            $i = 0;
            $exportdata;
            $data = $this->_getplacementlistbystate($_SESSION['selectedsession'], $_SESSION['selectedstate']);
            //$data = $_SESSION['placementlistinfo'];
            foreach ($data as $value) {

                if ($i == 0) {
                    echo '<table width="100%" border="0">';
                    echo '<th>';
                    echo '<td height="370" align="center" valign="top">';
                    echo '<h2>CHUKWUEMEKA ODIMEGWU OJUKWU UNIVERSITY, IGBARIAM</h2>';
                    echo '<h3>STUDENTS INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES) UNIT</h3>';
                    echo ' <h3>SUPERVISORY LIST BY STATE FOR ' . $value->session .
                    ' SIWES PROGRAMME (' . $value->startmonth . '-' . $value->endmonth .
                    ' ' . $value->attachmentyear . ' )</h3>';

                    echo '<br />';
                    echo '<table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse">';

                    echo ' <tr  align="left" style="border:solid 1px;">';
                    //echo ' <td colspan="15" style="font-weight:bold"><font size="3" face="Arial, Helvetica, sans-serif"> Faculty: '.$placedata[$masterdata[$mas]->studentid]->facultyname.'<br> Department: '.$placedata[$masterdata[$mas]->studentid]->department.' </font>';
                    echo ' </td> ';
                    echo ' </tr> ';
                    echo ' <tr align="left" class="fieldDarkText" style="border:solid 1px;"> ';
                    echo '   <td height="22" style="border:solid 1px;">SN.</td>';
                    echo '<td width="25%" style="border:solid 1px;">Name of Student</td>';
                    echo '<td style="border:solid 1px;">Matric NO.</td>';
                    echo '<td width="18%" style="border:solid 1px;">Course of Study</td>';
                    echo '<td width="18%" style="border:solid 1px;">State</td>';
                    echo '<td width="18%" style="border:solid 1px;">Location</td>';
                    echo ' <td width="10%" style="border:solid 1px;">Company</td>';
                    echo ' <td width="10%" style="border:solid 1px;">Address</td>';
                    echo ' <td width="18%" style="border:solid 1px;">Company Supervisor Contact</td>';
                    echo ' <td width="18%" style="border:solid 1px;">Student Contact</td>';
                    echo ' </tr>';
                }
                echo ' <tr align="left" style="border:solid 1px;"> ';
                echo ' <td width="5%" height="30" style="border:solid 1px;">&nbsp;<font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($i + 1) . '</font></td> ';
                echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($value->lastname) . ' ' . utf8_decode($value->firstname) . ' ' . utf8_decode($value->middlename) . '</font></td>';
                echo '<td width="10%" style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($value->regnumber) . ' </font></td>';
                echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;' . utf8_decode($value->department) . ' </font></td>';
                echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;' . utf8_decode($value->state) . ' </font></td>';
                echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;' . utf8_decode($value->location) . ' </font></td>';
                echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($value->placeofattachment) .  '</font></td>';
                echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($value->Addressofattachment) . '</font></td>';
                echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($value->employerphonenumber) . '</font></td>';
                echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($value->phonenumber) . '</font></td>';

                echo ' </tr>';


                $i = $i + 1;
            }

            echo '</table>';
            echo '</td>';
            echo ' </tr>';
            echo ' </table>';

            //$returnvalue = $this->processplacementliststateexport($exportdata);
            unset($_SESSION['selectedsession']);
            unset($_SESSION['selectedstate']);
            //if ($returnvalue) {
            // unset($_SESSION['selectedsession']);
            //$this->session->unset_userdata('placementlistinfo');
            //$this->session->set_flashdata('success', 'Export process Completed');
            // redirect(site_url('Administrator/placementlist/exportplacementlist'));
            // }
            //$this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function exportunplacedstudent() {
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicplacementbysessionlistinitialize();
            $this->data['active'] = "uplaced student";
            $filename = 'export_' . date('Y-m-d') . '.xls'; // Define Excel (.xls) file name
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            // $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

            $this->data['sessiondata'] = $this->_getallsessionname();
            //prepare the placementlist data ready to be exported to excel
            $i = 0;
            $exportdata;
            $data = $this->_getunplacedstudentlist($_SESSION['selectedsession']);
            //$data = $_SESSION['placementlistinfo'];
            foreach ($data as $value) {
                if ($i == 0) {
                    echo '<table width="100%" border="0">';
                    echo '<th>';
                    echo '<td height="370" align="center" valign="top">';
                    echo '<h2>CHUKWUEMEKA ODIMEGWU OJUKWU UNIVERSITY, IGBARIAM</h2>';
                    echo '<h3>STUDENTS INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES) UNIT</h3>';
                    echo ' <h3> LIST OF UNPLACED STUDENT </h3>';

                    echo '<br />';
                    echo '<table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse">';

                    echo ' <tr  align="left" style="border:solid 1px;">';
                    //echo ' <td colspan="15" style="font-weight:bold"><font size="3" face="Arial, Helvetica, sans-serif"> Faculty: '.$placedata[$masterdata[$mas]->studentid]->facultyname.'<br> Department: '.$placedata[$masterdata[$mas]->studentid]->department.' </font>';
                    echo ' </td> ';
                    echo ' </tr> ';
                    echo ' <tr align="left" class="fieldDarkText" style="border:solid 1px;"> ';
                    echo '   <td height="22" style="border:solid 1px;">SN.</td>';
                    echo '<td width="25%" style="border:solid 1px;">Name of Student</td>';
                    echo '<td style="border:solid 1px;">Matric NO.</td>';
                    echo '<td width="18%" style="border:solid 1px;">Course of Study</td>';
                    echo ' <td width="10%" style="border:solid 1px;">Level</td>';
                    echo ' <td width="18%" style="border:solid 1px;">Phone Number</td>';
                    echo ' </tr>';
                }
                echo ' <tr align="left" style="border:solid 1px;"> ';
                echo ' <td width="5%" height="30" style="border:solid 1px;">&nbsp;<font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($i + 1) . '</font></td> ';
                echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($value->lastname) . ' ' . utf8_decode($value->firstname) . ' ' . utf8_decode($value->middlename) . '</font></td>';
                echo '<td width="10%" style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($value->regnumber) . ' </font></td>';
                echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;' . utf8_decode($value->department) . ' </font></td>';
                echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($value->level) . '</font></td>';
                echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($value->phonenumber) . '</font></td>';

                echo ' </tr>';



                $i = $i + 1;
            }

            echo '</table>';
            echo '</td>';
            echo ' </tr>';
            echo ' </table>';

            //$returnvalue = $this->processunplacedstudentexport($exportdata);
            //if ($returnvalue) {
            unset($_SESSION['selectedsession']);
            //$this->session->unset_userdata('placementlistinfo');
            //$this->session->set_flashdata('success', 'Export process Completed');
            // redirect(site_url('Administrator/placementlist/exportplacementlist'));
            // }
            //$this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function printplacementlist() {
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicplacementbysessionlistinitialize();
            $this->data['active'] = "placementlist";
            //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
            //$this->data['sessiondata'] = $this->_getallsessionname();
            //prepare the placementlist data ready to be exported to excel
            $i = 0;
            $printdata;
            $data = $_SESSION['printplacementlistinfo'];
            if ($data) {
                foreach ($data as $value) {

                    $printdata[$i][0] = strip_quotes($value->regnumber);
                    $printdata[$i][1] = $i + 1;
                    $printdata[$i][2] = $i + 1;
                    $printdata[$i][3] = strip_quotes($value->lastname);
                    $printdata[$i][4] = strip_quotes($value->firstname);
                    $printdata[$i][5] = strip_quotes($value->middlename);
                    $printdata[$i][6] = strip_quotes($value->facultyname);
                    $printdata[$i][7] = strip_quotes($value->department);
                    $printdata[$i][8] = strip_quotes($value->level);
                    $printdata[$i][9] = strip_quotes($value->phonenumber);
                    $printdata[$i][10] = strip_quotes($value->placeofattachment) . ', ' . strip_quotes($value->Addressofattachment) . ',  ' . strip_quotes($value->state);
                    $printdata[$i][11] = strip_quotes($value->startmonth);
                    $printdata[$i][12] = strip_quotes($value->endmonth);
                    $printdata[$i][13] = strip_quotes($value->attachmentyear);
                    $printdata[$i][14] = strip_quotes($value->bankname);
                    $printdata[$i][15] = strip_quotes($value->accountnumber);
                    $printdata[$i][16] = strip_quotes($value->sortcode);
                    $printdata[$i][17] = strip_quotes($value->session);
                    $printdata[$i][18] = strip_quotes($value->employerpayment);
                    $printdata[$i][19] = strip_quotes($value->remark);
                    $i = $i + 1;
                }
                $this->data['printdata'] = $printdata;
            }
            // print_r($printdata);exit;
            // $returnvalue = $this->processplacementlistexport($exportdata);
            // if ($returnvalue) {
            unset($_SESSION['printplacementlistinfo']);
            //$this->session->unset_userdata('printplacementlistinfo');
            //$this->session->set_flashdata('success', 'Export process Completed');
            // redirect(site_url('Administrator/placementlist/exportplacementlist'));
            // }


            $this->load->view('administrator/print_page_placementlist', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
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

    public function _basicsendsmsinitialize() {

        $this->data['subview'] = 'Administrator/send_sms_page';
        $this->data['pageheading'] = 'Send SMS to Student';
        //$this->data['uploaddata'] = '';
        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->placementlistmodel->sendsmsmodel('tbl_placement_list', 'placementlistid', 'id DESC');
    }

    public function _basicgeneratepininitialize() {

        $this->data['subview'] = 'Administrator/generate_pin_page';
        $this->data['pageheading'] = 'Generate Pin';
        //$this->data['uploaddata'] = '';
        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->placementlistmodel->generatepinmodel('tbl_pins_serials', 'id', 'id DESC');
    }

    public function _basicprintpininitialize() {

        $this->data['subview'] = 'Administrator/print_pin_page';
        $this->data['pageheading'] = 'Print Pin';
        //$this->data['uploaddata'] = '';
        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->placementlistmodel->printpinmodel('tbl_pins_serials', 'id', 'id DESC');
    }

    public function _basicplacementbysessionlistinitialize() {

        $this->data['subview'] = 'Administrator/export_placementlist_page';
        $this->data['pageheading'] = 'Export placement List';
        $this->data['uploaddata'] = '';


        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->placementlistmodel->placementlistbysessionmodel('tbl_placement_list', 'placementlistid', 'id DESC');
    }

    public function _basicplacementbystateinitialize() {

        $this->data['subview'] = 'Administrator/export_placementbystate_page';
        $this->data['pageheading'] = 'Export Supervisory List';
        $this->data['uploaddata'] = '';


        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->placementlistmodel->placementlistbystatemodel('tbl_placement_list', 'placementlistid', 'id DESC');
    }

    public function _basicunplacedstudentinitialize() {

        $this->data['subview'] = 'Administrator/export_unplacedstudent_page';
        $this->data['pageheading'] = 'Export Student Without Placement';
        $this->data['uploaddata'] = '';


        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->placementlistmodel->placementlistbysessionmodel('tbl_placement_list', 'placementlistid', 'id DESC');
    }

    public function _basicprintplacementlistinitialize() {

        $this->data['subview'] = 'Administrator/print_placementlist_page';
        $this->data['pageheading'] = 'Print placement List';
        $this->data['uploaddata'] = '';


        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->placementlistmodel->printplacementlistmodel('tbl_placement_list', 'placementlistid', 'id DESC');
    }

    public function processexportplacementlist($exportdata) {
        $filename = 'export_' . date('Y-m-d') . '.xls'; // Define Excel (.xls) file name
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        $fac = '';
        $dept = '';
        $currentsession = $exportdata[0][18];
        $duration = $exportdata[0][12] . '-' . $exportdata[0][13] . ' ' . $exportdata[0][14];
        echo '<table width="100%" border="0">';
        echo '<th>';
        echo '<td height="370" align="center" valign="top">';
        echo '<h2>CHUKWUEMEKA ODIMEGWU OJUKWU UNIVERSITY, IGBARIAM</h2>';
        echo '<h3>STUDENTS INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES) UNIT</h3>';
        echo ' <h3>PLACEMENT LIST FOR ' . $exportdata[0][18] . ' SIWES PROGRAMME (' . $exportdata[0][12] . '-' . $exportdata[0][13] . ' ' . $exportdata[0][14] . ' )</h3>';

        echo '<br />';
        echo '<table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse">';


        // $pagecounter = 0;
        for ($i = 0; $i < count($exportdata); $i++) {
            if (($fac != $exportdata[$i][7]) || ($dept != $exportdata[$i][8])) {

                echo ' <tr  align="left" style="border:solid 1px;">';
                echo ' <td colspan="15" style="font-weight:bold"><font size="3" face="Arial, Helvetica, sans-serif"> Faculty: ' . $exportdata[$i][7] . '<br> Department: ' . $exportdata[$i][8] . ' </font>';
                echo ' </td> ';
                echo ' </tr> ';
                echo ' <tr align="left" class="fieldDarkText" style="border:solid 1px;"> ';
                echo '   <td height="22" style="border:solid 1px;">SN.</td>';
                echo '<td width="25%" style="border:solid 1px;">Name of Student</td>';
                echo '<td style="border:solid 1px;">Matric NO.</td>';
                echo '<td width="18%" style="border:solid 1px;">Course of Study/Level</td>';
                echo '<td width="10%" style="border:solid 1px;">ML No.</td>';
                echo '<td width="10%" style="border:solid 1px;">PL No.</td>';
                echo ' <td width="10%" style="border:solid 1px;">Organisation where student worked.</td>';
                echo ' <td width="18%" style="border:solid 1px;">Date of Commencement</td>';
                echo '<td width="18%" style="border:solid 1px;">Date of Completion</td>';
                echo '<td width="18%" style="border:solid 1px;">Amount Earned</td>';
                echo '<td width="18%" style="border:solid 1px;">Amount Paid</td>';
                echo '<td width="18%" style="border:solid 1px;">Bank Name</td>';
                echo '<td width="18%" style="border:solid 1px;">Account No.</td>';
                echo '<td width="18%" style="border:solid 1px;">Sort Code</td>';
                echo ' <td width="18%" style="border:solid 1px;">Remarks</td>';
                echo ' </tr>';
            }
            //for ($j = 0; $j < count($this->data['printdata'][$i]); $j++) {

            echo ' <tr align="left" style="border:solid 1px;"> ';
            echo ' <td width="5%" height="30" style="border:solid 1px;">&nbsp;<font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($exportdata[$i][0]) . '</font></td> ';
            echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($exportdata[$i][4]) . ' ' . utf8_decode($exportdata[$i][5]) . ' ' . utf8_decode($exportdata[$i][6]) . '</font></td>';
            echo '<td width="10%" style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($exportdata[$i][1]) . ' </font></td>';
            echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;' . utf8_decode($exportdata[$i][8]) . '/' . utf8_decode($exportdata[$i][9]) . ' </font></td>';
            echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($exportdata[$i][3]) . '</font></td>';
            echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;' . utf8_decode($exportdata[$i][2]) . '</font></td>';
            echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($exportdata[$i][11]) . '</font></td>';
            echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($exportdata[$i][12]) . ', ' . utf8_decode($exportdata[$i][14]) . '</font></td>';
            echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($exportdata[$i][13]) . ', ' . utf8_decode($exportdata[$i][14]) . '</font></td>';
            echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;' . utf8_decode($exportdata[$i][19]) . '</font></td>';
            echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">0</font></td>';
            echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($exportdata[$i][15]) . '</font></td>';
            echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($exportdata[$i][16]) . ' </font></td>';
            echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' . utf8_decode($exportdata[$i][17]) . '</font></td>';
            echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;' . utf8_decode($exportdata[$i][20]) . '</font></td>';

            echo ' </tr>';


            $fac = $exportdata[$i][7];
            $dept = $exportdata[$i][8];
        }


        echo '</table>';
        echo '</td>';
        echo ' </tr>';
        echo ' </table>';
    }

    public function processplacementlistexport($exportdata) {


        $currentsession = $exportdata[0][18];
        $duration = $exportdata[0][12] . '-' . $exportdata[0][13] . ' ' . $exportdata[0][14];
        $this->excel->setActiveSheetIndex(0);
//name the worksheet
        $this->excel->getActiveSheet()->setTitle('placementlist worksheet');

        $this->excel->getActiveSheet()->setCellValue('A1', "CHUKWUEMEKA ODIMEGWU OJUKWU UNIVERSITY, IGBARIAM " . "\n" . " STUDENTS INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES) UNIT" . "\n" . " PLACEMENT LIST FOR " . $currentsession . " SIWES PROGRAMME (" . $duration . ")");
//change the font size
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);

//make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);




//merge cell A1 until D1
        $this->excel->getActiveSheet()->mergeCells('A1:L5');
        $this->excel->getActiveSheet()->getStyle('A1:L5')->getAlignment()->setWrapText(true);
//set aligment to center for that merged cell (A1 to D1)
        $this->excel->getActiveSheet()->getStyle('A1:L5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $row = 6;
        $value = '';
        $dept = '';
        $fac = '';

        for ($i = 0; $i < count($exportdata); $i++) {

            if (($fac != $exportdata[$i][6]) || ($dept != $exportdata[$i][7])) {
                $value = "Faculty of " . $exportdata[$i][7] . "\n" . "Department of " . $exportdata[$i][8];
                $this->excel->getActiveSheet()->setCellValue('A' . $row, $value);
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(14);
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->mergeCells('A' . $row . ':' . 'L' . ($row + 1));
                $this->excel->getActiveSheet()->getStyle('A' . $row . ':' . 'L' . ($row + 1))->getAlignment()->setWrapText(true);


                $row = $row + 2;

                $this->excel->getActiveSheet()->setCellValue('A' . $row, 'SN');
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('B' . $row, 'Name Of Student');
                $this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('C' . $row, 'Matric. NO');
                $this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('D' . $row, 'Course of Study/Level');
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('E' . $row, 'ML No');
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('F' . $row, 'PL No.');
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('G' . $row, 'Organisation where student worked');
                $this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('H' . $row, 'Date of Commencement');
                $this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('H' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('I' . $row, 'Date of Completion');
                $this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('I' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('J' . $row, 'Amount Earned');
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('K' . $row, 'Amount Paid');
                $this->excel->getActiveSheet()->getStyle('K' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('K' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('K' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('L' . $row, 'Bank Name');
                $this->excel->getActiveSheet()->getStyle('L' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('L' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('L' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('M' . $row, 'Account No');
                $this->excel->getActiveSheet()->getStyle('M' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('M' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('M' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('N' . $row, 'Sort Code');
                $this->excel->getActiveSheet()->getStyle('N' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('N' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('N' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('O' . $row, 'Remarks');
                $this->excel->getActiveSheet()->getStyle('O' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('O' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('O' . $row)->getAlignment()->setWrapText(true);


                $row = $row + 1;
            }

            $this->excel->getActiveSheet()->setCellValue('A' . $row, $exportdata[$i][0]);
            $this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('B' . $row, $exportdata[$i][4] . ' ' . $exportdata[$i][5] . ' ' . $exportdata[$i][6]);
            $this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('C' . $row, $exportdata[$i][1]);
            $this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('D' . $row, $exportdata[$i][8] . '/' . $exportdata[$i][9]);
            $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('E' . $row, $exportdata[$i][3]);
            $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('F' . $row, $exportdata[$i][2]);
            $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('G' . $row, $exportdata[$i][11]);
            $this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('H' . $row, $exportdata[$i][12] . ', ' . $exportdata[$i][14]);
            $this->excel->getActiveSheet()->getStyle('H' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('I' . $row, $exportdata[$i][13] . ', ' . $exportdata[$i][14]);
            $this->excel->getActiveSheet()->getStyle('I' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('J' . $row, $exportdata[$i][19]);
            $this->excel->getActiveSheet()->getStyle('J' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('K' . $row, '');
            $this->excel->getActiveSheet()->getStyle('K' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('L' . $row, $exportdata[$i][15]);
            $this->excel->getActiveSheet()->getStyle('L' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('M' . $row, " " . $exportdata[$i][16]);
            $this->excel->getActiveSheet()->getStyle('M' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('N' . $row, " " . $exportdata[$i][17]);
            $this->excel->getActiveSheet()->getStyle('N' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('O' . $row, $exportdata[$i][20]);
            $this->excel->getActiveSheet()->getStyle('O' . $row)->getAlignment()->setWrapText(true);

            $fac = $exportdata[$i][6];
            $dept = $exportdata[$i][7];
            $row = $row + 1;
        }
        $xls_filename = 'export_' . date('Y-m-d') . '.xls'; // Define Excel (.xls) file name
//$filename='just_some_random_name.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $xls_filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');

        return 1;
    }

    public function processplacementliststateexport($exportdata) {


        $currentsession = $exportdata[0][18];
        $duration = $exportdata[0][12] . '-' . $exportdata[0][13] . ' ' . $exportdata[0][14];
        $this->excel->setActiveSheetIndex(0);
//name the worksheet
        $this->excel->getActiveSheet()->setTitle('placementlist worksheet');

        $this->excel->getActiveSheet()->setCellValue('A1', "CHUKWUEMEKA ODIMEGWU OJUKWU UNIVERSITY, IGBARIAM " . "\n" . " STUDENTS INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES) UNIT" . "\n" . " PLACEMENT LIST FOR " . $currentsession . " SIWES PROGRAMME (" . $duration . ")");
//change the font size
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);

//make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);




//merge cell A1 until D1
        $this->excel->getActiveSheet()->mergeCells('A1:L5');
        $this->excel->getActiveSheet()->getStyle('A1:L5')->getAlignment()->setWrapText(true);
//set aligment to center for that merged cell (A1 to D1)
        $this->excel->getActiveSheet()->getStyle('A1:L5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $row = 6;
        $value = '';
        //$dept = '';
        //$fac = '';
        //$row =  $row +2;

        $this->excel->getActiveSheet()->setCellValue('A' . $row, 'SN');
        $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('B' . $row, 'Name Of Student');
        $this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('C' . $row, 'Matric. NO');
        $this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('D' . $row, 'Course of Study/Level');
        $this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('E' . $row, 'ML No');
        $this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('F' . $row, 'PL No.');
        $this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('G' . $row, 'Organisation where student worked');
        $this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('H' . $row, 'Phone Number');
        $this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('H' . $row)->getAlignment()->setWrapText(true);
        /**
          $this->excel->getActiveSheet()->setCellValue('I' . $row, 'Date of Completion');
          $this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setSize(12);
          $this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setBold(true);
          $this->excel->getActiveSheet()->getStyle('I' . $row)->getAlignment()->setWrapText(true);
          $this->excel->getActiveSheet()->setCellValue('J' . $row, 'Amount Earned');
          $this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setSize(12);
          $this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setBold(true);
          $this->excel->getActiveSheet()->getStyle('J' . $row)->getAlignment()->setWrapText(true);
          $this->excel->getActiveSheet()->setCellValue('K' . $row, 'Amount Paid');
          $this->excel->getActiveSheet()->getStyle('K' . $row)->getFont()->setSize(12);
          $this->excel->getActiveSheet()->getStyle('K' . $row)->getFont()->setBold(true);
          $this->excel->getActiveSheet()->getStyle('K' . $row)->getAlignment()->setWrapText(true);
          $this->excel->getActiveSheet()->setCellValue('L' . $row, 'Bank Name');
          $this->excel->getActiveSheet()->getStyle('L' . $row)->getFont()->setSize(12);
          $this->excel->getActiveSheet()->getStyle('L' . $row)->getFont()->setBold(true);
          $this->excel->getActiveSheet()->getStyle('L' . $row)->getAlignment()->setWrapText(true);
          $this->excel->getActiveSheet()->setCellValue('M' . $row, 'Account No');
          $this->excel->getActiveSheet()->getStyle('M' . $row)->getFont()->setSize(12);
          $this->excel->getActiveSheet()->getStyle('M' . $row)->getFont()->setBold(true);
          $this->excel->getActiveSheet()->getStyle('M' . $row)->getAlignment()->setWrapText(true);
          $this->excel->getActiveSheet()->setCellValue('N' . $row, 'Sort Code');
          $this->excel->getActiveSheet()->getStyle('N' . $row)->getFont()->setSize(12);
          $this->excel->getActiveSheet()->getStyle('N' . $row)->getFont()->setBold(true);
          $this->excel->getActiveSheet()->getStyle('N' . $row)->getAlignment()->setWrapText(true);
          $this->excel->getActiveSheet()->setCellValue('O' . $row, 'Remarks');
          $this->excel->getActiveSheet()->getStyle('O' . $row)->getFont()->setSize(12);
          $this->excel->getActiveSheet()->getStyle('O' . $row)->getFont()->setBold(true);
          $this->excel->getActiveSheet()->getStyle('O' . $row)->getAlignment()->setWrapText(true);
         * */
        for ($i = 0; $i < count($exportdata); $i++) {

            // if (($fac != $exportdata[$i][6]) || ($dept != $exportdata[$i][7])) {






            $row = $row + 1;
            //}

            $this->excel->getActiveSheet()->setCellValue('A' . $row, $exportdata[$i][0]);
            $this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('B' . $row, $exportdata[$i][4] . ' ' . $exportdata[$i][5] . ' ' . $exportdata[$i][6]);
            $this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('C' . $row, $exportdata[$i][1]);
            $this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('D' . $row, $exportdata[$i][8] . '/' . $exportdata[$i][9]);
            $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('E' . $row, $exportdata[$i][3]);
            $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('F' . $row, $exportdata[$i][2]);
            $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('G' . $row, $exportdata[$i][11]);
            $this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('H' . $row, ' ' . $exportdata[$i][10]);

            /*             * *
              $this->excel->getActiveSheet()->getStyle('H' . $row)->getAlignment()->setWrapText(true);
              $this->excel->getActiveSheet()->setCellValue('I' . $row, $exportdata[$i][13].', '. $exportdata[$i][14]);
              $this->excel->getActiveSheet()->getStyle('I' . $row)->getAlignment()->setWrapText(true);
              $this->excel->getActiveSheet()->setCellValue('J' . $row, $exportdata[$i][19]);
              $this->excel->getActiveSheet()->getStyle('J' . $row)->getAlignment()->setWrapText(true);
              $this->excel->getActiveSheet()->setCellValue('K' . $row, '');
              $this->excel->getActiveSheet()->getStyle('K' . $row)->getAlignment()->setWrapText(true);
              $this->excel->getActiveSheet()->setCellValue('L' . $row, $exportdata[$i][15]);
              $this->excel->getActiveSheet()->getStyle('L' . $row)->getAlignment()->setWrapText(true);
              $this->excel->getActiveSheet()->setCellValue('M' . $row, " ".$exportdata[$i][16]);
              $this->excel->getActiveSheet()->getStyle('M' . $row)->getAlignment()->setWrapText(true);
              $this->excel->getActiveSheet()->setCellValue('N' . $row, " ".$exportdata[$i][17]);
              $this->excel->getActiveSheet()->getStyle('N' . $row)->getAlignment()->setWrapText(true);
              $this->excel->getActiveSheet()->setCellValue('O' . $row, $exportdata[$i][20]);
              $this->excel->getActiveSheet()->getStyle('O' . $row)->getAlignment()->setWrapText(true);
             * */
            // $fac = $exportdata[$i][6];
            // $dept = $exportdata[$i][7];
            // $row =  $row +1;
        }
        $xls_filename = 'export_' . date('Y-m-d') . '.xls'; // Define Excel (.xls) file name
//$filename='just_some_random_name.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $xls_filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }

    public function processunplacedstudentexport($exportdata) {
        $row = 1;
        $this->excel->setActiveSheetIndex(0);
//name the worksheet
        $this->excel->getActiveSheet()->setTitle('Unplacedstudentlist worksheet');
        $this->excel->getActiveSheet()->setCellValue('A' . $row, 'SN');
        $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('B' . $row, 'Matric. NO');
        $this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('C' . $row, 'Name Of Student');
        $this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('D' . $row, 'Course of Study');
        $this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('E' . $row, 'Level');
        $this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);
        $this->excel->getActiveSheet()->setCellValue('F' . $row, 'Phone Number');
        $this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);
        $this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);

        $row = 2;
        for ($i = 0; $i < count($exportdata); $i++) {




            //$row =  $row +1;


            $this->excel->getActiveSheet()->setCellValue('A' . $row, $exportdata[$i][0]);
            $this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('C' . $row, $exportdata[$i][1]);
            $this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('B' . $row, $exportdata[$i][2] . ' ' . $exportdata[$i][3] . ' ' . $exportdata[$i][4]);
            $this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('D' . $row, $exportdata[$i][5]);
            $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('E' . $row, $exportdata[$i][6]);
            $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('F' . $row, $exportdata[$i][7]);
            $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);

            $row = $row + 1;
        }
        $xls_filename = 'export_' . date('Y-m-d') . '.xls'; // Define Excel (.xls) file name
//$filename='just_some_random_name.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $xls_filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }

    public function _getactivatedsession() {
        $sessiondata = 0;
        $query = $this->db->get_where('tbl_session', array('sessionstatus' => 'activated'));
        if ($query->num_rows() > 0) {
            $sessiondata = $query->result();
        }
        return $sessiondata;
    }

    public function _getplacementlistnumber($session) {
        $masterlistno = 0;
        $query = $this->db->get_where('tbl_master_placement_number', array('sessionname' => $session));
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $masterlistno = $row;
            }
        }
        return $masterlistno;
    }

    public function _getplacementlistbysession($session) {
        $placementlist = array();
        $index = 0;
        $this->db->select();
        $this->db->from('tbl_faculty');
        $this->db->order_by('tbl_faculty.facultyname asc');
        $query = $this->db->get();

        if ($query->num_rows()) {
            foreach ($query->result() as $outerrow) {
                $this->db->select();
                $this->db->from('tbl_department');
                $this->db->where('tbl_department.facultyid', $outerrow->facultyid);
                $this->db->order_by('tbl_department.departmentname asc');
                $innerquery = $this->db->get();
                if ($innerquery->num_rows()) {
                    foreach ($innerquery->result() as $innerrow) {
                        $this->db->select();
                        $this->db->from('tbl_student');
                        $this->db->join('tbl_placement_list', 'tbl_student.studentid = tbl_placement_list.studentid', 'inner');
                        $this->db->join('tbl_faculty', 'tbl_student.facultyid = tbl_faculty.facultyid', 'inner');
                        $this->db->join('tbl_master_list', 'tbl_student.studentid = tbl_master_list.studentid', 'inner');
                        $this->db->where('tbl_master_list.sessionid = tbl_placement_list.sessionid');
                        $this->db->where('tbl_student.department', $innerrow->departmentname);
                        $this->db->where('tbl_placement_list.sessionid', $session);
                        $this->db->where('tbl_master_list.mobilizationstatus', 'mobilized');
                        $this->db->order_by('tbl_student.regnumber asc');
                        $innermostquery = $this->db->get();
//echo $this->db->last_query();exit;
                        if ($innermostquery->num_rows()) {
                            foreach ($innermostquery->result() as $innermostrow) {
                                $placementlist[$innermostrow->studentid] = $innermostrow;
                                //$index = $index + 1;
                            }
                        }
                    }
                }
            }
        }
        return $placementlist;
    }
    
    public function _getplacedstudentcontact($session) {
        $placementlist = array();
        $index = 0;
        
                        $this->db->select();
                        $this->db->from('tbl_student');
                        $this->db->join('tbl_placement_list', 'tbl_student.studentid = tbl_placement_list.studentid', 'inner');
                        //$this->db->join('tbl_faculty', 'tbl_student.facultyid = tbl_faculty.facultyid', 'inner');
                        $this->db->join('tbl_master_list', 'tbl_student.studentid = tbl_master_list.studentid', 'inner');
                        $this->db->where('tbl_master_list.sessionid = tbl_placement_list.sessionid');
                       // $this->db->where('tbl_student.department', $innerrow->departmentname);
                        $this->db->where('tbl_placement_list.sessionid', $session);
                        $this->db->where('tbl_master_list.mobilizationstatus', 'mobilized');
                        $this->db->where('tbl_student.phonenumber !=', '');
                        $this->db->order_by('tbl_student.regnumber asc');
                        $innermostquery = $this->db->get();
//echo $this->db->last_query();exit;
                        if ($innermostquery->num_rows()) {
                            foreach ($innermostquery->result() as $innermostrow) {
                                $placementlist[$index] = $innermostrow;
                                $index = $index + 1;
                            }
                        }
                   
            
        
        return $placementlist;
    }

    public function _getmasterlistbysession($session) {
        $masterlist = array();
        $index = 0;
        $this->db->select();
        $this->db->from('tbl_faculty');
        $this->db->order_by('tbl_faculty.facultyname asc');
        $query = $this->db->get();

        if ($query->num_rows()) {
            foreach ($query->result() as $outerrow) {
                $this->db->select();
                $this->db->from('tbl_department');
                $this->db->where('tbl_department.facultyid', $outerrow->facultyid);
                $this->db->order_by('tbl_department.departmentname asc');
                $innerquery = $this->db->get();
                if ($innerquery->num_rows()) {
                    foreach ($innerquery->result() as $innerrow) {
                        $this->db->select();
                        $this->db->from('tbl_student');
                        $this->db->join('tbl_master_list', 'tbl_student.studentid = tbl_master_list.studentid', 'inner');
                        $this->db->join('tbl_faculty', 'tbl_student.facultyid = tbl_faculty.facultyid', 'inner');
                        $this->db->where('tbl_student.department', $innerrow->departmentname);
                        $this->db->where('tbl_master_list.sessionid', $session);
                        $this->db->where('tbl_master_list.mobilizationstatus', 'mobilized');
                        $this->db->order_by('tbl_student.regnumber asc');
                        $innermostquery = $this->db->get();
//echo $this->db->last_query();exit;
                        if ($innermostquery->num_rows()) {
                            foreach ($innermostquery->result() as $innermostrow) {
                                $masterlist[$index] = $innermostrow;
                                $index = $index + 1;
                            }
                        }
                    }
                }
            }
//$masterlist = $query->result();
        }

        return $masterlist;
    }
    
    public function _getmobilizedstudentcontact($session) {
        $masterlist = array();
        $index = 0;
        
                        $this->db->select();
                        $this->db->from('tbl_student');
                        $this->db->join('tbl_master_list', 'tbl_student.studentid = tbl_master_list.studentid', 'inner');
                        //$this->db->join('tbl_faculty', 'tbl_student.facultyid = tbl_faculty.facultyid', 'inner');
                        //$this->db->where('tbl_student.department', $innerrow->departmentname);
                        $this->db->where('tbl_master_list.sessionid', $session);
                        $this->db->where('tbl_master_list.mobilizationstatus', 'mobilized');
                        $this->db->where('tbl_student.phonenumber !=', '');
                        $this->db->order_by('tbl_student.regnumber asc');
                        $innermostquery = $this->db->get();
echo $this->db->last_query();exit;
                        if ($innermostquery->num_rows()) {
                            foreach ($innermostquery->result() as $innermostrow) {
                                $masterlist[$index] = $innermostrow;
                                $index = $index + 1;
                            }
                        }
                 

                        //print_r($masterlist);exit;

        return $masterlist;
    }

    public function _getallstudentcontact($session) {
        $studentlist = array();
        $index = 0;
        
                        $this->db->select();
                        $this->db->from('tbl_student');
                        $this->db->join('tbl_master_list', 'tbl_student.studentid = tbl_master_list.studentid', 'inner');
                        //$this->db->join('tbl_faculty', 'tbl_student.facultyid = tbl_faculty.facultyid', 'inner');
                        //$this->db->where('tbl_student.department', $innerrow->departmentname);
                        $this->db->where('tbl_master_list.sessionid', $session);
                        //$this->db->where('tbl_master_list.mobilizationstatus', 'mobilized');
                        $this->db->where('tbl_student.phonenumber !=', '');
                        $this->db->order_by('tbl_student.regnumber asc');
                        $innermostquery = $this->db->get();
//echo $this->db->last_query();exit;
                        if ($innermostquery->num_rows()) {
                            foreach ($innermostquery->result() as $innermostrow) {
                                $masterlist[$index] = $innermostrow;
                                $index = $index + 1;
                            }
                        }
                 

        

        return $masterlist;
    }

    
    public function _getplacementlistbystate($session, $state) {
        $placementlist = array();
        $index = 0;

        $this->db->select();
        //$this->db->select('tbl_placement_list.employerphonenumber as companycontact');
        $this->db->from('tbl_student');
        
        $this->db->join('tbl_placement_list', 'tbl_student.studentid = tbl_placement_list.studentid', 'inner');
        $this->db->join('tbl_location', 'tbl_placement_list.locationid = tbl_location.locationid', 'inner');
        $this->db->join('tbl_faculty', 'tbl_student.facultyid = tbl_faculty.facultyid', 'inner');
        $this->db->join('tbl_master_list', 'tbl_student.studentid = tbl_master_list.studentid', 'inner');
        $this->db->where('tbl_master_list.sessionid = tbl_placement_list.sessionid');
//$this->db->where('tbl_student.department', $innerrow->departmentname);
        $this->db->where('tbl_placement_list.sessionid', $session);
        if ($state != 'all') {
            $this->db->where('tbl_placement_list.state', $state);
        }
        $this->db->where('tbl_master_list.mobilizationstatus', 'mobilized');
        
//$this->db->order_by('tbl_student.regnumber asc');
        if ($state == 'all') {
            $this->db->order_by('tbl_placement_list.state asc');
            
        }
        $this->db->order_by('tbl_location.location asc');
        $innermostquery = $this->db->get();
//echo $this->db->last_query();exit;
        if ($innermostquery->num_rows()) {
            foreach ($innermostquery->result() as $innermostrow) {
                $placementlist[$index] = $innermostrow;
                $index = $index + 1;
            }
        }
        return $placementlist;
    }

    public function _getunplacedstudentlist($session) {
        $placementlist = array();
        $index = 0;

        $innermostquery = $this->db->query("SELECT studentid
FROM tbl_master_list where mobilizationstatus = 'mobilized'
and sessionid ='" . $session . "' and 
 studentid NOT IN
    (SELECT studentid
     FROM tbl_placement_list)");
//$innermostquery = $this->db->get();
//echo $this->db->last_query();exit;
        if ($innermostquery->num_rows()) {
            foreach ($innermostquery->result() as $innermostrow) {
                $this->db->select('regnumber,firstname,lastname,
             middlename,department,level,phonenumber');
                $this->db->from('tbl_student');
                $this->db->where('tbl_student.studentid', $innermostrow->studentid);
                $this->db->where('tbl_student.phonenumber !=', '');
                $stdquery = $this->db->get();
                foreach ($stdquery->result() as $stdrow) {
                    $placementlist[$index] = $stdrow;
                    $index = $index + 1;
                }
            }
        }
        return $placementlist;
    }

    public function _getsession($session) {
        $this->db->select('sessionname');
        $this->db->where('sessionname =', $session);
        $query = $this->db->get('tbl_session');

        return $query;
    }

    public function _getallsessionname() {
        $session = 0;
        $this->db->select();
        $query = $this->db->get('tbl_session');
        if ($query->num_rows() > 0) {
            $session = $query->result();
        }
        return $session;
    }

    public function _getpin($uploaddate) {
        //$printid = 0;
        $printid = array();
        $index = 0;
        $this->db->select();
        $this->db->from('tbl_pins_serials');
        $this->db->where('datecreated >=', $uploaddate);

        $query = $this->db->get();

        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $printid[$index] = $row;
                $index = $index + 1;
            }
        }
        //echo $this->db->last_query();exit;
        return $printid;
    }

    public function verifypinserialexist($pin, $serial) {
        $pindata = array();
        $query = $this->db->get_where('tbl_pins_serials', array('pin' => $pin, 'serial' => $serial));
        if ($query->num_rows() > 0) {
            $pindata = $query->result();
        }
        //echo $this->db->query;
        return $pindata;
    }

    function _generatepinserial($len = 16) {

        $source = "434506304927384129012543456789012545678901236475960781901926304927384567890125678912567890123434567890123456789012901234";
        $range = strlen($source);
        $output = '';
        for ($i = 0; $i < $len; $i++) {
            $output .= substr($source, rand(0, $range - 1), 1);
        }
        return $output;
    }

    public function _generate_unique_pin_serial() {
        $pin = $this->_generatepinserial();
        $serial = $this->_generatepinserial();

        while (count($this->verifypinserialexist($pin, $serial)) > 0) {
            $pin = $this->_generatepinserial();
            $serial = $this->_generatepinserial();
        }
        $pinserial = array($pin, $serial);
        return $pinserial;
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
