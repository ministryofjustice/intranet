<?php

namespace MOJ_Intranet\Admin_Commands;

abstract class Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = null;

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = null;

    /**
     * Method to execute the command.
     *
     * @return void
     */
    abstract public function execute();
}
