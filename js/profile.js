(function () {

    document.addEventListener("DOMContentLoaded", initialiser);

    function initialiser(evt) {
        let editPens = document.querySelectorAll(".edit-pen");
        for(let editPen of editPens)
            editPen.addEventListener("click", clickPen);
    }

    function clickPen(evt){
        let parent = this.parentNode;
        let input = parent.firstElementChild;
        input.toggleAttribute('readonly');
        parent.classList.toggle('edit-mode');
        this.classList.toggle('edit-mode');
    }



}());