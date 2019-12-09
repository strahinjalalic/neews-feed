    </div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $("#register").click(function() {
            $(".register").slideUp("slow", function() {
                $(".login").slideDown("slow");
            });
        });

        $("#login").click(function() {
            $(".login").slideUp("slow", function() {
                $(".register").slideDown("slow");
            })
        });
    });
</script>

<?php if(isset($_POST['submit'])) {
    if($reg_query) {
        echo '<script>
        $(document).ready(function() {
            $(".register").hide();
            $(".login").show();
            $("#login_succeed").text("Registration successfully completed! Please Login now!");

            $(".reg_form").find("input[type=text], input[type=email], input[type=password]").val("");
        });
    </script>';
    } 
}
?>

<?php
if(!empty($login_errors)) {
        echo '<script>
            $(document).ready(function() {
                $(".register").hide();
                $(".login").show();
            });
        </script>';
}
?>

</body>
</html>