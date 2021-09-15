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
 * @author    amayard@cblue.be
 * @date      16/08/2021
 * @copyright 2021, CBlue SPRL, support@cblue.be
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   block_admin_presets
 */

namespace block_admin_presets\task;

use admin_presets_export;
use coding_exception;
use core\task\scheduled_task;
use dml_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

class automatic_export extends scheduled_task {

    /**
     * @return string
     * @throws coding_exception
     */
    public function get_name() {
        return get_string('automaticexport', 'block_admin_presets');
    }

    /**
     * This function is meant to be called by a CRON task, see the ../db/tasks.php file to check how it is programmed.
     * This function will get all site's settings through the _get_setting() function, it'll then will loop through them and construct a $_POST array that'll be handled to create an admin_presets valid database record. The record will be named with a timestamp YearMonthDayHourMinuteSecond.
     * If the config is successfully recorded, the function will return true, if not it'll throw the appropriate Exception along the way.
     *
     * For testing purposes you can manually launch this function : sudo -u www-data php7.3 admin/cli/scheduled_task.php --execute="\block_admin_presets\task\automatic_export"
     *
     * @return bool
     * @throws \moodle_exception
     * @throws coding_exception
     * @throws dml_exception
     */
    public function execute() {
        global $CFG, $DB;

        $config = get_config('block_admin_presets', 'automaticexport');
        if (empty($config)) {
            return;
        }

        require_once $CFG->dirroot . "/blocks/admin_presets/lib/admin_presets_export.class.php";

        $export = new admin_presets_export();

        // Reload site settings.
        $sitesettings = $export->load_site_settings();

        //Construct $presets for each site setting
        foreach ($sitesettings as $sitesetting => $settingvalue) {
            foreach ($settingvalue as $key => $value) {
                $presets[$key . '@@' . $sitesetting] = '1';
            }
        }

        // admin_preset record.
        $preset = new stdClass();
        $preset->userid = '1';
        $preset->name = date('YmdGis');
        $preset->comments = $presets['comments']['text'];
        $preset->site = $CFG->wwwroot;
        $preset->author = $presets['author'];
        $preset->moodleversion = $CFG->version;
        $preset->moodlerelease = $CFG->release;
        $preset->timecreated = time();
        $preset->timemodified = 0;
        if (!$preset->id = $DB->insert_record('block_admin_presets', $preset)) {
            print_error('errorinserting', 'block_admin_presets');
        }

        // Store it here for logging and other future id-oriented stuff.
        $this->id = $preset->id;

        // We must ensure that there are settings selected.
        foreach ($presets as $varname => $value) {

            unset($setting);

            if (strstr($varname, '@@') != false) {

                $settingsfound = true;

                $name = explode('@@', $varname);
                $setting = new StdClass();
                $setting->adminpresetid = $preset->id;
                $setting->plugin = $name[1];
                $setting->name = $name[0];
                $setting->value = $sitesettings[$setting->plugin][$setting->name]->get_value();

                if (!$setting->id = $DB->insert_record('block_admin_presets_it', $setting)) {
                    print_error('errorinserting', 'block_admin_presets');
                }

                // Setting attributes must also be exported.
                if ($attributes = $sitesettings[$setting->plugin][$setting->name]->get_attributes_values()) {
                    foreach ($attributes as $attributename => $value) {

                        $attr = new StdClass();
                        $attr->itemid = $setting->id;
                        $attr->name = $attributename;
                        $attr->value = $value;

                        $DB->insert_record('block_admin_presets_it_a', $attr);
                    }
                }
            }
        }

        // If there are no valid or selected settings we should delete the admin preset record.
        if (empty($settingsfound)) {
            $DB->delete_records('block_admin_presets', ['id' => $preset->id]);
            redirect($CFG->wwwroot . '/blocks/admin_presets/index.php?action=export',
                get_string('novalidsettingsselected', 'block_admin_presets'), 4);
        }


        // Trigger the as it is usually triggered after execute finishes.
        $export->log();

        return true;
    }
}