/**
 Custom module for you to write your own javascript functions
 **/
var Custom = function () {
    // public functions
    return {
        init: function () {
            var compressItem1 = document.querySelector('#compress-item_1');
            var compressItem2 = document.querySelector('#compress-item_2');
            var expandItem1 = document.querySelector('#expand-item_1');
            var expandItem2 = document.querySelector('#expand-item_2');
            var tableElement_1 = document.querySelector('#tab-element_1');
            var tableElement_2 = document.querySelector('#tab-element_2');
            var formElement_1 = document.querySelector('#form-element_1');
            var formElement_2 = document.querySelector('#form-element_2');
            var repondreItems = document.querySelectorAll('.repondre');
            var voirItems = document.querySelectorAll('.see_item');
            var modifierItems = document.querySelectorAll('.edit_item');
            var checkBoxes = document.querySelectorAll('.check_item');
            var tab1 = document.querySelector('#tab1'),
                tab2 = document.querySelector('#tab2'),
                modalOffre = document.querySelector('#offre_y'),
                modalDesc = document.querySelector('#desc_y'),
                modalEtat = document.querySelector('#etat_y'),
                modalEtatValue = document.querySelector('#etat-value'),
                modalId = document.querySelector('#id_y'),
                modalCode = document.querySelector('#code_y'),
                modalClient = document.querySelector('#client_y'),
                modalBoutique = document.querySelector('#boutique_y'),
                modalDate = document.querySelector('#date_y');


            //----------------- compression extension de la page -------------------------
            compressItem1.onclick = function () {
                tableElement_1.className = 'col-lg-9';
                formElement_1.className = 'col-lg-3 thumbnail';
                compressItem1.className = 'hidden';
                expandItem1.className = 'fa fa-expand';
            };
            expandItem1.onclick = function () {
                tableElement_1.className = '';
                formElement_1.className = 'hidden';
                expandItem1.className = 'hidden';
                compressItem1.className = 'fa fa-compress';
            };
            compressItem2.onclick = function () {
                tableElement_2.className = 'col-lg-9';
                formElement_2.className = 'col-lg-3 thumbnail';
                compressItem2.className = 'hidden';
                expandItem2.className = 'fa fa-expand';
            };
            expandItem2.onclick = function () {
                tableElement_2.className = '';
                formElement_2.className = 'hidden';
                expandItem2.className = 'hidden';
                compressItem2.className = 'fa fa-compress';
            };

            tab1.onclick = function () {
                document.querySelector('.modal-footer .repondre').style = "display: none";
                document.querySelector('.modal-footer .edit_item').style = "display: inline-block";
                document.querySelector('.modal-footer .delete_item').style = "display: inline-block";
            };
            tab2.onclick = function () {
                document.querySelector('.modal-footer .repondre').style = "display: inline-block";
                document.querySelector('.modal-footer .edit_item').style = "display: none";
                document.querySelector('.modal-footer .delete_item').style = "display: none";

            };

            //----------------------------- Modification et Chargement des formulaires --------------------------------------------

            //----------- changing check box -------------
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
            //------------------------- reinitialise chekboxes of a form after a reset click -----------------
            var resetItems = document.querySelectorAll('input[type="reset"]');
            var length = resetItems.length;
            for (var i = 0; i < length; i++) {
                resetItems[i].onclick = function () {
                    var p = this.parentNode;
                    var chexbs = p.querySelectorAll('input[type="checkbox"]');
                    var elt;
                    var length2 = chexbs.length;
                    for (var o = 0; o < length2; o++) {
                        if ((elt = chexbs[o]).checked) elt.click();
                    }
                    /*var etatElement = p.querySelector('select option');
                     etatElement.innerText = 'Etat relatif au produit';
                     etatElement.selected = "selected";*/

                };
            }

            //---------------------------- repondre -------------------------------------
            length = repondreItems.length;
            for (i = 0; i < length; i++) {
                (function (i) {
                    var item = repondreItems[i];
                    item.onclick = function () {
                        //------ Compress the table 2 -----------------
                        tableElement_2.className = 'col-lg-9 col-md-6 col-xs-12';
                        formElement_2.className = 'col-lg-3 thumbnail';
                        compressItem2.className = 'hidden';
                        expandItem2.className = 'fa fa-expand';
                        //------------------------------- fill the form ---------------------
                        var parent = item.parentNode.parentNode, etatElement;
                        formElement_2.querySelector('input[type="reset"]').click(); //reinitialize the form
                        if (parent.id === "stack1") {//from the modal data 2
                            formElement_2.querySelector('#id_x').value = document.querySelector('#id_y').innerText;
                            formElement_2.querySelector('#code_x').value = document.querySelector('#code_y').innerText;
                            formElement_2.querySelector('#offre_x').value = document.querySelector('#offre_y').innerText;
                            formElement_2.querySelector('#client_x').value = document.querySelector('#client_y').innerText;
                            etatElement = formElement_2.querySelector('#etat_x');
                            etatElement.value = modalEtatValue.innerText;
                            etatElement.selected = "selected";
                        } else {//from the table 2
                            formElement_2.querySelector('#id_x').value = parent.querySelector('#id_item').innerText;
                            formElement_2.querySelector('#code_x').value = parent.querySelector('#code_item').innerText;
                            formElement_2.querySelector('#offre_x').value = parent.querySelector('#offre_item').innerText;
                            formElement_2.querySelector('#client_x').value = parent.querySelector('#client_item').innerText;
                            etatElement = formElement_2.querySelector('#etat_x');
                            etatElement.value = parent.querySelector('#etat_item input').value;
                            etatElement.selected = "selected";
                        }


                    };
                })(i);
            }
            //-------------------------- voir ---------------------
            length = voirItems.length;
            for (var j = 0; j < length; j++) { // show modal
                (function (i) {
                    var item = voirItems[i];
                    item.onclick = function () {
                        var parent = item.parentNode.parentNode;
                        modalId.innerText = parent.querySelector('#id_item').innerText;//id of the current object
                        modalCode.innerText = parent.querySelector('#code_item').innerText;
                        modalClient.innerText = parent.querySelector('#client_item').innerText;
                        modalBoutique.innerText = parent.querySelector('#boutique_item').innerText;
                        modalDate.innerText = parent.querySelector('#date_item').innerText;
                        modalDesc.innerText = parent.querySelector('#desc_item').innerText;
                        var b = parent.querySelector('#etat_item');
                        modalEtatValue.innerText = b.querySelector('input').value;
                        modalEtat.innerText = b.innerText;
                        document.querySelector('#comment_y').value = parent.querySelector('#comment_item').value;
                        modalOffre.innerText = parent.querySelector('#offre_item').innerText;
                    }
                })(j);
            }
            //---------------------- modifier --------------------
            length = modifierItems.length;
            for (var k = 0; k < length; k++) {
                (function (i) {
                    var item = modifierItems[i];
                    item.onclick = function () {
                        //------ Compress the table 1 -----------------
                        tableElement_1.className = 'col-lg-9 col-md-6 col-xs-12';
                        formElement_1.className = 'col-lg-3 thumbnail';
                        compressItem1.className = 'hidden';
                        expandItem1.className = 'fa fa-expand';
                        //------------------------------- fill form ---------------------
                        var parent = item.parentNode.parentNode, offreElement;
                        var a;
                        formElement_1.querySelector('input[type="reset"]').click(); //reinitialize the form
                        if (parent.id === "stack1") {//from model data 1
                            formElement_1.querySelector('.id_x').value = modalId.innerText;
                            formElement_1.querySelector('.code_x').value = document.querySelector('#code_y').innerText;
                            formElement_1.querySelector('#boutique_x').value = document.querySelector('#boutique_y').innerText;
                            formElement_1.querySelector('.desc_x').value = document.querySelector('#desc_y').innerText;
                            a = formElement_1.querySelector('.etat_x');
                            a.value = modalEtatValue.innerText;
                            a.selected = "selected"; // element select
                            formElement_1.querySelector('#offre_x').value = document.querySelector('#offre_y').innerText;
                        } else {//from the table 1
                            formElement_1.querySelector('.id_x').value = parent.querySelector('#id_item').innerText;
                            formElement_1.querySelector('.code_x').value = parent.querySelector('#code_item').innerText;
                            formElement_1.querySelector('#boutique_x').value = parent.querySelector('#boutique_item').innerText;
                            formElement_1.querySelector('.desc_x').value = parent.querySelector('#desc_item').innerText;
                            a = formElement_1.querySelector('.etat_x');
                            a.value = parent.querySelector('#etat_item input').value;
                            a.selected = "selected"; // element select
                            offreElement = formElement_1.querySelector('#offre_x');
                            offreElement.value = parent.querySelector('#offre_item').innerText;
                        }
                    }
                })(k);
            }

        }

    };
}();

jQuery(document).ready(function () {
    Custom.init();
});
