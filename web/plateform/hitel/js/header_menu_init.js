/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var element  = $(".tmsearchbox");
var url_cat = $(".tmsearchbox").attr("categorie");
var url_reg = $(".tmsearchbox").attr("region");
var input_cat = $("select[name=search_categories]");
var input_reg = $("select[name=search_region]");
element.submit(
    function(){
        var region = input_cat.val();
        var categorie = input_reg.val();
        var search = $("input[name=search_query]").val();
        if(search.length==0){
            alert("Aucune donnee specifiee");
            return false;
        }
    }
);
$.ajax({
    url :url_cat,
    data:{},
    dataType:"json",
    success: function(data, status){
        console.log(data);
        input_cat.html("");
        for(var i =0;i<data.length;i++)
            input_cat.prepend("<option value='"+data[i].name+"'>"+data[i].name+"</option>");
    }
});
$.ajax({
    url :url_cat,
    data:{},
    dataType:"json",
    success: function(data, status){
        f = data;
        console.log(data);
        input_cat.html("");
        for(var i =0;i<data.length;i++)
            input_cat.prepend("<option value='"+data[i].name+"'>---"+data[i].name+"</option>");
        input_cat.prepend("<option value='0' selected='selected'>Toutes les categories</option>");
    }
});
$.ajax({
    url :url_reg,
    data:{},
    dataType:"json",
    success: function(data, status){
        f = data;
        console.log(data);
        input_reg.html("");
        for(var i =0;i<data.length;i++)
            input_reg.prepend("<option value='"+data[i].ville+"'>---"+data[i].continent+"-"+data[i].pays+"-"+data[i].ville+"</option>");
        input_reg.prepend("<option value='0' selected='selected'>Toutes les regions</option>");
    }
});

        /*
         * INITIALISATION TO CARD
         */
        var initCard = function(){
            var nextId = localStorage.getItem("evm_card_number");
            var content = $("ul.menu_panier");
            content.html("");
            if(nextId==null){
                nextId = 1;
                localStorage.setItem("evm_card_number",nextId);
               
            }
            var somme=0,num=0;
            var PANIER=[];
            for(var i=1;i<nextId;i++){
                var data = localStorage.getItem("evm_card_product" + i);
                if(data!=null){
                    data = data.split(",");
                    var code=data[0],prix=data[1],nom=data[2],photo=data[3];
                    var nums = data[4];
                    var node = "<li title='"+nom+"'>"+
                                          "<div class='col-sm-12 no-pad block-img'>"+
                                             "<div class='col-sm-2 no-pad'>"+
                                                "<img class='' src='"+photo+"'>"+
                                             "</div>"+
                                             "<div class='col-sm-7'>"+
                                                "<a href=''><b>"+nom+"</b></a> code "+code+" "+
                                            " </div>"+
                                             "<div class='col-sm-3 no-pad'>"+
                                                "<b><font color='red'>"+prix+"Fcfa</font></b>(x"+nums+")"+
                                             "</div>"+
                                          "</div>"+
                                       "</li>";
                    if(num<6)
                        content.prepend(node);
                    somme+=prix;
                    num++;
                }
                
            }
            if(num>=6)
                content.append("<li><center>"+num+" produits dans le panier</center></li>");
            $("font.somme_card").html(somme+" cfa");
            $(".num_prod.panier").html(num);
        };
        function isCarding(code_){
            var nextId = localStorage.getItem("evm_card_number");
            var content = $("ul.menu_panier");
            content.html("");
            if(nextId==null){
                nextId = 1;
                localStorage.setItem("evm_card_number",nextId);
               
            }
            for(var i=1;i<nextId;i++){
                var data = localStorage.getItem("evm_card_product" + i);
                if(data!=null){
                    data = data.split(",");
                    var code=data[0];
                    if(code.search(code_)>=0)
                        return i;
                }
            }
            return -1;
        }
        initCard();
        /*
         * ADD ITEMM TO CARD
         */
        $("add-card-prod").click(
            function(){
                var data = $(this).attr("data").split(",");
                var code=data[0],prix=data[1],nom=data[2],photo=data[3];
                if(!confirm("Voulez vous ajouter "+nom+" au panier?"))return;
                console.log(data);
                var id = localStorage.getItem("evm_card_number");
                if(id==null)
                    id = 1;
                localStorage.setItem("evm_card_number"+parseInt(id),$(this).attr("data"));
                localStorage.setItem("evm_card_number",parseInt(id)+1);
                initCard();
            }
        );
        function add_to_card(elt){
                var data = $(elt).attr("data").split(",");
                var code=data[0],prix=data[1],nom=data[2],photo=data[3];
                if(!confirm("Voulez vous ajouter "+nom+" au panier?"))return;
                var isAd = isCarding(code),qt=1;
                if(isAd<0){
                    openNotif('Panier',"Vous avez ajouté 1 "+nom+" au panier?");
                    var id = localStorage.getItem("evm_card_number");
                    if(id==null)
                        id = 1;
                    localStorage.setItem("evm_card_product"+id,$(elt).attr("data")+",1");
                    localStorage.setItem("evm_card_number",parseInt(id)+1)
                }else{
                    var it = localStorage.getItem("evm_card_product"+isAd);
                    var dt = it.split(",");
                    qt=parseInt(dt[4]);
                    qt+=1;
                    openNotif('Panier',"Vous avez ajouté "+qt+" "+nom+" au panier?");
                    localStorage.setItem("evm_card_product"+isAd,$(elt).attr("data")+","+qt);
                }
                console.log(data);
                initCard();
            }
function closeNotif(id){
    var elt = $(id);
    if(elt.hasClass('display')){
        elt.fadeOut(1000);
        elt.removeClass('display');
    }
}
var fermeture=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];

function hideNotif(index, id){
    var element;
    element = fermeture[index];
    clearInterval(element);
    $("#"+id).removeClass("display");
    $("#"+id).css("opacity",1);
}
var UP=-1;
function openNotif(title,body){
    
    Notification.requestPermission( function(status) {$(document).find("#son-notif")[0].play();var n = new Notification(title, {body: body,icon: LINK_FILE_ASSERT+"/plateform/img/logoApm.png"});});
    return;
    var lesNotifs = $("#zone-notif").attr('data').split(" ");
    var elt;
    var inter = [];
    var continu = true;
    var items = $("div#zone-notif .item.display");
    if(items.length == lesNotifs.length){
        //for(var i=0;i<lesNotifs.length/2;i++)
            $(items[++UP%lesNotifs.length]).removeClass("display");
    }
    $(document).find("#son-notif")[0].play();
    lesNotifs.forEach(
        function(item,index){
            elt = $("#"+item);
            if(!elt.hasClass('display') && continu){
                $(".title-notif",elt).html(title);
                $(".body-notif",elt).html(body);
                elt.addClass('display');
                elt.animate({opacity:0.9},10000,function(){
                   fermeture[index] = setInterval("hideNotif("+index+", '"+item+"')",5000);
                });
                continu = false;
            }
        }
    );
}