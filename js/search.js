
$(document).ready(function(){
    let search = $("input[type=search]");
    keyup();
    search.keyup(keyup);   
});

function keyup(evt){
    var query = $("input[type=search]").val().trim();
    if (query != "") {
        $.ajax({
                url: 'recherche.php',
                method: 'POST',
                data: {query:query},
                success: function(data)
                {
                    $('.search-img').css("opacity", "0");
                    $('.search-img').css("visibility", "hidden");
                    $('.results').html(data).css("display", "flex");
                    
                }
                
        });
    }else{
        $('.results').html("").css("display","none");
        $('.search-img').css("opacity", "0.5");
        $('.search-img').css("visibility", "visible");

    }
}

