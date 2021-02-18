(function (){
    document.addEventListener("DOMContentLoaded", init);
    let password;
    let passwordError;
    let passwordConfirm;
    let passwordConfirmError;
    let pseudo;
    let pseudoError;
    let email;
    let emailError;
    let passwordRegex = new RegExp("^(?=.*[a-z])(?=.{8,})");
    let pseudoRegex = new RegExp ("^[a-zA-Z0-9]{4,15}$");
    let mailRegex = new RegExp("[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$");

    function init(evt){
        password = document.getElementById('password');
        password.addEventListener("keyup", inputValidation);
        passwordError = document.querySelector(".password+span.error");
        passwordConfirm = document.getElementById('password-confirm');
        passwordConfirm.addEventListener("keyup", inputValidation);
        passwordConfirmError = document.querySelector(".password-confirm+span.error");
        pseudo = document.getElementById('pseudo');
        pseudo.addEventListener("keyup", inputValidation)
        pseudoError = document.querySelector(".pseudo+span.error");
        email = document.getElementById('email');
        email.addEventListener("keyup", inputValidation);
        emailError = document.querySelector(".email+span.error");

    }

    function inputValidation(evt){
        if(this == password){
            if(!passwordRegex.test(this.value)){
                passwordError.textContent = "Le mot de passe doit contenir 8 caractères minimum";
                this.classList.add('invalid');
            }else{
                passwordError.textContent = "";
                this.classList.remove('invalid');
            }
        }else if(this == passwordConfirm){
            if(password.value != passwordConfirm.value){
                passwordConfirmError.textContent = "Les deux mots de passe ne correspondent pas"
                password.classList.add('invalid');
                passwordConfirm.classList.add('invalid');
            }else{
                passwordConfirmError.textContent = ""
                password.classList.remove('invalid');
                passwordConfirm.classList.remove('invalid');
            }
        }else if(this == pseudo){
            if(!pseudoRegex.test(this.value.trim())){
                pseudoError.textContent = "Le pseudo doit contenir entre 4 et 10 caractères sans espace";
                this.classList.add('invalid');
            }else{
                pseudoError.textContent = "";
                this.classList.remove('invalid');
            }
        }else if(this == email){
            if(!mailRegex.test(this.value.trim())){
                emailError.textContent = "Le format d'adresse est invalide";
                this.classList.add('invalid');
            }else{
                emailError.textContent = "";
                this.classList.remove('invalid');
            }
        }
        
    }

  
}());