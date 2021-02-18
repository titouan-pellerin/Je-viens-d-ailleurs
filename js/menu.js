(function () {

    document.addEventListener("DOMContentLoaded", initialiser);

    function initialiser(evt) {
        var wrapperMenu = document.querySelector('.wrapper-menu');
        wrapperMenu.addEventListener('click', menuFullScreen);

    }

    function menuFullScreen(evt) {
        document.querySelector(".left-menu").classList.remove("small-menu","stick-to-left");
        document.querySelector(".header").classList.toggle('fullscreen');
        this.classList.toggle('open');
    }

}());