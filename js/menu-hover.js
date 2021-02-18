(function () {

    document.addEventListener("DOMContentLoaded", initialiser);

    function initialiser(evt) {
        document.querySelector('.menu-links').addEventListener("mouseover", menuHover);
        document.querySelector('.menu-links').addEventListener("mouseleave", menuLeave);
    }

    function menuHover(evt){
        document.querySelector('.left-menu').classList.remove('small-menu');
    }

    function menuLeave(evt){
        document.querySelector('.left-menu').classList.add('small-menu');
    }

}());