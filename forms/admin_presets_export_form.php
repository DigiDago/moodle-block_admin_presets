<?php

require_once($CFG->dirroot.'/lib/formslib.php');


class admin_presets_export_form extends moodleform {

    function definition () {

        global $CFG, $USER, $OUTPUT;

        $mform = & $this->_form;

        // Preset attributes
        $mform->addElement('header', 'general', get_string('presetsettings', 'block_admin_presets'));

        $mform->addElement('text', 'name', get_string('name'), 'maxlength="254" size="60"');
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('htmleditor', 'comments', get_string('comments'));
        $mform->setType('comments', PARAM_CLEANHTML);

        $mform->addElement('text', 'author', get_string('author', 'block_admin_presets'), 'maxlength="254" size="60"');
        $mform->setType('author', PARAM_TEXT);
        $mform->setDefault('author', $USER->firstname.' '.$USER->lastname);

        $mform->addElement('checkbox', 'excludesensiblesettings', get_string('autohidesensiblesettings', 'block_admin_presets'));

        // Moodle settings table
        $mform->addElement('header', 'general', get_string('adminsettings', 'block_admin_presets'));
        $mform->addElement('html', '<div id="settings_tree_div" class="ygtv-checkbox"><img src="'.$OUTPUT->pix_url('i/loading_small', 'core').'"/></div><br/>');

        // Submit
        $mform->addElement('submit', 'admin_presets_submit', get_string('savechanges'));
    }
}
