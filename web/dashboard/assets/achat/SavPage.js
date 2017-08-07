/**
 Custom module for you to write your own javascript functions
 **/
var GroupeOffrePage = function () {
    //global variables
    //----pages
    var tabElement_1 = $('#tab_1'),
        tabElement_2 = $('#tab_2'),
        tab1 = document.querySelector('#tab1'),
        tab2 = document.querySelector('#tab2');
    //----- modal
    var modal_stk = {'modalElement': "#stack1"};

    //this.modalElement = stk;
    //---- notification
    var notifAlert = document.querySelector("#notif-active");
    //---------------------- modifier --------------------
    var uncheckBoxes = function (parent) {
        var chexbs = $('input[type="checkbox"]:checked', parent);
        var len = chexbs.length;
        for (var i = 0; i < len; i++) {
            chexbs.click();
        }
    };
    var reinitializeModal = function () {
        // var parent = $(elt).parents('.portlet').find('.modal-body');
        if (!$('table', modal_stk.modalElement).hasClass("hidden")) $('table', '.modal').addClass("hidden");
        if ($('.alerte', modal_stk.modalElement).hasClass('hidden')) $('.alerte', '.modal').removeClass("hidden");
        $('td', modal_stk.modalElement).text('');
    };
    var getCheckedBoxes = function (parent) {
        return $('input[type="checkbox"]:checked', parent);
    };
    var play = function (parent) {
        //var parent = $(e).parents('.portlet');
        reinitializeModal();
        var cboxes = getCheckedBoxes(parent);
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
        $('.id', form).val($('.id', modal_stk.modalElement).val());
        $('.code', form).val($('.code', modal_stk.modalElement).text());
        $('.boutique', form).val($('.boutique', modal_stk.modalElement).text());
        $('.desc', form).val($('.desc', modal_stk.modalElement).text());
        var a = $('.etat', form);
        a.val($('.etatID', modal_stk.modalElement).text());
        a.select(); // element select
        var element = $('.offre', form);
        element.val($('.offreID', modal_stk.modalElement).text());
        element.select();
        if (form.hasClass('form2')) {
            element = $('.client', form);
            element.val($('.clientID', modal_stk.modalElement).text()); element.select();
            $('.comment', form).val($('.comment', modal_stk.modalElement).text());
        }
    };
    var tableToFormLoad = function (parent, form_parent) {
        var form = $('.form-element', form_parent);
        $('.id', form).val($('.id', parent).text());
        $('.code', form).val($('.code', parent).text());
        $('.boutique', form).val($('.boutique', parent).text());
        $('.desc', form).val($('.desc', parent).text());
        var a = $('.etat', form);
        a.val($('.etat input', parent).val());
        a.select(); // element select
        var element  = $('.offre', form);
        element.val($('.offreID', parent).text());
        element .select();
        if (form.hasClass('form2')) {
            element = $('.client', form);
            element.val($('.clientID', parent).text());element.select();
            $('.comment', form).val($('.comment', parent).text());
        }
    };
    var reset = function () {
        //------------------------- reinitialise chekboxes of a form after a reset click -----------------
        $('input[type="reset"]').click(function () {
            var p = $(this).parent();
            uncheckBoxes(p); //p= form
            $('input[type="text"]', p).val('');
        });
    };
    var afficherImpl = function (parent) {
        //parent is a row here
        $('.alerte', modal_stk.modalElement).addClass("hidden");
        $('table', modal_stk.modalElement).removeClass("hidden");
        $('.code', modal_stk.modalElement).text($('.code', parent).text());
        $('.id', modal_stk.modalElement).val($('.id', parent).text());
        $('.offreID', modal_stk.modalElement).text($('.offreID', parent).text());
        $('.client', modal_stk.modalElement).text($('.client', parent).text());
        $('.clientID', modal_stk.modalElement).text($('.clientID', parent).text());
        $('.boutique', modal_stk.modalElement).text($('.boutique', parent).text());
        $('.date', modal_stk.modalElement).text($('.date', parent).text());
        $('.desc', modal_stk.modalElement).text($('.desc', parent).text());
        var b = $('.etat', parent);
        $('.etatID', modal_stk.modalElement).text($('input', b).val());
        $('.etat', modal_stk.modalElement).text(b.text());
        $('.comment', modal_stk.modalElement).text($('.comment', parent).text());
        $('.offre', modal_stk.modalElement).text($('.offre', parent).text());
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
                var cboxes= getCheckedBoxes($('tbody', ptl));
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
                if (modal_stk.modalElement === '#stack1') {
                    modifierImpl(tabElement_1);
                    modalToFormLoad(tabElement_1);
                } else if (modal_stk.modalElement === '#stack2') {
                    modifierImpl(tabElement_2);
                    modalToFormLoad(tabElement_2);
                }
            }
        });
    };
    var supprimer = function () {
        $('.delete_item').click(function () {
            var elt, elements = [];
            var p = $(this).parents();
            var c;
            if ($(p[0]).hasClass('modal-footer')) {//suppression à partir de la modal
                c = confirm('Etes vous sûr de vouloir supprimer l\'enregistrement référencée : ' + $('.code', p[1]).text() + ' ?');
                if (c) elements[0] = $('.id', p[1]).val();//from the modal
            } else { //suppression apartir de la table
                var cboxes = getCheckedBoxes($('.tab-element', p[5])), tr, //p5: search for tab element from portlet
                    nb = cboxes.length;
                for (var i = 0; i < nb; i++) {
                    elt = $(cboxes[i]);
                    tr = elt.parents()[2]; // recupérer la ligne du tableau
                    c = confirm('etes vous sûr de vouloir supprimer l\'enregistrement référencée : ' + $('.code', tr).text() + ' ?');//confirmation
                    if (!c)continue;// skip the current element if no
                    elements[i] = $('.id', tr).text();
                }
            }
            if (elements.length > 0) deleteElement(elements, p[5]);
        });
    };

    var deleteElement = function (elements, parent) {
        var items = JSON.stringify(elements);
        return $.ajax({
            url: "delete",
            type: "post",
            dataType: 'json',
            data: 'items=' + items,
            error: function () {
                elements = null;
                alert("Un problème est survenu. veuillez vérifier vos données et/ou actualiser la page, svp!");
            },
            beforeSend: function () {
            },
            complete: function () {
                notifAlert.click();
                elements = null;
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
    var ajaxForm = function (form) { // create and update
        if (form === undefined || !$(form).parent().hasClass('form1') && !$(form).parent().hasClass('form2')) return;
        var formData = new FormData(form);
        if (formData['service_apres_vente'] === null) return;
        return $.ajax({
            url: "index",
            type: "post",
            dataType: 'json',
            data: formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,   // tell jQuery not to set contentType
            error: function () {
                alert("Un problème est survenu. Veuillez vérifier vos données et réactualiser la page, svp!");
                form.querySelector('input[type="reset"]').click(); //reinitialize the form
            },
            beforeSend: function () {
            },
            complete: function () {
                form.querySelector('input[type="reset"]').click(); //reinitialize the form
                notifAlert.click();
            },
            success: function (json) {
                json = JSON.parse(json);
                var data = json.item;
                var parent = $(form).parents('.portlet');
                var table = $('.datatable_ajax', parent).dataTable();
                if (data !== null && data.isNew === true) {
                    setTimeout(function () { //on create, clear and reload the table
                        $('.filter-cancel', parent).click();
                    }, 50);
                } else if (data !== null && data.id !== null && data.isNew === false) {//Update the table
                    if($(form).parent().hasClass('form1')) {
                        if (data.descriptionPanne !== "undefined") {
                            var tr = table.find('input[name="id_' + data.id + '"]', 'tbody').parents('tr')[0];
                            table.api().cell(tr, '.desc').data('<span class="desc">' + data.descriptionPanne + '</span>');
                        }
                    }else if($(form).parent().hasClass('form2')){
                        table.api().ajax.reload();
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
    var remplacerElement = function () {
        //----------- replacer un element: changing check box -------------
        var checkBoxes = document.querySelectorAll('.check_item');
        length = checkBoxes.length;
        for (i = 0; i < length; i++) {
            checkBoxes[i].onchange = function () {
                var parent = this.parentNode,
                    etatItem = parent.querySelector('.changeable_item');
                if (this.checked) {
                    parent.querySelector('#replacer').className = '';
                    etatItem.className = 'hidden changeable_item';
                } else {
                    parent.querySelector('#replacer').className = 'hidden';
                    etatItem.className = 'form-control changeable_item';
                }
                //parent.parentNode.querySelector('#etat_x').value = "Non soumise";
            };
        }
    };
    // public functions
    return {
        init: function () {
            reset();
            modifier();
            supprimer();
            pageForm();
            afficher();
            tab1.onclick = function () { //gestion des affichages avec  deux modal
                modal_stk.modalElement = '#stack1';
            };

            tab2.onclick = function () {
                modal_stk.modalElement = '#stack2';
            };

            $('.composer_item').click(function () {
                modifierImpl($(this).parents('.portlet'));
            });
            $('.tab-reload').click(function () {
                $('.datatable_ajax', $(this).parents('.portlet')).DataTable().ajax.reload();
            });
            $('input[type="submit"]').click(function(e){
                e.returnValue = false;
                if (e.preventDefault) {
                    e.preventDefault();
                }
                ajaxForm(this.parentNode);
            });
        }
    };
}();

jQuery(document).ready(function () {
    GroupeOffrePage.init();
});
