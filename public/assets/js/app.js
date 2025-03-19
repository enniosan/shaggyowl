/*      app     */

/*  registro le interazioni */


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


document.querySelectorAll('.action-icon').forEach( (item) => { 

    item.addEventListener( "click" , (e) => {

        id      = item.dataset.id;
        action  = item.dataset.action;

        manageActions( id, action );

    })

} );


document.querySelector("#editCancel").addEventListener("click", (e) => {
    closeModal();
});


document.querySelector('#editSave').addEventListener('click', function (e) {

    //  ottengo le informazioni del'azione
    action  = e.target.dataset.action;
        
    token_name = document.querySelector('meta[name="csrf-token-name"]').content;
    token_value = document.querySelector('meta[name="csrf-token-hash"]').content;


    form = document.querySelector("#editForm");
    form.action = "/app/" + action;

    // aggiungi il token al form
    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", token_name);
    hiddenField.setAttribute("value", token_value);
    form.appendChild(hiddenField);


    if( action == "update" ){
        form.id.value = e.target.dataset.id;
    }

    form.submit();


    /*

    //  recupero i dati dal form

    params = {
        nome: document.querySelector("#e_nome").value,
        cognome: document.querySelector("#e_cognome").value,
        email: document.querySelector("#e_email").value,
        indirizzo: document.querySelector("#e_indirizzo").value,    
        sesso: document.querySelector("#e_sesso").value
    };

    params[token_name] = token_value;

    if( action == "update" ){
        params.id = id;
    }

    //  creo il form

    form = new FormData();

    for( i in params ){
        form.append( i, params[i] );
    }

    //  invio i dati

    form.action= "/app/" + action;
    form.method = "POST";

    console.log( form );

    form.submit();
    
    */

});







showModal = ( mask ) => {
    try{

        document.querySelector("#modalContainer").classList.remove("hiddener");
        document.querySelectorAll(".modalMask").forEach( (i) => { i.classList.add("hiddener"); } );
        
        if( mask != "" ){
            document.querySelector("#" + mask).classList.remove("hiddener");
            document.body.classList.add("bodyModal");
            
        }
    }catch(e){
        document.body.classList.remove("bodyModal");
        
    }

}   

closeModal = () => {
    document.querySelector("#modalContainer").classList.add("hiddener");
    document.body.classList.remove("bodyModal");
}



manageActions = ( id, action ) => {

    // reset a default
    document.querySelector("#editSave").setAttribute("data-action", false );
    document.querySelector("#editSave").setAttribute("data-id", false );


    if( action == "delete"){
            
        if( confirm( "Vuoi cancellare questo elemento ?") ){

            showModal("waitMask");

            sendPostForm( "POST", "/app/delete", { id : id } );
        
        }

    }else if( action == "edit"){
        
        showModal("waitMask");

        try{

            //  recupero i dati dell'utente

            fetch( "/app/getUser/" + id ).then( (response => response.json()) ).then( (data) => {

                if( data.id ){

                    //  solo se ho l'utente
                    //  imposto i dati per il l'esecuzione dell'azione
                    
                    document.querySelector("#editTitle").innerHTML = "Modifica utente #" + data.id + " -" + data.nome + " " + data.cognome;
                    
                    document.querySelector("#editSave").setAttribute("data-action", "update" );
                    document.querySelector("#editSave").setAttribute("data-id", id );

                    for( i in data ){

                        try{
                        
                            document.querySelector("#e_" + i ).value = data[i];
                        
                        }catch(e){
                        
                            console.log( "campo " + i + "non trovato " );
                        
                        }
                    }

                    //  mostro il form dopo mezzo secondo
                    //  ho fatto l'animazione con il logo... fammela almeno vedere ^__^

                    setTimeout( () => { showModal("editMask"); }, 500);

                }else{

                    alert('no');
                    showModal("errorMask");

                }
            
                console.log( data );
            }).catch( (e) => {
            
                console.log( e );
                showModal("errorMask");
            });
        }catch(e){

            console.log( e );
            showModal("errorMask");

        }
        
    }
}


/*
    Crea ed invia un form post con i parametri passati, alla url desiderata
*/

sendPostForm = ( verb, url, params ) => {

    //  csrf token
    //  prendo nome e valore dal campo meta

    token_name  = document.querySelector('meta[name="csrf-token-name"]').content;
    token_value = document.querySelector('meta[name="csrf-token-hash"]').content;

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


goToaster = ( type, message ) => {
    
    document.querySelector("#toaster-ico").src='/assets/icons/ko.svg';

    if( type == "success" ){
        document.querySelector("#toaster-ico").src='/assets/icons/ok.svg';
    }

    document.querySelector("#toaster").classList.add( type );
    document.querySelector("#toaster-message").innerHTML = message;
    document.querySelector("#toaster").classList.remove("hiddener");

    setTimeout( () => { document.querySelector("#toaster").classList.add("hiddener"); }, 5000 );
}



save = () => {

    //  aggiungo il csrf token

    token_name = document.querySelector('meta[name="csrf-token-name"]').content;
    token_value = document.querySelector('meta[name="csrf-token-hash"]').content;

    var hiddenField = document.createElement("input");

    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", token_name);
    hiddenField.setAttribute("value", token_value);

    document.querySelector("#editForm").appendChild(hiddenField);

}



if( toaster){ goToaster( toaster.type, toaster.message ); }