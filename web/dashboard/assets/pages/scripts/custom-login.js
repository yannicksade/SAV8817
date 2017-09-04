/**
 * Created by pc on 04/09/2017.
 */

var PageLoginScript = function () {
    var readFile = function (file) {
        var reader = new FileReader();
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
                $('#ct-sp').click();
            }
            if (reader.readyState === reader.LOADING) { //1

            }

            if (reader.readyState === reader.EMPTY) { //0
                //aucune donnée chargée!
                alert('aucun fichier chargé');
                var ctp = $('#ct-sp');
                if(ctp)ctp.click();
            }
        };
        reader.readAsDataURL(file);
    };
    var loadFile = function () {
        var allowedfileTypes = ["jpg", "png", "gif", "jpeg"];
        $('.preview-file-input').change(function (e) {
            var elt = e.target;
            var files = elt.files;
            var fileLength = files.length;
            for (var i = 0; i < fileLength; i++) { //vérification du type de fichier lu
                var fileType = files[0].name.split('.');
                fileType = fileType[fileType.length - 1].toLowerCase();// éviter les extensions en majuscule
                if (allowedfileTypes.indexOf(fileType) !== -1) {
                    var ctp = $('#ct-sp');
                    if(ctp)ctp.click();
                    readFile(files[0]);
                } else {
                    alert('Fichier non valide');
                }
            }
        });
    };

    return {
        init: function () {
            $('#ct-sp').click(function () {
                $(this).toggleClass('hidden');
            });
            loadFile();

        }
    }

}();

jQuery(document).ready(function () {
    PageLoginScript.init();

});