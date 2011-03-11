<?php

require_once($CFG->dirroot.'/lib/formslib.php');


class admin_presets_import_form extends moodleform {


    function definition () {

        global $CFG;

        $mform = & $this->_form;

        $mform->addElement('header', 'general', get_string('selectfile', 'block_admin_presets'));

        // File upload
        $mform->addElement('filepicker', 'xmlfile', get_string('selectfile', 'block_admin_presets'));
        $mform->addRule('xmlfile', null, 'required');

        // Rename input
        $mform->addElement('text', 'name', get_string('renamepreset', 'block_admin_presets'), 'maxlength="254" size="40"');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('submit', 'admin_presets_submit', get_string('savechanges'));
    }
}
