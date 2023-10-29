<script type="text/javascript" src="/js/logout.js"></script>
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="navbar-brand w-100 order-1 order-md-0">
		<span class="navbar-brand mr-auto">Validator</span>
    </div>
    <div class="mx-auto order-2">
	<span class="navbar-brand mx-auto current_time"></span>
    </div>
    <div class="navbar-collapse collapse w-100 order-3 dual-collapse2" id="navbarSupportedContent">
		<ul class="navbar-nav ml-auto">
<?php if (!isset($_SESSION['user_id'])) { ?>
			<form action="/" method="POST">
				<input type="text" class="input-xlarge" name="user" placeholder="User" required>
				<input type="password" class="input-xlarge" name="password" placeholder="Password" required>
				<button class="btn btn-outline-info my-2 my-sm-0" type="submit">Login</button>
			</form>
<?php } else { ?>
	<button type="button" id="campana_button" style="visibility: hidden"><img src="../img/alarm_msg.png" alt="campana alarma" id="campana_msg" width="40" height="20"></button>
			<li class="nav-item active dropdown my-lg-0">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?php echo $_SESSION['user_name']; ?>
					<?php if ($_SESSION['user_admin'] == 1) {
    echo ' (admin)';
} ?>
				</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="/">Cambiar entorno</a>
					<a class="dropdown-item" href="/change_state.php">Cambio de flujo</a>
					<a class="dropdown-item" href="/statistics.php">Estad&iacute;sticas</a>
					<a class="dropdown-item" href="/all_information_equipment.php" target="_blank">Modelos</a>
				<!--	<a class="dropdown-item" href="/labels_printer/formulario_impresion.php">Impresi&oacute;n Etiquetas</a> -->
	<?php if ($_SESSION['user_admin'] != 1 && $_SESSION['department_id'] != 1 ) { ?>
					<a class="dropdown-item" href="/user_profile.php">Editar Perfil</a>
	<?php }else{?>
					<a class="dropdown-item" href="/user_management.php">Gesti&oacute;n Usuarios</a>                                        
	<?php } ?>
	<?php if ($_SESSION['user_admin'] == 1) { ?>
					<a class="dropdown-item" href="/ws_server/gestion_server.php">Gesti&oacute;n Puertos</a>
					<a class="dropdown-item" href="/ws_server/puerto_ticket.php">Puertos websocket</a>                                        
					<a class="dropdown-item" href="/msg_bandeja.php">Enviar mensaje</a>
	<?php } ?>
				<div class="dropdown-divider"></div>
					<a class="dropdown-item cerrar_sesion" href="#" >Logout</a>
				</div>
			</li>
<?php } ?>
		</ul>
	</div>
</nav>