<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of department
 *
 * @author TOCHUKWU
 */
class department extends Admin_Controller{
    //put your code here

    function  __construct() {
        parent::__construct();
        $this->load->model('departmentmodel');
       //  $this->_set_nav_menus();
    }

   

    /**
     * this is a default method
     * this loads the department page
     * it passes the subview page to the layout_main page
     */
     public function adddepartment() {
         if($this->session->userdata("admin_isloggedin")){
             //get list of faculties
             $this->data['active'] = "department";
        $this->data['facultydata'] = $this->_getfaculty();

        $value = 0;
        $this->_basicdepartmentinitialize();

        $validationRules = $this->departmentmodel->_rules;

        $this->form_validation->set_rules($validationRules);

        //get list of created term group
       // $department_data = $this->departmentmodel->get_all();

        //save it in this data so that it can be displayed in the table
        $this->data['createddepartment'] = $this->_getdepartmentinnerjoinfaculty();
        if ($this->form_validation->run() == true) {
            //after posting form below code is executed
            $now = 'now()';
            $data = $this->departmentmodel->array_from_post(array('departmentname','facultyid','departmentid'));

            //if the department id isset then update else insert
            if (($data['departmentid']) != '') {
                $resultdata = $this->_getdepartment($data['departmentname']);
                if($resultdata != ''){
                     $this->session->set_flashdata('error', 'A Department with '.$data['departmentname']. ' already Exits' );
                redirect(site_url('administrator/department/adddepartment/'));
                }else{
                $value = $this->departmentmodel->save_update(array(
                            'departmentname' => $data['departmentname'],'facultyid' => $data['facultyid']), $data['departmentid']);
                }
            } else {
                 $resultdata = $this->_getdepartment($data['departmentname']);
                if($resultdata != ''){
                     $this->session->set_flashdata('error', 'A Department with '.$data['departmentname']. ' already Exits, Try another or Update' );
                redirect(site_url('administrator/department/adddepartment/'));
                }else{
                $this->db->set('datecreated', $now, false);
                $this->db->set('datemodified', $now, false);
                $insertdata = array(
                    'departmentid' => $this->departmentmodel->generate_unique_id(),
                    'departmentname' => $data['departmentname'],
                    'facultyid' => $data['facultyid'],
                );

                $this->db->insert('tbl_department', $insertdata);
                $value = $this->db->insert_id();
            }
            if ($value != 0) {
                $this->session->set_flashdata('success', 'Changes successful');
                redirect(site_url('administrator/department/adddepartment/'));
            } else {
                $this->session->set_flashdata('error', 'An Error Occurred During Update - Kindly retry Operation');
                redirect(site_url('administrator/department/adddepartment/'));
            }
            //reset departmentid
            $this->data['departmentid'] = '';
        }
}
        $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }


    /**
     *
     * @param <type> $departmentid
     * this method updates department based on id
     */
    public function updatedepartment($departmentid = null) {
        if($this->session->userdata("admin_isloggedin")){
          //get all faculties  
        $this->data['facultydata'] = $this->_getfaculty();
        $this->data['active'] = "department";
        $this->_basicdepartmentinitialize();
        $this->data['departmentid'] = $departmentid;

        //get list of created department
        //$department_data = $this->departmentmodel->get_all();
        $this->data['createddepartment'] =   $this->_getdepartmentinnerjoinfaculty();

        //get the selected termgroupdata based on the termgroupid
        $onedepartmentdata = $this->departmentmodel->get_where(array('departmentid' => $departmentid), true);
        // $valid_user = $this->termmodel->get_all();
        $this->data['department'] = $onedepartmentdata;


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
    public function deletedepartment($departmentid = null) {
        //intialize tbl_department table database parameter
        if($this->session->userdata("admin_isloggedin")){

        $this->data['facultydata'] = $this->_getfaculty();
$this->data['active'] = "department";
        $this->_basicdepartmentinitialize();

        $deleterecord = $this->departmentmodel->delete($departmentid);

        $departmentdata = $this->departmentmodel->get_all();
        $this->data['createddepartment'] = $departmentdata;


        if ($deleterecord) {
            $this->session->set_flashdata('success', 'Delete successful');
            redirect(site_url('administrator/department/adddepartment/' ));
        } else {
            $this->department->set_flashdata('error', 'An Error Occurred During Delete - Kindly retry Operation');
            redirect(site_url('administrator/department/adddepartment/'));
        }

        $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }


     public function _basicdepartmentinitialize() {

         $this->data['subview'] = 'administrator/department_page';
         $this->data['pageheading'] = 'Create Department';
        $this->data['departmentid'] = '';


        //initialize the departmentmodel with database parameter
        $this->departmentmodel->adddepartmentmodel('tbl_department', 'departmentid', 'id DESC');
    }


     public function _getfaculty() {
 $query = $this->db->get('tbl_faculty');
        $facultydata = $query->result();

        return $facultydata;
    }

     public function _getdepartmentinnerjoinfaculty() {
        //get list of created term group
        //joining terms and termgroup tables
        $this->db->select('*');
        $this->db->from('tbl_department');
        $this->db->join('tbl_faculty', 'tbl_faculty.facultyid = tbl_department.facultyid','inner');

        $query = $this->db->get();
        //save it in this data so that it can be displayed in the table
       $result = $query->result();

        return $result;
    }

     public function _getdepartment($departmentname){
          $data = '';
       $query =  $this->db->get_where('tbl_department', array('departmentname' => $departmentname));

        if($query->num_rows() > 0){
       foreach($query->result() as $row){
           $data = $row;
        }
       }
       return $data;
    }
}
?>
