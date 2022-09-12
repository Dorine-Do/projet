$(document).ready( function () {
    $('#tableToSort').DataTable({
        language: {
            processing:     "Traitement en cours...",
            search:         "Rechercher&nbsp;: ",
            lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
            info:           "Affichage des &eacute;lements _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            infoEmpty:      "Affichage des l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            infoPostFix:    "",
            loadingRecords: "Chargement en cours...",
            zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
            emptyTable:     "Aucune donnée disponible dans le tableau",
            paginate: {
                first:      "Premier",
                previous:   "Pr&eacute;c&eacute;dent",
                next:       "Suivant",
                last:       "Dernier"
            },
            aria: {
                sortAscending:  ": activer pour trier la colonne par ordre croissant",
                sortDescending: ": activer pour trier la colonne par ordre décroissant"
            }
        }
    });
    $('#tableToSort_filter input:first-of-type').addClass('form-control');
    $('#tableToSort_filter input:first-of-type').css('width', 'auto');
    $('#tableToSort_filter input:first-of-type').css('display', 'inline-block');
    $('#tableToSort_length select:first-of-type').addClass('form-control');
    $('#tableToSort_length select:first-of-type').css('width', '47px');
    $('#tableToSort_length select:first-of-type').css('display', 'inline-block');
} );