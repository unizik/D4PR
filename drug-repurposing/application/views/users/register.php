<div class="row">
                    <div class="col-md-12">
                        <h1 class="page-header">
                            Registration <small>Form</small>
                        </h1>
                    </div>
                </div> 
                 <!-- /. ROW  -->
              <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Basic Information
                        </div>
                        <div class="panel-body">
                            <?php if($this->session->flashdata('success')) echo get_success($this->session->flashdata('success')); ?>
                                       <?php if($this->session->flashdata('error')) echo get_error($this->session->flashdata('error')); ?>                                       
                            
                            <div class="row">
                                <div class="col-lg-1">
                                    
                                </div>
                                <div class="col-lg-11">
                                    <form action="<?php echo site_url('users/registration/addregistration') ?>" method="POST" >
                                        <input type="hidden" name="userid" value="<?php if ($this->data['userid'] != '') echo $this->data['user']->userid; ?>" />
                                        <input type="hidden" name="federatedemail" value="<?php if ($this->data['userid'] != '') echo $federatedemail; ?>" />
                                        <div class="form-group col-md-9">
                                            <label>First Name</label>
                                            <input class="form-control" name="firstname" placeholder="Enter text" required="required" value="<?php if ($this->data['userid'] != '')
                                               echo $this->data['user']->firstname; ?>" />
                                        </div>
                                         <div class="form-group col-md-9">
                                            <label>Last Name</label>
                                            <input class="form-control" name="lastname" placeholder="Enter text" required="required" value="<?php if ($this->data['userid'] != '')
                                               echo $this->data['user']->lastname; ?>" />
                                        </div>
                                         <div class="form-group col-md-9">
                                            <label>Middle Name</label>
                                            <input class="form-control" name="middlename" placeholder="Enter text"  value="<?php if ($this->data['userid'] != '')
                                               echo $this->data['user']->middlename; ?>" />
                                        </div>
                                         <div class="form-group col-md-9">
                                            <label>Alternative Email</label>
                                            <input class="form-control" name="personalemail" placeholder="Enter text" required="required" value="<?php if ($this->data['userid'] != '')
                                               echo $this->data['user']->personalemail; ?>" />
                                        </div>
                                         <div class="form-group col-md-9">
                                            <label>Institution</label>
                                            <input class="form-control"  name="institution" placeholder="Enter text" required="required" value="<?php if ($this->data['userid'] != '')
                                               echo $this->data['user']->institution; ?>" />
                                        </div> <div class="form-group col-md-9">
                                            <label>ORCID Id</label>
                                            <input class="form-control" name="orcidid" placeholder="Enter text" required="required" value="<?php if ($this->data['userid'] != '')
                                               echo $this->data['user']->orcidid; ?>" />
                                        </div>
                                         <div class="form-group col-md-9">
                                            <label>Field of Study</label>
                                            <input class="form-control" name="fieldofstudy" placeholder="Enter text" required="required" value="<?php if ($this->data['userid'] != '')
                                               echo $this->data['user']->fieldofstudy; ?>" />
                                        </div>
                                         <div class="form-group col-md-9">
                                            <label>Area of Specialization</label>
                                            <input class="form-control" name="areaofspecialization" placeholder="Enter text" required="required" value="<?php if ($this->data['userid'] != '')
                                               echo $this->data['user']->areaofspecialization; ?>" />
                                        </div>
                                         <div class="form-group col-md-9">
                                            <label>Phone Number</label>
                                            <input class="form-control" name="phonenumber" placeholder="Enter text" required="required" value="<?php if ($this->data['userid'] != '')
                                               echo $this->data['user']->phonenumber; ?>" />
                                        </div>
                                         <div class="form-group col-md-9">
                                            <label>Address</label>
                                            <input class="form-control" name="address" placeholder="Enter text" required="required" value="<?php if ($this->data['userid'] != '')
                                               echo $this->data['user']->address; ?>" />
                                        </div>
                                         
                                        <div class="form-group col-md-9">
                                        <input type="submit" class="btn btn-primary" value="Submit and Continue" />
                                        
                                        </div>
                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                                
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>