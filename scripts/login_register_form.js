// Evénement jquery: Page chargée
$(document).ready(function(){
    $('input[name=isOrganizer]').change(function() {
        if($(this).is(":checked")) {
            $('.toremove').remove();
        }
        else {
            $('.append').append('<label class="toremove" for="address">Address</label><input class="toremove" name="address" id="address" type="text" placeholder="123 Main Street, New York, NY 10030" required/><br class="toremove"><label class="toremove" for="phone_number">Phone number</label><input class="toremove" name="phone_number" id="phone_number" type="text" placeholder="0612345678" required/><br class="toremove">');
        }  
    });
});