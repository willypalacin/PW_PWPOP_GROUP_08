$(document).ready(function() {
    $('#upload-form').submit(function(event) {
        var payload = {
            username: $('input[name=title]').val(),
            password: $('input[name=title]').val()
        };

        $.ajax({
            type: 'POST',
            url: '/upload',
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify(payload), // our data object
            dataType: 'json' // what type of data do we expect back from the server
        })
            .done(function(data) {
                console.log("hola, dins de ajax.js, de upload-form");
                console.log(data);
            })
            .fail(function(error) {
                console.log(error);
            });

        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
    });
});
