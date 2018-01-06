/**
 Custom module for you to write your own javascript functions
 **/
var OffrePage = function () {
    var modal = GlobalPageCustomScript.modal;
    var initEditables = function () {
        var id = $('[name="_id"]', modal.display).val();
        $.fn.editable.defaults.mode = 'inline';
        var nbProcessusEnCours = 0;
        var labelProcess = $('#ajax-label-process');
        //global settings
        $.fn.editable.defaults.inputclass = 'form-control';
        $.fn.editable.defaults.url = id+'/edit';
        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.ajaxOptions = {type: "POST"};

        var boutiqueID = $('[data-display=boutiqueID]', modal.display).text();
        if(!isNaN(parseInt(boutiqueID))){
            $('td[data-display=categorie] span', modal.display).editable({
                pk: '1',
                type: 'select',
                inputClass : 'form-control',
                name: 'categorie',
                source: '../categorie/'+boutiqueID
            });
        }
        //editables element samples
        $('td[data-display=designation] span', modal.display).editable({
            pk: '1',
            name: 'designation',
            inputClass : 'form-control',
            validate : function (value) {
                    if ($.trim(value) === '') return 'This field is required';
                },
            success: function (json) {
                json = JSON.parse(json);
                var item = json.item;
                if (item && item.action == 1) {
                    $('.data-content .datatable_ajax').DataTable().ajax.reload();
                }
            },
            error: function () {
                if (nbProcessusEnCours < 0) $('#ct-sp').addClass('hidden'); // if spinner errs
                alert('error');
            }
        });
        $('td[data-display=description] span', modal.display).editable({
            pk: id,
            inputClass : 'form-control',
            type: 'textarea',
            name: 'categorie'

        });

        $('td[data-display=dateExpiration] span', modal.display).editable({
            pk: id,
            name: 'dateExpiration',
            format: 'yyyy-mm-dd hh:ii',
            viewformat: 'dd/mm/yyyy hh:ii',
            validate: function (v) {
                if (v && v.getDate() == 10) return 'Day cant be 10!';
            },
            datetimepicker: {
                rtl: App.isRTL(),
                todayBtn: 'linked',
                weekStart: 1
            }
        });
        $('td[data-display=dureeGarantie] span', modal.display).editable({
            pk: id,
            name: 'dureeGarantie',
            type: 'number'
        });
        $('td[data-display=apparenceNeuf] span', modal.display).editable({
            pk: id,
            name: 'apparenceNeuf',
            type : 'select',
            source : [
                {
                    value: 0,
                    text: 'Brand New'
                },
                {
                    value: 1,
                    text: 'Used'
                }
            ]
        });

        /* $('td span', modal.display).on('save', function () {
         nbProcessusEnCours -= 1;
         labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
         });*/
        /*$('td span', modal.display).submit(function () {
         nbProcessusEnCours += 1;
         labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
         if (nbProcessusEnCours === 1) $('#ct-sp').click();
         });*/
        $('td[data-display=etat] span', modal.display).editable({
            pk: id,
            name: 'etat',
            type: 'select',
            success: function (json) {
                json = JSON.parse(json);
                var item = json.item;
                if (item && item.action == 1) {
                    $('.data-content .datatable_ajax').DataTable().ajax.reload();
                }
            },
            error: function () {
                if (nbProcessusEnCours < 0) $('#ct-sp').addClass('hidden'); // if spinner errs
                alert('error');
            },
            source: [
                {
                    value: 0,
                    text: 'disponible en stock'
                },
                {
                    value: 1,
                    text: 'non disponible'
                },
                {
                    value: 2,
                    text: 'vente sur commande'
                },
                {
                    value: 3,
                    text: 'vente suspendue'
                },
                {
                    value: 4,
                    text: 'vente annulée'
                },
                {
                    value: 5,
                    text: 'stock limité'
                },
                {
                    value: 6,
                    text: 'en panne'
                },
                {
                    value: 7,
                    text: 'vente restreinte en région'
                },
                {
                    value: 8,
                    text: 'vente interdit'
                }
            ]
        });
        $('td[data-display=remiseProduit] span', modal.display).editable({
            pk: id,
            name: 'remiseProduit',
            type: 'number'
        });
        $('td[data-display=modeVente] span', modal.display).editable({
            pk: id,
            name: 'modeVente',
            type: 'select',
            source:[
                {
                    value:0,
                    text: 'Normale'
                },
                {
                    value:1,
                    text: 'Enchère'
                },
                {
                    value:2,
                    text: 'Solde'
                },
                {
                    value:3,
                    text: 'Exclusivité'
                }
            ]
        });
        $('td[data-display=modelDeSerie] span', modal.display).editable({
            pk: id,
            name: 'modelDeSerie'
        });
        $('td[data-display=prixUnitaire] span', modal.display).editable({
            pk: id,
            name: 'prixUnitaire',
            type: 'number'
        });
        $('td[data-display=quantite] span', modal.display).editable({
            pk: id,
            name: 'quantite',
            type: 'number'
        });
        $('td[data-display=unite] span', modal.display).editable({
            pk: id,
            name : 'unite'
        });
        $('td[data-display=publiable] span', modal.display).editable({
            pk: id,
            name: 'publiable',
            type: 'select',
            source: [
                {
                    value: 0,
                    text: 'No'
                },
                {
                    value: 1,
                    text: 'Yes'
                }
            ]
        });
        $('td[data-display=typeOffre] span', modal.display).editable({
            pk: id,
            name : 'typeOffre',
            type : 'select',
            source : [{
                value: 0,
                text: 'Article'
            },
                {
                    value: 1,
                    text: 'Produit'
                },
                {
                    value: 2,
                    text: 'Service'
                }
            ]
        });

    };

    return {
        init: function () {
            $('#enable-edit').change(function () {
                if (!modal.enableEdit) initEditables();
                if (modal.enableEdit) $('.editable').editable('toggleDisabled');
                modal.enableEdit = true;
            });
        }
    };
}();

jQuery(document).ready(function () {
    OffrePage.init();
});
