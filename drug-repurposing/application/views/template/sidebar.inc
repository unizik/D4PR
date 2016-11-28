<nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    
                    <li>
                        <a href="index.html" class=""><i class="fa fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="ui-elements.html"><i class="fa fa-user"></i>Profile</a>
                    </li>
                    <li>
                        <a href="#" class="<?php if(isset($active)){if($active == "drug"){ echo "active-menu";} }?>"><i class="fa fa-ambulance"></i> Drug<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a  href="<?php echo site_url('users/drug') ?>">Add Drug</a>
                            </li>
                            <li>
                                <a href="#">View Drugs</a>
                            </li>
                            
                        </ul>
                    </li>
			<li>
                        <a href="#"><i class="fa fa-file"></i> Project<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                             <li>
                                <a href="#">Initiate Project</a>
                            </li>
                            <li>
                                <a href="#">Start Project</a>
                            </li>
                            <li>
                                <a href="#">View Project</a>
                            </li>
                            <li>
                                <a href="#">Make Publication</a>
                            </li>
                            
                        </ul>
                    </li>		
                    <li>
                        <a href="#"><i class="fa fa-folder"></i> Repository<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="#">Project Repository</a>
                            </li>
                            <li>
                                <a href="#">Publication Repository</a>
                            </li>
                            
                        </ul>
                    </li>
                    <li>
                        <a href="ui-elements.html"><i class="fa fa-power-off"></i> Sign Out</a>
                    </li>
                </ul>

            </div>

        </nav>
        <!-- /. NAV SIDE  -->
        
        <div id="page-wrapper" >
            <div id="page-inner">