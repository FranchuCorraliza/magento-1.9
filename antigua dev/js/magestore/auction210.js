/** Auction 2.1.0 JS update **/

var AuctionUpdater210 = Class.create();
AuctionUpdater210.prototype = {
	initialize: function(url){
		this.url = url;
	},
	updateInfo : function(elementId,productId){
		var rq_url = this.url;
		var auctionId = ($('auction_id') != null) ? $('auction_id').value : null; 
		if((auctionId != null ) && elementId == 'auction_info_'+auctionId ){
			if($('is_bidding').value != '1'){
				if($('current_bid_id_'+auctionId))
					rq_url += '&current_bid_id=' + $('current_bid_id_'+auctionId).value;
				new Ajax.Updater('results_bid_after_'+auctionId,rq_url,{method: 'get', onComplete:function(){updateAuctionComplete();reFormatPrice('#auction');}, onFailure: ""});
			}
		} else {
			if($('current_bid_id_'+productId) != null)
				rq_url += '&current_bid_id=' + $('current_bid_id_'+productId).value;
			new Ajax.Updater('results_update_auction_'+productId,rq_url,{method: 'get', onComplete:function(){updateAuctionListComplete();},onFailure: ""});
		}
	}
}
var auctionUpdater210 = false;
function updateAuctionInfo(elementId, url, delay){
	if (auctionUpdater210 == false) auctionUpdater210 = new AuctionUpdater210(url);
	auctionUpdater210.updateInfo(elementId,'');
	setTimeout("updateAuctionInfo('"+ elementId +"','"+ url +"','"+delay+"')", delay);
}

var auctionUpdateUrl = false;
var acElementIds = [];
var acProductIds = [];
var acAuctionIds = [];
function updateAuctionListInfo(elementId, url, productId, auctionId, delay){
	acElementIds[acElementIds.length] = elementId;
	acProductIds[acProductIds.length] = productId;
	acAuctionIds[acAuctionIds.length] = auctionId;
	if (auctionUpdateUrl == false){
		auctionUpdateUrl = url;
		setTimeout("auctionUpdateListInfo("+delay+")",delay);
	}
}

function auctionUpdateListInfo(delay){
	if (auctionUpdateUrl == false) return false;
	var requestUrl = auctionUpdateUrl;
	var ids = '';
	var currentIds = '';
	for (var i=0; i<acAuctionIds.length; i++){
		ids += acAuctionIds[i] + ',';
		if ($('current_bid_id_'+acProductIds[i]))
			currentIds += $('current_bid_id_'+acProductIds[i]).value + ',';
		else
			currentIds += '0,';
	}
	ids = ids.substr(0,ids.length-1);
	currentIds = currentIds.substr(0,currentIds.length-1);
	requestUrl += '&ids=' + ids + '&current_bid_ids=' + currentIds;
	new Ajax.Request(requestUrl,{
		method: 'post',
		postBody: '',
		onException: function (xhr, e){},
		onComplete: function(xhr){
			response = xhr.responseText;
			if (response && response.isJSON()){
				completeUpdateListInfo(response.evalJSON());
			}
		}
	});
	setTimeout("auctionUpdateListInfo("+delay+")",delay);
}

function completeUpdateListInfo(response){
	var rsHtml = false;
	for (var i=0; i<acAuctionIds.length; i++){
		rsHtml = response[acAuctionIds[i]];
		if (rsHtml == undefined) continue;
		if (rsHtml == 2) {
			$('current_price'+acProductIds[i]).innerHTML = $('init_price'+acProductIds[i]).innerHTML;
			$('bidder'+acProductIds[i]).innerHTML = '';
			$('current_bid_id_'+acProductIds[i]).value = '';
			$('codecolor'+acProductIds[i]).value = 1;
		} else {
			$('results_update_auction_'+acProductIds[i]).update(rsHtml);
			updateAuctionListComplete();
			$('results_update_auction_'+acProductIds[i]).update('');
		}
	}
	reFormatPrice('#gridauction');
}

function reFormatPrice(selector){
	$$(selector+' span.price').each(function(el){
		currencyConvert.updatePrice(el);
	});
}

var CurrencyConvert = Class.create();
CurrencyConvert.prototype = {
	initialize: function(base,current,convert){
		var basePrice = base.match('1.000.00')[0];
		this.base = base.replace(basePrice,'');
		this.baseDecimalSymbol = basePrice.charAt(5);
		this.baseGroupSymbol = basePrice.charAt(1);
		
		var price = current.match('1.000.00')[0];
		this.current = current.replace(price,'');
		this.convert = parseFloat(convert);
		this.priceFormat = {
			decimalSymbol: price.charAt(5),
			groupLength: 3,
			groupSymbol: price.charAt(1),
			integerRequired: 1,
			pattern: current.replace(price,'%s'),
			precision: 2,
			requiredPrecision: 2,
		};
	},
	updatePrice: function(el){
		var price = el.innerHTML;
		if (price.startsWith(this.base) || price.endsWith(this.base)){
			price = price.replace(this.base,'');
			el.innerHTML = this.formatPrice(price);
		}
	},
	formatPrice: function(price){
		if (price.search(this.baseGroupSymbol) != -1)
			price = price.replace(this.baseGroupSymbol,'');
		price = price.replace(this.baseDecimalSymbol,'.');
		price = parseFloat(price) * this.convert;
		return formatCurrency(price,this.priceFormat);
	},
	getBasePrice: function(price){

		price = price+'';
		var priceFormat = this.priceFormat;
		if (price.search(priceFormat.groupSymbol) != -1)
			price = price.replace(priceFormat.groupSymbol,'');
		price = price.replace(priceFormat.decimalSymbol,'.');
		return parseFloat(price)/this.convert;
	}
}
