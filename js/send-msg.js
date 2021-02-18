var $_GET = {};
var chat;

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
    chat = document.querySelector(".chat");
    chat.scrollTop = chat.scrollHeight;
    $(".typezone").submit(sendMsg);
});

function sendMsg(evt){
    evt.preventDefault();
    let msgInput = $("input[name=newMsg]");
    var msg = msgInput.val();
    msgInput.val("");
    
    if (msg.trim() != "") {
        $.ajax({
                url: 'messagerie.php?conversation='+$_GET['conversation'],
                method: 'POST',
                data: {newMsg:msg},
                success: function(data)
                {
                    $('.chat').html(data);
                    chat.scrollTop = chat.scrollHeight;
                }
        });
    }
}
