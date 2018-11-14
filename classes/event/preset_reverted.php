<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Admin presets block main controller
 *
 * @package          blocks/admin_presets
 * @copyright        2017 Digidago <contact@digidago.com><www.digidago.com>
 * @author           Jordan Kesraoui | DigiDago
 * @orignalauthor    David Monllaó <david.monllao@urv.cat>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_admin_presets\event;

defined('MOODLE_INTERNAL') || die();

class preset_reverted extends \core\event\base
{

    protected function init()
    {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'block_admin_presets';
    }

    public static function get_name()
    {
        return get_string('eventpresetreverted', 'block_admin_presets');
    }

    public function get_description()
    {
        return "User {$this->userid} has reverted the preset with id {$this->objectid}.";
    }

    public function get_legacy_logdata()
    {
        return array($this->courseid, 'block_admin_presets', 'rollback', '',
            $this->objectid, $this->contextinstanceid);
    }
}
