/*      app     */



/*  pulsante logout */
document.querySelector('#logout').addEventListener('click', function () {
    if( confirm( "Uscire dall'applicazione ?") ){
        document.location.href="/logout";     
    }
});


document.querySelectorAll('.sorter').forEach( (item) => { 

    item.addEventListener( "click" , (e) => {

        document.location.href="/app?o=" + item.dataset.field + "&v=" + item.dataset.sort;
    })

} );

document.querySelectorAll('.commonField').forEach( (item) => { 

    item.addEventListener( "mouseover" , (e) => {

        dr = item.dataset.row;

        document.querySelectorAll('.commonField').forEach( (cell) => {
            cell.classList.remove("backgroundRow");
        });

        document.querySelectorAll('.row_' + dr).forEach( (cell) => {
            cell.classList.add("backgroundRow");
        });

        

    })

} );


document.querySelector("#changeIpp").addEventListener("change", (e) => {
    document.location.href = "/app?ipp=" + e.target.options[e.target.options.selectedIndex].value;
});


/*  pulsanti di azione 

    in base al tasto lancio l'azione corrispondente

*/


document.querySelectorAll('.action-icon').forEach( (item) => { 

    item.addEventListener( "click" , (e) => {

        id      = item.dataset.id;
        action  = item.dataset.action;
        

        if( action == "delete"){
            
            if( confirm( "Vuoi cancellare questo elemento ?") ){

                sendPostForm( "delete", "/app/delete", { id : id } );
            
            }
        }
    })

} );



/*
    Crea ed invia un form post con i parametri passati, alla url desiderata
*/

sendPostForm = ( verb, url, params ) => {

    //  csrf token
    //  prendo nome e valore dal campo meta

    token_name = document.querySelector('meta[name="csrf-token-name"]').content;
    token_value = document.querySelector('meta[name="csrf-token-hash"]').content;


    //  creare una chiamata DELETE con un form

    

    //  creo il form
    var form = document.createElement("form");
    form.setAttribute("method", verb);

    //  imposto l'url

    form.setAttribute("action", url);
    

    //  aggiungo i parametri

    for( var key in params ){

        var hiddenField = document.createElement("input");

        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", key);
        hiddenField.setAttribute("value", params[key]);

        form.appendChild(hiddenField);

    }

    //  aggiungo anche il token

    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", token_name);
    hiddenField.setAttribute("value", token_value);
    
    form.appendChild(hiddenField);


    //  associo ed eseguo il form
    document.body.appendChild(form);
    form.submit();

}