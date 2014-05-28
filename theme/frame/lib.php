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
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string $css The CSS
 * @param theme_config $theme The theme config object.
 * @return string The parsed CSS The parsed CSS.
 */
function theme_frame_process_css($css, $theme) {
    // Set the background image for the logo.
    $logo = $theme->setting_file_url('logo', 'logo');
    $css = theme_frame_set_logo($css, $logo);

    // Set the frame margin
    if (!isset($theme->settings->framemargin)) {
        $framemargin = 15; // default
    } else {
        $framemargin = $theme->settings->framemargin;
    }
    $css = theme_frame_set_framemargin($css, $framemargin);

    // Set the images according to color trend
    if (!isset($theme->settings->trendcolor)) {
        $trendcolor = 'mink'; // default
    } else {
        $trendcolor = $theme->settings->trendcolor;
    }
    $css = theme_frame_set_trendcolor($css, $trendcolor);

    // Set custom CSS
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_frame_set_customcss($css, $customcss);

    return $css;
}

/**
 * Adds the logo to CSS.
 *
 * @param string $css The CSS.
 * @param string $logo The URL of the logo.
 * @return string The parsed CSS
 */
function theme_frame_set_logo($css, $logo) {
    $tag = '[[setting:logo]]';
    $replacement = $logo;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_frame_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM and $filearea === 'logo') {
        $theme = theme_config::load('frame');
        return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_frame_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/**
 * Returns an object containing HTML for the areas affected by settings.
 *
 * Do not add Frame specific logic in here, child themes should be able to
 * rely on that function just by declaring settings with similar names.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footnote HTML to use as a footnote. By default ''.
 */
function theme_frame_get_html_for_settings(renderer_base $output, moodle_page $page) {
    global $CFG;
    $return = new stdClass;

    $return->navbarclass = '';
    if (!empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }

    if (!empty($page->theme->settings->logo)) {
        $return->heading = html_writer::link($CFG->wwwroot, '', array('title' => get_string('home'), 'class' => 'logo'));
    } else {
        $return->heading = $output->page_heading();
    }

    $return->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $return->footnote = '<div class="footnote text-center">'.$page->theme->settings->footnote.'</div>';
    }

    return $return;
}

function theme_frame_set_framemargin($css, $framemargin) {
    $tag = '[[setting:framemargin]]';
    $css = str_replace($tag, $framemargin.'px', $css);

    // Set .headermenu margin
    $calculated = $framemargin + 22; // 17px is the width of the frame; 5px to avoid to have all stuck
    $tag = '[[calculated:headermenumargin]]';
    $css = str_replace($tag, $calculated.'px', $css);

    return $css;
}

function theme_frame_set_trendcolor($css, $trendcolor) {
    // __setting_trendcolor__ is part of URLS so it is already between double square bracket.
    // I can not enclose it between double square bracket once again otherwise images path parser get confused.
    $tag = urlencode('__setting_trendcolor__'); // urlencode is useless but it is correct to put it here
    $css = str_replace($tag, $trendcolor, $css);

    // of the basis of the general choosed trend, I need some colour definition.
    switch ($trendcolor) {
        case 'blueberry':
            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#DAF1FF', $css);

            // block header background
            $tag = '[[setting:begincolor]]';
            $css = str_replace($tag, '#EBFFFE', $css);
            $tag = '[[setting:endcolor]]';
            $css = str_replace($tag, '#CDE2F3', $css);
            break;
        case 'lemon':
            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#FFEB9A', $css);

            // block header background
            $tag = '[[setting:begincolor]]';
            $css = str_replace($tag, '#FFFAA6', $css);
            $tag = '[[setting:endcolor]]';
            $css = str_replace($tag, '#F7DA41', $css);
            break;
        case 'lime':
            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#F0F5BB', $css);

            // block header background
            $tag = '[[setting:begincolor]]';
            $css = str_replace($tag, '#FBFFDA', $css);
            $tag = '[[setting:endcolor]]';
            $css = str_replace($tag, '#E2E499', $css);
            break;
        case 'mink':
            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#EFEFEF', $css);

            // block header background
            $tag = '[[setting:begincolor]]';
            $css = str_replace($tag, '#FEFEFE', $css);
            $tag = '[[setting:endcolor]]';
            $css = str_replace($tag, '#E3DFD4', $css);
            break;
        case 'orange':
            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#FFD46C', $css);

            // block header background
            $tag = '[[setting:begincolor]]';
            $css = str_replace($tag, '#FFE8D0', $css);
            $tag = '[[setting:endcolor]]';
            $css = str_replace($tag, '#FDC06D', $css);
            break;
        case 'peach':
            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#FCD3BC', $css);

            // block header background
            $tag = '[[setting:begincolor]]';
            $css = str_replace($tag, '#FFE6D7', $css);
            $tag = '[[setting:endcolor]]';
            $css = str_replace($tag, '#F7C099', $css);
            break;
        case 'silver':
            // page background
            $tag = '[[setting:pagebackground]]';
            $css = str_replace($tag, '#EFF0F2', $css);

            // block header background
            // block header background
            $tag = '[[setting:begincolor]]';
            $css = str_replace($tag, '#FDFEFF', $css);
            $tag = '[[setting:endcolor]]';
            $css = str_replace($tag, '#E0DFDD', $css);
            break;
        default:
            debugging('It seems a colour has been added to the frame trend colours folder but was not fully managed. The code must be updated by a developer.');
    }
    return $css;
}

/**
 * All theme functions should start with theme_frame_
 * @deprecated since 2.5.1
 */
function frame_set_logo() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/**
 * All theme functions should start with theme_frame_
 * @deprecated since 2.5.1
 */
function frame_pluginfile() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/**
 * All theme functions should start with theme_frame_
 * @deprecated since 2.5.1
 */
function frame_set_customcss() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/**
 * All theme functions should start with theme_frame_
 * @deprecated since 2.5.1
 */
function frame_get_html_for_settings() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/**
 * All theme functions should start with theme_frame_
 * @deprecated since 2.5.1
 */
function frame_set_framemargin() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}

/**
 * All theme functions should start with theme_frame_
 * @deprecated since 2.5.1
 */
function frame_set_trendcolor() {
    throw new coding_exception('Please call theme_'.__FUNCTION__.' instead of '.__FUNCTION__);
}
