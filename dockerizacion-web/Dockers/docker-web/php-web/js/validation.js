$.validator.setDefaults({
    debug: false,
    success: "valid",
    onfocusout: false,
    invalidHandler: function(form, validator) {
        var errors = validator.numberOfInvalids();
        if (errors) {
            validator.errorList[0].element.focus();
        }
    },
    submitHandler: function(form, event) {
        event.preventDefault();
        DebugLog('intento de submit');
    }
});

$.validator.addMethod(
    "regex",
    function(value, element, regexp) { 
        var re = new RegExp(regexp);
        var serial_generado = re;       
        var no_label = false;
        if ($(element).attr('id') != 'serial') {
            no_label = (value == 'NO_LABEL');
        }        
        if (($(element).attr('id') == 'serial') || ($(element).attr('id') == 'serial_check')) {     
           serial_generado = new RegExp("^([A-Z]{2}20[0-9]{8})$");
        } 
     return this.optional(element) || no_label || re.test(value) || serial_generado.test(value);
    },
    "No cumple con el formato esperado."
);