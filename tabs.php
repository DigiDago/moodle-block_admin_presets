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

defined('MOODLE_INTERNAL') || die();

$adminpresetsurl = $CFG->wwwroot.'/blocks/admin_presets/index.php';

$adminpresetstabs = array('base' => 'base',
    'export' => 'export',
    'import' => 'import');

if (!array_key_exists($this->action, $adminpresetstabs)) {
    $row[] = new tabobject($this->action, $adminpresetsurl.'?action='.$this->action, get_string('action'.$this->action, 'block_admin_presets'));
}

foreach ($adminpresetstabs as $actionname) {
    $row[] = new tabobject($actionname, $adminpresetsurl.'?action='.$actionname, get_string('action'.$actionname,'block_admin_presets'));
}

print_tabs(array($row), $this->action);
