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

use coding_exception;
use core\task\scheduled_task;
use DateTime;

defined('MOODLE_INTERNAL') || die();

class automatic_export_cleanup extends scheduled_task {

    /**
     * @return string
     * @throws coding_exception
     */
    public function get_name() {
        return get_string('automaticexportcleanup', 'block_admin_presets');
    }

    /**
     * This function is meant to be called by a CRON task, see the ../db/tasks.php file to check how it is programmed.
     * This function will get all records of the admin_presets plugin than will sort them into 4 arrays depending on the time creation of the record.
     * Each array will then be processed according to what cleanup we want to do :
     * records that are younger than 1 week will stay untouched
     * records that are older than a week but younger than 2 weeks will be deleted 1 day out of 2
     * records that are older than two weeks but younger than 1 month will be deleted 3 day out of 4
     * records that are older than 1 month but younger than 3 months will be deleted 3 day out of 4
     * records that are older than 3 months will all be deleted
     *
     * This function will return true if deletion was successfull. Otherwise it'll trhow the apropriate Exception along the way.
     *
     * For testing purposes you can manually launch this function : sudo -u www-data php7.3 admin/cli/scheduled_task.php --execute="\block_admin_presets\task\automatic_export_cleanup"
     *     *
     * @return bool|void
     * @throws \dml_exception
     */
    public function execute() {
        global $CFG, $DB;

        $config = get_config('block_admin_presets', 'automaticexportcleanup');
        if (empty($config)) {
            return;
        }

        //get all admin_presets records from DB
        $presets = $DB->get_records('block_admin_presets');

        //set-up arrays for dispatching presets records
        $twoweeks = [];
        $onemonth = [];
        $threemonths = [];
        $older = [];

        foreach ($presets as $preset) {

            // checks name to see if automatic backup
            if (preg_match('/^[0-9]{14}$/', $preset->name)) {
                // check if record is imported or local and sort records according to their age
                if ($preset->timeimported == 0) { //if backup is local, check on creation datetime
                    if ($preset->timecreated > strtotime('-1 weeks')) {
                        continue;
                    } elseif ($preset->timecreated > strtotime('-2 weeks')) {
                        $twoweeks[] = $preset;
                        continue;
                    } elseif ($preset->timecreated > strtotime('-1 months')) {
                        $onemonth[] = $preset;
                        continue;
                    } elseif ($preset->timecreated > strtotime('-3 months')) {
                        $threemonths[] = $preset;
                        continue;
                    } elseif ($preset->timecreated < strtotime('-3 months')) {
                        $older[] = $preset;
                        continue;
                    }
                }
            }
        }

        //loop through each now sorted array to delete unwanted records
        $this->delete_all_except_nth(2, $twoweeks);
        $this->delete_all_except_nth(4, $onemonth);
        $this->delete_all_except_nth(8, $threemonths);
        $this->delete_all($older);

        return true;
    }

    /**
     * This function is used to delete records from the admin_presets plugin
     * It is called by the delete_all_but_nth() and delete_all() functions.
     * @param $record
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function delete($record) {
        global $DB;

        if (!$DB->delete_records('block_admin_presets', array('id' => $record->id))) {
            print_error('errordeleting', 'block_admin_presets');
        }

        // Getting items ids before deleting to delete item attributes.
        $items = $DB->get_records('block_admin_presets_it', array('adminpresetid' => $record->id), 'id');
        foreach ($items as $item) {
            $DB->delete_records('block_admin_presets_it_a', array('itemid' => $item->id));
        }

        if (!$DB->delete_records('block_admin_presets_it', array('adminpresetid' => $record->id))) {
            print_error('errordeleting', 'block_admin_presets');
        }

        // Deleting the preset applications.
        if ($previouslyapplied = $DB->get_records('block_admin_presets_app',
            array('adminpresetid' => $record->id), 'id')) {

            foreach ($previouslyapplied as $application) {

                // Deleting items.
                if (!$DB->delete_records('block_admin_presets_app_it',
                    array('adminpresetapplyid' => $application->id))) {

                    print_error('errordeleting', 'block_admin_presets');
                }

                // Deleting attributes.
                if (!$DB->delete_records('block_admin_presets_app_it_a',
                    array('adminpresetapplyid' => $application->id))) {

                    print_error('errordeleting', 'block_admin_presets');
                }
            }

            if (!$DB->delete_records('block_admin_presets_app',
                array('adminpresetid' => $record->id))) {

                print_error('errordeleting', 'block_admin_presets');
            }
        }
    }

    /**
     * This function will delete every record except 1 out of nth.
     * so if  nth = 8, it'll delete 7/8 record
     *
     * The object parameter should be a collection of record of admin_prestes. You can get such a collenction using :
     * $object = $DB->get_records('block_admin_presets');
     *
     * @param int $nth
     * @param $object
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function delete_all_except_nth(int $nth, $object) {
        $reference_date = new DateTime("1970-01-01");

        foreach ($object as $record) {
            $recorddate = new DateTime();
            $recorddate->setTimestamp($record->timecreated);
            if((date_diff($reference_date, $recorddate, true)->days)%$nth != 0) {
                $this->delete($record);
            }
        }
    }

    /**
     * This function loops through a collection of admin_presets records to delete each of them.
     * You can get such a collenction using :
     * $object = $DB->get_records('block_admin_presets');
     *
     * @param $object
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function delete_all($object) {
        foreach ($object as $record) {
            $this->delete($record);
        }
    }

}