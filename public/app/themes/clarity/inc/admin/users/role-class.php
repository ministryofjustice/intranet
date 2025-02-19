<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

class Role
{
    protected string $name;
    protected string $display_name;
    protected array $capabilities;

    public function upsertRole()
    {
        // Get the role
        $role = get_role($this->name);

        // If the role doesn't exist, create it
        if (!$role) {
            add_role($this->name, $this->display_name, $this->capabilities);
            return;
        }

        // Lets loop over the capabilities and add or remove them
        foreach ($this->capabilities as $capability => $value) {
            // If the capability is already set to the correct value, skip updating it
            if ($role->capabilities[$capability] === $value) {
                continue;
            }

            if ($value) {
                // Add the capability
                $role->add_cap($capability);
            } else {
                // Remove the capability
                $role->remove_cap($capability);
            }
        }
    }
}
