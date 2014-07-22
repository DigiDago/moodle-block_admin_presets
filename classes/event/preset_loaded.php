<?php

namespace block_admin_presets\event;

defined('MOODLE_INTERNAL') || die();

class preset_loaded extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'block_admin_presets';
    }

    public static function get_name() {
        return get_string('eventpresetloaded', 'block_admin_presets');
    }

    public function get_description() {
        return "User {$this->userid} has loaded the preset with id {$this->objectid}.";
    }

    public function get_url() {
        return new \moodle_url('/blocks/admin_presets/index.php', array('action' => 'load', 'mode' => 'preview', 'id' => $this->objectid));
    }

    public function get_legacy_logdata() {
        return array($this->courseid, 'block_admin_presets', 'load', '',
            $this->objectid, $this->contextinstanceid);
    }
}
