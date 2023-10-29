<?php

	// Obtenemos los entornos
	$environment_options = '';
	$sql = "SELECT id_environment, validator_environment.name AS environment_name, validator_environment_shelf.id AS id_shelf, shelf, rack
			FROM validator_environment_shelf
			LEFT JOIN validator_environment ON validator_environment_shelf.id_environment = validator_environment.id
			ORDER BY id_environment ASC, shelf ASC;";
	$result = $mysqli->query($sql);
	while ($row = $result->fetch_assoc()) {
		$shelves[]= $row;
        unset($row);
    }
	mysqli_free_result($result);

?>
<div class="d-flex flex-column sticky-footer-wrapper">
    <main class="flex-fill">
		<form class="form-horizontal no-context-form text-center">
			<div class="control-group">
				<label class="control-label" for="environment">
					<h3>Entorno</h3>
				</label>
				<div class="controls">
					<select id="environment" name="environment" class="input-xlarge" required="">
						<option selected="selected" value="">Ninguno</option>
					</select>
				</div>
				<p>
				<div class="controls">
					<button id="singlebutton" class="btn btn-primary">Cargar Entorno</button>
				</div>
			</div>
			<div class="control-group" id="shelves_selector"></div>
		</form>
    </main>
    <footer>
	</footer>
</div>
<!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
<script type="text/javascript" src="js/libs/jquery.js"></script>
<script type="text/javascript" src="js/libs/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="js/libs/jquery-ui-1.10.3.custom.min.js"></script>
<script>

	var shelves = <?php echo json_encode($shelves); ?>;

	$(function() {
		$("#environment").val($("#environment option:first").val());

		$.each($.unique(ArrayColumn(shelves, 'environment_name').sort()), function(index, environment_name) {
			var environment = shelves.find(bandeja => bandeja.environment_name == environment_name);
			$('#environment').append($('<option>', {
				value: environment.id_environment,
				text : environment.environment_name
			}));
		});

		$(document).on("change", "#environment", function() {
			var selected_shelves = shelves.filter(bandeja => bandeja.id_environment == $("#environment").val());
			racks = ArrayColumn(shelves, 'rack').sort(function(a, b){return a-b});
			racks = $.grep(racks, function(n, i){
				return (n !== "" && n != null);
			});
			racks = $.unique(racks.sort());
			$('#shelves_selector').empty();
			$.each(racks, function(index, rack) {
				var rack_shelves = shelves.filter(bandeja => bandeja.id_environment == $("#environment").val() && bandeja.rack == rack);
				if (rack_shelves.length > 0) {
					$('#shelves_selector').append('<hr>');
					$('#shelves_selector').append('<h3 class="rack" id_rack='+rack+' >Rack '+ rack +'</h3>');
					$('#shelves_selector').append('<div class="form-check form-check-inline">');
					$.each(rack_shelves, function(index, shelf) {
						$('#shelves_selector').append('<input type="checkbox" id_rack= '+rack+'  id="shelf_'+ shelf.shelf +'" value="'+ shelf.shelf +'">');
						$('#shelves_selector').append('<label class="form-check-label" for="shelf_'+ shelf.shelf +'"> '+ shelf.shelf +'</label>');
					});
					$('#shelves_selector').append('</div>');
				}
			});
		});

		$(document).ready(function() {
			$('form').on('submit', function(event){
				var selected_shelves = Array();
				$.each($('input[type="checkbox"]:checked'), function(index, shelf) {
					selected_shelves.push($(shelf).val());
				});
				event.preventDefault();
				if (selected_shelves.length > 0) {
					window.location.replace('/?environment='+ $("#environment").val() +'&shelves='+ selected_shelves.join(','));
				}
			});
		});

		$(document).on('click', 'h3.rack', function () {
			var rack = $(this).attr("id_rack");
			var checkbox_rack_desmarcados =  $('input[type="checkbox"][id_rack='+rack+']:not(:checked)');
			var	checkbox_rack = $('input[type="checkbox"][id_rack='+rack+']');
			checkbox_rack.prop("checked",  checkbox_rack_desmarcados.length != 0 ? 1 : 0);
		});

	});
</script>