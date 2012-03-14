<?php

require_once($CFG->dirroot.'/blocks/admin_presets/lib/admin_presets_base.class.php');


/**
 * Rollback class
 *
 * @since      Moodle 2.0
 * @package    block/admin_presets
 * @copyright  2010 David Monllaó <david.monllao@urv.cat>
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt
 */
class admin_presets_rollback extends admin_presets_base {

    /**
     * Displays the different previous applications of the preset
     */
    public function show() {

        global $CFG, $DB, $OUTPUT;

        $table = new html_table();
        $table->attributes['class'] = 'generaltable boxaligncenter';
        $table->head  = array(get_string('timeapplied', 'block_admin_presets'), get_string('user'), get_string('actions'));
        $table->align = array('left', 'center', 'left');

        // Preset data
        $preset = $DB->get_record('block_admin_presets', array('id' => $this->id));

        // Applications data
        $applications = $DB->get_records('block_admin_presets_app', array('adminpresetid' => $this->id));
        if (!$applications) {
            print_error('notpreviouslyapplied', 'block_admin_presets');
        }

        foreach ($applications as $application) {

            $format = get_string('strftimedatetime', 'langconfig');

            $user = $DB->get_record('user', array('id' => $application->userid));

            $rollbacklink = $CFG->wwwroot.'/blocks/admin_presets/index.php?action=rollback&mode=execute&id='.$application->id.'&sesskey='.sesskey();
            $action = html_writer::link($rollbacklink, get_string("rollback", "block_admin_presets"));

            $table->data[] = array(strftime($format, $application->time),
                                   $user->firstname.' '.$user->lastname,
                                   '<div>'.$action.'</div>'
                                  );
        }


        $this->outputs .= '<br/>'.$OUTPUT->heading(get_string("presetname", "block_admin_presets").': '.$preset->name, 3);
        $this->outputs .= html_writer::table($table);
    }


