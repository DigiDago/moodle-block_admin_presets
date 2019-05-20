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
 * @copyright        2019 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | DigiDago
 * @orignalauthor    David Monllaó <david.monllao@urv.cat>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

class admin_presets_load_form extends moodleform
{

    private $preview;

    public function __construct($url, $preview = false)
    {
        $this->preview = $preview;
        parent::__construct($url);
    }

    public function definition()
    {

        global $OUTPUT;

        $mform = &$this->_form;

        // Moodle settings table.
        $mform->addElement('header', 'general',
            get_string('adminsettings', 'block_admin_presets'));

        $class = '';
        if (!$this->preview) {
            $class = 'ygtv-checkbox';
        }
        $mform->addElement('html', '<div id="settings_tree_div" class="' . $class .
            '"><img src="' . $OUTPUT->pix_icon('i/loading_small',
                get_string('loading', 'block_admin_presets')) . '"/></div>');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Submit.
        if (!$this->preview) {
            $mform->addElement('submit', 'admin_presets_submit',
                get_string('loadselected', 'block_admin_presets'));
        }
    }
}
