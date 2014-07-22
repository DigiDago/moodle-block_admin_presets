<?php

class block_admin_presets extends block_list {


    function init() {
        $this->title = get_string('pluginname', 'block_admin_presets');
    }


    function get_content() {

        global $CFG, $OUTPUT;

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new object();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (!has_capability('moodle/site:config', context_system::instance())) {
            $this->content = '';
            return $this->content;
        }

        $this->content->items[] = '<img src="'.$OUTPUT->pix_url("i/backup").'" class="icon" alt="'.get_string('actionexport', 'block_admin_presets').'" />
                                   <a title="'.get_string('actionexport', 'block_admin_presets').'" href="'.$CFG->wwwroot.'/blocks/admin_presets/index.php?action=export">
                                   '.get_string('actionexport', 'block_admin_presets').'</a>';

        $this->content->items[] = '<img src="'.$OUTPUT->pix_url("i/restore").'" class="icon" alt="'.get_string('actionimport', 'block_admin_presets').'" />
                                   <a title="'.get_string('actionimport', 'block_admin_presets').'" href="'.$CFG->wwwroot.'/blocks/admin_presets/index.php?action=import">
                                   '.get_string('actionimport', 'block_admin_presets').'</a>';

        $this->content->items[] = '<img src="'.$OUTPUT->pix_url("i/repository").'" class="icon" alt="'.get_string('actionbase', 'block_admin_presets').'" />
                                   <a title="'.get_string('actionbase', 'block_admin_presets').'" href="'.$CFG->wwwroot.'/blocks/admin_presets/index.php">
                                   '.get_string('actionbase', 'block_admin_presets').'</a>';

        return $this->content;
    }


    function applicable_formats() {
        return array('site' => true);
    }

    function has_config() {
        return true;
    }

}
