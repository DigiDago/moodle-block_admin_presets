<?php

defined('MOODLE_INTERNAL') || die();

$adminpresetsurl = $CFG->wwwroot.'/blocks/admin_presets/index.php';

$adminpresetstabs = array('base' => 'base',
    'export' => 'export',
    'import' => 'import');

if (!array_key_exists($this->action, $adminpresetstabs)) {
    $row[] = new tabobject($this->action, $adminpresetsurl.'?action='.$this->action, get_string('action'.$this->action, 'block_admin_presets'));
}

foreach ($adminpresetstabs as $actionname) {
    $row[] = new tabobject($actionname, $adminpresetsurl.'?action='.$actionname, get_string('action'.$actionname,'block_admin_presets'));
}

print_tabs(array($row), $this->action);
