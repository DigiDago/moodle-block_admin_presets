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

if ($ADMIN->fulltree) {

    $sensiblesettingsdefault = 'recaptchapublickey@@none, recaptchaprivatekey@@none, googlemapkey@@none, ';
    $sensiblesettingsdefault .= 'secretphrase@@none, cronremotepassword@@none, smtpuser@@none, ';
    $sensiblesettingsdefault .= 'smtppass@none, proxypassword@@none, password@@quiz, ';
    $sensiblesettingsdefault .= 'enrolpassword@@moodlecourse, allowedip@@none, blockedip@@none';

    $settings->add(new admin_setting_configtextarea('admin_presets/sensiblesettings',
            get_string('sensiblesettings', 'block_admin_presets'),
            get_string('sensiblesettingstext', 'block_admin_presets'),
            $sensiblesettingsdefault, PARAM_TEXT));
}
