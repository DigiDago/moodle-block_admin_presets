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

class block_admin_presets extends block_list {

    /**
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_admin_presets');
    }

    public function get_content() {

        global $CFG, $OUTPUT;

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (!has_capability('moodle/site:config', context_system::instance())) {
            $this->content = '';
            return $this->content;
        }

        $this->content->items[] = $OUTPUT->pix_icon("i/backup",
                get_string('actionexport', 'block_admin_presets'),
                "moodle", array("class" => "icon")) . '<a title="'.
            get_string('actionexport', 'block_admin_presets').
            '" href="'.$CFG->wwwroot.'/blocks/admin_presets/index.php?action=export">'.
            get_string('actionexport', 'block_admin_presets').
            '</a>';

        $this->content->items[] = $OUTPUT->pix_icon("i/restore",
                get_string('actionimport', 'block_admin_presets'),
                "moodle", array("class" => "icon")).
            '<a title="'.get_string('actionimport', 'block_admin_presets').
            '" href="'.$CFG->wwwroot.
            '/blocks/admin_presets/index.php?action=import">'.
            get_string('actionimport', 'block_admin_presets').
            '</a>';

        $this->content->items[] = $OUTPUT->pix_icon("i/repository",
                get_string('actionbase', 'block_admin_presets'),
                "moodle", array("class" => "icon")).
            '<a title="'.
            get_string('actionbase', 'block_admin_presets').
            '" href="'.
            $CFG->wwwroot.
            '/blocks/admin_presets/index.php">'
            .get_string('actionbase', 'block_admin_presets').'</a>';

        return $this->content;
    }

    public function applicable_formats() {
        return array('site' => true);
    }

    public function has_config() {
        return true;
    }

}
