// Requiere jquery
$(function() {
    if ($('#caps_warning').length == 0) {
        html_caps_warning = '<div class="modal fade" id="caps_warning" tabindex="-1" role="dialog" aria-labelledby="caps_warning" aria-hidden="false" data-backdrop="static">' + "\n";
        html_caps_warning += '    <div class="modal-dialog" role="document">' + "\n";
        html_caps_warning += '        <div class="modal-content" style="width: 550px;">' + "\n";
        html_caps_warning += '            <div class="modal-header">' + "\n";
        html_caps_warning += '                <h5 class="modal-title" id="exampleModalLabel">Advertencia</h5>' + "\n";
        html_caps_warning += '                <button type="button" class="close" data-dismiss="modal" aria-label="Close">' + "\n";
        html_caps_warning += '                <span aria-hidden="true">&times;</span>' + "\n";
        html_caps_warning += '                </button>' + "\n";
        html_caps_warning += '            </div>' + "\n";
        html_caps_warning += '            <div class="modal-body">' + "\n";
        html_caps_warning += '                El teclado ha tenido activado el Bloq Mayus mientras rellenaba el campo.' + "\n";
        html_caps_warning += '            </div>' + "\n";
        html_caps_warning += '            <div class="modal-footer">' + "\n";
        html_caps_warning += '                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>' + "\n";
        html_caps_warning += '            </div>' + "\n";
        html_caps_warning += '        </div>' + "\n";
        html_caps_warning += '    </div>' + "\n";
        html_caps_warning += '</div>';

        div_msg_warning = $(html_caps_warning).appendTo('body');
    }

    const capsElement = document.querySelector(".shelf_data");

    // Add the event listener to the document.
    //document.addEventListener("keypress", (e)=> {
    $(document).on("keyup keypress paste", ".shelf_data", function(e) {
        let CapsLock_is_on = isCapslock(e);
        DebugInfo(`caps ${CapsLock_is_on}`);
        if( CapsLock_is_on && !$(this).hasClass("CapsLock_used") ){
            $(this).addClass("CapsLock_used");
            $(this).addClass("is-invalid");
        }
        if ( $(this).val() == '' ) {
            $(this).removeClass('is-invalid');
        }
    });

    $(document).on("blur", ".shelf_data", function() {
        if ( $(this).hasClass('CapsLock_used') ) {
            $("#caps_warning").modal('show');
        }
        $(this).removeClass('CapsLock_used');
    });

    $(document).on("select", ".shelf_data", function() {
        $(this).removeClass('is-invalid');
    });

    function isCapslock(e) {
        const IS_MAC = /Mac/.test(navigator.platform);

        const charCode      = e.charCode;
        const shiftKey      = e.shiftKey;

        capsLock = false;
        if (charCode >= 97 && charCode <= 122){
            capsLock = shiftKey;
        } else if (charCode >= 65 && charCode <= 90 && !(shiftKey && IS_MAC)) {
            capsLock = !shiftKey;
        }

        return capsLock;
    }
})