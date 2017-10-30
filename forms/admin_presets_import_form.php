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

require_once($CFG->dirroot.'/lib/formslib.php');


class admin_presets_import_form extends moodleform {


    function definition () {

        global $CFG;

        $mform = & $this->_form;

        $mform->addElement('header', 'general', get_string('selectfile', 'block_admin_presets'));

        // File upload
        $mform->addElement('filepicker', 'xmlfile', get_string('selectfile', 'block_admin_presets'));
        $mform->addRule('xmlfile', null, 'required');

        // Rename input
        $mform->addElement('text', 'name', get_string('renamepreset', 'block_admin_presets'), 'maxlength="254" size="40"');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('submit', 'admin_presets_submit', get_string('savechanges'));
    }
}
