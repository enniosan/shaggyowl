/**/

document.getElementById('loginForm').addEventListener('submit', function(event) {

    event.preventDefault();

    document.querySelector('#login-error').innerHTML = "";
    
    const data = {
        "username" : document.querySelector('#username').value,
        "password" : document.querySelector('#password').value
    };


    fetch('/login/auth', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }).then(response => response.json()).then(data => {
    
        if( data.success ){
            window.location.href = data.location;
        }else{

            document.querySelector('#login-error').innerHTML = data.message;
        
        }
    
    
    });



});