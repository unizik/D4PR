
        <?php
        // put your code here
class read_excel_file extends User_Controller{

     function  __construct() {
        parent::__construct();
        //$this->load->model('studentmodel');
        $this->load->library('Excel/reader');
        $this->data['dept'] = '';
    }
    public function index(){
    $facultyid = strtolower($_GET["facultyid"]);

          $data = '';        
       $query =  $this->db->get_where('tbl_department', array('facultyid' => $facultyid));
       if($query->num_rows() > 0){

           $data = $query->result();           
           }
           ?>

                                <select  id="department" name="department" class="validate[required] form-control">
                                    <option value="">Select Department</option>                                    
                                    <?php

                                    if($this->data != ''){
                                    //echo ' hjjii';exit;
                                    foreach($data as  $value){
                echo '<option value="'.$value->departmentname.'"  >'.$value->departmentname.'</option>';

                                    }
                                    }
                ?>


                                </select>
                            
           
     <?php   
    }

}

        ?>
    
