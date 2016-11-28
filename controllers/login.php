<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of login
 *
 * @author TOCHUKWU
 */
class Login extends MY_Controller {
    //put your code here
    public function  __construct() {
        parent::__construct();
        $this->load->model('usersmodel');
    }
 public function index(){
     $this->data['userid'] = '';
     $this->session->set_userdata("user_isloggedin", true);
      $this->data['subview'] = 'users/register';
         
        $this->load->view('template/layout_main', $this->data);
   }
   
   public function verifylogin(){
        $valid_student =0;
        $this->data['help_text'] = 'Enter your user name and password, so to gain access into the system';
        
         
/****
       $this->form_validation->set_rules('username', 'Username', 'trim|required');
       $this->form_validation->set_rules('password', 'Password', 'trim|required');

       //if($this->form_validation->run() == true){
           $data = $this->usersmodel->array_from_post(array('username', 'password'));
           $data['password'] = md5($data['password']);
           
           $valid_user = $this->usersmodel->get_where($data, true);

           $query = $this->db->get_where('tbl_student',array('regnumber'=>$data['username'], 'password'=>$data['password']));

           if($query->num_rows() > 0){
               foreach($query->result() as $row){
                   $valid_student = $row;
               }
           }
           if(count($valid_user) == 1){
              // echo 'am hereoo';exit;
                $this->session->set_userdata("user_id", $valid_user->userid);
                $this->session->set_userdata("user_name", $valid_user->username);
                $this->session->set_userdata("last_name", $valid_user->lastname);
                $this->session->set_userdata("first_name", $valid_user->firstname);
                $this->session->set_userdata("user_email", $valid_user->email);
                $this->session->set_userdata("access_level", $valid_user->accesslevel);

                //set the userlogin session detail
                $this->data['login_session'] = $this->session->userdata("user_name");
                //echo $this->data['login_session'];
                if($valid_user->accesslevel == 'admin') {
                    $this->session->set_userdata("admin_isloggedin", true);
                   // echo $this->data['login_session'];exit;
                    redirect(site_url('Administrator/dashboard'));
                }
                else if($valid_user->accesslevel == 'user') {
                    $this->session->set_userdata("user_isloggedin", true);
                   // echo $this->data['login_session'];exit;
                    redirect(site_url('users/dashboard'));
                }
                //else if($user->role == 'edcoperator') redirect(site_url('operatorboard'));
                else show_error("INVALID/UNCERTAIN DATA RECEIVED");
           
                
           }else if($valid_student){

               $this->session->set_userdata("student_id", $valid_student->studentid);
                $this->session->set_userdata("user_name", $valid_student->regnumber);
                $this->session->set_userdata("last_name", $valid_student->lastname);
                $this->session->set_userdata("first_name", $valid_student->firstname);


                //set the userlogin session detail
                $this->data['login_session'] = $this->session->userdata("user_name");
                //echo $this->data['login_session'];

                    $this->session->set_userdata("student_isloggedin", true);
                   // echo $this->data['login_session'];exit;
                    redirect(site_url('student/dashboard'));

       }else{
                $this->session->set_flashdata('error', 'Invalid Username/Password');
                redirect(site_url('login'));
           }
 * ***/
 
       //}

       $this->load->view('login_page', $this->data);
   }

   public function logout() {
        $this->session->unset_userdata('loggedin');
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata("admin_isloggedin");
        $this->session->unset_userdata("user_isloggedin");
        $this->session->unset_userdata('student_id');
        $this->session->unset_userdata('user_name');
        $this->session->unset_userdata("student_isloggedin");
        $this->session->unset_userdata("access_level");
        redirect(site_url('login'));
    }
}
?>
