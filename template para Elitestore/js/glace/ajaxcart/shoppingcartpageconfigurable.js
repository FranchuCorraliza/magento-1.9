//update reload price function for configurable products to work on shopping cart page
if (typeof(Product) !== 'undefined') {    
    Product.Config.prototype.reloadPrice = function reloadPrice() {
        if (this.config.disablePriceReload) {
            return;
        }
        var price    = 0;
        var oldPrice = 0;
        for(var i=this.settings.length-1;i>=0;i--){
            var selected = this.settings[i].options[this.settings[i].selectedIndex];
            if(selected.config){
                price    += parseFloat(selected.config.price);
                oldPrice += parseFloat(selected.config.oldPrice);
            }
        }
        
        //removed these lines used on product page
        //optionsPrice.changePrice('config', {'price': price, 'oldPrice': oldPrice});
        //optionsPrice.reload();

        return price;

        if($('product-price-'+this.config.productId)){
            $('product-price-'+this.config.productId).innerHTML = price;
        }
        this.reloadOldPrice();
    };
}