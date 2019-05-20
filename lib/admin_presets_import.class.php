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

require_once($CFG->dirroot . '/blocks/admin_presets/lib/admin_presets_base.class.php');

class admin_presets_import extends admin_presets_base
{


    /**
     * Displays the import moodleform
     */
    public function show()
    {

        global $CFG;

        $url = $CFG->wwwroot . '/blocks/admin_presets/index.php?action=import&mode=execute';
        $this->moodleform = new admin_presets_import_form($url);
    }


    /**
     * Imports the xmlfile into DB
     */
    public function execute()
    {

        global $CFG, $USER, $DB;

        confirm_sesskey();

        $sitesettings = $this->_get_site_settings();

        $url = $CFG->wwwroot . '/blocks/admin_presets/index.php?action=import&mode=execute';
        $this->moodleform = new admin_presets_import_form($url);

        if ($data = $this->moodleform->get_data()) {

            $usercontext = context_user::instance($USER->id);

            // Getting the file.
            $xmlcontent = $this->moodleform->get_file_content('xmlfile');
            $xml = simplexml_load_string($xmlcontent);
            if (!$xml) {
                redirect($CFG->wwwroot . '/blocks/admin_presets/index.php?action=import',
                    get_string('wrongfile', 'block_admin_presets'), 4);
            }

            // Preset info.
            $preset = new StdClass();
            foreach ($this->rel as $dbname => $xmlname) {
                $preset->$dbname = (String)$xml->$xmlname;
            }
            $preset->userid = $USER->id;
            $preset->timeimported = time();

            // Overwrite preset name.
            if ($data->name != '') {
                $preset->name = $data->name;
            }

            // Inserting preset.
            if (!$preset->id = $DB->insert_record('block_admin_presets', $preset)) {
                print_error('errorinserting', 'block_admin_presets');
            }

            // Store it here for logging and other future id-oriented stuff.
            $this->id = $preset->id;

            // Plugins settings.
            $xmladminsettings = $xml->ADMIN_SETTINGS[0];
            foreach ($xmladminsettings as $plugin => $settings) {

                $plugin = strtolower($plugin);

                if (strstr($plugin, '__') != false) {
                    $plugin = str_replace('__', '/', $plugin);
                }

                $pluginsettings = $settings->SETTINGS[0];

                if ($pluginsettings) {
                    foreach ($pluginsettings->children() as $name => $setting) {

                        $name = strtolower($name);

                        // Default to ''.
                        if ($setting->__toString() === false) {
                            $value = '';
                        } else {
                            $value = $setting->__toString();
                        }

                        if (empty($sitesettings[$plugin][$name])) {
                            debugging('Setting ' . $plugin . '/' . $name .
                                ' not supported by this Moodle version', DEBUG_DEVELOPER);
                            continue;
                        }

                        // Cleaning the setting value.
                        if (!$presetsetting = $this->_get_setting($sitesettings[$plugin][$name]->get_settingdata(),
                            $value)) {
                            debugging('Setting ' . $plugin . '/' . $name . ' not implemented', DEBUG_DEVELOPER);
                            continue;
                        }

                        $settingsfound = true;

                        // New item.
                        $item = new StdClass();
                        $item->adminpresetid = $preset->id;
                        $item->plugin = $plugin;
                        $item->name = $name;
                        $item->value = $presetsetting->get_value();

                        // Inserting items.
                        if (!$item->id = $DB->insert_record('block_admin_presets_it', $item)) {
                            print_error('errorinserting', 'block_admin_presets');
                        }

                        // Adding settings attributes.
                        if ($setting->attributes() && ($itemattributes = $presetsetting->get_attributes())) {

                            foreach ($setting->attributes() as $attrname => $attrvalue) {

                                $itemattributenames = array_flip($itemattributes);

                                // Check the attribute existence.
                                if (!isset($itemattributenames[$attrname])) {
                                    debugging('The ' . $plugin . '/' . $name . ' attribute ' . $attrname .
                                        ' is not supported by this Moodle version', DEBUG_DEVELOPER);
                                    continue;
                                }

                                $attr = new StdClass();
                                $attr->itemid = $item->id;
                                $attr->name = $attrname;
                                $attr->value = $attrvalue->__toString();
                                $DB->insert_record('block_admin_presets_it_a', $attr);
                            }
                        }
                    }
                }
            }

            // If there are no valid or selected settings we should delete the admin preset record.
            if (empty($settingsfound)) {
                $DB->delete_records('block_admin_presets', array('id' => $preset->id));
                redirect($CFG->wwwroot . '/blocks/admin_presets/index.php?action=import',
                    get_string('novalidsettings', 'block_admin_presets'), 4);
            }

            // Trigger the as it is usually triggered after execute finishes.
            $this->log();

            redirect($CFG->wwwroot . '/blocks/admin_presets/index.php?action=load&id=' . $preset->id);
        }
    }
}
