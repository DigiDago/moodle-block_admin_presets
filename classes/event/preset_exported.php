<?php

namespace block_admin_presets\event;

defined('MOODLE_INTERNAL') || die();

class preset_exported extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'block_admin_presets';
    }

    public static function get_name() {
        return get_string('eventpresetexported', 'block_admin_presets');
    }

    public function get_description() {
        return "User {$this->userid} has exported the preset with id {$this->objectid}.";
    }

    public function get_url() {
        return new \moodle_url('/blocks/admin_presets/index.php', array('action' => 'load', 'mode' => 'preview', 'id' => $this->objectid));
    }

    public function get_legacy_logdata() {
        return array($this->courseid, 'block_admin_presets', 'export', '',
            $this->objectid, $this->contextinstanceid);
    }
}
