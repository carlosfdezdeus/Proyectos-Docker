function Seconds2Time(s) {
	if (s == null || s <= 0) return '0:00:00';
	var minutes = Math.floor(s / 60) % 60;
	var hours = Math.floor(minutes / 60);
	minutes = ( minutes < 10 ? "0" : "" ) + minutes;
	seconds = ( s % 60 < 10 ? "0" : "" ) + s % 60;
	return hours + ":" + minutes + ":" + seconds;
}

function RefreshTimeFromLastActivity() {
	$.each(shelves, function(index, shelf) {
		if (shelf.s_from_launch !== null) {
			shelf.s_from_launch++;
		}
		shelf.time_from_launch = Seconds2Time(shelf.s_from_launch);
		$('.time_from_launch[id_shelf='+ shelf.id_shelf +']').html(shelf.time_from_launch);
		if (shelf.s_from_last_server_activity_datetime !== null) {
			shelf.s_from_last_server_activity_datetime++;
		}
		shelf.time_from_last_server_activity_datetime = Seconds2Time(shelf.s_from_last_server_activity_datetime);
		$('.time_from_last_server_activity_datetime[id_shelf='+ shelf.id_shelf +']').html(shelf.time_from_last_server_activity_datetime);
	})
}

setInterval('RefreshTimeFromLastActivity()', 1000);