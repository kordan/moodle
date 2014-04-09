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
 * formal_white module upgrade code
 *
 * This file keeps track of upgrades to
 * the theme plugin
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may lose the effort they've put
 * into customising and setting up your theme.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package    theme_formal_white
 * @copyright  Mediatouch 2000 (http://mediatouch.it/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_theme_formal_white_upgrade($oldversion) {
    global $CFG;

    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2012051503) {
        $currentsetting = get_config('theme_formal_white');

        if (isset($currentsetting->displaylogo)) { // useless but safer
            // Create a new config setting called headercontent and give it the current displaylogo value.
            set_config('headercontent', $currentsetting->displaylogo, 'theme_formal_white');
            unset_config('displaylogo', 'theme_formal_white');
        }

        if (isset($currentsetting->logo)) { // useless but safer
            // Create a new config setting called headercontent and give it the current displaylogo value.
            set_config('customlogourl', $currentsetting->logo, 'theme_formal_white');
            unset_config('logo', 'theme_formal_white');
        }

        if (isset($currentsetting->frontpagelogo)) { // useless but safer
            // Create a new config setting called headercontent and give it the current displaylogo value.
            set_config('frontpagelogourl', $currentsetting->frontpagelogo, 'theme_formal_white');
            unset_config('frontpagelogo', 'theme_formal_white');
        }

        upgrade_plugin_savepoint(true, 2012051503, 'theme', 'formal_white');
    }

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014040900) {
        // Migrate logo URL.
        $settingnames = array('customlogourl', 'frontpagelogourl');
        foreach ($settingnames as $settingname) {
            $logo = get_config('theme_formal_white', $settingname);
            if ($logo === '') {
                // No logo means nothing to do.
            } else {
                // migrate relative path to absolute one
                if (strtolower(substr($logo, 0, 4)) != 'http') { // https is taken into account
                    $logo = $CFG->wwwroot.'/'.$logo;
                }
                if ($logo = clean_param($logo, PARAM_URL)) {
                    require_once("$CFG->libdir/filelib.php");
                    if ($content = download_file_content($logo)) {
                        $filename = preg_replace('/^.*\//', '', $logo);
                        if (!$filename = clean_param($filename, PARAM_FILE)) {
                            // Some name is better than no name...
                            $filename = 'logo.jpg';
                        }
                        $fs = get_file_storage();
                        // 'filearea' has the same name of the setting
                        $record = array(
                            'contextid' => context_system::instance()->id, 'component' => 'theme_formal_white',
                            'filearea' => $settingname, 'itemid' => 0, 'filepath' => '/', 'filename' => $filename);
                        $fs->create_file_from_string($record, $content);
                        set_config($settingname, '/'.$filename, 'theme_formal_white');
                        unset($content);
                    } else {
                        // Prompt for new logo, the old setting was invalid.
                        unset_config('theme_formal_white', $settingname);
                    }
                } else {
                    // Prompt for new logo, the old setting was invalid.
                    unset_config('theme_formal_white', $settingname);
                }
            }
        }
        upgrade_plugin_savepoint(true, 2014040900, 'theme', 'formal_white');
    }

    return true;
}
