const form = {
    email: document.getElementById('email'),
    pesel: document.getElementById('pesel'),
/*  phone: document.getElementById('phone'),
    fname: document.getElementById('fname'),
    sname: document.getElementById('sname'), */
    submit: document.getElementById('btn-submit'),
    messages: document.getElementById('form-messages')
}

form.submit.addEventListener('click', () => {
    const request = new XMLHttpRequest();

    request.onload = () => {

        let responseObject = null;

        try {
            responseObject = JSON.parse(request.responseText);
        } catch (e) {
            console.error('Could not parse JSON!');
        }

        if (responseObject) {
            handleResponse(responseObject);
        }

    }

    const requestData = `email=${form.email.value}&pesel=${form.pesel.value}`;
    request.open('post', 'check-login.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(requestData);

    function handleResponse (responseObject) {
        if (responseObject.ok) {
            console.log('login OK!')
        } else {
            while (form.messages.firstChild) {
                form.messages.removeChild(form.messages.firstChild);
            }
        
            responseObject.messages.forEach((message) => {
                const li = document.createElement('li')
                li.textContent = message;
                form.messages.appendChild(li);
                
            });

            form.messages.style.display = "block";
        
        }
    }
    
});