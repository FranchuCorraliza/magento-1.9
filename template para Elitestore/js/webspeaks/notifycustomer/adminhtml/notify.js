jQuery.noConflict();

jQuery(document).ready(function($) {
    var searchResults = [];
    $('#find-customer-btn').click(function() {
        $('#loading-mask').show();
        var url = $('#find-customer-btn').attr('data-url') + '?q=' +$('#notification_customer_query').val();
        $.getJSON(url, function(data){
            $('#notification_customer_id').empty();
            var options = [];
            options.push('<option value="">---</option>');
            $.each(data, function(index, text) {
                searchResults[text.id] = text.email;
                var option = '<option value="'+text.id+'">'+text.name+'</option>';
                options.push(option);
            });
            $('#notification_customer_id').append(options.join('')).show();
            $('#select-customer-btn').show();
            $('#loading-mask').hide();
        });
    });

    $('#notification_customer_id').on('change', function() {
        var id = $(this).val();
        if (!id) {
            return false;
        }
        $('#notification_customer_email').val(searchResults[id]);
    });
});
