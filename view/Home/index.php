<?php
	require_once "../../config/conexion.php"; // conexion.php ya contiene session_start()
	if (!isset($_SESSION['user_id'])) {
?>

<!DOCTYPE html> //template Blank-Start UI//
<html>
	<?php require_once("../MainHead/head.php");?>
    <title>DDN_Ticket - Requerimientos TI </title>
</head>  
<body class="with-side-menu">

    <?php require_once("../MainHeader/header.php");?>

	<div class="mobile-menu-left-overlay"></div>

    <?php require_once("../MainNav/nav.php");?>

	<div class="page-content">
		<div class="container-fluid">
			Home del Sitio.
		</div>
	</div>

	<?php require_once("../MainJs/js.php");?>
	<script type="text/javascript" src="home.js"></script>   
</body>
</html>
<?php
	} else {
		header('Location: ' . \Conectar::ruta() . "index.php"); // Redirige al inicio si ya está logueado
		exit();
	}
?>