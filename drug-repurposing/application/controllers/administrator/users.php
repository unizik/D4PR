<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of users
 *
 * @author TOCHUKWU
 */
class users extends Admin_Controller {

    //put your code here

    function __construct() {
        parent::__construct();
        $this->load->model('usersmodel');
        //  $this->_set_nav_menus();
    }

    /**
     * this is a default method
     * this loads the session page
     * it passes the subview page to the layout_main page
     */
    public function addusers() {
         if($this->session->userdata("admin_isloggedin")){
        $value = 0;
        $this->_basicuserinitialize();

        $validationRules = $this->usersmodel->_rules;
        
        $this->form_validation->set_rules($validationRules);
        $this->data['active'] = "user";
        //get list of created users
        $user_data = $this->usersmodel->get_all();

        //save it in this data so that it can be displayed in the table
        $this->data['createduser'] = $user_data;
        if ($this->form_validation->run() == true) {
            //after posting form below code is executed
            $uniqueuserid = $this->usersmodel->generate_unique_id();
            $now = 'now()';
            $data = $this->usersmodel->array_from_post(array('userid', 'lastname', 'firstname', 'middlename', 'username', 'password',
                        'email', 'phonenumber', 'userpassport', 'accesslevel'));
            
            if (($data['userid']) != '') {
                $uniqueuserid = $data['userid'];
            }
            $logo_name = $this->_handlepassportUpload($uniqueuserid);
            if ($logo_name != null)
                $data['userpassport'] = $logo_name;
                else
                    $data['userpassport'] = $data['userid'].'.jpg';
            //if the user id isset then update else insert
            if (($data['userid']) != '') {
                $value = $this->usersmodel->save_update($data, $data['userid']);
            } else {
                $this->db->set('datecreated', $now, false);
                $this->db->set('datemodified', $now, false);
                $insertdata = array(
                    'userid' => $uniqueuserid,
                    'lastname' => $data['lastname'],
                    'firstname' => $data['firstname'],
                    'middlename' => $data['middlename'],
                    'username' => $data['username'],
                    'password' => md5($data['password']),
                    'email' => $data['email'],
                    'phonenumber' => $data['phonenumber'],
                    'userpassport' => $data['userpassport'],
                    'accesslevel' => $data['accesslevel']
                );

                $this->db->insert('tbl_user', $insertdata);
                $value = $this->db->insert_id();
            }

            if ($value != 0) {
                $this->session->set_flashdata('success', 'Changes successful');
                redirect(site_url('administrator/users/addusers/'), 'refresh');
            } else {
                $this->session->set_flashdata('error', 'An Error Occurred During Update - Kindly retry Operation');
                redirect(site_url('Administrator/users/addusers/'), 'refresh');
            }
            //reset sessionid
            $this->data['userid'] = '';
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
    public function updateuser($userid = null) {
        if($this->session->userdata("admin_isloggedin")){
        $this->_basicuserinitialize();
        $this->data['userid'] = $userid;
        $this->data['active'] = "user";
        //get list of created users
        $user_data = $this->usersmodel->get_all();
        $this->data['createduser'] = $user_data;

        //get the selected termgroupdata based on the termgroupid
        $oneuserdata = $this->usersmodel->get_where(array('userid' => $userid), true);
        // $valid_user = $this->termmodel->get_all();
        $this->data['users'] = $oneuserdata;


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
    public function deleteuser($userid = null) {
        //intialize tbl_session table database parameter
       if($this->session->userdata("admin_isloggedin")){
        $this->_basicuserinitialize();
        $this->data['active'] = "user";
        $deleterecord = $this->usersmodel->delete($userid);

        $user_data = $this->usersmodel->get_all();
        $this->data['createduser'] = $user_data;


        if ($deleterecord) {
            $this->session->set_flashdata('success', 'Delete successful');
            redirect(site_url('administrator/users/addusers/'));
        } else {
            $this->session->set_flashdata('error', 'An Error Occurred During Delete - Kindly retry Operation');
            redirect(site_url('Administrator/users/addusers/'));
        }

        $this->load->view('template/layout_main', $this->data);
        } else {
            redirect(site_url('login'));
        }
    }

    public function uploadsignature(){
       if($this->session->userdata("admin_isloggedin")){

           $this->data['subview'] = 'Administrator/upload_signature_page';
        $this->data['pageheading'] = 'Upload Signature';
        //$this->data['userid'] = '';
        $this->data['active'] = "signature";
        $this->data['page_level_scripts'] = '<script src="' . base_url('resources/assets/js/bootstrap-fileupload.js') . '"></script>';
        $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/css/bootstrap-fileupload.min.css') . '" rel="stylesheet">';

        $this->usersmodel->uploadsignaturemodel('tbl_user', 'userid', 'id DESC');

           $validationRules = $this->usersmodel->_rules;
//echo 'am here';exit;
        $this->form_validation->set_rules($validationRules);
        
        if ($this->form_validation->run() == true) {
            //echo 'am here o';exit;
            $this->_handlesignatureupload('directorsignature');
             $this->session->set_flashdata('success', 'Signture Upload Successful');
            redirect(site_url('administrator/users/uploadsignature/'));
       }
       $this->load->view('template/layout_main', $this->data);
       } else {
            redirect(site_url('login'));
        }
    }

    public function _basicuserinitialize() {

        $this->data['subview'] = 'Administrator/create_user_page';
        $this->data['pageheading'] = 'Create Users';
        $this->data['userid'] = '';
        $this->data['page_level_scripts'] = '<script src="' . base_url('resources/assets/js/bootstrap-fileupload.js') . '"></script>';
        $this->data['page_level_styles'] = '<link href="' . base_url('resources/assets/css/bootstrap-fileupload.min.css') . '" rel="stylesheet">';

        //initialize the sessionmodel with database parameter
        $this->usersmodel->addusermodel('tbl_user', 'userid', 'id DESC');
    }

    public function _handlepassportUpload($filename) {
        if (!empty($_FILES['userpassport']['name'])) {

            $config = array(
                'upload_path' => './resources/userimg/',
                'allowed_types' => 'jpg|png|jpeg|gif',
                'max_size' => '1500',
                'overwrite' => TRUE,
                'file_name' => $filename . '.jpg',
                'remove_spaces' => TRUE
            );
            $this->load->library('upload', $config, 'logo_object');

            if (!$this->logo_object->do_upload('userpassport')) {
                $this->session->set_flashdata('error', $this->logo_object->display_errors());
                redirect(site_url('administrator/users/addusers/'));
            } else {
                $upload_data = $this->logo_object->data();
                return $upload_data['file_name'];
            }
        } else {
            return null;
        }
    }

    public function _handlesignatureupload($filename) {
        if (!empty($_FILES['directorsignature']['name'])) {

            $config = array(
                'upload_path' => './resources/directorsignature/',
                'allowed_types' => 'jpg|png|jpeg|gif',
                'max_size' => '1500',
                'overwrite' => TRUE,
                'file_name' => $filename . '.jpg',
                'remove_spaces' => TRUE
            );
            $this->load->library('upload', $config, 'logo_object');

            if (!$this->logo_object->do_upload('directorsignature')) {
                $this->session->set_flashdata('error', $this->logo_object->display_errors());
                redirect(site_url('administrator/users/uploadsignature/'));
            } else {
                $upload_data = $this->logo_object->data();
                return $upload_data['file_name'];
            }
        } else {
            return null;
        }
    }

}

