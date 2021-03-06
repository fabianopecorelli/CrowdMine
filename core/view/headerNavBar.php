<?php
    if(isset($user)) {
        $fullname = getUserFullName($user);
        $profileImg = getUserImageBig($user);
    }
?>

<nav class="navbar navbar-default" id="navbar">
    <div class="container-fluid">
        <div class="navbar-collapse collapse in" style="background:#2b2b33;">
            <ul class="nav navbar-nav navbar-mobile" style="overflow: hidden;">
                <li>
                    <button type="button" class="sidebar-toggle" style="margin-left: -20px;">
                        <img style="height: 60px;width: auto; " src="<?php echo STYLE_DIR ?>/img/Favicon_3.png" />
                    </button>
                </li>
                <li class="logo">
                    <span class="navbar-brand"><span class="highlight"><?php echo $fullname; ?></span></span>
                </li>
                <li>
                    <?php if(isset($user)){?>
                        <button type="button" class="navbar-toggle">
                            <img class="profile-img" src="<?php echo $profileImg; ?>">
                        </button>
                    <?php }else{?>
                        <button type="button" class="navbar-toggle" style="color:white">
                            <i class="fa fa-bars"></i>
                        </button>
                    <?php }?>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-left col-md-8 col-lg-9" style="padding: 0;">

                <li>
                    <a href="<?php echo DOMINIO_SITO; ?>" class="hidden-lg hidden-md hidden-xs" style="margin-left: -40px;">
                        <img style="height: 70px; max-width: 100%; " src="<?php echo STYLE_DIR ?>img/Favicon_3.png" />
                    </a>
                    <a href="<?php echo DOMINIO_SITO; ?>" class="hidden-sm">
                        <img class="img-responsive" style=" max-width: 100%" src="<?php echo STYLE_DIR ?>img/Logo_Crowdmine_3.png" />
                    </a>
                </li>
                <li class="navbar-search col-lg-9">

                    <input  class="search-form col-md-8" id="search" type="text" placeholder="Cerca annunci di lavoro..." > <!--style="height: 60%; padding-right: 0px; padding-left: 5px"-->

                    <button class="btn-search" id="search-button" onclick="inserciValore()"><i class="fa fa-search"></i></button>

                    <div class="col-md-2" style="padding-right: 0px; padding-left: 5px">
                        <!-- BARRA DI RICERCA->FORM->SCRITTA AVANZATE-->
                        <div class="col-md-4" style="padding-right: 10px; padding-left: 10px; padding-top: 7px">
                            <a href="<?php echo DOMINIO_SITO;?>/ricercaAnnuncio" class="text-center " >Avanzata</a>
                        </div>
                    </div>
                    <form id="form-ricercaAnnunci" method="post" action="<?php echo DOMINIO_SITO;?>/cercaAnnunciNavBar">
                        <input type="hidden" value="" id="value-to-send" name="form-text">
                    </form>
                </li>
            </ul>
            <?php
                if(isset($user)){
                    ?>
                <ul class="nav navbar-nav navbar-right">

                <li class="dropdown notification warning no-drop">

                    <a href="<?php echo DOMINIO_SITO;?>/messaging" class="dropdown-toggle" data-toggle="dropdown" onclick="location.href='<?php echo DOMINIO_SITO;?>/messaging';">
                        <div class="icon"><i class="fa fa-comments" aria-hidden="true"></i></div>
                        <div class="title">Messaggi</div>
                        <div class="count" style="display:none" id="mess"></div>
                    </a>
                   <div class="dropdown-menu">
                        <ul>
                            <li class="dropdown-header">Messaggi</li>
                            <li>
                                <a href="#">
                                    <span class="badge badge-warning pull-right">10</span>
                                    <div class="message">
                                        <img class="profile" src="https://placehold.it/100x100">
                                        <div class="content">
                                            <div class="title">"Payment Confirmation.."</div>
                                            <div class="description">Alan Anderson</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="badge badge-warning pull-right">5</span>
                                    <div class="message">
                                        <img class="profile" src="https://placehold.it/100x100">
                                        <div class="content">
                                            <div class="title">"Hello World"</div>
                                            <div class="description">Marco Harmon</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="badge badge-warning pull-right">2</span>
                                    <div class="message">
                                        <img class="profile" src="https://placehold.it/100x100">
                                        <div class="content">
                                            <div class="title">"Order Confirmation.."</div>
                                            <div class="description">Brenda Lawson</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="dropdown-footer">
                                <a href="#">Visualizza Tutti <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="dropdown notification danger">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <div class="icon"><i class="fa fa-bell" aria-hidden="true"></i></div>
                        <div class="title">Notifiche</div>
                        <div id="notification-count" class="count" style="display:none">0</div>
                    </a>
                    <div class="dropdown-menu">
                        <ul>
                            <li class="dropdown-header">Notifiche</li>
                            <li>
                                <ul id="lista-notifiche">
                                </ul>
                            </li>
                            <li class="dropdown-footer">
                                <a href="<?php echo DOMINIO_SITO;?>/notificheUtente">Visualizza Tutte <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="dropdown profile">
                    <a href="<?php echo DOMINIO_SITO;?>/ProfiloPersonale" class="dropdown-toggle" data-toggle="dropdown" onclick="location.href='<?php echo DOMINIO_SITO;?>/ProfiloPersonale';">
                        <img class="profile-img" src="<?php echo $profileImg; ?>">
                        <div class="title">Profile</div>
                    </a>
                    <div class="dropdown-menu">
                        <div class="profile-info">
                            <h4 class="username"><?php echo $fullname; ?></h4>
                        </div>
                        <ul class="action">
                            <?php if($user->getRuolo() == RuoloUtente::AMMINISTRATORE || $user->getRuolo()==RuoloUtente::MODERATORE){?>
                            <li>
                                <a href="<?php echo DOMINIO_SITO?>/UtentiSegnalati">
                                    Gestione del sito
                                </a>
                            </li>
                            <?php } ?>
                            <li>
                                <a href="<?php echo DOMINIO_SITO;?>/ProfiloPersonale">
                                    Profilo
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo DOMINIO_SITO;?>/annunciPreferiti">
                                    I miei preferiti
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo DOMINIO_SITO;?>/ProfiloPersonale#tab2">
                                    Impostazioni
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo DOMINIO_SITO;?>/logout">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <?php
                }else{
                    ?>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?php echo DOMINIO_SITO;?>/auth"><span class="fa fa-sign-out"></span> Accedi</a></li>
                </ul>
            <?php
                }
                ?>
        </div>
    </div>
</nav>
