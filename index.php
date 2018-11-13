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

require_once('../../config.php');

$action = optional_param('action', 'base', PARAM_ALPHA);
$mode = optional_param('mode', 'show', PARAM_ALPHAEXT);


require_login();

if (!$context = context_system::instance()) {
    print_error('wrongcontext', 'error');
}

require_capability('moodle/site:config', $context);


// Loads the required action class and form.
$classname = 'admin_presets_'.$action;
$formname = $classname.'_form';
$formpath = $CFG->dirroot.'/blocks/admin_presets/forms/'.$formname.'.php';
require_once($CFG->dirroot.'/blocks/admin_presets/lib/'.$classname.'.class.php');
if (file_exists($formpath)) {
    require_once($formpath);
}

if (!class_exists($classname)) {
    print_error('falseaction', 'block_admin_presets', $action);
}

$url = new moodle_url('/blocks/admin_presets/index.php');
$url->param('action', $action);
$url->param('mode', $mode);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);


// Executes the required action.
$instance = new $classname();
if (!method_exists($instance, $mode)) {
    print_error('falsemode', 'block_admin_presets', $mode);
}

// Executes the required method and displays output.
$instance->$mode();
$instance->log();
$instance->display();
