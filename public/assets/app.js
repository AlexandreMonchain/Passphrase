$(document).ready(function () {
    $('#password-generator-form').on('submit', function (e) {
        e.preventDefault(); // Empêche le rechargement de la page
        
        // Désactiver le bouton et afficher l'animation de chargement
        $('#generate-btn').prop('disabled', true);
        $('#loading-animation').show();

        $.ajax({
            url: '/',
            method: 'POST',
            data: $(this).serialize(), // Récupère les données du formulaire
            success: function (response) {
                $('#generated-passwords').html(response); // Met à jour la section des mots de passe
            },
            complete: function() {
                // Réactiver le bouton et cacher l'animation après la réponse
                $('#generate-btn').prop('disabled', false);
                $('#loading-animation').hide();
            }
        });
    });

    // Fonctionnalité de copie
    $(document).on('click', '.copy-btn', function(e) {
        e.preventDefault();
        var password = $(this).data('password');
        var tempInput = $('<input>');
        $('body').append(tempInput);
        tempInput.val(password).select();
        document.execCommand('copy');
        tempInput.remove();

        // Feedback visuel pour informer que le mot de passe a été copié
        $(this).html('<i class="fas fa-check"></i>'); // Change l'icône temporairement
        var button = $(this);
        setTimeout(function() {
            button.html('<i class="fas fa-copy"></i>'); // Remet l'icône de copie après 2 secondes
        }, 2000);
    });
});    