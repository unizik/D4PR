<?php
//session_start();
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of session
 *
 * @author TOCHUKWU
 */
class masterlist extends Admin_Controller {

    //put your code here
    public $sessionstatus = '';

    function __construct() {
        parent::__construct();
		ini_set('memory_limit', "256M");
        $this->load->model('masterlistmodel');
        //$this->load->library('Excel/ExcelReader');
        $this->load->library('excel');
        session_start();
//print_r($this->ExcelReader);
        // $this->sessionstatus = array('activated'=>'Activate', 'deactivated'=>'Deactivate');
        //  $this->_set_nav_menus();
    }

    public function index() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicuploadmasterlistinitialize();
            $this->session->set_userdata('uploadedfile', '');
$this->data['active'] = "qualifiedlist";
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

    /**
     * this is a default method
     * this loads the session page
     * it passes the subview page to the layout_main page
     */
    public function uploadmasterlist() {
        if ($this->session->userdata("admin_isloggedin")) {
            $value = 0;
            $this->_basicuploadmasterlistinitialize();
            $this->data['active'] = "masterlist";
            //get faculty
            $this->data['facultydata'] = $this->_getfaculty();
            //get department
            $this->data['departmentdata'] = $this->_getdepartment();

            $this->data['sessiondata'] = $this->_getactivatedsession();

            $validationRules = $this->masterlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
//echo $this->form_validation->run().'  jjfjjf';exit;

            if ($this->form_validation->run() == true) {
                $arrUploadData = array();
                //after posting form below code is executed
                $now = 'now()';
                $data = $this->masterlistmodel->array_from_post(array('masterlistfile'));

                $filename = 'masterlist'; //$this->masterlistmodel->generate_unique_id();
                $excelfile = $this->_handlemasterlistupload($filename);


                //read file from path
                //return;
                // echo base_url('resources/masterlistfile/' . $filename . '.xls');
                $excelfilepath = './resources/masterlistfile/' . $excelfile;
                $objPHPExcel = PHPExcel_IOFactory::load($excelfilepath);
//get only the Cell Collection
                $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
//extract to a PHP readable array format
                foreach ($cell_collection as $cell) {
                    $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                    $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                    $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                    //header will/should be in row 1 only. of course this can be modified to suit your need.
                    //if ($row == 1) {
                    //    $header[$row][$column] = $data_value;
                    // } else {
                    $arr_data[$row][$column] = $data_value;
                    // }
                }
//send the data in an array format
                //  $data['header'] = $header;
                $data['values'] = $arr_data;


                /**
                  //echo $excelfilepath;exit;
                  $this->excelreader->setOutputEncoding('CPa25a');
                  $this->excelreader->Spreadsheet_Excel_Reader();
                  $this->excelreader->read($excelfilepath);

                  for ($row = 1; $row <= $this->excelreader->sheets[0]['numRows']; $row++) {

                  for ($col = 1; $col <= $this->excelreader->sheets[0]['numCols']; $col++) {

                  if (isset($this->excelreader->sheets[0]['cells'][$row][$col])) {
                  // if($col == 9 || $col == 10)
                  //    $this->excelreader->formatRecords[$row][$col] = $this->excelreader->sheets[0]['cells'][$row][$col];
                  $arrUploadData[$row - 1][$col - 1] = $this->excelreader->sheets[0]['cells'][$row][$col];
                  }
                  }
                  }
                 * * */
                $_SESSION['uploadedfile'] = $arr_data;
//print_r($arr_data);exit;
                // $_SESSION['uploadedfile'] = $arrUploadData;
                //$this->session->set_userdata('uploadedfile', $arrUploadData);
            }

            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function loadexportmasterlistpage() {
        //echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicmasterbysessionlistinitialize();

            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
            $this->data['active'] = "masterlist";
            $this->data['sessiondata'] = $this->_getallsessionname();
            $validationRules = $this->masterlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                //get post data
                $data = $this->input->post();

                $this->data['masterlistdata'] = $this->_getmasterlistbysession($data['session']);
                if (count($this->data['masterlistdata']) == 0) {
                    $this->session->set_flashdata('error', 'No Masterlist Data exit for this session');
                    redirect(site_url('Administrator/masterlist/loadexportmasterlistpage'));
                }
                //print_r($this->data['masterlistdata']);exit;
                //$_SESSION['masterlistinfo'] = $this->data['masterlistdata'];
                $_SESSION['selectedsession'] = $data['session'];
                //$this->session->set_userdata('masterlistinfo', $this->data['masterlistdata']);
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

     public function loadexportstudentlistpage() {
        //echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicstudentbysessionlistinitialize();
            $this->data['active'] = "mobilized student";
            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

            $this->data['sessiondata'] = $this->_getallsessionname();
            $validationRules = $this->masterlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                //get post data
                $data = $this->input->post();

                $this->data['masterlistdata'] = $this->_getmasterlistbysession($data['session']);
                if (count($this->data['masterlistdata']) == 0) {
                    $this->session->set_flashdata('error', 'No Masterlist Data exit for this session');
                    redirect(site_url('Administrator/masterlist/loadexportstudentlistpage'));
                }
                //print_r($this->data['masterlistdata']);exit;
               // $_SESSION['masterlistinfo'] = $this->data['masterlistdata'];
                //$this->session->set_userdata('masterlistinfo', $this->data['masterlistdata']);
                $_SESSION['selectedsession'] = $data['session'];
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function loadprintmasterlistpage() {
        // echo $this->data['login_session'].'dash';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicprintmasterlistinitialize();
            $this->data['active'] = "masterlist";
            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

            $this->data['sessiondata'] = $this->_getallsessionname();

            //get faculty
            //$this->data['facultydata'] = $this->_getfaculty();
            //get department
            //$this->data['departmentdata'] = $this->_getdepartment();

            $validationRules = $this->masterlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
            if ($this->form_validation->run() == true) {
                //get post data
                $data = $this->input->post();

                $this->data['printmasterlistdata'] = $this->_getmasterlistbyparameter($data['session']); //, $data['facultyid'], $data['department']);
                if (count($this->data['printmasterlistdata']) == 0) {
                    $this->session->set_flashdata('error', 'No Masterlist Data exit for this session');
                    
                    redirect(site_url('Administrator/masterlist/loadprintmasterlistpage'));
                }
                
                $_SESSION['printmasterlistinfo'] = $this->data['printmasterlistdata'];
                //$this->session->set_userdata('printmasterlistinfo', $this->data['printmasterlistdata']);
            }
            $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function exportmasterlist() {
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicmasterbysessionlistinitialize();
            $this->data['active'] = "masterlist";
            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
            //$_SESSION['selectedsession'] = $data['session'];                                                
                                    
             $filename = 'export_' . date('Y-m-d') . '.xls'; // Define Excel (.xls) file name
         header('Content-type: application/vnd.ms-excel');
       header('Content-Disposition: attachment; filename='.$filename);
        $fac = '';
        $dept = '';

            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

            $this->data['sessiondata'] = $this->_getallsessionname();
            //prepare the placementlist data ready to be exported to excel
            $i = 0;
            $exportdata;
            $masterdata = $this->_getmasterlistbysession($_SESSION['selectedsession']);
            //$placedata = $this->_getplacementlistbysession($_SESSION['selectedsession']);
            //$data = $_SESSION['placementlistinfo'];
            for($mas = 0; $mas < count($masterdata); $mas++) {
                
                    if($i == 0){
                         echo '<table width="100%" border="0">';
                    echo '<th>';
                        echo '<td height="370" align="center" valign="top">';
                           echo '<h2>CHUKWUEMEKA ODIMEGWU OJUKWU UNIVERSITY, IGBARIAM</h2>';
                            echo '<h3>STUDENTS INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES) UNIT</h3>';
                           echo ' <h3>MASTER LIST FOR ' . $masterdata[$mas]->session.
                                   ' SIWES PROGRAMME ('.  $masterdata[$mas]->startmonth.'-'.$masterdata[$mas]->endmonth.
                                           ' '.$masterdata[$mas]->attachmentyear.' )</h3>';

                            echo '<br />';
                            echo '<table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse">';
                    }

                     if (($fac != $masterdata[$mas]->facultyname) || ($dept != $masterdata[$mas]->department) ) { 

                               echo ' <tr  align="left" style="border:solid 1px;">';
                               echo ' <td colspan="15" style="font-weight:bold"><font size="3" face="Arial, Helvetica, sans-serif"> Faculty: '.$masterdata[$mas]->facultyname.'<br> Department: '.$masterdata[$mas]->department.' </font>';
                                    echo ' </td> ';
                                    echo ' </tr> ';
                                   echo ' <tr align="left" class="fieldDarkText" style="border:solid 1px;"> ';
                                     echo '   <td height="22" style="border:solid 1px;">SN.</td>';
                                    echo '<td width="25%" style="border:solid 1px;">Name of Student</td>';
                                    echo '<td style="border:solid 1px;">Matric NO.</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Year of Study</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Course of Study</td>';
                                    echo '<td width="10%" style="border:solid 1px;">ML No.</td>';                                   
                                    echo '<td width="18%" style="border:solid 1px;">Period of Attachment</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Nationality</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Bank Name</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Account No.</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Sort Code</td>';
                                   echo ' <td width="18%" style="border:solid 1px;">Remarks</td>';
                                   echo ' </tr>';
                                }


                                 echo ' <tr align="left" style="border:solid 1px;"> ';
                                    echo ' <td width="5%" height="30" style="border:solid 1px;">&nbsp;<font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($i + 1) . '</font></td> ';
                                    echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' .utf8_decode($masterdata[$mas]->lastname) . ' ' . utf8_decode($masterdata[$mas]->firstname) . ' ' . utf8_decode($masterdata[$mas]->middlename). '</font></td>';
                                    echo '<td width="10%" style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->regnumber).' </font></td>';
                                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;'.  utf8_decode($masterdata[$mas]->level).' Level'.' </font></td>';
                                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->department) .'</font></td>';
                                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;'. utf8_decode($i + 1).'</font></td>';                                    
                                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->startmonth).' - '.utf8_decode($masterdata[$mas]->endmonth)." ". utf8_decode($masterdata[$mas]->attachmentyear).'</font></td>';
                                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->nationality).'</font></td>';                                   
                                   echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->bankname).'</font></td>';
                                   echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->accountnumber).' </font></td>';
                                   echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'.  utf8_decode($masterdata[$mas]->sortcode).'</font></td>';
                                   echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'.  utf8_decode($masterdata[$mas]->remark).'</font></td>';

                               echo ' </tr>';
                            
                    

                $i = $i + 1;
                $fac = $masterdata[$mas]->facultyname;
                $dept = $masterdata[$mas]->department;
                
                
            }
           echo '</table>';
                    echo '</td>';
               echo ' </tr>';
           echo ' </table>';

            
            
                unset($_SESSION['selectedsession']);
                
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function exportstudentlist() {
        if ($this->session->userdata("admin_isloggedin")) {
            $this->_basicstudentbysessionlistinitialize();

            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
            $this->data['active'] = "mobilized student";
            $this->data['sessiondata'] = $this->_getallsessionname();
            //prepare the masterlist data ready to be exported to excel
             $filename = 'export_' . date('Y-m-d') . '.xls'; // Define Excel (.xls) file name
         header('Content-type: application/vnd.ms-excel');
       header('Content-Disposition: attachment; filename='.$filename);
        $fac = '';
        $dept = '';

            $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

            $this->data['sessiondata'] = $this->_getallsessionname();
            //prepare the placementlist data ready to be exported to excel
            $i = 0;
            $exportdata;
            $masterdata = $this->_getmasterlistbysession($_SESSION['selectedsession']);
            //$placedata = $this->_getplacementlistbysession($_SESSION['selectedsession']);
            //$data = $_SESSION['placementlistinfo'];
            for($mas = 0; $mas < count($masterdata); $mas++) {
                
                    if($i == 0){
                         echo '<table width="100%" border="0">';
                    echo '<th>';
                        echo '<td height="370" align="center" valign="top">';
                           echo '<h2>CHUKWUEMEKA ODIMEGWU OJUKWU UNIVERSITY, IGBARIAM</h2>';
                            echo '<h3>STUDENTS INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES) UNIT</h3>';
                           echo ' <h3>MASTER LIST FOR ' . $masterdata[$mas]->session.
                                   ' SIWES PROGRAMME ('.  $masterdata[$mas]->startmonth.'-'.$masterdata[$mas]->endmonth.
                                           ' '.$masterdata[$mas]->attachmentyear.' )</h3>';

                            echo '<br />';
                            echo '<table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse">';
                    }

                     if (($fac != $masterdata[$mas]->facultyname) || ($dept != $masterdata[$mas]->department) ) { 

                               echo ' <tr  align="left" style="border:solid 1px;">';
                               echo ' <td colspan="15" style="font-weight:bold"><font size="3" face="Arial, Helvetica, sans-serif"> Faculty: '.$masterdata[$mas]->facultyname.'<br> Department: '.$masterdata[$mas]->department.' </font>';
                                    echo ' </td> ';
                                    echo ' </tr> ';
                                   echo ' <tr align="left" class="fieldDarkText" style="border:solid 1px;"> ';
                                     echo '   <td height="22" style="border:solid 1px;">SN.</td>';
                                    echo '<td width="25%" style="border:solid 1px;">Name of Student</td>';
                                    echo '<td style="border:solid 1px;">Matric NO.</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Year of Study</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Course of Study</td>';
                                    echo '<td width="10%" style="border:solid 1px;">ML No.</td>';                                   
                                    echo '<td width="18%" style="border:solid 1px;">Period of Attachment</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Nationality</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Bank Name</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Account No.</td>';
                                    echo '<td width="18%" style="border:solid 1px;">Sort Code</td>';
                                   echo ' <td width="18%" style="border:solid 1px;">Remarks</td>';
                                   echo '<td width="18%" style="border:solid 1px;">Phone Number</td>';
                                   echo ' </tr>';
                                }


                                 echo ' <tr align="left" style="border:solid 1px;"> ';
                                    echo ' <td width="5%" height="30" style="border:solid 1px;">&nbsp;<font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($i + 1) . '</font></td> ';
                                    echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">' .utf8_decode($masterdata[$mas]->lastname) . ' ' . utf8_decode($masterdata[$mas]->firstname) . ' ' . utf8_decode($masterdata[$mas]->middlename). '</font></td>';
                                    echo '<td width="10%" style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->regnumber).' </font></td>';
                                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;'.  utf8_decode($masterdata[$mas]->level).' Level'.' </font></td>';
                                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->department) .'</font></td>';
                                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">&nbsp;'. utf8_decode($i + 1).'</font></td>';                                    
                                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->startmonth).' - '.utf8_decode($masterdata[$mas]->endmonth)." ". utf8_decode($masterdata[$mas]->attachmentyear).'</font></td>';
                                    echo '<td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->nationality).'</font></td>';                                   
                                   echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->bankname).'</font></td>';
                                   echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'. utf8_decode($masterdata[$mas]->accountnumber).' </font></td>';
                                   echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'.  utf8_decode($masterdata[$mas]->sortcode).'</font></td>';
                                   echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'.  utf8_decode($masterdata[$mas]->remark).'</font></td>';
                                   echo ' <td style="border:solid 1px;"><font size="2" face="Arial, Helvetica, sans-serif">'.  utf8_decode($masterdata[$mas]->phonenumber).'</font></td>';

                               echo ' </tr>';
                            
                    

                $i = $i + 1;
                $fac = $masterdata[$mas]->facultyname;
                $dept = $masterdata[$mas]->department;
                
                
            }
           echo '</table>';
                    echo '</td>';
               echo ' </tr>';
           echo ' </table>';

            
            
