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
 * Used to be used for tracking conditions that apply before activities are
 * displayed to students ('conditional availability').
 *
 * Now replaced by the availability API. This library is a stub; some functions
 * still work while others throw exceptions. New code should not rely on the
 * classes, functions, or constants defined here.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated Since Moodle 2.7
 */

defined('MOODLE_INTERNAL') || die();

/**
 * CONDITION_STUDENTVIEW_HIDE - The activity is not displayed to students at all when conditions aren't met.
 */
define('CONDITION_STUDENTVIEW_HIDE', 0);
/**
 * CONDITION_STUDENTVIEW_SHOW - The activity is displayed to students as a greyed-out name, with
 * informational text that explains the conditions under which it will be available.
 */
define('CONDITION_STUDENTVIEW_SHOW', 1);

/**
 * CONDITION_MISSING_NOTHING - The $item variable is expected to contain all completion-related data
 */
define('CONDITION_MISSING_NOTHING', 0);
/**
 * CONDITION_MISSING_EXTRATABLE - The $item variable is expected to contain the fields from
 * the relevant table (course_modules or course_sections) but not the _availability data
 */
define('CONDITION_MISSING_EXTRATABLE', 1);
/**
 * CONDITION_MISSING_EVERYTHING - The $item variable is expected to contain nothing except the ID
 */
define('CONDITION_MISSING_EVERYTHING', 2);

/**
 * OP_CONTAINS - comparison operator that determines whether a specified user field contains
 * a provided variable
 */
define('OP_CONTAINS', 'contains');
/**
 * OP_DOES_NOT_CONTAIN - comparison operator that determines whether a specified user field does not
 * contain a provided variable
 */
define('OP_DOES_NOT_CONTAIN', 'doesnotcontain');
/**
 * OP_IS_EQUAL_TO - comparison operator that determines whether a specified user field is equal to
 * a provided variable
 */
define('OP_IS_EQUAL_TO', 'isequalto');
/**
 * OP_STARTS_WITH - comparison operator that determines whether a specified user field starts with
 * a provided variable
 */
define('OP_STARTS_WITH', 'startswith');
/**
 * OP_ENDS_WITH - comparison operator that determines whether a specified user field ends with
 * a provided variable
 */
define('OP_ENDS_WITH', 'endswith');
/**
 * OP_IS_EMPTY - comparison operator that determines whether a specified user field is empty
 */
define('OP_IS_EMPTY', 'isempty');
/**
 * OP_IS_NOT_EMPTY - comparison operator that determines whether a specified user field is not empty
 */
define('OP_IS_NOT_EMPTY', 'isnotempty');

require_once($CFG->libdir.'/completionlib.php');

/**
 * Core class to handle conditional activities.
 *
 * This class is now deprecated and partially functional. Public functions either
 * work and output deprecated messages or (in the case of the more obscure ones
 * which weren't really for public use, or those which can't be implemented in
 * the new API) throw exceptions.
 *
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated Since Moodle 2.7
 */
class condition_info extends condition_info_base {
    /**
     * Constructs with course-module details.
     *
     * @global moodle_database $DB
     * @uses CONDITION_MISSING_NOTHING
     * @param object $cm Moodle course-module object. Required ->id, ->course
     *   will save time, using a full cm_info will save more time
     * @param int $expectingmissing Used to control whether or not a developer
     *   debugging message (performance warning) will be displayed if some of
     *   the above data is missing and needs to be retrieved; a
     *   CONDITION_MISSING_xx constant
     * @param bool $loaddata If you need a 'write-only' object, set this value
     *   to false to prevent database access from constructor
     * @deprecated Since Moodle 2.7
     */
    public function __construct($cm, $expectingmissing = CONDITION_MISSING_NOTHING,
            $loaddata=true) {
        global $DB;
        debugging('The condition_info class is deprecated; change to \core_availability\info_module',
                DEBUG_DEVELOPER);

        // Check ID as otherwise we can't do the other queries.
        if (empty($cm->id)) {
            throw new coding_exception('Invalid parameters; item ID not included');
        }

        // Load cm_info object.
        if (!($cm instanceof cm_info)) {
            // Get modinfo.
            if (empty($cm->course)) {
                $modinfo = get_fast_modinfo(
                        $DB->get_field('course_modules', 'course', array('id' => $cm->id), MUST_EXIST));
            } else {
                $modinfo = get_fast_modinfo($cm->course);
            }

            // Get $cm object.
            $cm = $modinfo->get_cm($cm->id);
        }

        $this->item = $cm;
    }

