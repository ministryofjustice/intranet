<?php

namespace MOJ_Intranet\Admin_Commands;

class Add_Comments_Root extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Add comments root';
    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Add root comment to existing comments';
    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        //
        $query = "SELECT comment_ID FROM  $wpdb->comments WHERE comment_parent = 0";
        $comments = $wpdb->get_col($wpdb->prepare($query));

        if ($comments)
        {
            foreach ($comments as $parent_id)
            {
                $this->getCommentChildren ($parent_id, array ($parent_id));
            }
        }
    }

    /**
     * @param $parent_id
     * @param $children
     * @return bool
     *
     * Recursive function to set the original root comment on children and grandchildren
     *
     */

    private function getCommentChildren ($parent_id, $children)
    {
        global $wpdb;

        $query = 'SELECT comment_ID FROM  '.$wpdb->comments.' c WHERE c.comment_parent IN ('.implode(' ,', $children).')';
        $comments = $wpdb->get_col($wpdb->prepare($query));

        //If there are children

        if ($comments)
        {
            echo 'Setting ('.implode(' ,', $comments).') parent as '.$parent_id.'<br>';

            foreach ($comments as $comment)
            {
                add_comment_meta($comment, 'root_comment_id', $parent_id);
            }
            $this->getCommentChildren ($parent_id, $comments);

        } else {
            echo "Done with this branch! $parent_id<br>";
            return false;
        }

    }

}
