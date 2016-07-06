<?php

namespace MOJ_Intranet\Admin_Commands;

class Document_Permissions_Reset extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Document CPT Permissions Reset';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Reset permissions for agency and global editor';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {


        $roles = [ 'agency-editor', 'editor'];

        $caps = [ 
            'edit_documents',
            'edit_others_documents',
            'edit_private_documents',
            'edit_published_documents',
            'read_documents',
            'read_document_revisions',
            'read_private_documents',
            'delete_documents',
            'delete_others_documents',
            'delete_private_documents',
            'delete_published_documents',
            'publish_documents',
        ];

        foreach ($roles as $role) {
            $wpRole = get_role($role);
            foreach ($caps as $cap) {
                
                    $wpRole->add_cap($cap);

            }
        }
        echo '<p>Document Permissions reset</p>';
    }
}