    /**
     * Adds the extra availability conditions (if any) into the given
     * course-module (or section) object.
     *
     * This function may be called statically (for the editing form) or
     * dynamically.
     *
     * @param object $cm Moodle course-module data object
     * @deprecated Since Moodle 2.7 (does nothing)
     */
    public static function fill_availability_conditions($cm) {
        debugging('Calls to condition_info::fill_availability_conditions should be removed',
                DEBUG_DEVELOPER);
    }

    /**
     * Gets the course-module object with full necessary data to determine availability.
     *
     * @return object Course-module object with full data
     * @deprecated Since Moodle 2.7
     */
    public function get_full_course_module() {
        debugging('Calls to condition_info::get_full_course_module should be removed',
                DEBUG_DEVELOPER);
        return $this->item;
    }

    /**
     * Used to update a table (which no longer exists) based on form data
     * (which is no longer used).
     *
     * Should only have been called from core code. Now removed (throws exception).
     *
     * @param object $cm Course-module with as much data as necessary, min id
     * @param object $fromform Data from form
     * @param bool $wipefirst If true, wipes existing conditions
     * @deprecated Since Moodle 2.7 (not available)
     * @throws Always throws a coding_exception
     */
    public static function update_cm_from_form($cm, $fromform, $wipefirst=true) {
        throw new coding_exception('Function no longer available');
    }

    /**
     * Used to be used in course/lib.php because we needed to disable the
     * completion JS if a completion value affects a conditional activity.
     *
     * Should only have been called from core code. Now removed (throws exception).
     *
     * @global stdClass $CONDITIONLIB_PRIVATE
     * @param object $course Moodle course object
     * @param object $item Moodle course-module
     * @deprecated Since Moodle 2.7 (not available)
     * @throws Always throws a coding_exception
     */
    public static function completion_value_used_as_condition($course, $cm) {
        throw new coding_exception('Function no longer available');
    }
}


/**
 * Handles conditional access to sections.
 *
 * This class is now deprecated and partially functional. Public functions either
 * work and output deprecated messages or (in the case of the more obscure ones
 * which weren't really for public use, or those which can't be implemented in
 * the new API) throw exceptions.
 *
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated Since Moodle 2.7
 */
class condition_info_section extends condition_info_base {
    /**
     * Constructs with course-module details.
     *
     * @global moodle_database $DB
     * @uses CONDITION_MISSING_NOTHING
     * @param object $section Moodle section object. Required ->id, ->course
     *   will save time, using a full section_info will save more time
     * @param int $expectingmissing Used to control whether or not a developer
     *   debugging message (performance warning) will be displayed if some of
     *   the above data is missing and needs to be retrieved; a
     *   CONDITION_MISSING_xx constant
     * @param bool $loaddata If you need a 'write-only' object, set this value
     *   to false to prevent database access from constructor
     * @deprecated Since Moodle 2.7
     */
    public function __construct($section, $expectingmissing = CONDITION_MISSING_NOTHING,
            $loaddata=true) {
        global $DB;
        debugging('The condition_info_section class is deprecated; change to \core_availability\info_section',
                DEBUG_DEVELOPER);

        // Check ID as otherwise we can't do the other queries.
        if (empty($section->id)) {
            throw new coding_exception('Invalid parameters; item ID not included');
        }

        // Load cm_info object.
        if (!($section instanceof section_info)) {
            // Get modinfo.
            if (empty($section->course)) {
                $modinfo = get_fast_modinfo(
                        $DB->get_field('course_sections', 'course', array('id' => $section->id), MUST_EXIST));
            } else {
                $modinfo = get_fast_modinfo($section->course);
            }

            // Get $cm object.
            foreach ($modinfo->get_section_info_all() as $possible) {
                if ($possible->id === $section->id) {
                    $section = $possible;
                    break;
                }
            }
        }

        $this->item = $section;
    }

