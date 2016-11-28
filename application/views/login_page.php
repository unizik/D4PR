<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
<title>::COOU SIWES PORTAL::</title>

<link href="<?php echo base_url('resources/assets/css/bootstrap.min.css')?>" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('resources/assets/css/orhneal-theme.css')?>" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('resources/assets/css/styles.css')?>" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('resources/assets/css/icons.css')?>" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>

<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/charts/sparkline.min.js')?>js/plugins/charts/sparkline.min.js"></script>

<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/uniform.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/select2.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/inputmask.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/autosize.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/inputlimit.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/listbox.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/multiselect.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/validate.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/tags.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/switch.min.js')?>"></script>

<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/uploader/plupload.full.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/uploader/plupload.queue.min.js')?>"></script>

<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/wysihtml5/wysihtml5.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/forms/wysihtml5/toolbar.js')?>"></script>

<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/interface/daterangepicker.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/interface/fancybox.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/interface/moment.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/interface/jgrowl.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/interface/datatables.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/interface/colorpicker.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/interface/fullcalendar.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/plugins/interface/timepicker.min.js')?>"></script>

<script type="text/javascript" src="<?php echo base_url('resources/assets/js/bootstrap.min.js')?>"></script>
<script type="text/javascript" src="<?php echo base_url('resources/assets/js/application.js')?>"></script>

</head>

<body class="full-width page-condensed">

	<!-- Navbar -->
	<div class="navbar navbar-inverse" role="navigation">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-right">
				<span class="sr-only">Toggle navbar</span>
				<i class="icon-grid3"></i>
			</button>
			<a class="navbar-brand" href="#"><img src="<?php echo base_url('resources/assets/images/uniziklogo3.png')?>" alt="COOU SIWES"></a>
		</div>

		<ul class="nav navbar-nav navbar-right collapse">
                    
			<li><img src="<?php echo base_url('resources/assets/images/siwesportal.png')?>" alt="COOU SIWES"></li>
			
		</ul>
	</div>
	<!-- /navbar -->


	<!-- Login wrapper -->
	<div class="login-wrapper">
    	<form action="<?php echo site_url('login/verifylogin'); ?>" method="post" role="form" >
			
            <?php if (strlen(trim(validation_errors())) > 0)
                                echo get_error(validation_errors()); ?>
                        <?php if($this->session->flashdata('success')) echo get_success($this->session->flashdata('success')); ?>
                        <?php if ($this->session->flashdata('error'))
                                echo get_error($this->session->flashdata('error')); ?>
            <div class="popup-header">
				<a href="#" class="pull-left"><i class="icon-user-plus"></i></a>
				<span class="text-semibold">User Login</span>
				<div class="btn-group pull-right">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-cogs"></i></a>
                    <ul class="dropdown-menu icons-right dropdown-menu-right">
						<li><a href="#"><i class="icon-people"></i> Change user</a></li>
						<li><a href="#"><i class="icon-info"></i> Forgot password?</a></li>
						<li><a href="#"><i class="icon-support"></i> Contact admin</a></li>
						<li><a href="#"><i class="icon-wrench"></i> Settings</a></li>
                    </ul>
				</div>
			</div>
			<div class="well">
                            
				<div class="form-group has-feedback">
					<label>Username</label>
					<input type="text" name="username" id="username" class="form-control" placeholder="Username">
					<i class="icon-users form-control-feedback"></i>
				</div>

				<div class="form-group has-feedback">
					<label>Password</label>
					<input type="password" name="password" id="password" class="form-control" placeholder="Password">
					<i class="icon-lock form-control-feedback"></i>
				</div>

				<div class="row form-actions">
					<div class="col-xs-6">
						<div class="checkbox checkbox-success">
						<label>
							<input type="checkbox" class="styled">
							Remember me
						</label>
						</div>
					</div>

					<div class="col-xs-6">
						<button type="submit" class="btn btn-warning pull-right"><i class="icon-menu2"></i> Sign in</button>
					</div>
				</div>
                                
			</div>
    	</form>
	</div>  
	<!-- /login wrapper -->


    <?php $this->load->view('template/layout_footer'); ?>


</body>
</html>