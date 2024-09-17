<?php
namespace MOJ_Intranet\Admin_Commands;

class SyncUserRoles extends AdminCommand
{
    /**
     * Name of the command.
     *
     * @var ?string
     */
    public ?string $name = 'Sync user roles from codebase to database';

    /**
     * Description of what this command will do.
     *
     * @var ?string
     */
    public ?string $description = 'This update operation should be run to sync/migrate user roles.';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute(): void
    {
        $theme_dir = get_stylesheet_directory();

        require_once $theme_dir . '/inc/admin/users/add-agency-admin.php';
        require_once $theme_dir . '/inc/admin/users/add-agency-editor.php';
        require_once $theme_dir . '/inc/admin/users/add-regional-editor.php';
        require_once $theme_dir . '/inc/admin/users/add-team-lead.php';
        require_once $theme_dir . '/inc/admin/users/add-team-author.php';
        require_once $theme_dir . '/inc/admin/users/delete-roles.php';
    }
}
