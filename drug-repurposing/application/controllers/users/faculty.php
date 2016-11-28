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
class faculty extends User_Controller{
    //put your code here

    function  __construct() {
        parent::__construct();
        $this->load->model('facultymodel');
       //  $this->_set_nav_menus();
    }

   

    /**
     * this is a default method
     * this loads the session page
     * it passes the subview page to the layout_main page
     */
     public function addfaculty() {
         if($this->session->userdata("user_isloggedin")){
        $value = 0;
        $this->_basicfacultyinitialize();
$this->data['active'] = "faculty";
        $validationRules = $this->facultymodel->_rules;

        $this->form_validation->set_rules($validationRules);
    
        //get list of created faculty
        $faculty_data = $this->facultymodel->get_all();

        //save it in this data so that it can be displayed in the table
        $this->data['createdfaculty'] = $faculty_data;
        if ($this->form_validation->run() == true) {
            //after posting form below code is executed
            $now = 'now()';
            $data = $this->facultymodel->array_from_post(array('facultyname','facultyid'));

            //if the faculty id isset then update else insert
            if (($data['facultyid']) != '') {
                 //check if faculty with the same name exits
                $resultdata = $this->_getfaculty($data['facultyname']);
                if($resultdata != ''){
                     $this->session->set_flashdata('error', 'A Faculty with '.$data['facultyname']. ' already Exits' );
                redirect(site_url('users/faculty/addfaculty/'));
                }else{
                $value = $this->facultymodel->save_update(array(
                            'facultyname' => $data['facultyname']), $data['facultyid']);
                }
            } else {
                 //check if faculty with the same name exits
                $resultdata = $this->_getfaculty($data['facultyname']);
                if($resultdata != ''){
                     $this->session->set_flashdata('error', 'A Faculty with '.$data['facultyname']. ' already Exits, Try another or Update' );
                redirect(site_url('users/faculty/addfaculty/'));
                }else{
                $this->db->set('datecreated', $now, false);
                $this->db->set('datemodified', $now, false);
                $insertdata = array(
                    'facultyid' => $this->facultymodel->generate_unique_id(),
                    'facultyname' => $data['facultyname'],
                );

                $this->db->insert('tbl_faculty', $insertdata);
                $value = $this->db->insert_id();                             
            }
            }
            if ($value != 0) {
                $this->session->set_flashdata('success', 'Changes successful');
                redirect(site_url('users/faculty/addfaculty/'));
            } else {
                $this->session->set_flashdata('error', 'An Error Occurred During Update - Kindly retry Operation');
                redirect(site_url('users/faculty/addfaculty/'));
            }
            //reset sessionid
            $this->data['facultyid'] = '';
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
            redirect(site_url('users/faculty/addfaculty/' ));
        } else {
            $this->session->set_flashdata('error', 'An Error Occurred During Delete - Kindly retry Operation');
            redirect(site_url('users/faculty/addfaculty/'));
        }

        $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }


     public function _basicfacultyinitialize() {

         $this->data['subview'] = 'users/faculty_page';
         $this->data['pageheading'] = 'Create Faculty';
        $this->data['facultyid'] = '';


        //initialize the sessionmodel with database parameter
        $this->facultymodel->addfacultymodel('tbl_faculty', 'facultyid', 'id DESC');
    }

    public function _getfaculty($facultyname){       
        $data = '';
       $query =  $this->db->get_where('tbl_faculty', array('facultyname' => $facultyname));      
       if($query->num_rows() > 0){                  
       foreach($query->result() as $row){
           $data = $row;
        }
       }
       return  $data;
    }
}
?>