    /**
     * Adds the extra availability conditions (if any) into the given
     * course-module (or section) object.
     *
     * This function may be called statically (for the editing form) or
     * dynamically.
     *
     * @param object $section Moodle section data object
     * @deprecated Since Moodle 2.7 (does nothing)
     */
    public static function fill_availability_conditions($section) {
        debugging('Calls to condition_info_section::fill_availability_conditions should be removed',
                DEBUG_DEVELOPER);
    }

    /**
     * Gets the section object with full necessary data to determine availability.
     *
     * @return section_info Section object with full data
     * @deprecated Since Moodle 2.7
     */
    public function get_full_section() {
        debugging('Calls to condition_info_section::get_full_section should be removed',
                DEBUG_DEVELOPER);
        return $this->item;
    }

    /**
     * Utility function that used to be called by modedit.php; updated a
     * table (that no longer exists) based on the module form data.
     *
     * Should only have been called from core code. Now removed (throws exception).
     *
     * @param object $section Section object, must at minimum contain id
     * @param object $fromform Data from form
     * @param bool $wipefirst If true, wipes existing conditions
     * @deprecated Since Moodle 2.7 (not available)
     * @throws Always throws a coding_exception
     */
    public static function update_section_from_form($section, $fromform, $wipefirst=true) {
        throw new coding_exception('Function no longer available');
    }

    public function get_full_information($modinfo=null) {
        $information = parent::get_full_information($modinfo);

        // Grouping conditions.
        if ($this->item->groupingid > 0) {
            $information .= get_string(
                    'requires_grouping',
                    'condition', groups_get_grouping_name($this->item->groupingid));
        }

        return $information;
    }
}


/**
 * Base class to handle conditional items (course_modules or sections).
 *
 * This class is now deprecated and partially functional. Public functions either
 * work and output deprecated messages or (in the case of the more obscure ones
 * which weren't really for public use, or those which can't be implemented in
 * the new API) throw exceptions.
 *
 * @copyright 2012 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated Since Moodle 2.7
 */
abstract class condition_info_base {
    /** @var cm_info|section_info Item with availability data */
    protected $item;

    /**
     * The operators that provide the relationship
     * between a field and a value.
     *
     * @deprecated Since Moodle 2.7 (not available)
     * @throws Always throws a coding_exception
     */
    public static function get_condition_user_field_operators() {
        throw new coding_exception('Function no longer available');
    }

    /**
     * Returns list of user fields that can be compared.
     *
     * If you specify $formatoptions, then format_string will be called on the
     * custom field names. This is necessary for multilang support to work so
     * you should include this parameter unless you are going to format the
     * text later.
     *
     * @param array $formatoptions Passed to format_string if provided
     * @deprecated Since Moodle 2.7 (not available)
     * @throws Always throws a coding_exception
     */
    public static function get_condition_user_fields($formatoptions = null) {
<<<<<<< HEAD
        throw new coding_exception('Function no longer available');
=======
        global $DB, $CFG;

        $userfields = array(
            'firstname' => get_user_field_name('firstname'),
            'lastname' => get_user_field_name('lastname'),
            'email' => get_user_field_name('email'),
            'city' => get_user_field_name('city'),
            'country' => get_user_field_name('country'),
            'url' => get_user_field_name('url'),
            'icq' => get_user_field_name('icq'),
            'skype' => get_user_field_name('skype'),
            'aim' => get_user_field_name('aim'),
            'yahoo' => get_user_field_name('yahoo'),
            'msn' => get_user_field_name('msn'),
            'idnumber' => get_user_field_name('idnumber'),
            'institution' => get_user_field_name('institution'),
            'department' => get_user_field_name('department'),
            'phone1' => get_user_field_name('phone1'),
            'phone2' => get_user_field_name('phone2'),
            'address' => get_user_field_name('address')
        );

        // Go through the custom profile fields now
        if ($user_info_fields = $DB->get_records('user_info_field')) {
            require_once($CFG->dirroot . '/user/profile/lib.php');
            foreach ($user_info_fields as $field) {
                // This logic is the same as used in profile_user_record function
                // to exclude some field types from being loaded into the $USER
                // record.
                require_once($CFG->dirroot . '/user/profile/field/' .
                        $field->datatype . '/field.class.php');
                $newfield = 'profile_field_' . $field->datatype;
                $formfield = new $newfield();
                if (!$formfield->is_user_object_data()) {
                    continue;
                }

                if ($formatoptions) {
                    $userfields[$field->id] = format_string($field->name, true, $formatoptions);
                } else {
                    $userfields[$field->id] = $field->name;
                }
            }
        }

        return $userfields;
>>>>>>> 5c1049f72bfc192420281551af7356cb5ec18ea3
    }

