(function () {
    "use strict";
    document.addEventListener("DOMContentLoaded", initialiser);
    
    function initialiser(evt) {
        let btnNon = document.querySelector('.confirm-no');
        let btnNon2 = document.querySelector('.confirm-no2');
        let suppr = document.querySelector('.delete');
        let signal = document.querySelector('.signal');

        if(btnNon != null && suppr != null){
            btnNon.addEventListener("click", non);
            suppr.addEventListener("click", popup);
        }

        if(btnNon2 != null && signal != null){
            btnNon2.addEventListener("click", non2);
            signal.addEventListener("click", popup2);
        }

    }

    function non(evt){
        let divPopup = document.querySelector('.popup-container');
        divPopup.classList.toggle("visible");
    }

    function popup(evt) {
        let divPopup = document.querySelector('.popup-container');
        divPopup.classList.toggle("visible");
    }

    function non2(evt){
        let divPopup = document.querySelector('.popup-container2');
        divPopup.classList.toggle("visible");
    }

    function popup2(evt) {
        let divPopup = document.querySelector('.popup-container2');
        divPopup.classList.toggle("visible");
    }

}
());