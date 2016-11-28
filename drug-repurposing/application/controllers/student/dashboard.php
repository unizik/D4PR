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
class Dashboard extends Student_Controller{
    //put your code here

    function  __construct() {
        parent::__construct();
        //$this->load->model('schoolconfigmodel');
        // $this->_set_nav_menus();
    }

    /**
     * this is a default method
     * that loads once the login attempt
     * is successful
     * it passes the subview page to the layout_main page
     */
     public function index() {
    // echo $this->data['login_session'].'dash';exit;
        if($this->session->userdata("student_isloggedin")){
         $this->data['subview'] = 'student/dashboard_page';
         $this->data['active'] = "dashboard";
        $this->load->view('template/layout_main', $this->data);
        } else {
            //echo 'am here';exit;
           redirect(site_url('login'));
        }
        
    }


     /**
     * this is a score configuration method
     * used to create the number of forms for
      * the CAs and Exam
     */
    public function schoolconfig($schoolid = null) {
        if($schoolid == null) show_error ('Invalid School ID - Contact Admin if Issue Persists');

        $this->data['help_text'] = 'Here you can configure/setup your school application, You need to fill out all the required field. However, your school Id cannot be edited'.
                '. Also, you can upload your school logo and Principal signature here.';


        $validationRules = $this->schoolconfigmodel->_rules;
        $this->form_validation->set_rules($validationRules);

        if($this->form_validation->run() == true){
            //after posting form below code is executed

            $data = $this->schoolconfigmodel->array_from_post(array('schoolwebsite','schoolname','schooladdress','principalname'));

            $logo_name = $this->_handleLogoUpload($schoolid);
            if($logo_name != null) $data['schoollogo'] = $logo_name;

            $signature_name = $this->_handleSignatureUpload($schoolid);
            if($signature_name != null) $data['principalsignature'] = $signature_name;

            // send update to database
            $isUpdate = $this->schoolconfigmodel->save_update( $data, $schoolid);
          
             if($isUpdate){
                 $this->session->set_flashdata('updatesuccess','Changes updated' );
                 redirect(site_url('administrator/dashboard/schoolconfig/'.$schoolid), 'refresh');
             }else{
                 $this->session->set_flashdata('error','An Error Occurred During Update - Kindly retry Operation' );
                 redirect(site_url('administrator/dashboard/schoolconfig/'.$schoolid));
             }

        }

        $this->data['page_level_scripts'] = '<script src="' . base_url('resources/bootstrap/js/bootstrap-fileupload.js') . '"></script>';
        $this->data['page_level_styles'] = '<link href="' . base_url('resources/bootstrap/css/bootstrap-fileupload.min.css') .'" rel="stylesheet">';
         $this->data['subview'] = 'Administrator/school_config_page';

        $this->load->view('template/layout_main', $this->data);
    }

    public function _handleSignatureUpload($filename){

        if(!empty($_FILES['principalsignature']['name'])){

             $config = array(
                     'upload_path' => './resources/principalsignature/',
                     'allowed_types' => 'jpg|png|jpeg|gif',
                     'max_size' => '1500',
                     'overwrite' => TRUE,
                     'file_name' => $filename . '.jpg',
                     'remove_spaces' => TRUE
                 );
            $this->load->library('upload', $config, 'signature_object');

            if (!$this->signature_object->do_upload('principalsignature')){
                $this->session->set_flashdata('error', $this->signature_object->display_errors() . '[Signature]');
                redirect(site_url('administrator/dashboard/schoolconfig/'.$filename));
            }
            else{
                $upload_data = $this->signature_object->data();
                return $upload_data['file_name'];
            }

        }else{
            return null;
        }

    }

    public function _handleLogoUpload($filename){
        if(!empty($_FILES['schoollogo']['name'])){

             $config = array(
                     'upload_path' => './resources/schoollogo/',
                     'allowed_types' => 'jpg|png|jpeg|gif',
                     'max_size' => '1500',
                     'overwrite' => TRUE,
                     'file_name' => $filename . '.jpg',
                     'remove_spaces' => TRUE
                 );
            $this->load->library('upload', $config, 'logo_object');

            if (!$this->logo_object->do_upload('schoollogo')){
                $this->session->set_flashdata('error', $this->logo_object->display_errors() . '[Logo]');
                redirect(site_url('administrator/dashboard/schoolconfig/'.$filename));
            }
            else{
                $upload_data = $this->logo_object->data();
                return $upload_data['file_name'];
            }

        }else{
            return null;
        }
    }

     public function _set_nav_menus(){
        $this->data['navmenus'] = array(

         array(
                'menuname' => 'Create Session',
                'url' => 'administrator/session/'
                ));

    }
}
?>
