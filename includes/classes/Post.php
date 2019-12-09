<?php
class Post
{
    private $user_object;

    public function __construct($user)
    {
        $this->user_object = new User($user);
    }

    public function submitPost($body, $post_to)
    {
        global $database;
        $body = $database->escape_string($body);
        $delete_spaces = preg_match('/\s+/', '', $body);

        if ($delete_spaces != '') { //ako ima sadrzaja
            $date_added = date("Y-m-d H:i:s");
            $added_by = $this->user_object->getUsername();

            if($user_to == $added_by) { //ako je korisnik na svom profilu
               $user_to = "none";
            }   
        }
    }
}
