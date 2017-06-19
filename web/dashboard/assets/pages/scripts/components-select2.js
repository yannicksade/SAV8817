var ComponentsSelect2 = function () {

    var handleDemo = function () {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Select a State";

        $(".select2-multiple").select2({
            placeholder: placeholder,
            width: null
        });

        $(".select2-allow-clear").select2({
            allowClear: true,
            placeholder: placeholder,
            width: null
        });

        // @see https://select2.github.io/examples.html#data-ajax
        function formatRepo(repo) {
            if (repo.loading) return repo.text;

            var markup = "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__avatar img-responsive'><img src='" + repo.url_image + "' /></div>" +
                "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title'>" + repo.designation + "</div>";

            if (repo.description) {
                markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
            }


            /* markup += "<div class='select2-result-repository__statistics'>" +
             "<div class='select2-result-repository__forks'><span class='glyphicon glyphicon-flash'></span> " + repo.forks_count + " Forks</div>" +
             "<div class='select2-result-repository__stargazers'><span class='glyphicon glyphicon-star'></span> " + repo.stargazers_count + " Stars</div>" +
             "<div class='select2-result-repository__watchers'><span class='glyphicon glyphicon-eye-open'></span> " + repo.watchers_count + " Watchers</div>" +
             "</div>" +
             "</div></div>";*/
            return markup;
        }

        function formatRepoSelection(repo) {
            return repo.designation || repo.text;
        }

        var offre;
        /*        var xhr = new XMLHttpRequest();
         xhr.open('GET');
         xhr.onreadystatechange = function () {
         if (xhr.readyState == 4 && xhr.status == 200) {
         var data = JSON.parse(xhr.responseText);*/
        $(".js-data-example-ajax").select2({
            width: "off",
            placeholder: "Saisissez de préférence le code du produit",
            allowClear: true,
            ajax: {
                url: "http://localhost/SAV8817.git/web/app_dev.php/apm/vente/offre/query/ajax-offre",
                dataType: "json",
                delay: 500,
                data: function (params) {
                    return {
                        q: params.term // search term
                        //page: params.page
                    };
                },
                processResults: function (data) { //traite les donnée avant de les retouner
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {// let our custom formatter work
                return markup;
            },
            minimumInputLength: 1, //déclenche la recherche pour un minimun de 1 donnée
            templateResult: formatRepo, //fournit un template pour le formattage des données
            templateSelection: formatRepoSelection, //selectionne l'attribut à afficher
            minimumResultsForSearch: 1 // rends disponible la search bar si le resultat de données est supérieur ou egal à 10
        });


        $("button[data-select2-open]").click(function () {
            $("#" + $(this).data("select2-open")).select2("open");
        });

        $(":checkbox").on("click", function () {
            $(this).parent().nextAll("select").prop("disabled", !this.checked);
        });

        // copy Bootstrap validation states to Select2 dropdown
        //
        // add .has-waring, .has-error, .has-succes to the Select2 dropdown
        // (was #select2-drop in Select2 v3.x, in Select2 v4 can be selected via
        // body > .select2-container) if _any_ of the opened Select2's parents
        // has one of these forementioned classes (YUCK! ;-))
        $(".select2, .select2-multiple, .select2-allow-clear, .js-data-example-ajax").on("select2:open", function () {
            if ($(this).parents("[class*='has-']").length) {
                var classNames = $(this).parents("[class*='has-']")[0].className.split(/\s+/);

                for (var i = 0; i < classNames.length; ++i) {
                    if (classNames[i].match("has-")) {
                        $("body > .select2-container").addClass(classNames[i]);
                    }
                }
            }
        });

        $(".js-btn-set-scaling-classes").on("click", function () {
            $("#select2-multiple-input-sm, #select2-single-input-sm").next(".select2-container--bootstrap").addClass("input-sm");
            $("#select2-multiple-input-lg, #select2-single-input-lg").next(".select2-container--bootstrap").addClass("input-lg");
            $(this).removeClass("btn-primary btn-outline").prop("disabled", true);
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleDemo();
        }
    };

}();

if (App.isAngularJsApp() === false) {
    jQuery(document).ready(function () {
        ComponentsSelect2.init();
    });
}