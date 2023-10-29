$(function () {

    $(".tablesorter").tablesorter({
        showProcessing: true,
        theme: 'blue',
        headers: {
            0: { sorter: "checkbox" },
        },
        widgets: ["group", "columns", "filter", "zebra", 'math'],
        widgetOptions: {
            group_collapsible: true,  // make the group header clickable and collapse the rows below it.
            group_collapsed: false, // start with all groups collapsed (if true)
            group_saveGroups: true,  // remember collapsed groups
            group_saveReset: '.group_reset', // element to clear saved collapsed groups
            group_count: " ({num})", // if not false, the "{num}" string is replaced with the number of rows in the group
            filter_childRows  : true,

            // apply the grouping widget only to selected column
            group_forceColumn: [],   // only the first value is used; set as an array for future expansion
            group_enforceSort: true, // only apply group_forceColumn when a sort is applied to the table

            // checkbox parser text used for checked/unchecked values
            group_checkbox: ['marcados', 'desmarcados'],

            // change these default date names based on your language preferences (see Globalize section for details)
            group_months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            group_week: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
            group_time: ["AM", "PM"],

            // use 12 vs 24 hour time
            group_time24Hour: true,
            // group header text added for invalid dates
            group_dateInvalid: 'Fecha inv&aacute;lida',

            // this function is used when "group-date" is set to create the date string
            // you can just return date, date.toLocaleString(), date.toLocaleDateString() or d.toLocaleTimeString()
            // reference: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date#Conversion_getter
            group_dateString: function (date) {
                return date.toLocaleString();
            },

            // event triggered on the table when the grouping widget has finished work
            group_complete: "groupingComplete"
        }
    });

});



function EliminarAgrupamientos() {
    $.each($('.tablesorter')[0].config.$headers, function( key, value ) {
        $($('.tablesorter')[0].config.$headers[key]).removeClass (function (index, className) {
            return (className.match (/(^|\s)group-\S+/g) || []).join(' ');
        });
        $($('.tablesorter')[0].config.$headers[key]).addClass("group-false");
    });
};


function EditarAgrupamientos(grouping) {
    $.each($('.tablesorter')[0].config.$headers, function( key, value ) {
        $($('.tablesorter')[0].config.$headers[key]).removeClass (function (index, className) {
            return (className.match (/(^|\s)group-\S+/g) || []).join(' ');
        });
        if (grouping[key]) {
            $($('.tablesorter')[0].config.$headers[key]).addClass(grouping[key]);
        } else {
            $($('.tablesorter')[0].config.$headers[key]).addClass("group-false");
        }
    });

    $('table').trigger('recalculate');
};