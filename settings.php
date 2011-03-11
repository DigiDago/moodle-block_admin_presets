<?php

defined('MOODLE_INTERNAL') || die();

$sensiblesettingsdefault = 'recaptchapublickey@@none, recaptchaprivatekey@@none, googlemapkey@@none, ';
$sensiblesettingsdefault.= 'secretphrase@@none, cronremotepassword@@none, smtpuser@@none, ';
$sensiblesettingsdefault.= 'smtppass@none, proxypassword@@none, password@@quiz, ';
$sensiblesettingsdefault.= 'enrolpassword@@moodlecourse, allowedip@@none, blockedip@@none';

$settings->add(new admin_setting_configtextarea('admin_presets/sensiblesettings',
        get_string('sensiblesettings', 'block_admin_presets'),
        get_string('sensiblesettingstext', 'block_admin_presets'),
        $sensiblesettingsdefault, PARAM_TEXT));
