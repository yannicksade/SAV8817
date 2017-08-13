/**
 Custom module for you to write your own javascript functions
 **/
var OffrePage = function () {
    //global variables
    //----pages
    var actionConfirm;
    var modalConfirm = $('#modal-confirm');
    var tabElement_1 = $('#tab_1'),
        tabElement_2 = $('#tab_2'),
        tab1 = document.querySelector('#tab1'),
        tab2 = document.querySelector('#tab2');
    var uploadedFile;
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

    var readFile = function (file, container) {
        var reader = new FileReader();
        reader.onload = function (evt) {
            if (reader.readyState === 2) {//2
                uploadedFile = evt.target.result;
                var element;
                element = document.createElement('img');
                element.style.maxWidth = "830px";
                element.style.maxHeight = "622px";
                element.src = this.result;
                element.className = "crop-image";
                var divElement = document.createElement('div');
                divElement.className = "big-view";
                divElement.appendChild(element);
                var oldView = container.querySelector('.big-view');
                if (oldView === null) container.appendChild(divElement); else container.replaceChild(divElement, oldView);

                element = element.cloneNode(false);
                //document.querySelector('a[href="#image_1"] '+modal_stk.modalElement);
                //$('.url-dir').val() + '1_' + $('.images', parent).text();
                element.className = "jcrop-preview";
                divElement = divElement.cloneNode(false);
                divElement.className = "preview-container";
                divElement.appendChild(element);
                var ppane = divElement.cloneNode(false);
                ppane.className = "preview-pane";
                ppane.appendChild(divElement);
                var oldPrev = container.querySelector('.preview-pane');
                if (oldPrev === null) container.appendChild(ppane); else container.replaceChild(ppane, oldPrev);
                FormImageCrop.init();
                nbProcessusEnCours -= 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                if (nbProcessusEnCours === 0) $('#ct-sp').click();
                $('.modal-spinner', container.parentNode).click();
            }
            if (reader.readyState === reader.LOADING) { //1

            }

            if (reader.readyState === reader.EMPTY) { //0
                //aucune donnée chargée!
                alert('aucun fichier chargé');
                nbProcessusEnCours -= 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                if (nbProcessusEnCours === 0) $('#ct-sp').click();
                $('.modal-spinner', container.parentNode).click();
            }
        };
        reader.readAsDataURL(file);
    };
    var loadFile = function () {
        var allowfileTypes = ["jpg", "png", "gif", "jpeg"];
        $('input[type="file"]').change(function () {
            var parent;
            var files = [];
            files = this.files;
            var fileLength = files.length;
            for (var i = 0; i < fileLength; i++) { //vérification du type de fichier lu
                var fileType = files[0].name.split('.');
                fileType = fileType[fileType.length - 1].toLowerCase();// éviter les extensions en majuscule
                if (allowfileTypes.indexOf(fileType) !== -1) {
                    parent = this.parentNode.parentNode; // modalFooter
                    //$(parent).modalmanager('loading');
                    nbProcessusEnCours += 1;
                    labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                    if (nbProcessusEnCours === 1) $('#ct-sp').click();
                    $('.modal-spinner', parent.parentNode).click();
                    readFile(files[0], parent);
                } else {
                    //modalNotification('images non valide');
                    alert('Fichier non valide');
                }
            }
        });
    };

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
    var nbProcessusEnCours = 0,
        labelProcess = $('#ajax-label-process');
    var isEditMode = false;
    //---- notification
    var notifAlert = document.querySelector("#notif-active");
    //---------------------- modifier --------------------
    var uncheckBoxes = function (parent) {
        $('input[type="checkbox"]:checked', parent).attr('checked', false);
    };
    var reinitializeModal = function () {
        $('.data-content', modal_stk.modalElement).addClass("hidden");
        $('.alerte', modal_stk.modalElement).removeClass("hidden");

        $('.id', modal_stk.modalElement).val('');
        $('td', modal_stk.modalElement).text('');
        //réinitialisation de la tab
        $(modal_stk.modalTab1, modal_stk.modalElement).click();
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
    var reset = function () {
        //------------------------- reinitialize chekboxes of a form after a reset click -----------------
        $('input[type="reset"]').click(function () {
            var p;
            var raz = $(this);
            if (raz.hasClass('reset-modal')) {
                p = raz.parents('.tab-pane');
                if (raz.hasClass('reset-image')) {
                    var parent = this.parentNode.parentNode.parentNode;//modal-Footer
                    var bv = parent.querySelector('.big-view');
                    bv.parentNode.removeChild(bv);
                    var prev = parent.querySelector('.preview-pane');
                    prev.parentNode.removeChild(prev);
                }
           $('.modal-spinner', p).addClass('hidden');
            } else {
                p = raz.parent();
                $('input[type="text"]', p).val(''); // in a case where p = form, rinitialize the form
                $('input[type="text"]', p).attr('readonly', true);
                $('select, textarea', p).attr('disabled', true);
                uncheckBoxes(p);
                $('input[type="submit"]', p).attr('disabled', true);
                $('.form-group .copier', p).attr('disabled', true);
                isEditMode = false;
            }
        });
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
            var ptl = p[5];// portlet
            if (!$(p[0]).hasClass('modal-footer')) { //chargement du formulaire partant du tableau
                modifierImpl(ptl);
                var cboxes = getCheckedBoxes($('tbody', ptl));
                var nb = cboxes.length;
                for (var i = 0; i < nb; i++) {
                    var elt = cboxes[i];
                    setTimeout(function () {
                        var p = $(elt).parents();
                        tableToFormLoad(p[2], ptl);// 1- a row of the table 2- portlet, parent of the form
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
            //alt.removeEventListener('click',null, false);
            var elt, elements = [];
            var p = $(this).parents();
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
                if (elements.length > 0) deleteElement(elements, p[5], p[0]);
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
        $('.input-group-addon input[type="checkbox"]').change(function () { //individual check and enabling text input
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
            e.returnValue = false;
            if (e.preventDefault()) e.preventDefault();
            var btn = this;
            if (btn.id === "crop") {
                var id = $('.id', modal_stk.modalElement).val();
                $(btn).attr('href', 'image?id=' + id);
            } else uploadedFile = null;
            //if (isEditMode) $('input[type="submit"]', p[2]).attr('disabled', true); désactiver les boutton soubmit lors d'un traitement
            ajaxForm(this.parentNode, $(this).attr("href"), uploadedFile); //troixième parent du boutton submit: portlet ou modal-footer
        });
    };
    var deleteElement = function (elements, parent, fromParent) {
        fromParent = $(fromParent);
        var items = JSON.stringify(elements);
        return $.ajax({
            url: "delete",
            type: "post",
            dataType: 'json',
            data: 'items=' + items,
            error: function () {
                nbProcessusEnCours -= 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                $('input[type="submit"]', fromParent).removeClass('disabled');
                if (fromParent.hasClass('modal-footer')) {
                    $('.modal-spinner', fromParent).click();
                }
                if (nbProcessusEnCours === 0) $('#ct-sp').click();
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
                $('input[type="submit"]', fromParent).addClass('disabled');
                if (fromParent.hasClass('modal-footer')) {
                    $('.modal-spinner', fromParent).click();
                }
                if (nbProcessusEnCours === 1) $('#ct-sp').click();
            },
            complete: function () {
                nbProcessusEnCours -= 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                $('input[type="submit"]', fromParent).removeClass('disabled');
                if (fromParent.hasClass('modal-footer')) {
                    $('.modal-spinner', fromParent).click();
                    setTimeout(function () {
                        $('.modal-action-terminated').click()
                        ;
                    }, 200);
                }
                if (nbProcessusEnCours === 0) $('#ct-sp').click();
                notifAlert.click();
            },
            success: function (json) {
                json = JSON.parse(json);
                var data = json.ids;
                if (data !== null && data !== undefined) {
                    var length = data.length;
                    for (var i = 0; i < length; i++) {
                        (function (i) {
                            setTimeout(function () {
                                var table = $('.datatable_ajax', parent).dataTable();
                                var tr = table.find('input[name="id_' + data[i] + '"]', 'tbody').parents('tr')[0];
                                table.api().row(tr).remove().draw();
                            }, 100 + 50 * i);
                        })(i);
                    }
                }
            }
        });
    };
    var ajaxForm = function (p, url, file) { // create and update a form from either the modal or anywhere in the body
        //if (parent === undefined || !$(parent).parent().hasClass('parent1') && !$(parent).parent().hasClass('parent2')) return; //les parentulaires doivente être encapsulés dans parent1 ou parent2 uniquement
        /*if (formData['offre'] === null) return;*/
        var parent = p.parentNode.parentNode, //a form or a container of a form (ex:modal-footer)
            pTab;
        var form, isaModal = false;
        if ($(parent).hasClass('modal-footer')) {isaModal = true; pTab = $(parent).parents('.tab-pane');}
        form = parent.querySelector('form');
        var formData = new FormData(form);
        if (file !== null) formData.append('file', file);
        return $.ajax({
            url: url,
            type: "post",
            dataType: 'json',
            data: formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,   // tell jQuery not to set contentType
            error: function () {
                nbProcessusEnCours -= 1;
                form.querySelector('input[type="reset"]').click(); //reinitialize the form
                $('input[type="submit"]', parent).removeClass('disabled');
                if (nbProcessusEnCours === 0) $('#ct-sp').click();
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
                if (isaModal) $('.modal-spinner', pTab).click();
                $('input[type="submit"]', parent).addClass('disabled');
                if (nbProcessusEnCours === 1) $('#ct-sp').click();
            },
            complete: function () {
                form.querySelector('input[type="reset"]').click(); //reinitialize the form
                $('input[type="submit"]', parent).removeClass('disabled');
                nbProcessusEnCours -= 1;
                labelProcess.html(nbProcessusEnCours + ' traitement(s) en cours...');
                if (isaModal) $('.modal-spinner', pTab).click();
                if (nbProcessusEnCours === 0) $('#ct-sp').click();
                notifAlert.click();
            },
            success: function (json) {
                json = JSON.parse(json);
                var data = json.item;
                var table = $('.datatable_ajax', parent).dataTable();
                if (data !== null && data.id !== null && data.isNew === true) { //ici, on traitera l'affichage des lignes nouvellement créées avec un style différent
                    setTimeout(function () { //on create, clear and reload the table
                        $('.filter-cancel', parent).click();
                        //var tr = table.find('input[name="id_' + data.id + '"]', 'tbody').parents('tr')[0];
                    }, 50);
                } else if (data !== null && data.id !== null && data.isNew === false) {//Update the table
                    if ($(form).parent().hasClass('form1')) {//ici, on traitera l'affichage des lignes modifiées avec un style différent
                        table.api().ajax.reload();
                        //var tr = table.find('input[name="id_' + data.id + '"]', 'tbody').parents('tr')[0];
                    }
                }
            }
        })
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
            reset();
            modifier();
            supprimer();
            pageForm();
            afficher();
            loadFile();
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
            $('#ct-sp').click(function () {
                $(this).toggleClass('hidden');
            });
            $('.modal-spinner').click(function () {
                $(this).toggleClass('hidden');
            });

            $('.tab-reload').click(function () {
                $('.datatable_ajax', $(this).parents('.portlet')).DataTable().ajax.reload();
            });

        }
    };
}();

jQuery(document).ready(function () {
    OffrePage.init();
});
