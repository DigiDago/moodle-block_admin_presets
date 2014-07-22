<?php

require_once($CFG->dirroot.'/blocks/admin_presets/lib/admin_presets_base.class.php');


/**
 * Class to import admin presets
 *
 * @since      Moodle 2.0
 * @package    block/admin_presets
 * @copyright  2010 David Monllaó <david.monllao@urv.cat>
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt
 */
class admin_presets_import extends admin_presets_base {


    /**
     * Displays the import moodleform
     */
    public function show() {

        global $CFG;

        $url = $CFG->wwwroot.'/blocks/admin_presets/index.php?action=import&mode=execute';
        $this->moodleform = new admin_presets_import_form($url);
    }


    /**
     * Imports the xmlfile into DB
     */
    public function execute() {

        global $CFG, $USER, $DB;

        confirm_sesskey();

        $sitesettings = $this->_get_site_settings();

        $url = $CFG->wwwroot.'/blocks/admin_presets/index.php?action=import&mode=execute';
        $this->moodleform = new admin_presets_import_form($url);

        if ($data = $this->moodleform->get_data()) {

            $usercontext = context_user::instance($USER->id);

            // Getting the file
            $xmlcontent = $this->moodleform->get_file_content('xmlfile');
            $xml = simplexml_load_string($xmlcontent);
            if (!$xml) {
                redirect($CFG->wwwroot.'/blocks/admin_presets/index.php?action=import', get_string('wrongfile', 'block_admin_presets'), 4);
            }

            // Preset info
            $preset = new StdClass();
            foreach ($this->rel as $dbname => $xmlname) {
                $preset->$dbname = (String)$xml->$xmlname;
            }
            $preset->userid = $USER->id;
            $preset->timeimported = time();

            // Overwrite preset name
            if ($data->name != '') {
                $preset->name = $data->name;
            }

            // Inserting preset
            if (!$preset->id = $DB->insert_record('block_admin_presets', $preset)) {
                print_error('errorinserting', 'block_admin_presets');
            }

            // Plugins settings
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
                        if (!$setting->__toString()) {
                            $value = '';
                        } else {
                            $value = $setting->__toString();
                        }

                        if (empty($sitesettings[$plugin][$name])) {
                            //debugging('Setting '.$plugin.'/'.$name.' not supported by this Moodle version');
                            continue;
                        }

                        // Cleaning the setting value
                        if (!$presetsetting = $this->_get_setting($sitesettings[$plugin][$name]->get_settingdata(), $value)) {
                            //debugging('Setting '.$plugin.'/'.$name.' not implemented');
                            continue;
                        }

                        $settingsfound = true;

                        // New item.
                        $item = new StdClass();
                        $item->adminpresetid = $preset->id;
                        $item->plugin = $plugin;
                        $item->name = $name;
                        $item->value = $presetsetting->get_value();

                        // Inserting items
                        if (!$item->id = $DB->insert_record('block_admin_presets_it', $item)) {
                            print_error('errorinserting', 'block_admin_presets');
                        }

                        // Adding settings attributes
                        if ($setting->attributes() && ($itemattributes = $presetsetting->get_attributes())) {

                            foreach ($setting->attributes() as $attrname => $attrvalue) {

                                $itemattributenames = array_flip($itemattributes);

                                // Check the attribute existence
                                if (empty($itemattributenames[$attrname])) {
                                    //debugging('The '.$plugin.'/'.$name.' attribute '.$attrname.' is not supported by this Moodle version');
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

            // If there are no valid or selected settings we should delete the admin preset record
            if (empty($settingsfound)) {
                $DB->delete_records('block_admin_presets', array('id' => $preset->id));
                redirect($CFG->wwwroot.'/blocks/admin_presets/index.php?action=import', get_string('novalidsettings', 'block_admin_presets'), 4);
            }

            redirect($CFG->wwwroot.'/blocks/admin_presets/index.php?action=load&id='.$preset->id);
        }
    }

}