    /**
     * Adds to the database a condition based on completion of another module.
     *
     * Should only have been called from core and test code. Now removed
     * (throws exception).
     *
     * @global moodle_database $DB
     * @param int $cmid ID of other module
     * @param int $requiredcompletion COMPLETION_xx constant
     * @deprecated Since Moodle 2.7 (not available)
     * @throws Always throws a coding_exception
     */
    public function add_completion_condition($cmid, $requiredcompletion) {
        throw new coding_exception('Function no longer available');
    }

    /**
     * Adds user fields condition
     *
     * Should only have been called from core and test code. Now removed
     * (throws exception).
     *
     * @param mixed $field numeric if it is a user profile field, character
     *                     if it is a column in the user table
     * @param int $operator specifies the relationship between field and value
     * @param char $value the value of the field
     * @deprecated Since Moodle 2.7 (not available)
     * @throws Always throws a coding_exception
     */
    public function add_user_field_condition($field, $operator, $value) {
        throw new coding_exception('Function no longer available');
    }

    /**
     * Adds to the database a condition based on the value of a grade item.
     *
     * Should only have been called from core and test code. Now removed
     * (throws exception).
     *
     * @global moodle_database $DB
     * @param int $gradeitemid ID of grade item
     * @param float $min Minimum grade (>=), up to 5 decimal points, or null if none
     * @param float $max Maximum grade (<), up to 5 decimal points, or null if none
     * @param bool $updateinmemory If true, updates data in memory; otherwise,
     *   memory version may be out of date (this has performance consequences,
     *   so don't do it unless it really needs updating)
     * @deprecated Since Moodle 2.7 (not available)
     * @throws Always throws a coding_exception
     */
    public function add_grade_condition($gradeitemid, $min, $max, $updateinmemory=false) {
        throw new coding_exception('Function no longer available');
    }

    /**
     * Erases from the database all conditions for this activity.
     *
     * Should only have been called from core and test code. Now removed
     * (throws exception).
     *
     * @deprecated Since Moodle 2.7 (not available)
     * @throws Always throws a coding_exception
     */
    public function wipe_conditions() {
        throw new coding_exception('Function no longer available');
    }

    /**
     * Integration point with new API; obtains the new class for this item.
     *
     * @return \core_availability\info Availability info for item
     */
    protected function get_availability_info() {
        if ($this->item instanceof section_info) {
            return new \core_availability\info_section($this->item);
        } else {
            return new \core_availability\info_module($this->item);
        }
    }

