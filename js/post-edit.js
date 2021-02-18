$(document).ready(function () {
    $(".edit-pen").each(function () {
        var editPen = $(this);
        var text = $(this).parent().children(":first-child");   
        var id = text.attr("id");     
        var classList = this.parentNode.firstElementChild.classList;
        var className = classList.value;
        if(text.prop("tagName") == "P"){
            text.after("<textarea class= '"+ className +" border-bottom' type = 'text' style = 'display:none'></textarea>");
        }else{
            text.after("<input class= '"+ className +" border-bottom' type = 'text' style = 'display:none'/>");
        }
        
        var textbox = text.next();
        textbox[0].name = this.parentNode.firstElementChild.id.replace("lbl", "txt");
        
        if(textbox.hasClass("post-title"))
            textbox.attr('name','newTitle');

        else if(textbox.hasClass("subtitle1"))
            textbox.attr('name','newSubtitle1');

        else if(textbox.hasClass("subtitle2"))
            textbox.attr('name','newSubtitle2');

        else if(textbox.hasClass("subtitle3"))
            textbox.attr('name','newSubtitle3');

        else if(textbox.hasClass("subtitle4"))
            textbox.attr('name','newSubtitle4');

        else if(textbox.hasClass("post-text1"))
            textbox.attr('name','newText1');

        else if(textbox.hasClass("post-text2"))
            textbox.attr('name','newText2');

        else if(textbox.hasClass("post-text3"))
            textbox.attr('name','newText3');

        else if(textbox.hasClass("post-text4"))
            textbox.attr('name','newText4');

        else if(textbox.hasClass("desc-lien"))
            textbox.attr('name', 'desc-lien');

        else if(textbox.hasClass("titreSection"))
            textbox.attr('name', 'titreSection');


        if(text.prop("tagName") == "P"){
            textbox.html(text.html());
        }else{
            textbox.val(text.html());
        }

        $(this).click(function () {
            this.parentNode.classList.add('edit-mode');
            this.classList.add('edit-mode');
            text.hide();
            text.next().show();
            text.next().focus();
        });
        
        textbox.focusout(function () {
            this.form.submit();
            $(this).hide();
            $(this).prev().html($(this).val());
            $(this).prev().show();
            this.parentNode.classList.remove('edit-mode');
            editPen.removeClass('edit-mode');
        });
    });
});