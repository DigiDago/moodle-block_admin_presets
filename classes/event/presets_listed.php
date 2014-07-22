<?php

namespace block_admin_presets\event;

defined('MOODLE_INTERNAL') || die();

class presets_listed extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'block_admin_presets';
    }

    public static function get_name() {
        return get_string('eventpresetslisted', 'block_admin_presets');
    }

    public function get_description() {
        return "User {$this->userid} listed the system presets.";
    }

    public function get_url() {
        return new \moodle_url('/block/admin_presets/index.php');
    }

    public function get_legacy_logdata() {
        return array($this->courseid, 'block_admin_presets', 'base', '',
            $this->objectid, $this->contextinstanceid);
    }
}
