
        <?php
        // put your code here
class ajax_student_location extends Student_Controller{

     function  __construct() {
        parent::__construct();
       // $this->load->model('studentmodel');
        $this->data['dept'] = '';
    }
    public function index(){
    $state = strtolower($_GET["state"]);

          $data = '';        
       $query =  $this->db->get_where('tbl_location', array('state' => $state));
       if($query->num_rows() > 0){

           $data = $query->result();           
           }
           ?>

                                <select  id="locationid" name="locationid" class="validate[required] form-control">
                                    <option value="">Select Location</option>                                    
                                    <?php

                                    if($this->data != ''){
                                    //echo ' hjjii';exit;
                                    foreach($data as  $value){
                echo '<option value="'.$value->locationid.'"  >'.$value->location.'</option>';

                                    }
                                    }
                ?>


                                </select>
                            
           
     <?php   
    }

}

        ?>
    
