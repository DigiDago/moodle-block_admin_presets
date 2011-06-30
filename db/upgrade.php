<?php
 
/**
 * @global moodle_database $DB
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_admin_presets_upgrade($oldversion, $block) {
    global $DB;
    
    $dbman = $DB->get_manager();
    
    if ($oldversion < 2011063000) {

        // Changing type of field moodleversion on table admin_preset to char
        $table = new xmldb_table('admin_preset');
        $field = new xmldb_field('moodleversion', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'author');

        // Launch change of type for field moodleversion
        $dbman->change_field_type($table, $field);
        
        upgrade_block_savepoint(true, 2011063000, 'admin_presets');
    }

    return true;
}
