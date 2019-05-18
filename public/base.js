function validarModal() {
    let min_price = document.getElementById("group_modal_min").value;
    let max_price = document.getElementById("group_modal_max").value;

    if (!/^([0-9999])*$/.test(min_price)) {
        alert("el numero de minimo precio no es un numero");
        return false;
    } else if (!/^([0-9999])*$/.test(max_price)) {
        alert("el numero de maximo precio no es un numero");
        return false;

    }
}

    /*$(document).ready(function() {
        $('#login-form').submit(function(event) {
            var payload = {
                username: $('input[name=username]').val(),
                password: $('input[name=password]').val()
            };

            $.ajax({
                type: 'POST',
                url: '/login',
                contentType: 'application/json;charset=utf-8',
                data: JSON.stringify(payload), // our data object
                dataType: 'json' // what type of data do we expect back from the server
            })
                .done(function(data) {
                    console.log(data);
                })
                .fail(function(error) {
                    console.log(error);
                });

            // stop the form from submitting the normal way and refreshing the page
            event.preventDefault();
        });
    });*/

