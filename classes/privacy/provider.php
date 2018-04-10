<?php
// …

namespace block_admin_presets\privacy;

use core_privacy\local\metadata\null_provider;

class provider implements
    // This plugin does not store any personal user data.
    null_provider {

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason() : string {
        return 'privacy:null_reason';
    }
}