    /**
     * Executes the application rollback
     *
     * Each setting value is checked against the config_log->value
     */
    public function execute() {

        global $CFG, $DB, $OUTPUT;

        confirm_sesskey();

        // Actual settings
        $sitesettings = $this->_get_site_settings();

        // To store rollback results
        $rollback = array();
        $failures = array();

        if (!$DB->get_record('block_admin_presets_app', array('id' => $this->id))) {
            print_error('wrongid', 'block_admin_presets');
        }

        // Items
        $itemsql = "SELECT cl.id, cl.plugin, cl.name, cl.value, cl.oldvalue, ap.adminpresetapplyid
                    FROM {block_admin_presets_app_it} ap
                    JOIN {config_log} cl ON cl.id = ap.configlogid
                    WHERE ap.adminpresetapplyid = {$this->id}";
        $itemchanges = $DB->get_records_sql($itemsql);
        if ($itemchanges) {

            foreach ($itemchanges as $change) {

                if ($change->plugin == '') {
                    $change->plugin = 'none';
                }

                // Admin setting
                if (!empty($sitesettings[$change->plugin][$change->name])) {

                    $actualsetting = $sitesettings[$change->plugin][$change->name];
                    $oldsetting = $this->_get_setting($actualsetting->get_settingdata(), $change->oldvalue);
                    $oldsetting->set_text();
                    $varname = $change->plugin.'_'.$change->name;

                    // Check if the actual value is the same set by the preset
                    if ($change->value == $actualsetting->get_value()) {

                        $oldsetting->save_value();

                        // Output table
                        $rollback[$varname]->plugin = $oldsetting->get_settingdata()->plugin;
                        $rollback[$varname]->visiblename = $oldsetting->get_settingdata()->visiblename;
                        $rollback[$varname]->oldvisiblevalue = $actualsetting->get_visiblevalue();
                        $rollback[$varname]->visiblevalue = $oldsetting->get_visiblevalue();

                        // Deleting the admin_preset_apply_item instance
                        $deletewhere = array('adminpresetapplyid' => $change->adminpresetapplyid,
                            'configlogid' => $change->id);
                        $DB->delete_records('block_admin_presets_app_it', $deletewhere);

                    } else {

                        $failures[$varname]->plugin = $oldsetting->get_settingdata()->plugin;
                        $failures[$varname]->visiblename = $oldsetting->get_settingdata()->visiblename;
                        $failures[$varname]->oldvisiblevalue = $actualsetting->get_visiblevalue();
                        $failures[$varname]->visiblevalue = $oldsetting->get_visiblevalue();
                    }
                }
            }

        }


        // Attributes
        $attrsql = "SELECT cl.id, cl.plugin, cl.name, cl.value, cl.oldvalue, ap.itemname, ap.adminpresetapplyid
                    FROM {block_admin_presets_app_it_a} ap
                    JOIN {config_log} cl ON cl.id = ap.configlogid
                    WHERE ap.adminpresetapplyid = {$this->id}";
        $attrchanges = $DB->get_records_sql($attrsql);
        if ($attrchanges) {

            foreach ($attrchanges as $change) {

                if ($change->plugin == '') {
                    $change->plugin = 'none';
                }

                // Admin setting of the attribute item
                if (!empty($sitesettings[$change->plugin][$change->itemname])) {

                    // Getting the attribute item
                    $actualsetting = $sitesettings[$change->plugin][$change->itemname];

                    $oldsetting = $this->_get_setting($actualsetting->get_settingdata(), $actualsetting->get_value());
                    $oldsetting->set_attribute_value($change->name, $change->oldvalue);
                    $oldsetting->set_text();

                    $varname = $change->plugin.'_'.$change->name;

                    // Check if the actual value is the same set by the preset
                    $actualattributes = $actualsetting->get_attributes_values();
                    if ($change->value == $actualattributes[$change->name]) {

                        $oldsetting->save_attributes_values();

                        // Output table
                        $rollback[$varname]->plugin = $oldsetting->get_settingdata()->plugin;
                        $rollback[$varname]->visiblename = $oldsetting->get_settingdata()->visiblename;
                        $rollback[$varname]->oldvisiblevalue = $actualsetting->get_visiblevalue();
                        $rollback[$varname]->visiblevalue = $oldsetting->get_visiblevalue();

                        // Deleting the admin_preset_apply_item_attr instance
                        $deletewhere = array('adminpresetapplyid' => $change->adminpresetapplyid,
                            'configlogid' => $change->id);
                        $DB->delete_records('block_admin_presets_app_it_a', $deletewhere);

                    } else {

                        $failures[$varname]->plugin = $oldsetting->get_settingdata()->plugin;
                        $failures[$varname]->visiblename = $oldsetting->get_settingdata()->visiblename;
                        $failures[$varname]->oldvisiblevalue = $actualsetting->get_visiblevalue();
                        $failures[$varname]->visiblevalue = $oldsetting->get_visiblevalue();
                    }
                }
            }

        }

        // Delete application if no items nor attributes of the application remains
        if (!$DB->get_record('block_admin_presets_app_it', array('adminpresetapplyid' => $this->id)) &&
            !$DB->get_records('block_admin_presets_app_it_a', array('adminpresetapplyid' => $this->id))) {

            $DB->delete_records('block_admin_presets_app', array('id' => $this->id));
        }

        // Display the rollback changes
        if (!empty($rollback)) {
            $this->outputs .= '<br/>'.$OUTPUT->heading(get_string('rollbackresults', "block_admin_presets"), 3, 'admin_presets_success');
            $this->outputs .= '<br/>';
            $this->_output_applied_changes($rollback);
        }

        // Display the rollback failures
        if (!empty($failures)) {
            $this->outputs .= '<br/>'.$OUTPUT->heading(get_string('rollbackfailures', 'block_admin_presets'), 3, 'admin_presets_error');
            $this->outputs .= '<br/>';
            $this->_output_applied_changes($failures);
        }

    }
}
