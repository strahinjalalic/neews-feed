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