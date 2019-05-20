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
 * Admin presets block main controller
 *
 * @package          blocks/admin_presets
 * @copyright        2019 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | DigiDago
 * @orignalauthor    David Monllaó <david.monllao@urv.cat>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @param int $oldversion
 * @param object $block
 * @return bool
 * @throws coding_exception
 * @throws ddl_exception
 * @throws ddl_field_missing_exception
 * @throws ddl_table_missing_exception
 * @throws downgrade_exception
 * @throws moodle_exception
 * @throws upgrade_exception
 * @global moodle_database $DB
 */
function xmldb_block_admin_presets_upgrade($oldversion, $block)
{
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2011063000) {

        // Changing type of field moodleversion on table admin_preset to char.
        $table = new xmldb_table('admin_preset');
        $field = new xmldb_field('moodleversion', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'author');

        // Launch change of type for field moodleversion.
        $dbman->change_field_type($table, $field);

        upgrade_block_savepoint(true, 2011063000, 'admin_presets');
    }

    // Renaming DB tables.
    if ($oldversion < 2012031401) {

        $tablenamechanges = array('admin_preset' => 'block_admin_presets',
            'admin_preset_apply' => 'block_admin_presets_app',
            'admin_preset_apply_item' => 'block_admin_presets_app_it',
            'admin_preset_apply_item_attr' => 'block_admin_presets_app_it_a',
            'admin_preset_item' => 'block_admin_presets_it',
            'admin_preset_item_attr' => 'block_admin_presets_it_a');

        // Just in case it gets to the max number of chars defined in the XSD.
        try {

            // Renaming the tables.
            foreach ($tablenamechanges as $from => $to) {

                $table = new xmldb_table($from);
                if ($dbman->table_exists($table)) {
                    $dbman->rename_table($table, $to);
                }
            }

            // Print error and rollback changes.
        } catch (Exception $e) {

            // Rollback tablename changes.
            foreach ($tablenamechanges as $to => $from) {

                $table = new xmldb_table($from);
                if ($dbman->table_exists($table)) {
                    $dbman->rename_table($table, $to);
                }
            }

            $debuginfo = get_string('errorupgradetablenamesdebug', 'block_admin_presets');
            throw new moodle_exception('errorupgradetablenames', 'block_admin_presets', '', null, $debuginfo);
        }

        upgrade_block_savepoint(true, 2012031401, 'admin_presets');
    }
    return true;
}
