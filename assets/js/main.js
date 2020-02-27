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

    $("#search_input").focus(function() {
        if(window.matchMedia( '(min-width: 800px)' ).matches) {
            $(this).animate({width: '30vh'}, 500);
        }
    });
    
    $('.button_search').on('click', function() {
        document.search.submit();
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
            page = 'ajax_load_notifications.php';
            $("span").remove("#unread_notification");
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

function getLiveSearchData(value, loggedIn) {
    $.post("ajax_search.php", {query: value, loggedIn: loggedIn}, function(data) {
        if($('.search_results_footer_empty')[0]) {
            $('.search_results_footer_empty').toggleClass('.search_results_footer');
            $('.search_results_footer_empty').toggleClass('.search_results_footer_empty');
        }
        $('.search_results').html(data);
        $('.search_results_footer').html("<a href='search.php?q='" + value + "'>See Results</a>");

        if(data="") {
            $('.search_results_footer').html("");
            $('.search_results_footer').toggleClass('.search_results_footer_empty');
            $('.search_results_footer').toggleClass('.search_results_footer');

        }
    });
}
