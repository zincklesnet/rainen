<?php
global $ADL_LP;
$adl_legal_pages = (!empty($args['adl_legal_pages'])) ? $args['adl_legal_pages'] : null;
$adl_lp_templates = (!empty($args['adl_lp_templates'])) ? $args['adl_lp_templates'] : null;
$homeTabData = array_merge(get_option('adl_lp_general', array()), get_option('adl_lp_social', array()), get_option('adl_lp_popup', array()), get_option('adl_lp_misc', array()), get_option('adl_lp_cookie', array()));
?>
<section style="background:#efefe9;">
    <div class="container-fluid" style="margin: 0; padding: 0">
        <div class="row">
            <div class="board">
                <!--board-inner starts ::: Tab Menu-->
                <div class="board-inner">
                    <ul class="nav nav-tabs" id="myTab">
                        <div class="liner"></div>
                        <li class="active">
                            <a href="#home" data-toggle="tab">
                              <span class="round-tabs one">
                                      <i class="glyphicon glyphicon-cog"></i>
                              </span>
                            </a>
                            <p style="position: absolute; left: 29%; bottom: -8px;">General Settings</p>

                        </li>

                        <li><a href="#createLegalPage" data-toggle="tab">
                             <span class="round-tabs two">
                                 <i class="glyphicon glyphicon-plus-sign"></i>
                             </span>
                            </a>
                            <p style="position: absolute; left: 33%; bottom: -8px;">Add New Page</p>

                        </li>
                        <li><a href="#allPages" data-toggle="tab" >
                             <span class="round-tabs three">
                                  <i class="glyphicon glyphicon-list"></i>
                             </span>
                            </a>
                            <p style="position: absolute; left: 33%; bottom: -8px;">All Legal Pages</p>
                        </li>

                        <li><a href="#editTemplates" data-toggle="tab" >
                             <span class="round-tabs four">
                                  <i class="glyphicon glyphicon-edit"></i>
                             </span>
                            </a>
                            <p style="position: absolute; left: 16%; bottom: -8px;">All Legal Page Templates</p>
                        </li>

                        <li><a href="#Support" data-toggle="tab">
                                 <span class="round-tabs five">
                                      <i class="glyphicon glyphicon-send"></i>
                                 </span>
                             </a>
                            <p style="position: absolute; left: 37%; bottom: -8px;">Get Support</p>

                        </li>

                    </ul>
                </div>   <!--Ends board-inner-->


                <div class="tab-content">

                    <div class="tab-pane fade in active" id="home">
                        <?php $ADL_LP->loadView('settings/tab-content/home', $homeTabData); ?>
                    </div>  <!--ends .tab-pane   #home-->

                    <div class="tab-pane fade" id="createLegalPage">
                        <?php $ADL_LP->loadView('settings/tab-content/create-page'); ?>
                    </div>  <!--ends .tab-pane   #createLegalPage-->


                    <div class="tab-pane fade" id="allPages">
                        <h3 class="head text-center">All Legal Pages</h3>
                        <?php $ADL_LP->loadView('settings/tab-content/list-legal-pages', $adl_legal_pages); ?>

                    </div> <!--ends .tab-pane   #allPages-->

                    <div class="tab-pane fade" id="editTemplates">
                        <h3 class="head text-center">Edit or Delete Page Template</h3>
                        <?php $ADL_LP->loadView('settings/tab-content/create-edit-templates-for-tab', $adl_lp_templates); ?>

                    </div> <!--ends .tab-pane   #editTemplates-->

                    <div class="tab-pane fade" id="Support">
                        <div class="text-center">
                            <i class="img-intro icon-checkmark-circle"></i>
                        </div>
                        <div
                            class="container">
                            <div class="row">
                                <div class="col-md-12 text-center" >
                                    <h3 style="margin-top: 50px;">====== How to use this plugin =======</h3>
                                    <p>Complete plugin usage guideline can be found : <a href="https://aazztech.com/legal-pages-pro-documentation/" target="_blank" title="Documentation of Legal Pages Pro Plugin">Here</a></p><br />

                                    <h3>====== Support Forum ======</h3>
                                    <p>If you need any help, please don't hesitate to post it on our <a href="https://aazztech.com/support" target="_blank">Support Forum</a>.</p><br />
                                </div>
                            </div>
                        </div>
                    </div> <!--ends .tab-pane   #Support-->

                    <div class="clearfix"></div>

                </div> <!--Ends tab-content -->

            </div> <!--    end .board -->

        </div>
    </div>
</section>
