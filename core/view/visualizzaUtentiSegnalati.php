<?php
include_once CONTROL_DIR  .  'UtentiSegnalatiControl.php';

if (isset($_SESSION["utentiSegnalati"])){
    $utentiSegnalati = unserialize($_SESSION['utentiSegnalati']);
    unset($_SESSION["utentiSegnalati"]);
} else {
    header("Location: " . DOMINIO_SITO . "/visualizzaUtentiSegnalati");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Flat Admin V.3 - Free flat-design bootstrap administrator templates</title>

    <link rel="stylesheet" href="<?php echo STYLE_DIR; ?>bootstrap\css\bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo STYLE_DIR; ?>assets\css\vendor.css">
    <link rel="stylesheet" type="text/css" href="<?php echo STYLE_DIR; ?>assets\css\flat-admin.css">
    <link rel="stylesheet" type="text/css" href="<?php echo STYLE_DIR; ?>assets\css\rating.css">
    <link rel="stylesheet" type="text/css" href="<?php echo STYLE_DIR; ?>plugins\toastr\toastr.css">

    <!-- Theme -->
    <link rel="stylesheet" type="text/css" href="<?php echo STYLE_DIR; ?>assets\css\theme\blue-sky.css">
    <link rel="stylesheet" type="text/css" href="<?php echo STYLE_DIR; ?>assets\css\theme\blue.css">
    <link rel="stylesheet" type="text/css" href="<?php echo STYLE_DIR; ?>assets\css\theme\red.css">
    <link rel="stylesheet" type="text/css" href="<?php echo STYLE_DIR; ?>assets\css\theme\yellow.css">
    <style>
        .navbar-collapse.in
        {
            overflow-y: hidden;
        }
    </style>
</head>
<body>
<div class="app app-default">

    <?php include "asidePannelloModeratore.php" ?>

    <script type="text/ng-template" id="sidebar-dropdown.tpl.html">
        <div class="dropdown-background">
            <div class="bg"></div>
        </div>
        <div class="dropdown-container">
            {{list}}
        </div>
    </script>
    <div class="app-container">
        <nav class="navbar navbar-default" id="navbar">
            <div class="container-fluid">
                <div class="navbar-collapse collapse in">
                    <ul class="nav navbar-nav navbar-mobile">
                        <li>
                            <button type="button" class="sidebar-toggle">
                                <i class="fa fa-bars"></i>
                            </button>
                        </li>
                        <li class="logo">
                            <a class="navbar-brand" href="#"><span class="highlight">Moderatore</span> </a>
                        </li>
                        <li>
                            <button type="button" class="navbar-toggle">
                                <img class="profile-img" src="<?php echo STYLE_DIR; ?>assets\images\profile.png">
                            </button>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-left">
                        <li class="navbar-search hidden-sm">
                            <input id="search" type="text" placeholder="Search..">
                            <button class="btn-search"><i class="fa fa-search"></i></button>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown notification warning">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <div class="icon"><i class="fa fa-comments" aria-hidden="true"></i></div>
                                <div class="title">Unread Messages</div>
                                <div class="count">99</div>
                            </a>
                            <div class="dropdown-menu">
                                <ul>
                                    <li class="dropdown-header">Message</li>
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
                                        <a href="#">View All <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="dropdown notification danger">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <div class="icon"><i class="fa fa-bell" aria-hidden="true"></i></div>
                                <div class="title">System Notifications</div>
                                <div class="count">10</div>
                            </a>
                            <div class="dropdown-menu">
                                <ul>
                                    <li class="dropdown-header">Notification</li>
                                    <li>
                                        <a href="#">
                                            <span class="badge badge-danger pull-right">8</span>
                                            <div class="message">
                                                <div class="content">
                                                    <div class="title">New Order</div>
                                                    <div class="description">$400 total</div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <span class="badge badge-danger pull-right">14</span>
                                            Inbox
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <span class="badge badge-danger pull-right">5</span>
                                            Issues Report
                                        </a>
                                    </li>
                                    <li class="dropdown-footer">
                                        <a href="#">View All <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="dropdown profile">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img class="profile-img" src="<?php echo STYLE_DIR; ?>assets\images\profile.png">
                                <div class="title">Profile</div>
                            </a>
                            <div class="dropdown-menu">
                                <div class="profile-info">
                                    <h4 class="username">Scott White</h4>
                                </div>
                                <ul class="action">
                                    <li>
                                        <a href="#">
                                            Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <span class="badge badge-danger pull-right">5</span>
                                            My Inbox
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            Setting
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-body app-heading">
                        <div class="app-title">
                            <div class="title"><span
                                    class="highlight">Utenti Segnalati</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        Lista Utenti Segnalati
                    </div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-xs-12">
								<div class="media social-post">
									<div class="media-left">
										<a href="#">
<!--											<img src="<?php /*echo STYLE_DIR; */?>assets\images\profile.png"/>
-->										</a>
									</div>
                                    <div class="section">
                                        <?php
                                        $start = true;
                                        foreach ($utentiSegnalati as $utente) {
                                            if ($start){
                                            $start=false;
                                            ?>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-xs-12">
                                                    <div class="media social-post">
                                                        <div class="media-left">
                                                            <a href="#">
                                                                <img src="<?php echo STYLE_DIR; ?>assets\images\profile.png"/>
                                                            </a>
                                                        </div>
                                                        <div class="section">
                                                            <div class="section-body">
                                                                <div class="media-body">
                                                                    <div class="pull-left">
                                                                        <div class="media-heading">
                                                                            <h4 class="title"><?php echo $utente->getNome() ?></h4>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-12 col-md-12 col-xs-12 pull-left" style="padding:0px">
                                                                        <div class="media-action">
                                                                            <form action="ConfermaSegnalazioneUtenteControl" method="post">
                                                                                <button class="btn btn-link" name="idUtenteConferma" type="button" data-toggle="modal" data-target="#myModal" value="<?php echo $utente->getId()?>""><i class="fa fa-check"></i> Conferma</button>
                                                                            </form>
                                                                            <form action="ConfermaSegnalazioneUtenteControl" method="post">
                                                                                <button class="btn btn-link" name="idUtenteElimina" type="submit" value="<?php echo $utente->getId()?>"><i class="fa fa-close"></i> Elimina</button>
                                                                            </form>
                                                                            <form action="ConfermaSegnalazioneUtenteControl" method="post">
                                                                                <button class="btn btn-link" name="idUtenteConferma" type="submit" value="<?php echo $utente->getId()?>"><i class="fa fa-check-circle"></i> invia all'amministratore</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        else {
                                        ?>
                                        <div class="row">
                                            <div class="overlined-row">
                                                <div class="media social-post">
                                                    <div class="media-left">
                                                        <a href="#">
                                                            <img src="<?php echo STYLE_DIR; ?>assets\images\profile.png"/>
                                                        </a>
                                                    </div>
                                                    <div class="section">
                                                        <div class="section-body">
                                                            <div class="media-body">
                                                                <div class="pull-left">
                                                                    <div class="media-heading">
                                                                        <h4 class="title"><?php echo $utente->getNome() ?></h4>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12 col-md-12 col-xs-12 pull-left" style="padding:0px">
                                                                    <div class="media-action">
                                                                        <form action="ConfermaSegnalazioneUtenteControl" method="post">
                                                                            <button class="btn btn-link" name="idUtenteConferma" type="button" data-toggle="modal" data-target="#myModal" value="<?php echo $utente->getId()?>""><i class="fa fa-check"></i> Conferma</button>
                                                                        </form>
                                                                        <form action="ConfermaSegnalazioneUtenteControl" method="post">
                                                                            <button class="btn btn-link" name="idUtenteElimina" type="submit" value="<?php echo $utente->getId()?>"><i class="fa fa-close"></i> Elimina</button>
                                                                        </form>
                                                                        <form action="ConfermaSegnalazioneUtenteControl" method="post">
                                                                            <button class="btn btn-link" name="idUtenteConferma" type="submit" value="<?php echo $utente->getId()?>"><i class="fa fa-check-circle"></i> invia all'amministratore</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                        <?php } }  ?>

					</div>
				</div>
			</div>
		</div>
    </div>
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <h4 class="modal-title">Conferma Ban</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Sicuro di voler bannare l'utente?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-sm btn-success">Ban Utente</button>
                                </div>
                            </div>
                        </div>
                    </div>

    <script type="text/javascript" src="<?php echo STYLE_DIR; ?>assets\js\vendor.js"></script>
    <script type="text/javascript" src="<?php echo STYLE_DIR; ?>assets\js\app.js"></script>
    <script type="text/javascript" src="<?php echo STYLE_DIR; ?>plugins\toastr\toastr.js"></script>

    <script>
		/*evidenzio segnalazioni nella barra laterale*/
		$("#segnalazioni").toggleClass("active");
		$('[data-toggle="tooltip"]').tooltip(); 
	</script>

    <?php
    if (isset($_SESSION['toast-type']) && isset($_SESSION['toast-message'])) {
        ?>
        <script>
            toastr["<?php echo $_SESSION['toast-type'] ?>"]("<?php echo $_SESSION['toast-message'] ?>");
        </script>
        <?php
        unset($_SESSION['toast-type']);
        unset($_SESSION['toast-message']);
    }
    ?>

</body>
</html>