                unset($_SESSION['selectedsession']);
               
        } else {
            //echo 'am here';exit;
            redirect(site_url('login'));
        }
    }

    public function printmasterlist() {
        //echo 'am here';exit;
        if ($this->session->userdata("admin_isloggedin")) {
            //echo 'am here';exit;
            $this->_basicmasterbysessionlistinitialize();
            $this->data['active'] = "masterlist";
            //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
            //$this->data['sessiondata'] = $this->_getallsessionname();
            //prepare the masterlist data ready to be exported to excel
            $i = 0;
            $printdata;
            $data = $_SESSION['printmasterlistinfo'];
            //$data = $this->session->userdata('printmasterlistinfo');
            if ($data) {
                foreach ($data as $value) {

                    $printdata[$i][0] = strip_quotes($value->regnumber);
                    $printdata[$i][1] = $i + 1;
                    $printdata[$i][2] = strip_quotes($value->lastname);
                    $printdata[$i][3] = strip_quotes($value->firstname);
                    $printdata[$i][4] = strip_quotes($value->middlename);
                    $printdata[$i][5] = strip_quotes($value->facultyname);
                    $printdata[$i][6] = strip_quotes($value->department);
                    $printdata[$i][7] = strip_quotes($value->level);
                    $printdata[$i][8] = strip_quotes($value->phonenumber);
                    $printdata[$i][9] = strip_quotes($value->bankname);
                    $printdata[$i][10] = strip_quotes($value->accountnumber);
                    $printdata[$i][11] = strip_quotes($value->sortcode);
                    $printdata[$i][12] = strip_quotes($value->session);
                    $printdata[$i][13] = strip_quotes($value->startmonth);
                    $printdata[$i][14] = strip_quotes($value->endmonth);
                    $printdata[$i][15] = strip_quotes($value->attachmentyear);
                    $printdata[$i][16] = strip_quotes($value->nationality);
                    $printdata[$i][17] = strip_quotes($value->remark);
                    $i = $i + 1;
                }
                $this->data['printdata'] = $printdata;
            }
            // print_r($printdata);exit;
            // $returnvalue = $this->processmasterlistexport($exportdata);
            // if ($returnvalue) {
            unset($_SESSION['printmasterlistinfo']);
            // $this->session->unset_userdata('printmasterlistinfo');
            //$this->session->set_flashdata('success', 'Export process Completed');
            // redirect(site_url('Administrator/masterlist/exportmasterlist'));
            // }
            //echo 'am here';exit;

            $this->load->view('administrator/print_page', $this->data);
        } else {
            //  echo 'am here exit';exit;
            redirect(site_url('login'));
        }
    }

    public function processmasterlist() {

        if ($this->session->userdata("admin_isloggedin")) {

            $activesession = '';
            $activesessionid = '';
            $this->_basicprocessmasterlistinitialize();
            
            $this->data['active'] = "masterlist";
            
            //get faculty
            $this->data['facultydata'] = $this->_getfaculty();
            //get department
            $this->data['departmentdata'] = $this->_getdepartment();

            $this->data['sessiondata'] = $this->_getactivatedsession();


            if ($this->data['sessiondata']) {
                foreach ($this->data['sessiondata'] as $row) {
                    $activesession = $row->sessionname;
                    $activesessionid = $row->sessionid;
                }
            } else {
                $this->session->set_flashdata('error', 'No Activated Session, Please activate a Session or Call  Administrator, If Problem Persists ');
                redirect(site_url('Administrator/masterlist/uploadmasterlist'));
            }
            $validationRules = $this->masterlistmodel->_rules;

            $this->form_validation->set_rules($validationRules);
//echo $this->form_validation->run().'  jjfjjf';exit;

            $newmasterlistno = 0;
            $masterlistno = $this->_getmasterlistnumber($activesessionid);
            // print_r($placementno);exit;
            if (!$masterlistno) {

                $this->session->set_flashdata('error', 'No Masterlist Number Initialized, Please Call  Administrator, If Problem Persists ');
                redirect(site_url('Administrator/masterlist/uploadmasterlist'));
            }
            if ($this->form_validation->run() == true) {
                $data = $this->masterlistmodel->array_from_post(array('facultyid', 'department', 'startmonth', 'endmonth', 'attachmentyear', 'session'));


                //check if a masterlist is already uploaded
                if ($_SESSION['uploadedfile'] != '') {
                    $now = 'now()';
                    $uploadedmasterlsit = $_SESSION['uploadedfile'];
                    $cellindex = 0;
                    $rowindex = 1;
                    $converteddata = array();
                    //convert to indexed array
                    for ($controw = 2; $controw <= count($uploadedmasterlsit); $controw++) {

                        foreach ($uploadedmasterlsit[$controw] as $value) {
                            if ($value != null)
                                $converteddata[$rowindex][$cellindex] = $value;
                            else
                                $converteddata[$rowindex][$cellindex] = '';
                            $cellindex = $cellindex + 1;
                        }
                        $cellindex = 0;
                        $rowindex = $rowindex + 1;
                    }

                    $uploadedmasterlsit = $converteddata;

                    //print_r($uploadedmasterlsit);exit;
                    //iterate through the uploaded masterlist
                    for ($row = 1; $row <= count($uploadedmasterlsit); $row++) {
                        $value = 0;
                        //check if student is already in masterlist table
                        $studentinfo = $this->_checkstudentinmasterlist($uploadedmasterlsit[$row][0]);
                        //if student info is found in masterlist then perform the following
                        // operations
                        if ($studentinfo) {
                            //check if the student have neva been mobilized and update
                            if ($studentinfo->masterlistnumber == '' && $studentinfo->session == '' &&
                                    $studentinfo->periodattachment == '' && $studentinfo->mobilizationstatus == 'not mobilized') {

                                //if execution get here it means the student's data was inserted
                                //into the master list table when student data is captured during registration
                                // therefore, the masterlist table need to be updated and student will be mobilized
                                //get the last updated masterlist number
                                $masterlistno = $this->_getmasterlistnumber($activesessionid);
                                //increment plcementno
                                $newmasterlistno = intval($masterlistno->masterlistnumber) + 1;
                                $updatedata = array(
                                    'masterlistnumber' => $newmasterlistno,
                                    'session' => $activesession,
                                    'sessionid' => $activesessionid,
                                    'startmonth' => $data['startmonth'],
                                    'endmonth' => $data['endmonth'],
                                    'attachmentyear' => $data['attachmentyear'],
                                    'remark' => $uploadedmasterlsit[$row][4],
                                    'mobilizationstatus' => 'mobilized');

                                $this->db->set($updatedata);
                                $this->db->where('regnumber', trim($uploadedmasterlsit[$row][0]));
                                $value = $this->db->update('tbl_master_list');

                                //check if update was successfull and update masterlistno
                                if ($value != 0) {
                                    $this->db->where('sessionid', $activesessionid);
                                    $this->db->update('tbl_master_placement_number', array(
                                        'masterlistnumber' => $newmasterlistno));
                                }
                            } else {
                                //if the execution gets here, it means the student
                                //was previously mobilized and is remobilized
                                //but first check if the student has already been mobilized for the current session
                                //if the student is already mobilized for the curent session skip the student
                                $curentsessionmobilizationstatus = $this->_checkstudentinmasterlistforcurrentsession($uploadedmasterlsit[$row][0], $activesessionid);

                                if ($curentsessionmobilizationstatus) {
                                    //update masterlist table
                                    $this->db->set('datemodified', $now, false);
                                    $updatedata = array(
                                        'session' => $activesession,
                                        'sessionid' => $activesessionid,
                                        'startmonth' => $data['startmonth'],
                                        'endmonth' => $data['endmonth'],
                                        'attachmentyear' => $data['attachmentyear'],
                                        'remark' => $uploadedmasterlsit[$row][4],
                                        'mobilizationstatus' => 'mobilized');

                                    $this->db->set($updatedata);
                                    $this->db->where('regnumber', trim($uploadedmasterlsit[$row][0]));
                                    $value = $this->db->update('tbl_master_list');

                                    //update student table
                                    $this->db->set('datemodified', $now, false);
                                    $updatestudentdata = array(
                                        'facultyid' => $data['facultyid'],
                                        'department' => $data['department'],
                                        'level' => '',
                                        'lastname' => $uploadedmasterlsit[$row][1],
                                        'firstname' => $uploadedmasterlsit[$row][2],
                                        'middlename' => $uploadedmasterlsit[$row][3],
                                        'nationality' => '',
                                        'bankname' => '',
                                        'accountnumber' => '',
                                        'sortcode' => '',
                                        'phonenumber' => ''
                                    );
                                    $this->db->set($updatestudentdata);
                                    $this->db->where('regnumber', trim($uploadedmasterlsit[$row][0]));
                                    $value = $this->db->update('tbl_student');
                                }
                                //else continue execution
                                //get previous mobilization
                                $previousstudentinfo = $this->_previousstudentmasterlist($uploadedmasterlsit[$row][0], $activesessionid);
                                if ($previousstudentinfo) {

                                    $masterlistno = $this->_getmasterlistnumber($activesessionid);
                                    //increment plcementno
                                    $newmasterlistno = intval($masterlistno->masterlistnumber) + 1;
                                    $this->db->set('datecreated', $now, false);
                                    $this->db->set('datemodified', $now, false);
                                    $insertdata = array(
                                        'masterlistid' => $this->masterlistmodel->generate_unique_id(),
                                        'masterlistnumber' => $newmasterlistno,
                                        'studentid' => $previousstudentinfo->studentid,
                                        'regnumber' => $previousstudentinfo->regnumber,
                                        'session' => $activesession,
                                        'sessionid' => $activesessionid,
                                        'startmonth' => $data['startmonth'],
                                        'endmonth' => $data['endmonth'],
                                        'attachmentyear' => $data['attachmentyear'],
                                        'remark' => $uploadedmasterlsit[$row][4],
                                        'mobilizationstatus' => 'mobilized'
                                    );

                                    $this->db->insert('tbl_master_list', $insertdata);
                                    $value = $this->db->insert_id();

                                    //check if insertion was successfull and update masterlistno
                                    if ($value != 0) {
                                        $this->db->where('sessionid', $activesessionid);
                                        $this->db->update('tbl_master_placement_number', array(
                                            'masterlistnumber' => $newmasterlistno));
                                    }
                                }
                            }
                        } else {
                            //if execution gets here it means the student's data have not been capture earlier
                            //thus since insertion into the masterlist table has higher priority, then its should
                            //mobilize student and add the student data into  tbl_student table
                            $masterlistno = $this->_getmasterlistnumber($activesessionid);
                            //increment plcementno
                            $newmasterlistno = intval($masterlistno->masterlistnumber) + 1;

                            $uniquestudentid = $this->masterlistmodel->generate_unique_id();
                            $this->db->set('datecreated', $now, false);
                            $this->db->set('datemodified', $now, false);
                            $insertdata = array(
                                'studentid' => $uniquestudentid,
                                'studentimg' => '',
                                'regnumber' => $uploadedmasterlsit[$row][0],
                                'password' => md5($uploadedmasterlsit[$row][0]),
                                'facultyid' => $data['facultyid'],
                                'department' => $data['department'],
                                'level' => '',
                                'lastname' => $uploadedmasterlsit[$row][1],
                                'firstname' => $uploadedmasterlsit[$row][2],
                                'middlename' => $uploadedmasterlsit[$row][3],
                                'gender' => '',
                                'nationality' => '',
                                'bankname' => '',
                                'accountnumber' => '',
                                'sortcode' => '',
                                'bloodgroup' => '',
                                'phonenumber' => ''
                            );

                            $this->db->insert('tbl_student', $insertdata);
                            $value = $this->db->insert_id();

                            //insert into the masterlist table

                            $this->db->set('datecreated', $now, false);
                            $this->db->set('datemodified', $now, false);
                            $insertdata = array(
                                'masterlistid' => $this->masterlistmodel->generate_unique_id(),
                                'masterlistnumber' => $newmasterlistno,
                                'studentid' => $uniquestudentid,
                                'regnumber' => $uploadedmasterlsit[$row][0],
                                'session' => $activesession,
                                'sessionid' => $activesessionid,
                                'startmonth' => $data['startmonth'],
                                'endmonth' => $data['endmonth'],
                                'attachmentyear' => $data['attachmentyear'],
                                'remark' => $uploadedmasterlsit[$row][4],
                                'mobilizationstatus' => 'mobilized'
                            );

                            $this->db->insert('tbl_master_list', $insertdata);
                            $value = $this->db->insert_id();

                            if ($value != 0) {
                                $this->db->where('sessionid', $activesessionid);
                                $this->db->update('tbl_master_placement_number', array(
                                    'masterlistnumber' => $newmasterlistno));
                            }
                        }
                    }

                    if ($value != 0) {
                        //unset the session holding the uploaded data
                        unset($_SESSION['uploadedfile']);

                        $this->session->set_flashdata('success', 'Master list is uploaded and saved successfully ');
                        redirect(site_url('Administrator/masterlist'));
                    } else {
                        unset($_SESSION['uploadedfile']);

                        $this->session->set_flashdata('error', 'The masterlist for this students has already been uploaded, Cross check and if problem persists, contact administrator ');
                        redirect(site_url('Administrator/masterlist'));
                    }
                    //unset the session holding the uploaded data
                    unset($_SESSION['uploadedfile']);
                }
            }


            $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function _checkstudentinmasterlist($regnumber) {
        $studentdata = 0;
        $query = $this->db->get_where('tbl_master_list', array('regnumber' => $regnumber));
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $studentdata = $row;
            }
        }
        return $studentdata;
    }

    public function _previousstudentmasterlist($regnumber, $session) {
        $studentdata = 0;
        $this->db->select();
        $this->db->where('sessionid !=', $session);
        $this->db->where('regnumber =', $regnumber);
        $query = $this->db->get('tbl_master_list');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $studentdata = $row;
            }
        }
        return $studentdata;
    }

    public function _checkstudentinmasterlistforcurrentsession($regnumber, $session) {
        $studentdata = 0;
        $this->db->select();
        $this->db->where('sessionid =', $session);
        $this->db->where('regnumber =', $regnumber);
        $query = $this->db->get('tbl_master_list');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $studentdata = $row;
            }
        }
        return $studentdata;
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

    public function _handlemasterlistupload($filename) {
        // echo 'am here oo';exit;
        if (!empty($_FILES['masterlistfile']['name'])) {
//echo $_FILES['masterlistfile']['type'];exit;
            $config = array(
                'upload_path' => './resources/masterlistfile/',
                'allowed_types' => 'jpg|png|jpeg|gif|xls',
                'max_size' => '1500',
                'overwrite' => TRUE,
                'file_name' => $filename . '.xls',
                'remove_spaces' => TRUE
            );
            $this->load->library('upload', $config, 'excel_object');

            if (!$this->excel_object->do_upload('masterlistfile')) {
                // var_dump($this->excel_object->data());exit;
                $this->session->set_flashdata('error', $this->excel_object->display_errors());
                redirect(site_url('Administrator/masterlist/uploadmasterlist'));
            } else {
                $upload_data = $this->excel_object->data();
                //  echo $upload_data['file_name'].' djdj';exit;
                return $upload_data['file_name'];
            }
        } else {
            return null;
        }
    }

    public function _basicuploadmasterlistinitialize() {

        $this->data['subview'] = 'Administrator/upload_masterlist_page';
        $this->data['pageheading'] = 'Upload Qualified List';
        $this->data['uploaddata'] = '';


        $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->masterlistmodel->uploadmasterlistmodel('tbl_master_list', 'masterlistid', 'id DESC');
    }

    public function _basicprocessmasterlistinitialize() {

        $this->data['subview'] = 'Administrator/upload_masterlist_page';
        $this->data['pageheading'] = 'Upload Master List';
        $this->data['uploaddata'] = '';


        $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->masterlistmodel->processmasterlistmodel('tbl_master_list', 'masterlistid', 'id DESC');
    }

    public function _basicmasterbysessionlistinitialize() {

        $this->data['subview'] = 'Administrator/export_masterlist_page';
        $this->data['pageheading'] = 'Export Master List';
        $this->data['uploaddata'] = '';


        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->masterlistmodel->masterlistbysessionmodel('tbl_master_list', 'masterlistid', 'id DESC');
    }

     public function _basicstudentbysessionlistinitialize() {

        $this->data['subview'] = 'Administrator/export_studentlist_page';
        $this->data['pageheading'] = 'Export Mobilized Student List';
        $this->data['uploaddata'] = '';


        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->masterlistmodel->masterlistbysessionmodel('tbl_master_list', 'masterlistid', 'id DESC');
    }

    public function _basicprintmasterlistinitialize() {

        $this->data['subview'] = 'Administrator/print_masterlist_page';
        $this->data['pageheading'] = 'Print Master List';
        $this->data['uploaddata'] = '';


        //$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';
        //initialize the sessionmodel with database parameter
        $this->masterlistmodel->printmasterlistmodel('tbl_master_list', 'masterlistid', 'id DESC');
    }

    public function processmasterlistexport($exportdata) {


        $currentsession = $exportdata[0][13];
        $duration = $exportdata[0][14] . '-' . $exportdata[0][15] . ' ' . $exportdata[0][16];
        $this->excel->setActiveSheetIndex(0);
//name the worksheet
        $this->excel->getActiveSheet()->setTitle('masterlist worksheet');

        $this->excel->getActiveSheet()->setCellValue('A1', "NNAMDI AZIKIWE UNIVERSITY, AWKA " . "\n" . " STUDENTS INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES) UNIT" . "\n" . " MASTER LIST FOR " . $currentsession . " SIWES PROGRAMME (" . $duration.")");
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
                $value = "Faculty of " . $exportdata[$i][6] ."\n". "Department of " . $exportdata[$i][7];
                $this->excel->getActiveSheet()->setCellValue('A' . $row, $value);
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(14);
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->mergeCells('A' . $row.':'.'L'.($row+1));
                $this->excel->getActiveSheet()->getStyle('A' . $row.':'.'L'.($row+1))->getAlignment()->setWrapText(true);


                $row =  $row +2;

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
                $this->excel->getActiveSheet()->setCellValue('D' . $row, 'Year of Study');
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('E' . $row, 'Course of Study');
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('F' . $row, 'ML No');
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('G' . $row, 'Period of Attachment');
                $this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('H' . $row, 'Nationality');
                $this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('H' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('I' . $row, 'Bank Name');
                $this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('I' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('J' . $row, 'Account No');
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('K' . $row, 'Sort Code');
                $this->excel->getActiveSheet()->getStyle('K' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('K' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('K' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('L' . $row, 'Remarks');
                $this->excel->getActiveSheet()->getStyle('L' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('L' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('L' . $row)->getAlignment()->setWrapText(true);


                $row =  $row +1;
                }

                $this->excel->getActiveSheet()->setCellValue('A' . $row, $exportdata[$i][0]);
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('B' . $row, $exportdata[$i][3] . ' ' . $exportdata[$i][4] . ' ' . $exportdata[$i][5]);
                $this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('C' . $row, $exportdata[$i][1]);
                $this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('D' . $row, $exportdata[$i][8] . ' Level');
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('E' . $row, $exportdata[$i][7]);
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('F' . $row, $exportdata[$i][2]);
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('G' . $row, $exportdata[$i][14] . '-' . $exportdata[$i][15] . ' ' . $exportdata[$i][16]);
                $this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('H' . $row, $exportdata[$i][17]);
                $this->excel->getActiveSheet()->getStyle('H' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('I' . $row, $exportdata[$i][10]);
                $this->excel->getActiveSheet()->getStyle('I' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('J' . $row, " ".$exportdata[$i][11]);
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('K' . $row, " ".$exportdata[$i][12]);
                $this->excel->getActiveSheet()->getStyle('K' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('L' . $row, $exportdata[$i][18]);
                $this->excel->getActiveSheet()->getStyle('L' . $row)->getAlignment()->setWrapText(true);

                $fac = $exportdata[$i][6];
                $dept = $exportdata[$i][7];
                $row =  $row +1;
}
$xls_filename = 'export_' . date('Y-m-d') . '.xls'; // Define Excel (.xls) file name
//$filename='just_some_random_name.xls'; //save our workbook as this file name
header('Content-Type: application/vnd.ms-excel'); //mime type
header('Content-Disposition: attachment;filename="'.$xls_filename.'"'); //tell browser what's the file name
header('Cache-Control: max-age=0'); //no cache

//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
//force user to download the Excel file without writing it to server's HD
$objWriter->save('php://output');


}

    public function processmobilizedstudentlistexport($exportdata) {


        $currentsession = $exportdata[0][13];
        $duration = $exportdata[0][14] . '-' . $exportdata[0][15] . ' ' . $exportdata[0][16];
        $this->excel->setActiveSheetIndex(0);
//name the worksheet
        $this->excel->getActiveSheet()->setTitle('masterlist worksheet');

        $this->excel->getActiveSheet()->setCellValue('A1', "NNAMDI AZIKIWE UNIVERSITY, AWKA " . "\n" . " STUDENTS INDUSTRIAL WORK EXPERIENCE SCHEME (SIWES) UNIT" . "\n" . " MASTER LIST FOR " . $currentsession . " SIWES PROGRAMME (" . $duration.")");
//change the font size
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);

//make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);




//merge cell A1 until D1
        $this->excel->getActiveSheet()->mergeCells('A1:M5');
        $this->excel->getActiveSheet()->getStyle('A1:M5')->getAlignment()->setWrapText(true);
//set aligment to center for that merged cell (A1 to D1)
        $this->excel->getActiveSheet()->getStyle('A1:M5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $row = 6;
        $value = '';
        $dept = '';
        $fac = '';

        for ($i = 0; $i < count($exportdata); $i++) {

            if (($fac != $exportdata[$i][6]) || ($dept != $exportdata[$i][7])) {
                $value = "Faculty of " . $exportdata[$i][6] ."\n". "Department of " . $exportdata[$i][7];
                $this->excel->getActiveSheet()->setCellValue('A' . $row, $value);
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(14);
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->mergeCells('A' . $row.':'.'L'.($row+1));
                $this->excel->getActiveSheet()->getStyle('A' . $row.':'.'L'.($row+1))->getAlignment()->setWrapText(true);


                $row =  $row +2;

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
                $this->excel->getActiveSheet()->setCellValue('D' . $row, 'Year of Study');
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('E' . $row, 'Course of Study');
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('F' . $row, 'ML No');
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('G' . $row, 'Period of Attachment');
                $this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('H' . $row, 'Nationality');
                $this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('H' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('I' . $row, 'Phone Number');
                $this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('I' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('J' . $row, 'Bank Name');
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('K' . $row, 'Account No');
                $this->excel->getActiveSheet()->getStyle('K' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('K' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('K' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('L' . $row, 'Sort Code');
                $this->excel->getActiveSheet()->getStyle('L' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('L' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('L' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('M' . $row, 'Remarks');
                $this->excel->getActiveSheet()->getStyle('M' . $row)->getFont()->setSize(12);
                $this->excel->getActiveSheet()->getStyle('M' . $row)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('M' . $row)->getAlignment()->setWrapText(true);


                $row =  $row +1;
                }

                $this->excel->getActiveSheet()->setCellValue('A' . $row, $exportdata[$i][0]);
                $this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('B' . $row, $exportdata[$i][3] . ' ' . $exportdata[$i][4] . ' ' . $exportdata[$i][5]);
                $this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('C' . $row, $exportdata[$i][1]);
                $this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('D' . $row, $exportdata[$i][8] . ' Level');
                $this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('E' . $row, $exportdata[$i][7]);
                $this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('F' . $row, $exportdata[$i][2]);
                $this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('G' . $row, $exportdata[$i][14] . '-' . $exportdata[$i][15] . ' ' . $exportdata[$i][16]);
                $this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('H' . $row, $exportdata[$i][17]);
                $this->excel->getActiveSheet()->getStyle('H' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('I' . $row, $exportdata[$i][9]);
                $this->excel->getActiveSheet()->getStyle('I' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('J' . $row, " ".$exportdata[$i][10]);
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('J' . $row, " ".$exportdata[$i][11]);
                $this->excel->getActiveSheet()->getStyle('J' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('K' . $row, " ".$exportdata[$i][12]);
                $this->excel->getActiveSheet()->getStyle('K' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->setCellValue('L' . $row, $exportdata[$i][18]);
                $this->excel->getActiveSheet()->getStyle('L' . $row)->getAlignment()->setWrapText(true);

                $fac = $exportdata[$i][6];
                $dept = $exportdata[$i][7];
                $row =  $row +1;
}
$xls_filename = 'export_' . date('Y-m-d') . '.xls'; // Define Excel (.xls) file name
//$filename='just_some_random_name.xls'; //save our workbook as this file name
header('Content-Type: application/vnd.ms-excel'); //mime type
header('Content-Disposition: attachment;filename="'.$xls_filename.'"'); //tell browser what's the file name
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
if ($query->num_rows() > 0)
$sessiondata = $query->result();

return $sessiondata;
}

public function _getmasterlistnumber($sessionid) {
$masterlistno = 0;
$query = $this->db->get_where('tbl_master_placement_number', array('sessionid' => $sessionid));
if ($query->num_rows()) {
foreach ($query->result() as $row) {
$masterlistno = $row;
}
}
return $masterlistno;
}

public function _getmasterlistbysession($session) {
$masterlist = array();
$index = 0;
$this->db->select();
$this->db->from('tbl_faculty');
$this->db->order_by('tbl_faculty.facultyname asc');
$query = $this->db->get();

if ($query->num_rows()) {
foreach($query->result() as $outerrow){
   $this->db->select();
$this->db->from('tbl_department');
$this->db->where('tbl_department.facultyid', $outerrow->facultyid);
$this->db->order_by('tbl_department.departmentname asc');
$innerquery = $this->db->get();
if ($innerquery->num_rows()) {
   foreach($innerquery->result() as $innerrow){
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
    foreach($innermostquery->result() as $innermostrow){
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

public function _getmasterlistbyparameter($session) {
$printmasterlist = array();
$index = 0;
$this->db->select();
$this->db->from('tbl_faculty');
$this->db->order_by('tbl_faculty.facultyname asc');
$query = $this->db->get();

if ($query->num_rows()) {
foreach($query->result() as $outerrow){
   $this->db->select();
$this->db->from('tbl_department');
$this->db->where('tbl_department.facultyid', $outerrow->facultyid);
$this->db->order_by('tbl_department.departmentname asc');
$innerquery = $this->db->get();
if ($innerquery->num_rows()) {
   foreach($innerquery->result() as $innerrow){
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
    foreach($innermostquery->result() as $innermostrow){
        $printmasterlist[$index] = $innermostrow;
        $index = $index + 1;
    }

}
   }
}
}
//$masterlist = $query->result();
}
return $printmasterlist;
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

}

?>
