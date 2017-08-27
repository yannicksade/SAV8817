/**
 * Created by Yannick sade on 22/08/2017.
 */

/*This class will contain all the common actions*/


var GlobalPageCustomScript = function () {
    var notifAlert;
    var nbProcessusEnCours = 0;
    var labelProcess = $('#ajax-label-process');
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
            var files = [];
            files = elt.files;
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
        if(oData)formData.append('oData', oData);
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
                    var table = $('.datatable_ajax', parent);
                    if (!table) table = option;
                    table.DataTable().ajax.reload();
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
    return {
        ajaxForm: ajaxForm, //submit post form
        anActionForm: anActionForm, //submit delete form
        init: function () {
            reset();
            loadFile();
            $('#ct-sp').click(function () {
                $(this).toggleClass('hidden');
            });
            $('.modal-spinner').click(function () {
                $(this).toggleClass('hidden');
            });

        }
    };


}();

jQuery(document).ready(function () {
    GlobalPageCustomScript.init();
});