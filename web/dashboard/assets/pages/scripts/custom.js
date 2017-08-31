/**
 * Created by Yannick sade on 22/08/2017.
 */

/*This class will contain all the common actions*/

var GlobalPageCustomScript = function () {
    var actionConfirm;
    var modalConfirm = $('#modal-confirm');
    var tabElement_1 = $('#tab_1'),
        tab1 = document.querySelector('#tab1');
    var uploadedFile;
    var isEditMode = false;
    var notifAlert;
    var nbProcessusEnCours = 0;
    var labelProcess = $('#ajax-label-process');

    var uncheckBoxes = function (parent) {
        $('input[type="checkbox"]:checked', parent).attr('checked', false);
    };
    var getCheckedBoxes = function (parent) { //:nth-child(1)
        return $('.checkboxes:checked', parent);
    };
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
    /*var modifier = function () {
        $('.edit_item').click(function () {
            var p = $(this).parents(); //dans le body
            if (!$(p[0]).hasClass('modal-footer')) { //chargement du formulaire partant du tableau
                //body data content
                modifierImpl(p[5]);
                 $('.checkboxes:checked', p[5]).each(function () {
                        var p = $(this).parents();
                        tableToFormLoad(p[2], parent);// 1- a row of the table 2- portlet, parent of the form
                        uncheckBoxes(p[1]); //td cell
                });
            } else {
                modifierImpl(modal_stk.bodyContainer);
                modalToFormLoad(modal_stk.bodyContainer);
            }
            isEditMode = true;
        });
    };*/
    var supprimer = function () {
        $('.delete_item').click(function () {
            var elt, elements = [];
            var child = this;
            var table = $('.data-content .datatable_ajax');
            //alt.removeEventListener('click',null, false);
            var p = $(child).parents();
            if ($(p[0]).hasClass('modal-footer')) {//suppression à partir de la modal
                elements[0] = $('input[name="_id"]', p[1]).val();
                $('.alerte', modalConfirm).html('Vous êtes sur le point de supprimer définitivement l\'offre de référence:<strong>' + $('td[data-display=_code]', p[1]).text() + '</strong><br/> Voulez-vous continuer?');
            } else { //suppression apartir de la table
                var cboxes = getCheckedBoxes($('.tab-element', p[5])), tr, //p5: search for tab element from portlet
                    nb = cboxes.length;
                $('.alerte', modalConfirm).html('Vous êtes sur le point de supprimer définitivement <strong>' + nb + '</strong> offre(s)<br/> Voulez-vous continuer?');
                for (var i = 0; i < nb; i++) {
                    elt = $(cboxes[i]);
                    tr = elt.parents()[2]; // recupérer la ligne du tableau

                    elements[i] = $('input[name="_id[]"]', tr).val();
                }

            }
            actionConfirm = 1; //s'assurer que l'action ne vient pas d'ailleurs

            modalConfirm.on('click', '#action-confirm', function () {
                if (actionConfirm !== 1) return;
                if (elements.length > 0) anActionForm(child, elements, table);
                elements = [];
            });
            modalConfirm.on('click', '.action-cancel', function () {
                if (actionConfirm !== 1) return;
                elements = [];
            });

        });
    };
    var pageManager = function () {
        //expand and compress
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
        //---- cloner une entrée déjà existente --------
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
            ajaxForm(this, uploadedFile, $('input[name="_id"]', modal_stk.modalElement).val());
        });
        $('.charger-image').click(function () { //manager une image
            reinitializeModal();
            $('#modal-tab_3').removeClass('hidden');
            $(modal_stk.modalTab3, modal_stk.modalElement).parent().removeClass('hidden');
            $(modal_stk.modalTab3, modal_stk.modalElement).parents('.data-content').find('.alerte').addClass('hidden');
            $(modal_stk.modalTab3, modal_stk.modalElement).click();
        });
        $('.alerte', modal_stk.modalElement).html('<strong>Aucun élément sélectionné</strong>');
        $('.composer_item').click(function () {
            modifierImpl($(this).parents('.data-content'));
        });
        $('.tab-reload').click(function () {
            $('.datatable_ajax', $(this).parents('.data-content')).DataTable().ajax.reload();
        });
    };
    var readFile = function (file, parent) {
        var reader = new FileReader();
        var item_sp;
        reader.onload = function (e) {
            if (reader.readyState === 2) {//2  done
                var elt = e.target;
                var uploadedFile = elt.result;
                var element;
                element = document.createElement('img');
                element.style.maxWidth = "830px";
                element.style.maxHeight = "622px";
                element.src = uploadedFile;
                element.className = "crop-image";
                var divElement = document.createElement('div');
                divElement.className = "big-view";
                divElement.appendChild(element);
                var container = document.querySelector('#view-image');
                var oldView = container.querySelector('.big-view');
                if (oldView === null) container.appendChild(divElement); else oldView.parentNode.replaceChild(divElement, oldView);

                element = element.cloneNode(false);
                element.className = "jcrop-preview";
                var divElement2 = document.createElement('div');
                divElement2.className = "preview-container";
                divElement2.appendChild(element);
                var ppane = divElement2.cloneNode(false);
                ppane.className = "preview-pane";
                ppane.appendChild(divElement2);
                var oldPrev = container.querySelector('.preview-container .preview-pane');
                if (oldPrev === null) container.appendChild(ppane); else oldPrev.parentNode.replaceChild(ppane, oldPrev);
                FormImageCrop.init();
                nbProcessusEnCours -= 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                if (nbProcessusEnCours <= 0) $('#ct-sp').click();
                parent = parent.parent();
                item_sp = $('.modal-spinner', parent);
                if (item_sp) item_sp.click();
            }
            if (reader.readyState === reader.LOADING) { //1

            }

            if (reader.readyState === reader.EMPTY) { //0
                //aucune donnée chargée!
                alert('aucun fichier chargé');
                nbProcessusEnCours -= 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                if (nbProcessusEnCours <= 0) $('#ct-sp').click();
                parent = parent.parent();
                item_sp = $('.modal-spinner', parent);
                if (item_sp) item_sp.click();
            }
        };
        reader.readAsDataURL(file);
    };
    var loadFile = function () {
        var allowedfileTypes = ["jpg", "png", "gif", "jpeg"];
        $('.preview-file-input').change(function (e) {
            var elt = e.target;
            var parent, p, item_sp;
            var files = elt.files;
            var fileLength = files.length;
            for (var i = 0; i < fileLength; i++) { //vérification du type de fichier lu
                var fileType = files[0].name.split('.');
                fileType = fileType[fileType.length - 1].toLowerCase();// éviter les extensions en majuscule
                if (allowedfileTypes.indexOf(fileType) !== -1) {
                    parent = $(this).parents('form');// the 2nd parent is the form
                    p = $(parent).parent();
                    nbProcessusEnCours += 1;
                    labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                    if (nbProcessusEnCours === 1) $('#ct-sp').click();
                    item_sp = $('.modal-spinner', p);
                    if (item_sp) item_sp.click();
                    readFile(files[0], parent);
                } else {
                    alert('Fichier non valide');
                }
            }
        });
    };
    var ajaxForm = function (element, file, oData) {
        var item_sp, parent;
        // data-content; where the data of the form is located
        /*--------  seek the form -------------*/
        var form = element.parentNode;
        var f_form = $(element).parents('form'); //target
        var form_name = f_form.attr('name');
        for (; $(form).attr('name') !== form_name && form !== null;) form = form.parentNode;
        //--------------------------------------
        parent = $(form).parents('.data-content');
        if (form === null) return;
        var formData = new FormData(form);
        if (file) formData.append('file', file);
        if (oData) formData.append('oData', oData);
        return $.ajax({
            url: $(element).attr('data-href'),
            type: "post",
            dataType: 'json',
            data: formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,   // tell jQuery not to set contentType
            error: function () {
                if (nbProcessusEnCours < 0) $('#ct-sp').addClass('hidden'); // if spinner errs
                App.alert({
                    type: 'danger', // alert's type
                    icon: 'warning',
                    message: 'Un problème est survenu. Veuillez vérifier vos données et si le problème persiste, réassayez plutard, svp!',  // alert's message
                    container: document.querySelector('.ajax-pane-notif'),  // alerts parent container(by default placed after the page breadcrumbs)
                    place: 'prepend', // "append" or "prepend" in container
                    closeInSeconds: 20 // auto close after defined seconds
                    /*close: true, // make alert closable
                     reset: true, // close all previouse alerts first
                     focus: true, // auto scroll to the alert after shown
                     closeInSeconds: 0, // auto close after defined seconds*/
                });
            },
            beforeSend: function () {
                nbProcessusEnCours += 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                if (nbProcessusEnCours === 1) $('#ct-sp').click();
                item_sp = $('.modal-spinner', parent);
                if (item_sp) item_sp.click();
                $(element).attr('disabled', true);

            },
            complete: function () {
                notifAlert = document.querySelector("#notif-active"); //notification
                if (notifAlert) notifAlert.click();
                $(element).attr('disabled', false);
                nbProcessusEnCours -= 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                item_sp = $('.modal-spinner', parent);
                if (item_sp) item_sp.click();
                if (nbProcessusEnCours === 0) $('#ct-sp').click();
                item_sp = $('input[type="reset"], a[type="reset"]', form);
                if (item_sp) item_sp.click(); //reinitialize the form
            },
            success: function (json) {
                json = JSON.parse(json);
                var data = json.item;
                if (data && !data.id && data.action == 0) { //For Create; update the table ici, on traitera l'affichage des lignes nouvellement créées avec un style différent
                    setTimeout(function () { //on create, clear and reload the table
                        $('.filter-cancel', parent).click();
                        //var tr = table.find('input[name="id_' + data.id + '"]', 'tbody').parents('tr')[0];
                    }, 50);
                } else if (data && data.id && data.action == 1) {//For Update, update
                    var table = $('.datatable_ajax', parent);
                    table.dataTable();
                    table.api().ajax.reload();
                    //var tr = table.find('input[name="id_' + data.id + '"]', 'tbody').parents('tr')[0];
                }
            }
        })
    };
    var anActionForm = function (child, elements, option) {
        var parent = $(child).parents('.data-content');
        var items = JSON.stringify(elements);
        var item_sp;
        return $.ajax({
            url: $(child).attr('data-href'),
            type: "post",
            dataType: 'json',
            data: 'items=' + items,
            error: function () {
                nbProcessusEnCours -= 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                item_sp = $('input[type="submit"], a[type="submit"]', parent);
                if (item_sp) item_sp.removeClass('disabled');
                if (nbProcessusEnCours < 0) $('#ct-sp').addClass('hidden');
                App.alert({
                    type: 'danger', // alert's type
                    icon: 'warning',
                    message: 'Un problème est survenu. Veuillez vérifier vos données et réessayez! si le problème persiste, réactualisez la page, svp!',  // alert's message
                    container: document.querySelector('.ajax-pane-notif'),  // alerts parent container(by default placed after the page breadcrumbs)
                    place: 'prepend', // "append" or "prepend" in container
                    closeInSeconds: 20 // auto close after defined seconds
                    /*close: true, // make alert closable
                     reset: true, // close all previouse alerts first
                     focus: true, // auto scroll to the alert after shown
                     */
                });
            },
            beforeSend: function () {
                nbProcessusEnCours += 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                $('input[type="submit"], a[type="submit"]', parent).addClass('disabled');
                item_sp = $('.modal-spinner', parent);
                if (item_sp) item_sp.click();
                if (nbProcessusEnCours === 1) $('#ct-sp').click();
            },
            complete: function () {
                notifAlert = document.querySelector("#notif-active");
                if (notifAlert) notifAlert.click();
                nbProcessusEnCours -= 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                item_sp = $('input[type="submit"], a[type="submit"]', parent);
                if (item_sp) item_sp.removeClass('disabled');
                item_sp = $('.modal-spinner', parent);
                if (item_sp) {
                    item_sp.click();
                    $('.modal-action-terminated', parent).click(); //doit nettoyer le formulaire apres opération; il ferme le modal apres suppression un 'ojet
                }
                if (nbProcessusEnCours <= 0) $('#ct-sp').click();
            },
            success: function (json) {
                json = JSON.parse(json);
                var ids = json.ids;
                if (ids && json.action == 3) {
                  option.DataTable().ajax.reload();
                }
            }
        });
    };
    var reset = function () {
        $('input[type="reset"], a[type="reset"]').click(function () {

            var p = $(this).parents('form');

            if (p.hasClass('data-form')) {//form on the direct body
                $('input[type="text"]', p).attr('readonly', true);
                $('select, textarea', p).attr('disabled', true);
                $('input[type="submit"]', p).attr('disabled', true);
                $('.form-group .copier', p).attr('disabled', true);
                $('input[type="text"]', p).val('');
                //isEditMode = false;
            }

            if ($(this).hasClass('reset-image')) {
                $('.big-view', p).remove();
                $('.preview-pane', p).remove();
                $(input['name="x"']).val('');
                $(input['name="y"']).val('');
                $(input['name="w"']).val('');
                $(input['name="h"']).val('');
            }

            if (p.hasClass('in-modal')) {
                $('.modal-spinner', p).addClass('hidden');
            }
        });

    };
    var displayText = function (display, parent) { //display = where to display text; parent = where to retreive data from
        $('.alerte', display).addClass("hidden"); //cacher l'affichage
        $('.data-content .display-control-static', display).each(function () {
            var input = $('[name="' + $(this).attr("data-display") + '"]', parent);
            if (input.is('a')) {
                $(this).html(input.text());
            }
            else if ('input') {
                $(this).html(input.val());
            }
        });
        $('[name="_id"]', display).val($('[name="_id[]"]', parent).val());
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
                displayText(modal_stk.modalElement, p[2]); // row line portlet. afficherImpl
                uncheckBoxes(p[1]); //td cell
            }, 100);
        }
    };
    var reinitializeModal = function () {
        // $('.data-content', modal_stk.modalElement).addClass("hidden");
        $('.alerte', modal_stk.modalElement).removeClass("hidden");
        /*$('.id', modal_stk.modalElement).val('');
         $('td', modal_stk.modalElement).text('');*/
        //réinitialisation de la tab
        $(modal_stk.modalTab1, modal_stk.modalElement).click(); //cback to the first tab
    };
    var afficher = function () {
        //-------------------------- voir ---------------------
        $('.see_item').click(function () {
            $('.data-content', modal_stk.modalElement).removeClass("hidden"); //actualiser les tabs du modals
            play($(this).parents('.data-content')); // remplacer "this" par le "parent" pour une rotation des elements selections
        });
    };

    return {
        ajaxForm: ajaxForm, //submit post form
        anActionForm: anActionForm, //submit delete form
        displayText: displayText,
        init: function () {
            reset();
            loadFile();
            pageManager();
            afficher();
            supprimer();
            $('#ct-sp').click(function () {
                $(this).toggleClass('hidden');
            });
            $('.modal-spinner').click(function () {
                $(this).toggleClass('hidden');
            });
            tab1.onclick = function () { //gestion des affichages avec  deux modal
                modal_stk.modalElement = '#m-ajax';
                modal_stk.bodyContainer = tabElement_1;
            };
        }
    };


}();

jQuery(document).ready(function () {
    GlobalPageCustomScript.init();
});