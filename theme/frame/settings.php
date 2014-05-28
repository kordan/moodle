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
 * Moodle's Frame theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_frame
 * @copyright 2013 Moodle, moodle.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Invert Navbar to dark background.
    $name = 'theme_frame/invert';
    $title = get_string('invert', 'theme_frame');
    $description = get_string('invertdesc', 'theme_frame');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Logo file setting.
    $name = 'theme_frame/logo';
    $title = get_string('logo','theme_frame');
    $description = get_string('logodesc', 'theme_frame');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Frame margin
    $name = 'theme_frame/framemargin';
    $title = get_string('framemargin', 'theme_frame');
    $description = get_string('framemargindesc', 'theme_frame');
    $default = '15';
    $choices = array(0 => '0px', 5 => '5px', 10 => '10px', 15 => '15px', 20 => '20px', 25 => '25px', 30 => '30px', 35 => '35px', 40 => '40px', 45 => '45px', 50 => '50px');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Trend colour settings
    $name = 'theme_frame/trendcolor';
    $title = get_string('trendcolor', 'theme_frame');
    $description = get_string('trendcolordesc', 'theme_frame');
    $default = 'mink';
    $trends = get_directory_list($CFG->dirroot.'/theme/frame/pix/trend/', '', false, true, false);
    $choices = array();
    foreach ($trends as $trend) {
        $choices[$trend] = get_string($trend, 'theme_frame');
    }
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // creditstomoodleorg: ctmo
    $name = 'theme_frame/creditstomoodleorg';
    $title = get_string('creditstomoodleorg', 'theme_frame');
    $description = get_string('creditstomoodleorgdesc', 'theme_frame');
    $default = '1';
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom CSS file.
    $name = 'theme_frame/customcss';
    $title = get_string('customcss', 'theme_frame');
    $description = get_string('customcssdesc', 'theme_frame');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Footnote setting.
    $name = 'theme_frame/footnote';
    $title = get_string('footnote', 'theme_frame');
    $description = get_string('footnotedesc', 'theme_frame');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}
