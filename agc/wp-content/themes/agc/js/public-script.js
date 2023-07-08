jQuery(document).ready(function ($) {
    $('.filter-button').on('click', function (e) {
        e.preventDefault();
        var filterValue = $('#product-filter').val();
        console.log(filterValue);

//        console.log(filter);
        $.ajax({
            url: AGCPUBLIC.ajaxurl,
            type: 'POST',
            data: {
                action: 'filter_products',
                filterValue: filterValue
            },
            success: function (response) {
                $('.product-list').html(response);
            },
            error: function (error) {
                console.log(error);
            }
        });
    });

});
