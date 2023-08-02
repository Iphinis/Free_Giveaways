// Evénement jquery: Page chargée
$(document).ready(function(){
    $('.hamburger').on('click', function(){
        $('nav ul').toggleClass('menu');
    });
});

// Evénement jquery: Fenêtre scrollée
$(window).on('scroll', function() {
    // La classe scroll va être ajoutée à toutes les balises nav si l'utilisateur scroll vers le haut
    if ($(window).scrollTop()) {
        $('nav').addClass('scroll');
    }
    // La classe scroll va être retirée à toutes les balises nav si l'utilisateur scroll sinon
    else {
        $('nav').removeClass('scroll');
    }
});