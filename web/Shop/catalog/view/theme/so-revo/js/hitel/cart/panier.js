
function Panier() {
	this.produits= [];

	this.getIndex = function(id){ /* recupere l'index d'un produit dans le tableau de produit*/	
		var i;
		for(i=0; i<this.produits.length; i++){
			if(this.produits[i].getId() == id)
				return i;			
		}
			return -1;
	}

	this.isAdd = function(id){ // teste si le produit avait deja été ajouté
		if(this.getIndex(id) > -1){
			return true;
		}
		return false;
	}

    this.addProduit = function(produit){// qte commandée et priceUnit unitaire
    	if( this.isAdd(produit.getId()) ){
    		var index = this.getIndex(produit.getId());
    		var lastQte = this.produits[index].getQuantity();
    		this.produits[index].setQuantity(parseInt(lastQte)+parseInt(produit.getQuantity()));   	  
        }
    	else{
    		this.produits.push(produit)
    	}
    }

    this.getProduit = function(id){
    	var index = this.getIndex(id);
		if(index >-1)
			return this.produits[index];
        return null;
    }

    this.updateProduit = function(id, quantity, priceUnit){ // new set qte et prix unitaire
    	var index = this.getIndex(id);
    	if( index >-1 ){ // si le produit existe vraiment dans le panier
    		this.produits[index].setQuantity(quantity);
    	}
    }	

    this.removeProduit = function(id) {
        var index = this.getIndex(id);
        if (index >-1) 
        	this.produits.splice(index, 1);
    }
    this.getPoidPanier = function(){
        var total = 0;
        this.produits.forEach(function(produit, index){
            total += parseInt(produit.getQuantity());
        });
        return total;
    }

    this.getPricePanier = function(){
        var total = 0;
		this.produits.forEach(function(produit, index){
			total += (produit.getPriceUnit()*produit.getQuantity());
		});
        return total;
    }
/*
    this.hydraterPanier = function(){
		$('#cart tbody').html("");
		panier.produits.forEach(function(produit, index){
			$('#cart tbody').prepend('<tr><td>'+produit.getId()+'</td> <td>'+produit.getModele()+'</td> <td id="prod'+produit.getId()+'"><input type="number" value="'+produit.getQuantity()+'"><button onclick="updateProduit('+produit.getId()+','+produit.getQuantity()+','+(produit.getPriceUnit()/produit.getQuantity())+')">Update</button> </td><td>'+produit.getPriceUnit()+'</td><td onclick="removeProduit('+produit.getId()+')"><button>Remove</button> </td></tr>');
		});
		$('#cart #total').text("Montant Total: "+ panier.getPriceUnitPanier());
	}*/
}
