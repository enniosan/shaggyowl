/**/

document.getElementById('loginForm').addEventListener('submit', function(event) {
    

    alert( event.target.id );
    event.target.disabled = true;


});