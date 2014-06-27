<?php

require_once("../../config.php");

$id = optional_param('id', 0, PARAM_INT);  // Course Module ID.
$a  = optional_param('a', 0, PARAM_INT);   // Assignment ID.

require_login();
$PAGE->set_context(context_system::instance());

if (!$id && !$a) {
    print_error('invalidcoursemodule');
}

$mapping = null;
if ($id) {
    $mapping = $DB->get_record('assignment_upgrade', array('oldcmid' => $id), '*', IGNORE_MISSING);
} else {
    $mapping = $DB->get_record('assignment_upgrade', array('oldinstance' => $a), '*', IGNORE_MISSING);
}

if (!$mapping) {
    $url = '';
    if (has_capability('moodle/site:config', context_system::instance())) {
        $url = new moodle_url('/admin/tool/assignmentupgrade/listnotupgraded.php');
    }
    print_error('assignmentneedsupgrade', 'assignment', $url);
}
<<<<<<< HEAD
=======
require_once($classfile);
$assignmentclass = "assignment_$assignment->assignmenttype";
$assignmentinstance = new $assignmentclass($cm->id, $assignment, $cm, $course);

/// Mark as viewed
$completion=new completion_info($course);
$completion->set_module_viewed($cm);
>>>>>>> 5c1049f72bfc192420281551af7356cb5ec18ea3

$url = new moodle_url('/mod/assign/view.php', array('id' => $mapping->newcmid));
redirect($url);
