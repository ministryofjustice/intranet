<?php

/**
 * Load admin commands and create an admin page to display them.
 */

// Only give access to administrators
if (!current_user_can('administrator')) {
    return;
}

$load_commands = array(
    // filename => Class_Name
    'assign-agency-terms-to-posts' => 'Assign_Agency_Terms_To_Posts',
    'hide-page-bylines' => 'Hide_Page_Bylines',
    'set-opt-in-permissions' => 'Set_Opt_In_Permissions',
    'set-admin-regional-permissions' => 'Set_Admin_Regional_Permissions',
    'set-regional-editor-permissions' => 'Set_Regional_Editor_Permissions',
    'remove-old-tabs-and-links' => 'Remove_Old_Tabs_And_Links',
);

$admin_commands = array();

require_once 'admin-commands/admin-command.php';

foreach ($load_commands as $include_file => $class_name) {
    require_once 'admin-commands/' . $include_file . '.php';
    $class = '\\MOJ_Intranet\\Admin_Commands\\' . $class_name;
    $admin_commands[$include_file] = new $class();
}

/**
 * Create the admin page.
 */
function add_admin_commands_page() {
    add_management_page('Admin Commands', 'Admin Commands', 'administrator', 'admin-commands', 'admin_commands_page');
}
add_action('admin_menu', 'add_admin_commands_page');

function admin_commands_page() {
    global $admin_commands;

    ?>
    <div class="wrap">
        <h1>Admin Commands</h1>
    <?php

    if (isset($_GET['run-command']) && isset($admin_commands[$_GET['run-command']])) {
        $command = $admin_commands[$_GET['run-command']];
        ?>
        <p><a href="<?php echo esc_attr(admin_url('tools.php?page=admin-commands')); ?>">Back to all commands</a></p>
        <h2>Running: <?php echo $command->name; ?></h2>
        <?php
        $command->execute();
    } else {
        ?>
        <div class="update-nag notice">
            <p><strong>Warning!</strong> Don't touch anything here unless you know what you're doing.</p>
        </div>

        <?php foreach ($admin_commands as $command_slug => $command): ?>
            <div class="card">
                <h2 class="alignleft"><?php echo $command->name; ?></h2>
                <a href="<?php echo esc_attr(admin_url('tools.php?page=admin-commands&run-command=' . $command_slug)); ?>"
                   class="button-primary alignright" style="margin-top: 10px">Run</a>
                <div class="clear"></div>
                <?php if (!empty($command->description)): ?>
                    <p><?php echo $command->description; ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php
    }
    ?>
    </div>
    <?php
}
