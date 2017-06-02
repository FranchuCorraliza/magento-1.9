var AsyncIndex = {
    containerId: 'detailed_log',
    url: '',
    isFocused: false,

    init: function(url)
    {
        var self = this;

        self.url = url;

        self.updateLog();
    },


    updateLog: function()
    {
        var self = this;
        var isDeveloper = $('is_developer').checked;

        var request = new Ajax.Request(self.url, {
            method     : 'POST',
            parameters : {'is_developer': isDeveloper },
            loaderArea : false,
            onSuccess : function(transport) {
                $(self.containerId).update(transport.responseText);
                
                setTimeout(function() {
                    self.updateLog();
                }, 100);
            }
        });
    }
};