    /**
     * Obtains a string describing all availability restrictions (even if
     * they do not apply any more).
     *
     * @param course_modinfo|null $modinfo Usually leave as null for default. Specify when
     *   calling recursively from inside get_fast_modinfo()
     * @return string Information string (for admin) about all restrictions on
     *   this item
     * @deprecated Since Moodle 2.7
     */
    public function get_full_information($modinfo=null) {
<<<<<<< HEAD
        debugging('condition_info*::get_full_information() is deprecated, replace ' .
                'with new \core_availability\info_module($cm)->get_full_information()',
                DEBUG_DEVELOPER);
        return $this->get_availability_info()->get_full_information($modinfo);
=======
        $this->require_data();

        $information = '';

        // Completion conditions
        if (count($this->item->conditionscompletion) > 0) {
            if (!$modinfo) {
                $modinfo = get_fast_modinfo($this->item->course);
            }
            foreach ($this->item->conditionscompletion as $cmid => $expectedcompletion) {
                if (empty($modinfo->cms[$cmid])) {
                    continue;
                }
                $information .= html_writer::start_tag('li');
                $information .= get_string(
                        'requires_completion_' . $expectedcompletion,
                        'condition', $modinfo->cms[$cmid]->name) . ' ';
                $information .= html_writer::end_tag('li');
            }
        }

        // Grade conditions
        if (count($this->item->conditionsgrade) > 0) {
            foreach ($this->item->conditionsgrade as $gradeitemid => $minmax) {
                // String depends on type of requirement. We are coy about
                // the actual numbers, in case grades aren't released to
                // students.
                if (is_null($minmax->min) && is_null($minmax->max)) {
                    $string = 'any';
                } else if (is_null($minmax->max)) {
                    $string = 'min';
                } else if (is_null($minmax->min)) {
                    $string = 'max';
                } else {
                    $string = 'range';
                }
                $information .= html_writer::start_tag('li');
                $information .= get_string('requires_grade_'.$string, 'condition', $minmax->name).' ';
                $information .= html_writer::end_tag('li');
            }
        }

        // User field conditions
        if (count($this->item->conditionsfield) > 0) {
            $context = $this->get_context();
            // Need the array of operators
            foreach ($this->item->conditionsfield as $field => $details) {
                $a = new stdclass;
                // Display the fieldname into current lang.
                if (is_numeric($field)) {
                    // Is a custom profile field (will use multilang).
                    $translatedfieldname = $details->fieldname;
                } else {
                    $translatedfieldname = get_user_field_name($details->fieldname);
                }
                $a->field = format_string($translatedfieldname, true, array('context' => $context));
                $a->value = s($details->value);
                $information .= html_writer::start_tag('li');
                $information .= get_string('requires_user_field_'.$details->operator, 'condition', $a) . ' ';
                $information .= html_writer::end_tag('li');
            }
        }

        // The date logic is complicated. The intention of this logic is:
        // 1) display date without time where possible (whenever the date is
        //    midnight)
        // 2) when the 'until' date is e.g. 00:00 on the 14th, we display it as
        //    'until the 13th' (experience at the OU showed that students are
        //    likely to interpret 'until <date>' as 'until the end of <date>').
        // 3) This behaviour becomes confusing for 'same-day' dates where there
        //    are some exceptions.
        // Users in different time zones will typically not get the 'abbreviated'
        // behaviour but it should work OK for them aside from that.

        // The following cases are possible:
        // a) From 13:05 on 14 Oct until 12:10 on 17 Oct (exact, exact)
        // b) From 14 Oct until 12:11 on 17 Oct (midnight, exact)
        // c) From 13:05 on 14 Oct until 17 Oct (exact, midnight 18 Oct)
        // d) From 14 Oct until 17 Oct (midnight 14 Oct, midnight 18 Oct)
        // e) On 14 Oct (midnight 14 Oct, midnight 15 Oct)
        // f) From 13:05 on 14 Oct until 0:00 on 15 Oct (exact, midnight, same day)
        // g) From 0:00 on 14 Oct until 12:05 on 14 Oct (midnight, exact, same day)
        // h) From 13:05 on 14 Oct (exact)
        // i) From 14 Oct (midnight)
        // j) Until 13:05 on 14 Oct (exact)
        // k) Until 14 Oct (midnight 15 Oct)

        // Check if start and end dates are 'midnights', if so we show in short form
        $shortfrom = self::is_midnight($this->item->availablefrom);
        $shortuntil = self::is_midnight($this->item->availableuntil);

        // For some checks and for display, we need the previous day for the 'until'
        // value, if we are going to display it in short form
        if ($this->item->availableuntil) {
            $daybeforeuntil = strtotime('-1 day', usergetmidnight($this->item->availableuntil));
        }

        // Special case for if one but not both are exact and they are within a day
        if ($this->item->availablefrom && $this->item->availableuntil &&
                $shortfrom != $shortuntil && $daybeforeuntil < $this->item->availablefrom) {
            // Don't use abbreviated version (see examples f, g above)
            $shortfrom = false;
            $shortuntil = false;
        }

        // When showing short end date, the display time is the 'day before' one
        $displayuntil = $shortuntil ? $daybeforeuntil : $this->item->availableuntil;

        if ($this->item->availablefrom && $this->item->availableuntil) {
            if ($shortfrom && $shortuntil && $daybeforeuntil == $this->item->availablefrom) {
                $information .= html_writer::start_tag('li');
                $information .= get_string('requires_date_both_single_day', 'condition',
                        self::show_time($this->item->availablefrom, true));
                $information .= html_writer::end_tag('li');
            } else {
                $information .= html_writer::start_tag('li');
                $information .= get_string('requires_date_both', 'condition', (object)array(
                         'from' => self::show_time($this->item->availablefrom, $shortfrom),
                         'until' => self::show_time($displayuntil, $shortuntil)));
                $information .= html_writer::end_tag('li');
            }
        } else if ($this->item->availablefrom) {
            $information .= html_writer::start_tag('li');
            $information .= get_string('requires_date', 'condition',
                self::show_time($this->item->availablefrom, $shortfrom));
            $information .= html_writer::end_tag('li');
        } else if ($this->item->availableuntil) {
            $information .= html_writer::start_tag('li');
            $information .= get_string('requires_date_before', 'condition',
                self::show_time($displayuntil, $shortuntil));
            $information .= html_writer::end_tag('li');
        }

        // The information is in <li> tags, but to avoid taking up more space
        // if there is only a single item, we strip out the list tags so that it
        // is plain text in that case.
        if (!empty($information)) {
            $li = strpos($information, '<li>', 4);
            if ($li === false) {
                $information = preg_replace('~^\s*<li>(.*)</li>\s*$~s', '$1', $information);
            } else {
                $information = html_writer::tag('ul', $information);
            }
            $information = trim($information);
        }
        return $information;
    }

