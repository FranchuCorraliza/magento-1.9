var Orderrequest = Class.create();
Orderrequest.prototype = {
	initialize: function(form, requestUrl) {
		this.requestUrl = requestUrl;
		this.form = form;
		//if ($(this.form)) {
		//	$(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
		//}
		this.onSave = this.nextStep.bindAsEventListener(this);
	},

	//save: function() {
            //alert(this.requestUrl);
		//var validator = new Validation(this.form);
                //var requestUrl = requestUrl + '';
		//if (validator.validate()) {
                    //TINY.box.show(this.requestUrl, 1, 400, 300, 1);
		//}
        //}


        save: function() {
		var validator = new Validation(this.form);
		if (validator.validate()) {
			$('time-please-wait').show();
			var request = new Ajax.Request(
				this.requestUrl,
				{
					method:'post',
					onComplete: '',
					onSuccess: this.onSave,
					onFailure: '',
          parameters: Form.serialize(this.form)
				}
			);
		}
	},

	nextStep: function(transport) {
		if (transport && transport.responseText){
			try{
				response = eval('(' + transport.responseText + ')');
			}
			catch (e) {
				response = {};
			}
		}
		// if (response.error != ''){
			// $('time-please-wait').hide();
			// $('orderrequest_message').hide();
			// alert(response.error);
                        // return false;
		// }
		if (response.html) {
			$('time-please-wait').hide();
			$('orderrequest_message').show();
			$('orderrequestForm').reset();
			refreshImage();
		} else {
			$('time-please-wait').hide();
			$('orderrequest_message').hide();
			alert(response.error);
				return false;
		}
	}
}

