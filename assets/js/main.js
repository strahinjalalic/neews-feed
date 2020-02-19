$(document).ready(function() {
    $("#submit_timeline").click(function() {
        $.ajax({
            type: "POST",
            url: "timeline_ajax_post.php",
            data: $("form.profile_post").serialize(),
            success: function(message) {
                $("post_form").modal('hide');
                location.reload();
            },
            error: function() {
                alert('Failure!');
            }
        });
    });
}); 

function getUser(value, user) {
    $.post("friends_search_ajax.php", {query:value, loggedIn:user}, function(data) {
        $(".results").html(data);
    });
}

function getDropdownData(loggedIn, type) {
    if($(".dropdown_data").css("height") == '0px') {
        var page;
        if(type == 'notification') {

        } else if(type == 'message') {
            page = 'ajax_load_messages.php';
            $("span").remove("#unread");
        }

        var ajax_req = $.ajax({
            url: page,
            type: 'POST',
            data: "page=1&loggedIn=" + loggedIn,
            cache: false,

            success: function(response) {
                $(".dropdown_data").html(response);
                $(".dropdown_data").css({"padding":"0px", "height":"280px", "border":"1px solid #DADADA"});
                $("#dropdown_value").val(type);
            }
        });
    } else {
        $(".dropdown_data").html("");
        $(".dropdown_data").css({"padding":"0px", "height":"0px", "border":"none"});
    }
}