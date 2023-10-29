<?php

    function mysqli_result_2_table( $query_result, $width = '100%' ) {

        $nrows = mysqli_num_rows($query_result);

        if( $nrows > 0 ) {

            $table = '<table style="width:' . $width . '" class="table table-bordered table-striped table-hover">';
            $table .= '<thead><tr>';
            $nfields = mysqli_num_fields($query_result);
            while( $field = mysqli_fetch_field( $query_result ) ) {
                $table .= '<th>' . ucfirst($field->name) . '</th>';
            }
            $table .= '</tr></thead><tbody>';

            $even = true;
            while( $row = mysqli_fetch_assoc($query_result) ) {

                $table .= '<tr>';
                $even = !$even;
                foreach($row as $field => $value) {
                    $table .= '<td class="align-middle">' . $value . '</td>';
                }
                $table .= '</tr>';
            }
            $table .= '</tbody></table>';
        } else {
            $table = '<div class="alert alert-info mt-4" role="alert">';
            $table .= '    <strong>La consulta no ha encontrado coincidencias.</strong>';
            $table .= '    <p>';
            $table .= '        No se han podido recuperar estadísticas de la DB.';
            $table .= '    </p>';
            $table .= '</div>';
        }

        return $table;
    }

?>


