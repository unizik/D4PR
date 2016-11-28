      <div class="row">
                          <div class="col-md-12">
                              <h1 class="page-header">
                                  Add Drug <small></small>
                              </h1>
                          </div>
                      </div>
                       <!-- /. ROW  -->
                    <div class="row">
                      <div class="col-lg-12">
                          <div class="panel panel-default">
                              <div class="panel-heading">
                                  Drug Details
                              </div>
                              <div class="panel-body">
                                  <div class="row">
                                      <div class="alert alert-danger" id="err_msg" style="display:none"></div>
                                      <div class="col-lg-3">

                                      </div>
                                      <div class="col-lg-9">
                                           <form name="" action=""  >
                                            <div class="form-group col-md-5">
                                                <label>Drug Name Convention</label>
                                                <select name='namingconvention' id="namingconvention" class="form-control" required="requred">
                                                    <option value=''>Select Name Convention</option>
                                                    <option value='Common Name'>Common Name</option>
                                                    <option value='IUPAC'>IUPAC</option>
                                                    <option value='INI'>INI</option>
                                                </select>
                                                <br>
                                                <label>Number of Drugs to add</label>
                                                <input type='number'  name='noofdrug' min='1' max='50' class="form-control" placeholder="Number of drugs" id='num' required='required'>
                                            </div>
                                              <div class="form-group col-md-9">
                                              <button type="button" onclick="loadamount();" class="btn btn-primary">Continue</button>
                                              </div>
                                          </form>
                                      </div>
                                      <!-- /.col-lg-6 (nested) -->

                                      <!-- /.col-lg-6 (nested) -->
                                  </div>
                                  <!-- /.row (nested) -->
                                  
                                   <?php if($this->session->flashdata('success')) echo get_success($this->session->flashdata('success')); ?>
                                       <?php if($this->session->flashdata('error')) echo get_error($this->session->flashdata('error')); ?>                                       
                            
                                  <div class="table-responsive">
                                      <form action="<?php echo site_url('users/drug/adddrug') ?>" method="POST" >
                                  
                                  
                                      <table class="table table-striped table-bordered table-hover" >
                                          <thead>
                                              <tr>
                                                  <th>Drug Name</th>
                                                  <th>GBCR Ligand</th>
                                                  <th>Kinetic Inhibitor</th>
                                                  <th>Nuclear Receptor Ligand</th>
                                                  <th>Ion Channel Modulator</th>
                                                  <th>Protease Inhibitor</th>
                                                  <th>Enzyme Inhibitor</th>
                                                 
                                              </tr>
                                          </thead>
                                          <tbody id="body">
                                              
                                          </tbody>
                                      </table>
                                 <div class="form-group col-md-9">
                                              <button type="submit"  class="btn btn-primary">Submit</button>
                                              </div>
                                  <form>
                                </div>

                              </div>
                              <!-- /.panel-body -->
                          </div>
                          <!-- /.panel -->
                      </div>
                      <!-- /.col-lg-12 -->

            </div>
        </div>
        <!-- /. ROW  -->

        
        <script type="text/javascript">

function loadamount(){
   //var err_msg = "";
    var htmldata ="";
  var num = document.getElementById('num').value.trim(); 
  var naming = document.getElementById('namingconvention').value;
 if(naming === ''){
     document.getElementById('err_msg').innerHTML = "Selct a Naming Covention!"; 
      document.getElementById('err_msg').style.display = 'block';
 }else if(num===''){  
      
      document.getElementById('err_msg').innerHTML = "Invalid number given!"; 
      document.getElementById('err_msg').style.display = 'block';
    document.getElementById('num').focus();
  }else if(parseInt(num) < 1 || parseInt(num) > 50){      
      document.getElementById('err_msg').innerHTML = "Please Enter Number between 1 and 50!";
      document.getElementById('err_msg').style.display = 'block';
    document.getElementById('num').focus();
   }else{
      
      for(x = 1; x <= num; x++ ){
     
    htmldata = htmldata+"<tr><td>"+
"<input class='form-control' name='drugname"+x+"'  size='30' type='text' required='required'></td>"+
                                                  "<td>"+
                                                    
                                                      "<input class='form-control' id='gl"+x+"' name='gl"+x+"' style='width:100px' type='number' required='required' onchange='validate(this.value,response"+x+")'>"+
                                                  
                                                  "</td>"+
                                                  "<td>"+
                                                    
                                                     "<input class='form-control' id='gl"+x+"' name='ki"+x+"' style='width:100px' type='number' required='required' onchange='validate(this.value,response"+x+")'>"+
                                              
                                                  "</td>"+
                                                  "<td>"+
                                                   
                                                      "<input class='form-control' id='gl"+x+"' name='nrl"+x+"' style='width:100px' type='number'required='required' onchange='validate(this.value,response"+x+")'>"+
                                                  
                                                 "</td>"+
                                                  "<td>"+
                                                    
                                                      "<input class='form-control' id='gl"+x+"' name='icm"+x+"' style='width:100px' type='number' required='required' onchange='validate(this.value,response"+x+")'>"+
                                                  
                                                 "</td>"+
                                                  "<td>"+
                                                    
                                                      "<input class='form-control' id='gl"+x+"' name='pi"+x+"' style='width:100px' type='number' required='required' onchange='validate(this.value,response"+x+")'>"+
                                                  
                                                 "</td>"+
                                                  "<td>"+
                                                   
                                                      "<input class='form-control' id='gl"+x+"' name='ei"+x+"' style='width:100px' type='number' required='required' onchange='validate(this.value,response"+x+")'>"+
                                                  
                                                 "</td>"+
                                               
                                              "</tr>";
                                        
                                                      }
                                                      
                                                      document.getElementById('body').innerHTML = htmldata;
  
  }
  }
  
  function validate(num,response){ 
  
 if(num===''){        
      alert("Invalid number"); 
      //document.getElementById('err_msg').style.display = 'block';
    //document.getElementById('num').focus();
  }
}
 
</script>