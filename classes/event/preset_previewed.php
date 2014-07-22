<?php

namespace block_admin_presets\event;

defined('MOODLE_INTERNAL') || die();

class preset_previewed extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'block_admin_presets';
    }

    public static function get_name() {
        return get_string('eventpresetpreviewed', 'block_admin_presets');
    }

    public function get_description() {
        return "User {$this->userid} has previewed the preset with id {$this->objectid}.";
    }

    public function get_url() {
        return new \moodle_url('/blocks/admin_presets/index.php', array('action' => 'load', 'mode' => 'preview', 'id' => $this->objectid));
    }
}
