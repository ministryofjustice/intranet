<?php

/**
 * Create a page to execute custom admin commands.
 */

$loadCommands = array(
    'assign-agency-terms-to-posts' => 'AssignAgencyTermsToPosts',
);

$adminCommands = array();

require_once 'admin-commands/admin-command.php';

foreach ($loadCommands as $includeFile => $className) {
    require_once 'admin-commands/' . $includeFile . '.php';
    $class = '\\MOJIntranet\\AdminCommands\\' . $className;
    $adminCommands[$includeFile] = new $class();
}

/**
 * Create the admin page.
 */

add_action('admin_menu', 'add_admin_commands_page');

function add_admin_commands_page() {
    add_management_page('Admin Commands', 'Admin Commands', 'administrator', 'admin-commands', 'admin_commands_page');
}

function admin_commands_page() {
    global $adminCommands;

    ?>
    <div class="wrap">
        <h1>Admin Commands</h1>
    <?php

    if (isset($_GET['run-command']) && isset($adminCommands[$_GET['run-command']])) {
        $command = $adminCommands[$_GET['run-command']];
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

        <?php foreach ($adminCommands as $commandId => $command): ?>
            <div class="card">
                <h2 class="alignleft"><?php echo $command->name; ?></h2>
                <a href="<?php echo esc_attr(admin_url('tools.php?page=admin-commands&run-command=' . $commandId)); ?>"
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
