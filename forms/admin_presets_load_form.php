<?php

require_once($CFG->dirroot.'/lib/formslib.php');


class admin_presets_load_form extends moodleform {

    private $preview;

    public function __construct($url, $preview = false) {
        $this->preview = $preview;
        parent::__construct($url);
    }


    function definition () {

    	global $OUTPUT;

        $mform = & $this->_form;

        // Moodle settings table
        $mform->addElement('header', 'general', get_string('adminsettings', 'block_admin_presets'));


        $class = '';
        if (!$this->preview) {
            $class = 'ygtv-checkbox';
        }
        $mform->addElement('html', '<div id="settings_tree_div" class="'.$class.'"><img src="'.$OUTPUT->pix_url('i/loading_small', 'core').'"/></div>');

        $mform->addElement('hidden', 'id');

        // Submit
        if (!$this->preview) {
            $mform->addElement('submit', 'admin_presets_submit', get_string('loadselected', 'block_admin_presets'));
        }

    }
}
