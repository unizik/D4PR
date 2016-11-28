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
class setup extends Admin_Controller{
    //put your code here

    function  __construct() {
        parent::__construct();
        $this->load->model('setupmodel');
       //  $this->_set_nav_menus();
    }

   

    /**
     * this is a default method
     * this loads the department page
     * it passes the subview page to the layout_main page
     */
     public function addlocation() {
         if($this->session->userdata("admin_isloggedin")){
             //get list of faculties

        $this->data['state'] = $this->_getstates();

        $value = 0;
        $this->_basiclocationinitialize();

        $validationRules = $this->setupmodel->_rules;

        $this->form_validation->set_rules($validationRules);

        $this->data['active'] = "location";
        //get list of created term group
       // $department_data = $this->departmentmodel->get_all();

        //save it in this data so that it can be displayed in the table
        $this->data['createdlocation'] = $this->_getlocation();
        if ($this->form_validation->run() == true) {
            //after posting form below code is executed
            $now = 'now()';
            $data = $this->setupmodel->array_from_post(array('state','location','locationid'));

            //if the department id isset then update else insert
            if (($data['locationid']) != '') {
                $resultdata = $this->_getsinglelocation($data['location']);
                if($resultdata != ''){
                     $this->session->set_flashdata('error', 'A location with '.$data['location']. ' already Exits' );
                redirect(site_url('administrator/setup/addlocation/'));
                }else{
                $value = $this->setupmodel->save_update(array(
                            'state' => $data['state'],'location' => $data['location']), $data['locationid']);
                }
            } else {
                 $resultdata = $this->_getsinglelocation($data['location']);
                if($resultdata != ''){
                    $this->session->set_flashdata('error', 'A location with '.$data['location']. ' already Exits' );
                redirect(site_url('administrator/setup/addlocation/'));
                }else{
                $this->db->set('datecreated', $now, false);
                $this->db->set('datemodified', $now, false);
                $insertdata = array(
                    'locationid' => $this->setupmodel->generate_unique_id(),
                    'state' => $data['state'],
                    'location' => $data['location'],
                );

                $this->db->insert('tbl_location', $insertdata);
                $value = $this->db->insert_id();
            }
            if ($value != 0) {
                $this->session->set_flashdata('success', 'Changes successful');
                redirect(site_url('administrator/setup/addlocation/'));
            } else {
                $this->session->set_flashdata('error', 'An Error Occurred During Update - Kindly retry Operation');
                redirect(site_url('administrator/setup/addlocation/'));
            }
            //reset departmentid
            $this->data['locationid'] = '';
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
    public function updatelocation($locationid = null) {
        if($this->session->userdata("admin_isloggedin")){
          //get all faculties  
        $this->data['state'] = $this->_getstates();

        $this->_basiclocationinitialize();
        $this->data['locationid'] = $locationid;

        $this->data['active'] = "location";
        
        //get list of created department
        //$department_data = $this->departmentmodel->get_all();
        $this->data['createdlocation'] =   $this->_getlocation();

        //get the selected termgroupdata based on the termgroupid
        $onelocationdata = $this->setupmodel->get_where(array('locationid' => $locationid), true);
        // $valid_user = $this->termmodel->get_all();
        $this->data['location'] = $onelocationdata;


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
    public function deletelocation($locationid = null) {
        //intialize tbl_department table database parameter
        if($this->session->userdata("admin_isloggedin")){

        $this->data['state'] = $this->_getstates();

        $this->data['active'] = "location";
        
        $this->_basiclocationinitialize();

        $deleterecord = $this->setupmodel->delete($locationid);

        $locationdata = $this->setupmodel->get_all();
        $this->data['createdlocation'] = $locationdata;
$this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/plugins/dataTables/dataTables.bootstrap.css') . '" rel="stylesheet">';

        if ($deleterecord) {
            $this->session->set_flashdata('success', 'Delete successful');
            redirect(site_url('administrator/setup/addlocation/' ));
        } else {
            $this->department->set_flashdata('error', 'An Error Occurred During Delete - Kindly retry Operation');
            redirect(site_url('administrator/setup/addlocation/'));
        }

        $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function spe1uploaddeadline() {
        if($this->session->userdata("admin_isloggedin")){
         
$this->data['active'] = "deadline";
         $this->_basicspe1uploaddeadlineinitialize();


         $this->data['spe1deadlinesetup'] = $this->_getspe1deadlinesetupsetup();
        $validationRules = $this->setupmodel->_rules;

        $this->form_validation->set_rules($validationRules);

        if ($this->form_validation->run() == true) {
$now = 'now()';
            $data = $this->setupmodel->array_from_post(array('spe1uploaddurationid','uploadstart','uploadend'));

                if (($data['spe1uploaddurationid']) != '') {

                    $splitdate = split('/', $data['uploadstart']);
            if(count($splitdate) == 3){
            $data['uploadstart'] = $splitdate[2].'-'.$splitdate[0].'-'.$splitdate[1];
            }
            $splitdate = split('/', $data['uploadend']);
            if(count($splitdate) == 3){
            $data['uploadend'] = $splitdate[2].'-'.$splitdate[0].'-'.$splitdate[1];
            }

                    $value = $this->setupmodel->save_update(array(
                                'uploadstart' => $data['uploadstart'], 'uploadend' => $data['uploadend']), $data['spe1uploaddurationid']);
                } else {

                    $splitdate = split('/', $data['uploadstart']);
            if(count($splitdate) == 3){
            $data['uploadstart'] = $splitdate[2].'-'.$splitdate[0].'-'.$splitdate[1];
            }
            $splitdate = split('/', $data['uploadend']);
            if(count($splitdate) == 3){
            $data['uploadend'] = $splitdate[2].'-'.$splitdate[0].'-'.$splitdate[1];
            }
                        $this->db->set('datecreated', $now, false);
                        $this->db->set('datemodified', $now, false);
                        $insertdata = array(
                            'spe1uploaddurationid' => $this->setupmodel->generate_unique_id(),                           
                            'uploadstart' => $data['uploadstart'],
                            'uploadend' => $data['uploadend']


                        );

                        $this->db->insert('tbl_spe1_upload_duration', $insertdata);
                        $value = $this->db->insert_id();

                }
                if ($value != 0) {
                    $this->session->set_flashdata('success', 'Changes successful');
                   redirect(site_url('administrator/setup/spe1uploaddeadline'));
                } else {
                    $this->session->set_flashdata('error', 'An Error Occurred During Update - Kindly retry Operation');
                   redirect(site_url('administrator/setup/spe1uploaddeadline'));
                }

        }
        $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function _basicspe1uploaddeadlineinitialize() {

        $this->data['subview'] = 'Administrator/spe1_deadline_page';
       $this->data['pageheading'] = 'Spe1 Submission Deadline';
        $this->data['spe1uploaddurationid'] = '';


        //initialize the sessionmodel with database parameter
        $this->setupmodel->addspe1deadlinesetup('tbl_spe1_upload_duration', 'spe1uploaddurationid', 'id DESC');
    }
     public function _basiclocationinitialize() {

         $this->data['subview'] = 'administrator/location_page';
         $this->data['pageheading'] = 'Create Locations';
        $this->data['locationid'] = '';


        //initialize the departmentmodel with database parameter
        $this->setupmodel->addlocationmodel('tbl_location', 'locationid', 'id DESC');
    }


     public function _getfaculty() {
 $query = $this->db->get('tbl_faculty');
        $facultydata = $query->result();

        return $facultydata;
    }

     public function _getlocation() {
        //get list of created term group
        //joining terms and termgroup tables
        $this->db->select('*');
        $this->db->from('tbl_location');
        $this->db->order_by('state asc');
        $this->db->order_by('location asc');

        $query = $this->db->get();
        //save it in this data so that it can be displayed in the table
       $result = $query->result();

        return $result;
    }

     public function _getsinglelocation($location){
          $data = '';
       $query =  $this->db->get_where('tbl_location', array('location' => $location));

        if($query->num_rows() > 0){
       foreach($query->result() as $row){
           $data = $row;
        }
       }
       return $data;
    }
    public function _getspe1deadlinesetupsetup() {
        $data = '';
        $this->db->select();
        //$this->db->where('schoolid =', $schoolid);
        $query = $this->db->get('tbl_spe1_upload_duration');

        if ($query->num_rows() > 0) {
            foreach($query->result() as $row)
            $data = $row;
        }
        return $data;
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
