<?php

namespace MOJ_Intranet\Admin_Commands;

abstract class AdminCommand
{
    /**
     * Name of the command.
     *
     * @var ?string
     */
    public ?string $name = null;

    /**
     * Description of what this command will do.
     *
     * @var ?string
     */
    public ?string $description = null;

    /**
     * Method to execute the command.
     *
     * @return void
     */
    abstract public function execute(): void;
}