    /**
     * Checks whether a given time refers exactly to midnight (in current user
     * timezone).
     *
     * @param int $time Time
     * @return bool True if time refers to midnight, false if it's some other
     *   time or if it is set to zero
     */
    private static function is_midnight($time) {
        return $time && usergetmidnight($time) == $time;
>>>>>>> 5c1049f72bfc192420281551af7356cb5ec18ea3
    }

    /**
     * Determines whether this particular item is currently available
     * according to these criteria.
     *
     * - This does not include the 'visible' setting (i.e. this might return
     *   true even if visible is false); visible is handled independently.
     * - This does not take account of the viewhiddenactivities capability.
     *   That should apply later.
     *
     * @uses COMPLETION_COMPLETE
     * @uses COMPLETION_COMPLETE_FAIL
     * @uses COMPLETION_COMPLETE_PASS
     * @param string $information If the item has availability restrictions,
     *   a string that describes the conditions will be stored in this variable;
     *   if this variable is set blank, that means don't display anything
     * @param bool $grabthelot Performance hint: if true, caches information
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user)
     * @param int $userid If set, specifies a different user ID to check availability for
     * @param course_modinfo|null $modinfo Usually leave as null for default. Specify when
     *   calling recursively from inside get_fast_modinfo()
     * @return bool True if this item is available to the user, false otherwise
     * @deprecated Since Moodle 2.7
     */
    public function is_available(&$information, $grabthelot=false, $userid=0, $modinfo=null) {
<<<<<<< HEAD
        debugging('condition_info*::is_available() is deprecated, replace ' .
                'with new \core_availability\info_module($cm)->is_available()',
                DEBUG_DEVELOPER);
        return $this->get_availability_info()->is_available(
                $information, $grabthelot, $userid, $modinfo);
=======
        $this->require_data();

        $available = true;
        $information = '';

        // Check each completion condition
        if (count($this->item->conditionscompletion) > 0) {
            if (!$modinfo) {
                $modinfo = get_fast_modinfo($this->item->course);
            }
            $completion = new completion_info($modinfo->get_course());
            foreach ($this->item->conditionscompletion as $cmid => $expectedcompletion) {
                // If this depends on a deleted module, handle that situation
                // gracefully.
                if (empty($modinfo->cms[$cmid])) {
                    global $PAGE;
                    if (isset($PAGE) && strpos($PAGE->pagetype, 'course-view-')===0) {
                        debugging("Warning: activity {$this->item->id} '{$this->item->name}' has condition " .
                                "on deleted activity $cmid (to get rid of this message, edit the named activity)");
                    }
                    continue;
                }

                // The completion system caches its own data
                $completiondata = $completion->get_data((object)array('id' => $cmid),
                        $grabthelot, $userid, $modinfo);

                $thisisok = true;
                if ($expectedcompletion==COMPLETION_COMPLETE) {
                    // 'Complete' also allows the pass, fail states
                    switch ($completiondata->completionstate) {
                        case COMPLETION_COMPLETE:
                        case COMPLETION_COMPLETE_FAIL:
                        case COMPLETION_COMPLETE_PASS:
                            break;
                        default:
                            $thisisok = false;
                    }
                } else {
                    // Other values require exact match
                    if ($completiondata->completionstate!=$expectedcompletion) {
                        $thisisok = false;
                    }
                }
                if (!$thisisok) {
                    $available = false;
                    $information .= html_writer::start_tag('li');
                    $information .= get_string(
                        'requires_completion_' . $expectedcompletion,
                        'condition', $modinfo->cms[$cmid]->name) . ' ';
                    $information .= html_writer::end_tag('li');
                }
            }
        }

        // Check each grade condition
        if (count($this->item->conditionsgrade)>0) {
            foreach ($this->item->conditionsgrade as $gradeitemid => $minmax) {
                $score = $this->get_cached_grade_score($gradeitemid, $grabthelot, $userid);
                if ($score===false ||
                        (!is_null($minmax->min) && $score<$minmax->min) ||
                        (!is_null($minmax->max) && $score>=$minmax->max)) {
                    // Grade fail
                    $available = false;
                    // String depends on type of requirement. We are coy about
                    // the actual numbers, in case grades aren't released to
                    // students.
                    if (is_null($minmax->min) && is_null($minmax->max)) {
                        $string = 'any';
                    } else if (is_null($minmax->max)) {
                        $string = 'min';
                    } else if (is_null($minmax->min)) {
                        $string = 'max';
                    } else {
                        $string = 'range';
                    }
                    $information .= html_writer::start_tag('li');
                    $information .= get_string('requires_grade_' . $string, 'condition', $minmax->name) . ' ';
                    $information .= html_writer::end_tag('li');
                }
            }
        }

        // Check if user field condition
        if (count($this->item->conditionsfield) > 0) {
            $context = $this->get_context();
            foreach ($this->item->conditionsfield as $field => $details) {
                $uservalue = $this->get_cached_user_profile_field($userid, $field);
                if (!$this->is_field_condition_met($details->operator, $uservalue, $details->value)) {
                    // Set available to false
                    $available = false;
                    // Display the fieldname into current lang.
                    if (is_numeric($field)) {
                        // Is a custom profile field (will use multilang).
                        $translatedfieldname = $details->fieldname;
                    } else {
                        $translatedfieldname = get_user_field_name($details->fieldname);
                    }
                    $a = new stdClass();
                    $a->field = format_string($translatedfieldname, true, array('context' => $context));
                    $a->value = s($details->value);
                    $information .= html_writer::start_tag('li');
                    $information .= get_string('requires_user_field_'.$details->operator, 'condition', $a) . ' ';
                    $information .= html_writer::end_tag('li');
                }
            }
        }

        // Test dates
        if ($this->item->availablefrom) {
            if (time() < $this->item->availablefrom) {
                $available = false;

                $information .= html_writer::start_tag('li');
                $information .= get_string('requires_date', 'condition',
                        self::show_time($this->item->availablefrom,
                            self::is_midnight($this->item->availablefrom)));
                $information .= html_writer::end_tag('li');
            }
        }

        if ($this->item->availableuntil) {
            if (time() >= $this->item->availableuntil) {
                $available = false;
                // But we don't display any information about this case. This is
                // because the only reason to set a 'disappear' date is usually
                // to get rid of outdated information/clutter in which case there
                // is no point in showing it...

                // Note it would be nice if we could make it so that the 'until'
                // date appears below the item while the item is still accessible,
                // unfortunately this is not possible in the current system. Maybe
                // later, or if somebody else wants to add it.
            }
        }

        // If the item is marked as 'not visible' then we don't change the available
        // flag (visible/available are treated distinctly), but we remove any
        // availability info. If the item is hidden with the eye icon, it doesn't
        // make sense to show 'Available from <date>' or similar, because even
        // when that date arrives it will still not be available unless somebody
        // toggles the eye icon.
        if (!$this->item->visible) {
            $information = '';
        }

        // The information is in <li> tags, but to avoid taking up more space
        // if there is only a single item, we strip out the list tags so that it
        // is plain text in that case.
        if (!empty($information)) {
            $li = strpos($information, '<li>', 4);
            if ($li === false) {
                $information = preg_replace('~^\s*<li>(.*)</li>\s*$~s', '$1', $information);
            } else {
                $information = html_writer::tag('ul', $information);
            }
            $information = trim($information);
        }
        return $available;
    }

