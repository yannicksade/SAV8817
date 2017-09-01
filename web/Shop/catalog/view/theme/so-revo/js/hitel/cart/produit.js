function Produit(id, name, model, quantity, priceUnit) {
	this.id = id;
	this.name = name;
	this.model = model;
	this.quantity = quantity;
	this.priceUnit = priceUnit;

	this.getId = function(){
		return this.id;
	}
	this.setId = function (id){
		this.id = id;
	}
	this.getName = function() {
		return this.name;
	}
	this.setName = function(name) {
		this.name = name;
	}

	this.getModel = function() {
		return this.model;
	}
	this.setModel = function(model) {
		this.model = model;
	}
	this.getQuantity = function() {
		return this.quantity;
	}
	this.setQuantity = function(quantity) {
		this.quantity = quantity;
	}
	this.setPriceUnit= function(priceUnit) {
		this.priceUnit = priceUnit;
	}
	this.getPriceUnit = function() {
		return this.priceUnit;
	}

	this.toString = function(){
		var str = "{ id: "+this.getId()+", Name: "+this.getName()+", Model: "+this.getModel()+", quantity: "+this.getQuantity()+",priceUnit: "+this.getPriceUnit()+" }";
		return str;
	}
}
