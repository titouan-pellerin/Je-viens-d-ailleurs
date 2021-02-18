(function (){

    let limit = 2;
    document.addEventListener("DOMContentLoaded", init);
    
    function init(evt){
        let catInputs = document.getElementsByClassName("inputcat");
        for(let catInput of catInputs){
            catInput.onclick = selectiveCheck;
        }
        let submit = document.querySelectorAll('.publish');
        submit[0].addEventListener("click", submitClick);
        submit[1].addEventListener("click", submitClick);

        let hideInputs = document.getElementsByClassName("input-hide");
        document.querySelector(".para2").addEventListener("click", function(){
            console.log("test");
            
            if(hideInputs[0] != null && hideInputs[1] != null ){
                hideInputs[0].style.display = "block";
                hideInputs[1].style.display = "block";
                hideInputs[1].addEventListener("click", function(){
                    if(hideInputs[2] != null && hideInputs[3] != null){
                        hideInputs[2].style.display = "block";
                        hideInputs[3].style.display = "block";
                    }
                });
            }
        });
        
    }

    function selectiveCheck (evt) {
        var checkedChecks = document.querySelectorAll(".inputcat:checked");
        let error = document.querySelector('.error-cat');
        if (checkedChecks.length >= limit + 1){
            error.textContent = "Sélectionner deux catégories maximum";
            return false;
        }
    }
    
    function submitClick(evt) {
        console.log("click submit");
        var checkedChecks = document.querySelectorAll(".inputcat:checked");
        let bigTitle = document.querySelector('.bigTitle');
        let bigSubtitle = document.querySelector('.bigSubtitle');
        let textContent = document.querySelector('.postText');
        let inputFiles = document.querySelector("#uploadInputSVG");

        let errorFile = document.querySelector('.error-file');
        
        let error = document.querySelector('.error-cat');
        if(checkedChecks.length < 1 && bigTitle.value != "" && bigSubtitle.value != "" && textContent.value != ""){
            evt.preventDefault();
            error.textContent = "Sélectionner une catégorie minimum";
        }else{
            error.textContent = "";
        }
        let extension = inputFiles.value.split('.')[1];

        if((inputFiles.files.lenght == 0 || extension != "svg") && bigTitle.value != "" && bigSubtitle.value != "" && textContent.value != ""){
            evt.preventDefault();
            if(extension != "svg" && extension != null){
                errorFile.textContent = "Vous devez ajouter un fichier SVG, pas un fichier ." + extension;
            }else{
            errorFile.textContent = "Vous devez ajouter un fichier SVG";
            }

        }
    }
    
    
    
}());