    /**
     * Shows a time either as a date or a full date and time, according to
     * user's timezone.
     *
     * @param int $time Time
     * @param bool $dateonly If true, uses date only
     * @return string Date
     */
    private function show_time($time, $dateonly) {
        return userdate($time,
                get_string($dateonly ? 'strftimedate' : 'strftimedatetime', 'langconfig'));
>>>>>>> 5c1049f72bfc192420281551af7356cb5ec18ea3
    }

    /**
     * Checks whether availability information should be shown to normal users.
     *
     * This information no longer makes sense with the new system because there
     * are multiple show options. (I doubt anyone much used this function anyhow!)
     *
     * @return bool True if information about availability should be shown to
     *   normal users
     * @deprecated Since Moodle 2.7
     */
    public function show_availability() {
        debugging('condition_info*::show_availability() is deprecated and there ' .
                'is no direct replacement (this is no longer a boolean value), ' .
                'please refactor code',
                DEBUG_DEVELOPER);
        return false;
    }

    /**
     * Used to be called by grade code to inform the completion system when a
     * grade was changed.
     *
     * This function should not have ever been used outside the grade API, so
     * it now just throws an exception.
     *
     * @param grade_grade $grade
     * @param bool $deleted
     * @deprecated Since Moodle 2.7 (not available)
     * @throws Always throws a coding_exception
     */
    public static function inform_grade_changed($grade, $deleted) {
        throw new coding_exception('Function no longer available');
    }

    /**
     * Used to be used for testing.
     *
     * @deprecated since 2.6
     */
    public static function wipe_session_cache() {
        debugging('Calls to completion_info::wipe_session_cache should be removed', DEBUG_DEVELOPER);
    }

    /**
     * Initialises the global cache
     *
     * @deprecated Since Moodle 2.7
     */
    public static function init_global_cache() {
        debugging('Calls to completion_info::init_globa_cache should be removed', DEBUG_DEVELOPER);
    }
}
