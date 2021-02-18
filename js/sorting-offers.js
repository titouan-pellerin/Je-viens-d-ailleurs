let lastChecked;
var $_GET = {};
let lis;

if(document.location.toString().indexOf('?') !== -1) {
    var query = document.location
                .toString()
                .replace(/^.*?\?/, '')
                .replace(/#.*$/, '')
                .split('&');

    for(var i=0, l=query.length; i<l; i++) {
    var aux = decodeURIComponent(query[i]).split('=');
    $_GET[aux[0]] = aux[1];
    }
}

$(document).ready(function(){

    lastChecked = document.querySelector(".toutafficher");
    lis = document.querySelectorAll("li.category");
    $.ajax({
        url: 'annonces.php',
        method: 'POST',
        data: {category:"toutafficher"},
        success: function(data)
        {
            $('.posts-list').html(data);
        }
    });

    let categories = $("input[type=checkbox]");
    categories.click(onclick);  

    if($_GET['categorie'] != null){
        for(let li of lis){
            if(li.textContent.trim() == $_GET['categorie']){
                li.click();
            }
        }
        
    }  

});

function onclick(evt){
    if(lastChecked == this){
        evt.preventDefault();
    }else if(lastChecked != null){
        lastChecked.checked = false;
    }
    lastChecked = this;
    if(this.checked){
        let categoryName = this.name;
        $.ajax({
            url: 'annonces.php',
            method: 'POST',
            data: {category:categoryName},
            success: function(data)
            {
                $('.posts-list').html(data);
            }
        });
    }    
        
}

function otherCat(param){
    for(let li of lis){
        if(param.textContent.trim() == li.textContent.trim()){
            li.click();
        }
    }
}
