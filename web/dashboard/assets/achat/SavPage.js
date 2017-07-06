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
    var stk = $('#stack'),
        modalID = $('.id', stk),
        modalOffreID = $('.offreID', stk),
        modalEtatValue = $('.etatID', stk),
        modalOffre = $('.offre', stk),
        modalDesc = $('.desc', stk),
        modalEtat = $('.etat', stk),
        modalCode = $('.code', stk),
        modalClient = $('.client', stk),
        modalBoutique = $('.boutique', stk),
        modalComment = $('.comment', stk),
        modalDate = $('.date', stk);
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
    var getCheckedBoxes = function (parent) {
        return $('input[type="checkbox"]:checked', parent);
    };
    var play = function (parent) {
        var cboxes = getCheckedBoxes(parent),
            nb = cboxes.length;
        for (var i = 0; i < nb; i++) {
            var elt = $(cboxes[i]);
            if (elt === null) continue;
            setTimeout(function () {
                var p = elt.parents();
                afficherImpl(p[2]);
                uncheckBoxes(p[1]);
            }, 100);
        }
    };
    var afficher = function (parent) {
        //-------------------------- voir ---------------------
        $('.see_item').click(function () {
            play(parent);
        });
        $('.modal-suiv', stk).click(function () {
            play(parent);
        });
    };
    var modalToFormLoad = function (parent) {
        var form = $('.form-element', parent);
        $('.id', form).val(modalID.val());
        $('.code', form).val(modalCode.text());
        $('.boutique', form).val(modalBoutique.text());
        $('.desc', form).val(modalDesc.text());
        var a = $('.etat', form);
        a.val(modalEtatValue.text());
        a.selected = "selected"; // element select
        var offreElement = $('.offre', form);
        offreElement.val(modalOffreID.text());
        offreElement.selected = "selected";
    };
    var tableToFormLoad = function (parent, form_parent) {
        var form = $('.form-element', form_parent);
        $('.id', form).val($('.id', parent).text());
        $('.code', form).val($('.code', parent).text());
        $('.boutique', form).val($('.boutique', parent).text());
        $('.desc', form).val($('.desc', parent).text());
        var a = $('.etat', form);
        a.val($('.etat input', parent).val());
        a.selected = "selected"; // element select
        var offreElement = $('.offre', form);
        offreElement.val($('.offreID', parent).text());
        offreElement.selected = "selected";
    };
    var reset = function () {
        //------------------------- reinitialise chekboxes of a form after a reset click -----------------
        $('input[type="reset"]').click(function () {
            var p = $(this).parent();
            uncheckBoxes(p);
            $('.id', p).val("");
            $('.code', p).val("");
            $('.boutique', p).val("");
            $('.desc', p).val("");
            $('.offre', p).val("");
            $('.etat', p).val("");
        });
    };
    var afficherImpl = function (parent) {
        $('.alerte', stk).addClass("hidden");
        $('table', stk).removeClass("hidden");
        modalID.val($('.id', parent).text());
        modalOffreID.text($('.offreID', parent).text());
        modalCode.text($('.code', parent).text());
        modalClient.text($('.client', parent).text());
        modalBoutique.text($('.boutique', parent).text());
        modalDate.text($('.date', parent).text());
        modalDesc.text($('.desc', parent).text());
        var b = $('.etat', parent);
        modalEtatValue.text($('input', b).val());
        modalEtat.text(b.text());
        modalComment.val($('.comment', parent).val());
        modalOffre.text($('.offre_item', parent).text());
    };
    var modifierImpl = function (parent) {
        //------ Compress the table 1 -----------------
        var form = $('.form-element', parent);
        $('.tab-element', parent).addClass('col-lg-9 col-md-6 col-xs-12');
        form.removeClass('hidden');
        $('.compress-item', parent).addClass('hidden');
        $('.expand-item', parent).removeClass('hidden');
        //reinitialize the form
        $('input[type="reset"]', parent).click();
        //formElement_1.querySelector('#check2_x').removeAttribute('disabled');
    };
    var modifier = function () {
        $('.edit_item').click(function () {
            var p = $(this).parents();
            var ptl = p[5];// portlet
            modifierImpl(ptl);
            if (!$(p[0]).hasClass('modal-footer')) { //chargement du formulaire partant du tableau
                var elt = getCheckedBoxes($('tbody', ptl));
                elt = $(elt).last();
                tableToFormLoad(elt.parents()[2], ptl);// 1- a row of the table 2- portlet, parent of the form
            } else modalToFormLoad(); //chargement du formulaire partant de la modal
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
            if (elements !== null) deleteElement(elements, p[5]);
        });
    };
    var repondre = function () {
        //---------------------------- repondre -------------------------------------
        $('.repondre_item').click(function () {
            var p = this.parents();
            var parent = p[5]; //portlet, tab element
            var form = $('.form-element', parent), etatElement;
            //------ Compress the table 2 -----------------
            $('.tab-element', parent).addClass('col-lg-9 col-md-6 col-xs-12');
            form.removeClass('hidden');
            $('.compress-item', parent).addClass('hidden');
            $('.expand-item', parent).removeClass('hidden');
            //------------------------------- fill the form ---------------------
            $('input[type="reset"]', form).click(); //reinitialize the form
            if ($(p[0]).hasClass('modal-footer')) {//from the modal data 2
                $('.id', form).val(modalID.val());
                $('.code', form).val(modalCode.text());
                $('.offre', form).val(modalOffre.text());
                $('.client', form).val(modalClient.text());
                etatElement = $('.etat', form);
                etatElement.val(modalEtatValue.text());
                etatElement.selected = "selected";
            } else {//from the table 2
                $('.id', form).val($('.id', parent).text());
                $('.code', form).val($('.code', parent).text());
                $('.offre', form).val($('.offre', parent).text());
                $('.client', form).val($('.client', parent).text());
                etatElement = $('.etat', form);
                etatElement.val($('.etat input', parent).val());
                etatElement.selected = "selected";
            }
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
    var ajaxForm = function (e) {
        e.returnValue = false;
        if (e.preventDefault) {
            e.preventDefault();
        }
        var form = this.parentNode;
        if (form === undefined) return;
        var formData = new FormData(form);
        if (formData['service_apres_vente'] === null) return;
        return $.ajax({
            url: "http://localhost/SAV8817.git/web/app_dev.php/apm/achat/service_apres_vente/",
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
                if (data !== null && data.isNew === true) {
                    setTimeout(function () { //on create, clear and reload the table
                        $('.filter-cancel', parent).click();
                    }, 50);
                } else if (data !== null && data.id !== null && data.isNew === false) {//Update the table
                    if (data.descriptionPanne !== "undefined") {
                        var table = $('.datatable_ajax', parent).dataTable();
                        var tr = table.find('input[name="id_' + data.id + '"]', 'tbody').parents('tr')[0];
                        table.api().cell(tr, '.desc').data('<span class="desc">' + data.descriptionPanne + '</span>');
                    }
                }
            }
        })
    };
    var pageForm = function (parent) {
        //----------------- compression et extension de la page -------------------------
        //parent = parent.parents('.portlet');
        $('.compress-item', parent).click(function () {
            $('.tab-element', parent).addClass('col-lg-9 col-md-6 col-xs-12');
            $('.form-element', parent).removeClass('hidden');
            $('.compress-item', parent).addClass('hidden');
            $('.expand-item', parent).removeClass('hidden');
        });
        $('.expand-item', parent).click(function () {
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
            repondre();
            pageForm(tabElement_1);
            afficher(tabElement_1);
            tab1.onclick = function () {
                pageForm(tabElement_1);
                afficher(tabElement_1);
                var modalRep = $('.modal-footer .repondre_item');
                modalRep.addClass('hidden');
                var modalModif = $('.modal-footer .edit_item'); // boutton a afficher
                modalModif.removeClass('hidden');
                var modalDelete = $('.modal-footer .delete_item');
                modalDelete.css('href', ".form-element_1");
                modalModif.removeClass('hidden');
                modalDelete.href = ".form-element_1"
            };

            tab2.onclick = function () {
                pageForm(tabElement_2);
                afficher(tabElement_2);
                var modalRep = $('.modal-footer .repondre_item');
                modalRep.removeClass('hidden');
                modalRep.css('href', ".form-element_2");
                $('.modal-footer .edit_item').addClass('hidden');
                $('.modal-footer .delete_item').addClass('hidden');
            };

            $('.composer_item').click(function () {
                modifierImpl($(this).parents('.portlet'));
            });
            $('.tab-reload').click(function () {
                $('.datatable_ajax', $(this).parents('.portlet')).DataTable().ajax.reload();
            });
            document.querySelector('input[type="submit"]').addEventListener('click', ajaxForm, false);
        }
    };
}();

jQuery(document).ready(function () {
    GroupeOffrePage.init();
});
