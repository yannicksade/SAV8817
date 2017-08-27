/**
 Custom module for you to write your own javascript functions
 **/
var OffrePage = function () {
    //global variables
    //----pages
    var notifAlert = document.querySelector("#notif-active");
    var actionConfirm;
    var modalConfirm = $('#modal-confirm');
    var tabElement_1 = $('#tab_1'),
        tabElement_2 = $('#tab_2'),
        tab1 = document.querySelector('#tab1'),
        tab2 = document.querySelector('#tab2');
    var uploadedFile;
        var labelProcess = $('#ajax-label-process');
    var isEditMode = false;
    //----- modal
    var modal_stk = {
        'modalElement': "#m-ajax", //identité de la modal
        'modalTab1': 'a[href="#modal-tab_1"]', //liens
        'modalTab2': 'a[href="#modal-tab_2"]',
        'modalTab3': 'a[href="#modal-tab_3"]',
        'modalTab4': 'a[href="#modal-tab_4"]',
        'modalTab5': 'a[href="#modal-tab_5"]',
        'tab1': '#modal-tab_1', //tab content
        'tab2': '#modal-tab_2',
        'tab3': '#modal-tab_3',
        'tab4': '#modal-tab_4',
        'tab5': '#modal-tab_5',
        'contentTabActive': '#modal-tab_1',
        'linkTabActive': 'a[href="#modal-tab_1"]'
    };
    /* var sendFile =  function sendFile(file) {
     var uri = "http://localhost/SAV8817.git/web/app_dev.php/apm/vente/offre/index?id="+29;
     var xhr = new XMLHttpRequest();
     var fd = new FormData();

     xhr.open("POST", uri, true);
     xhr.onreadystatechange = function() {
     if (xhr.readyState === 4 && xhr.status === 200) {
     alert(xhr.responseText); // handle response.
     }
     };
     fd.append('myFile', file);
     // Initiate a multipart/form-data upload
     xhr.send(fd);
     };*/

    var modalNotification = function (message) {
        var tmpl = [
            // tabindex is required for focus
            '<div class="modal hide fade" tabindex="-1">',
            '<div class="modal-header">',
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>',
            '<h4 class="modal-title">Alerte</h4>',
            '</div>',
            '<div class="modal-body">',
            '<p>' + message + '</p>',
            '</div>',
            '<div class="modal-footer">',
            '<a href="#" data-dismiss="modal" class="btn btn-default">Close</a>',
            '</div>',
            '</div>'
        ].join('');

        $(tmpl).modal('modal');
    };
    //---- notification


    var uncheckBoxes = function (parent) {
        $('input[type="checkbox"]:checked', parent).attr('checked', false);
    };
    var reinitializeModal = function () {
        $('.data-content', modal_stk.modalElement).addClass("hidden");
        $('.alerte', modal_stk.modalElement).removeClass("hidden");
        $('.id', modal_stk.modalElement).val('');
        $('td', modal_stk.modalElement).text('');
        //réinitialisation de la tab
        $(modal_stk.modalTab1, modal_stk.modalElement).click(); //cback to the first tab
    };
    var getCheckedBoxes = function (parent) { //:nth-child(1)
        return $('.checkboxes:checked', parent);
    };
    var play = function (parent) {
        //var parent = $(e).parents('.portlet');
        reinitializeModal();
        var cboxes = getCheckedBoxes($('tbody', parent));
        var nb = cboxes.length;
        if (cboxes === undefined || nb === 0)return;
        for (var i = 0; i < nb; i++) {
            var elt = cboxes[i];
            setTimeout(function () {
                var p = $(elt).parents();
                afficherImpl(p[2]); // row line portlet
                uncheckBoxes(p[1]); //td cell
            }, 100);
        }
    };
    var afficher = function () {
        //-------------------------- voir ---------------------
        $('.see_item').click(function () {
            play($(this).parents('.portlet')); // remplacer "this" par le "parent" pour une rotation des elements selections
        });
    };
    var modalToFormLoad = function (parent) {
        var form = $('.form-element', parent);
        var a;
        $('.id', form).val($('.id', modal_stk.modalElement).val());
        $('.code', form).val($('.code', modal_stk.modalElement).text());
        a = $('.categorie', form);
        a.val($('.categorieID', modal_stk.modalElement).text());
        a.select();
        a = $('.boutique', form);
        a.val($('.boutiqueID', modal_stk.modalElement).text());
        a.select();
        a = $('.etat', form);
        a.val($('.etatID', modal_stk.modalElement).text());
        a.select(); // element select
        $('.desc', form).val($('.desc', modal_stk.modalElement).text());
        $('.designation', form).val($('.designation', modal_stk.modalElement).text());
        a = $('.vendeurID', form);
        a.val($('.vendeurID', modal_stk.modalElement).text());
        a.select();
        $('.desc', form).val($('.desc', modal_stk.modalElement).text());
        $('.publiable', form).val($('.publiableID', modal_stk.modalElement).text());
        $('.dureeGarantie', form).val($('.dureeGarantie', modal_stk.modalElement).text());
        $('.apparence', form).val($('.apparenceID', modal_stk.modalElement).text());
        $('.modeVente', form).val($('.modeVenteID', modal_stk.modalElement).text());
        $('.modelDeSerie', form).val($('.modelDeSerie', modal_stk.modalElement).text());
        $('.prix', form).val($('.prix', modal_stk.modalElement).text());
        $('.quantite', form).val($('.quantite', modal_stk.modalElement).text());
        $('.unite', form).val($('.unite', modal_stk.modalElement).text());
        $('.remise', form).val($('.remise', modal_stk.modalElement).text());
        $('.type', form).val($('.typeID', modal_stk.modalElement).text());
    };
    var tableToFormLoad = function (parent, form_parent) {
        var form = $('.form-element', form_parent);
        var a;
        $('.id', form).val($('.id', parent).text());
        $('.code', form).val($('.code', parent).text());
        a = $('.categorie', form);
        a.val($('.categorieID', parent).text());
        a.select();
        a = $('.boutique', form);
        a.val($('.boutiqueID', parent).text());
        a.select();
        a = $('.etat', form);
        a.val($('.etat input', parent).val());
        a.select(); // element select
        $('.designation', form).val($('.designation', parent).text());
        a = $('.vendeurID', form);
        a.val($('.vendeurID', parent).text());
        a.select();
        $('.desc', form).val($('.desc', parent).text());
        $('.publiable', form).val($('.publiableID', parent).text());
        $('.dureeGarantie', form).val($('.dureeGarantie', parent).text());
        $('.apparence', form).val($('.apparenceID', parent).text());
        $('.modeVente', form).val($('.modeVenteID', parent).text());
        $('.modelDeSerie', form).val($('.modelDeSerie', parent).text());
        $('.prix', form).val($('.prix', parent).text());
        $('.quantite', form).val($('.quantite', parent).text());
        $('.unite', form).val($('.unite', parent).text());
        $('.type', form).val($('.typeID', parent).text());
        $('.remise', form).val($('.remise', parent).text());
    };
    var afficherImpl = function (parent) {
        //parent is a row here
        $('.alerte', modal_stk.modalElement).addClass("hidden");
        $('.data-content', modal_stk.modalElement).removeClass("hidden");

        $('.id', modal_stk.modalElement).val($('.id', parent).text());
        $('.code', modal_stk.modalElement).text($('.code', parent).text());
        $('.categorieID', modal_stk.modalElement).text($('.categorieID', parent).text());
        $('.categorie', modal_stk.modalElement).text($('.categorie', parent).text());
        $('.boutiqueID', modal_stk.modalElement).text($('.boutiqueID', parent).text());
        $('.boutique', modal_stk.modalElement).text($('.boutique', parent).text());
        $('.etatID', modal_stk.modalElement).text($('.etat input', parent).val());
        $('.etat', modal_stk.modalElement).text($('.etat', parent).text());
        $('.desc', modal_stk.modalElement).text($('.desc', parent).text());
        $('.designation', modal_stk.modalElement).text($('.designation', parent).text());
        $('.vendeurID', modal_stk.modalElement).text($('.vendeurID', parent).text());
        $('.vendeur', modal_stk.modalElement).text($('.vendeur', parent).text());
        $('.dureeGarantie', modal_stk.modalElement).text($('.dureeGarantie', parent).text());
        $('.dateCreation', modal_stk.modalElement).text($('.dateCreation', parent).text());
        $('.updatedAt', modal_stk.modalElement).text($('.updatedAt', parent).text());
        $('.credit', modal_stk.modalElement).text($('.credit', parent).text());
        $('.publiable', modal_stk.modalElement).text($('.publiable', parent).text());
        $('.publiableID', modal_stk.modalElement).text($('.publiableID', parent).text());
        $('.dureeGarantie', modal_stk.modalElement).text($('.dureeGarantie', parent).text());
        $('.dateExpiration', modal_stk.modalElement).text($('.dateExpiration', parent).text());
        $('.retourne', modal_stk.modalElement).html($('.retourne', parent).text());
        $('.apparence', modal_stk.modalElement).text($('.apparence', parent).text());
        $('.apparenceID', modal_stk.modalElement).text($('.apparenceID', parent).text());
        $('.modeVente', modal_stk.modalElement).text($('.modeVente', parent).text());
        $('.modeVenteID', modal_stk.modalElement).text($('.modeVenteID', parent).text());
        $('.modelDeSerie', modal_stk.modalElement).text($('.modelDeSerie', parent).text());
        $('.prix', modal_stk.modalElement).text($('.prix', parent).text());
        $('.quantite', modal_stk.modalElement).text($('.quantite', parent).text());
        $('.unite', modal_stk.modalElement).html($('.unite', parent).text());
        $('.rate', modal_stk.modalElement).text($('.rate', parent).text());
        $('.remise', modal_stk.modalElement).text($('.remise', parent).text());
        $('.type', modal_stk.modalElement).text($('.type', parent).text());
        $('.typeID', modal_stk.modalElement).text($('.typeID', parent).text());
    };
    var modifierImpl = function (parent) {
        //------ Compress the table 1 -----------------
        var form = $('.form-element', parent);
        $('.tab-element', parent).addClass('col-lg-9 col-md-6 col-xs-12');
        form.removeClass('hidden');
        $('.compress-item', parent).addClass('hidden');
        $('.expand-item', parent).removeClass('hidden');
        //reinitialize the form
        $('input[type="reset"]', form).click();
    };
    var modifier = function () {
        $('.edit_item').click(function () {
            var p = $(this).parents();
            var parent = p[5];// portlet
            if (!$(p[0]).hasClass('modal-footer')) { //chargement du formulaire partant du tableau
                modifierImpl(parent);
                var cboxes = $('.checkboxes:checked', parent)
                var nb = cboxes.length;
                for (var i = 0; i < nb; i++) {
                    var elt = cboxes[i];
                    setTimeout(function () {
                        var p = $(elt).parents();
                        tableToFormLoad(p[2], parent);// 1- a row of the table 2- portlet, parent of the form
                        uncheckBoxes(p[1]); //td cell
                    });
                }
            } else {
                //chargement du formulaire partant de la modal
                if (modal_stk.modalElement === '#m-ajax') {
                    modifierImpl(tabElement_1);
                    modalToFormLoad(tabElement_1);
                } else if (modal_stk.modalElement === '#stack2') {
                    modifierImpl(tabElement_2);
                    modalToFormLoad(tabElement_2);
                }
            }
            isEditMode = true;
        });
    };
    var supprimer = function () {
        $('.delete_item').click(function () {
            var elt, elements = [];
            var child = this;
            var table = $('.data-content .datatable_ajax');
            //alt.removeEventListener('click',null, false);
            var p = $(child).parents();
            if ($(p[0]).hasClass('modal-footer')) {//suppression à partir de la modal
                elements[0] = $('.id', p[1]).val();
                $('.alerte', modalConfirm).html('Vous êtes sur le point de supprimer définitivement l\'offre de référence:<strong>' + $('.code', p[1]).text() + '</strong><br/> Voulez-vous continuer?');
            } else { //suppression apartir de la table
                var cboxes = getCheckedBoxes($('.tab-element', p[5])), tr, //p5: search for tab element from portlet
                    nb = cboxes.length;
                $('.alerte', modalConfirm).html('Vous êtes sur le point de supprimer définitivement <strong>' + nb + '</strong> offre(s)<br/> Voulez-vous continuer?');
                for (var i = 0; i < nb; i++) {
                    elt = $(cboxes[i]);
                    tr = elt.parents()[2]; // recupérer la ligne du tableau

                    elements[i] = $('.id', tr).text();
                }

            }
            actionConfirm = 1;

            modalConfirm.on('click', '#action-confirm', function () {
                if (actionConfirm !== 1) return;
                if (elements.length > 0) GlobalPageCustomScript.anActionForm(child, elements, table);
                elements = [];
            });
            modalConfirm.on('click', '.action-cancel', function () {
                if (actionConfirm !== 1) return;
                elements = [];
            });

        });
    };
    var formManager = function () {
        //control des boutons collectifs
        $('.form-element .group-checkboxes-form').change(function () { //check and uncheck
            var checked = $(this).prop("checked");
            var set = $('.input-group-addon input[type="checkbox"]');
            $(set).each(function () {
                $(this).prop("checked", checked);
            });
            var p = $(this).parents()[2]; //form
            if (this.checked) {
                $('input[type="submit"]', p).attr('disabled', false);
                $('input[type="text"]', p).attr('readonly', false);
                $('select, textarea', p).attr('disabled', false);
                $('.code', p).attr('readonly', true);
                if (isEditMode) $('.form-group .copier', p).attr('disabled', false);
            } else {
                $('input[type="submit"]', p).attr('disabled', true);
                $('input[type="text"]', p).attr('readonly', true);
                $('select, textarea', p).attr('disabled', true);
                if (isEditMode) $('.form-group .copier', p).attr('disabled', true);
            }
        });

        //control des boutons individuels
        $('input[type="checkbox"].input-group-addon').change(function () { //individual check and enabling text input
            var p = $(this).parents()[1];// input-group
            if (this.checked) {
                $('input[type="text"]', p).attr('readonly', false);
                $('select, textarea', p).attr('disabled', false);
                $('.code', p).attr('readonly', true);
                $(this).attr("checked", true);
                if (isEditMode) $('input[type="submit"]', p[2]).attr('disabled', false);
            } else {
                $('input[type="text"]', p).attr('readonly', true);
                $('select, textarea', p).attr('disabled', true);
                $(this).attr("checked", false);
                if (isEditMode) $('input[type="submit"]', p[2]).attr('disabled', true);
                alert('2');
            }
        });

        $('.form-element .copier').click(function () {
            $('.alerte', modalConfirm).html('Vous êtes sur le point de cloner une offre. Il est conseillé de modifier quelque propriétée pour les différencier! <br/> Voulez-vous continuer ?');
            var btn = this;
            actionConfirm = 2;
            modalConfirm.on('click', '#action-confirm', function () { ///créer la modal
                if (actionConfirm !== 2) return;
                var p = $(btn).parents;
                $('.code', p[1]).val('mode clonage activé.');
                $('.id', p[1]).val('');
            });
        });


        //handle submitted buttons
        $('input[type="submit"]').click(function (e) {
            e.preventDefault();
            var btn = e.target;
            if (btn.id === "crop") {
              $(btn).attr('data-href', 'image');
            } else uploadedFile = null; //<-- add condition here
            GlobalPageCustomScript.ajaxForm(this, uploadedFile, $('.id', modal_stk.modalElement).val());
        });
    };
    var pageForm = function () {
        //----------------- compression et extension de la page -------------------------
        //parent = parent.parents('.portlet');
        $('.compress-item').click(function () {
            var parent = $(this).parents('.portlet');
            $('.tab-element', parent).addClass('col-lg-9 col-md-6 col-xs-12');
            $('.form-element', parent).removeClass('hidden');
            $('.compress-item', parent).addClass('hidden');
            $('.expand-item', parent).removeClass('hidden');
        });
        $('.expand-item').click(function () {
            var parent = $(this).parents('.portlet');
            $('.tab-element', parent).removeClass('col-lg-9 col-md-6 col-xs-12');
            $('.form-element', parent).addClass('hidden');
            $('.expand-item', parent).addClass('hidden');
            $('.compress-item', parent).removeClass('hidden');
        });
    };
    return {
        init: function () {
            modifier();
            supprimer();
            pageForm();
            afficher();
            formManager();

            tab1.onclick = function () { //gestion des affichages avec  deux modal
                modal_stk.modalElement = '#m-ajax';
            };

            $('.charger-image').click(function () {
                reinitializeModal();
                $('#modal-tab_3').removeClass('hidden');
                $(modal_stk.modalTab3, modal_stk.modalElement).parent().removeClass('hidden');
                $(modal_stk.modalTab3, modal_stk.modalElement).parents('.modal').find('.alerte').addClass('hidden');
                $(modal_stk.modalTab3, modal_stk.modalElement).click();
            });
            $('.alerte', modal_stk.modalElement).html('<strong>Aucun élément sélectionné</strong>');

            $('.composer_item').click(function () {
                modifierImpl($(this).parents('.portlet'));
            });

            $('.tab-reload').click(function () {
                $('.datatable_ajax', $(this).parents('.portlet')).DataTable().ajax.reload();
            });

            //document.querySelector('#tab_1 .submit_form').removeEventListener('submit',null,  false);
        }
    };
}();

jQuery(document).ready(function () {
    OffrePage.init();
});
