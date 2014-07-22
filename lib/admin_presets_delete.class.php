<?php

require_once($CFG->dirroot.'/blocks/admin_presets/lib/admin_presets_base.class.php');


/**
 * Delete class
 *
 * @since      Moodle 2.0
 * @package    block/admin_presets
 * @copyright  2010 David Monllaó <david.monllao@urv.cat>
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt
 */
class admin_presets_delete extends admin_presets_base {


    /**
     * Shows a confirm box
     */
    public function show() {

        global $DB, $CFG, $OUTPUT;

        // Getting the preset name
        $presetdata = $DB->get_record('block_admin_presets', array('id' => $this->id), 'name');

        $deletetext = get_string("deletepreset", "block_admin_presets", $presetdata->name);
        $confirmurl = $CFG->wwwroot.'/blocks/admin_presets/index.php?action='.$this->action.'&mode=execute&id='.$this->id.'&sesskey='.sesskey();
        $cancelurl = $CFG->wwwroot.'/blocks/admin_presets/index.php';

        // If the preset was applied add a warning text
        if ($previouslyapplied = $DB->get_records('block_admin_presets_app', array('adminpresetid' => $this->id))) {
            $deletetext .= '<br/><br/><strong>'.get_string("deletepreviouslyapplied", "block_admin_presets").'</strong>';
        }

        $this->outputs = $OUTPUT->confirm($deletetext, $confirmurl, $cancelurl);
    }


    /**
     * Delete the DB preset
     */
    public function execute() {

        global $DB, $CFG;

        confirm_sesskey();

        if (!$DB->delete_records('block_admin_presets', array('id' => $this->id))) {
            print_error('errordeleting', 'block_admin_presets');
        }

        // Getting items ids before deleting to delete item attributes
        $items = $DB->get_records('block_admin_presets_it', array('adminpresetid' => $this->id), 'id');
        foreach ($items as $item) {
            $DB->delete_records('block_admin_presets_it_a', array('itemid' => $item->id));
        }

        if (!$DB->delete_records('block_admin_presets_it', array('adminpresetid' => $this->id))) {
            print_error('errordeleting', 'block_admin_presets');
        }

        // Deleting the preset applications
        if ($previouslyapplied = $DB->get_records('block_admin_presets_app', array('adminpresetid' => $this->id), 'id')) {

            foreach ($previouslyapplied as $application) {

                // Deleting items
                if (!$DB->delete_records('block_admin_presets_app_it', array('adminpresetapplyid' => $application->id))) {
                    print_error('errordeleting', 'block_admin_presets');
                }

                // Deleting attributes
                if (!$DB->delete_records('block_admin_presets_app_it_a', array('adminpresetapplyid' => $application->id))) {
                    print_error('errordeleting', 'block_admin_presets');
                }
            }

            if (!$DB->delete_records('block_admin_presets_app', array('adminpresetid' => $this->id))) {
                print_error('errordeleting', 'block_admin_presets');
            }
        }

        // Trigger the as it is usually triggered after execute finishes.
        $this->log();

        redirect($CFG->wwwroot.'/blocks/admin_presets/index.php');
    }

}
