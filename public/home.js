


var counter = 0;

for(i = counter; i<counter+5; i++) {
    document.getElementById("p"+i).style.display = "block";
}


$(document).ready(function() {

    $('#login-form').submit(function (event) {


        $.ajax({
            type: 'POST',
            url: '/home',
            contentType: 'application/json;charset=utf-8',

            // what type of data do we expect back from the server
        })
            .done(function (data) {
                console.log(counter);
                counter = counter + 5;
                console.log(data["counter"]);
                for (i = counter; i < counter + 5; i++) {
                    document.getElementById("p" + i).style.display = "block";
                }


            })
            .fail(function (error) {

            });

        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
    });
});

   function clickado(id, idDelboton) {
        document.getElementById(idDelboton).src = "cor_lleno.png";
       var payload = {
           id_product: id,

       };


       $.ajax({
           type: 'POST',
           url: '/homeCor',
           contentType: 'application/json;charset=utf-8',
           data: JSON.stringify(payload), // our data object
           dataType: 'json'


           // what type of data do we expect back from the server
       })
           .done(function (data) {



           })
           .fail(function (error) {

           });

       // stop the form from submitting the normal way and refreshing the page
       event.preventDefault();

   }


