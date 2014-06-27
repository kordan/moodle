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
 * Strings for component 'assignment', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   mod_assignment
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['assignment:addinstance'] = 'Add a new assignment';
$string['assignment:exportownsubmission'] = 'Export own submission';
$string['assignment:exportsubmission'] = 'Export submission';
$string['assignment:grade'] = 'Grade assignment';
<<<<<<< HEAD
=======
$string['assignmentmail'] = '{$a->teacher} has posted some feedback on your
assignment submission for \'{$a->assignment}\'

You can see it appended to your assignment submission:

    {$a->url}';
$string['assignmentmailhtml'] = '<p>{$a->teacher} has posted some feedback on your
assignment submission for \'<i>{$a->assignment}</i>\'.</p>
<p>You can see it appended to your <a href="{$a->url}">assignment submission</a>.</p>';
$string['assignmentmailsmall'] = '{$a->teacher} has posted some feedback on your
assignment submission for \'{$a->assignment}\' You can see it appended to your submission';
$string['assignmentname'] = 'Assignment name';
>>>>>>> 5c1049f72bfc192420281551af7356cb5ec18ea3
$string['assignment:submit'] = 'Submit assignment';
$string['assignment:view'] = 'View assignment';
<<<<<<< HEAD
$string['assignmentneedsupgrade'] = 'The legacy "Assignment 2.2" activity has been disabled. Please request that your site administrator runs the assignment upgrade tool for all legacy assignments in this site.';
=======
$string['availabledate'] = 'Available from';
$string['cannotdeletefiles'] = 'An error occurred and files could not be deleted';
$string['cannotviewassignment'] = 'You can not view this assignment';
$string['changegradewarning'] = 'This assignment has graded submissions and changing the grade will not automatically re-calculate existing submission grades. You must re-grade all existing submissions, if you wish to change the grade.';
$string['closedassignment'] = 'This assignment is closed, as the submission deadline has passed.';
$string['comment'] = 'Comment';
$string['commentinline'] = 'Comment inline';
$string['commentinline_help'] = 'If enabled, the submission text will be copied into the feedback comment field during grading, making it easier to comment inline (using a different colour, perhaps) or to edit the original text.';
$string['configitemstocount'] = 'Nature of items to be counted for student submissions in online assignments.';
$string['configmaxbytes'] = 'Default maximum assignment size for all assignments on the site (subject to course limits and other local settings)';
$string['configshowrecentsubmissions'] = 'Everyone can see notifications of submissions in recent activity reports.';
$string['confirmdeletefile'] = 'Are you absolutely sure you want to delete this file?<br /><strong>{$a}</strong>';
$string['coursemisconf'] = 'Course is misconfigured';
$string['currentgrade'] = 'Current grade in gradebook';
$string['deleteallsubmissions'] = 'Delete all submissions';
$string['deletefilefailed'] = 'Deleting of file failed.';
$string['description'] = 'Description';
$string['downloadall'] = 'Download all assignments as a zip';
$string['draft'] = 'Draft';
$string['due'] = 'Assignment due';
$string['duedate'] = 'Due date';
$string['duedateno'] = 'No due date';
$string['early'] = '{$a} early';
$string['editmysubmission'] = 'Edit my submission';
$string['editthesefiles'] = 'Edit these files';
$string['editthisfile'] = 'Update this file';
$string['addsubmission'] = 'Add submission';
$string['emailstudents'] = 'Email alerts to students';
$string['emailteachermail'] = '{$a->username} has updated their assignment submission
for \'{$a->assignment}\' at {$a->timeupdated}

It is available here:

    {$a->url}';
$string['emailteachermailhtml'] = '<p>{$a->username} has updated their assignment submission for <i>\'{$a->assignment}\' at {$a->timeupdated}</i>.</p>
<p>It is <a href="{$a->url}">available on the site</a>.</p>';
$string['emailteachers'] = 'Email alerts to teachers';
$string['emailteachers_help'] = 'If enabled, teachers receive email notification whenever students add or update an assignment submission.

Only teachers who are able to grade the particular assignment are notified. So, for example, if the course uses separate groups, teachers restricted to particular groups won\'t receive notification about students in other groups.';
$string['emptysubmission'] = 'You have not submitted anything yet';
$string['enablenotification'] = 'Send notifications';
$string['enablenotification_help'] = 'If enabled, students will be notified when their assignment submissions are graded.';
$string['errornosubmissions'] = 'There are no submissions to download';
$string['existingfiledeleted'] = 'Existing file has been deleted: {$a}';
$string['failedupdatefeedback'] = 'Failed to update submission feedback for user {$a}';
$string['feedback'] = 'Feedback';
$string['feedbackfromteacher'] = 'Feedback from {$a}';
$string['feedbackupdated'] = 'Submissions feedback updated for {$a} people';
$string['finalize'] = 'Prevent submission updates';
$string['finalizeerror'] = 'An error occurred and that submission could not be finalised';
$string['futureaassignment'] = 'This assignment is not yet available.';
$string['graded'] = 'Graded';
$string['guestnosubmit'] = 'Sorry, guests are not allowed to submit an assignment. You have to log in/ register before you can submit your answer.';
$string['guestnoupload'] = 'Sorry, guests are not allowed to upload';
$string['helpoffline'] = '<p>This is useful when the assignment is performed outside of Moodle.  It could be
   something elsewhere on the web or face-to-face.</p><p>Students can see a description of the assignment,
   but can\'t upload files or anything.  Grading works normally, and students will get notifications of
   their grades.</p>';
$string['helponline'] = '<p>This assignment type asks users to edit a text, using the normal
   editing tools.  Teachers can grade them online, and even add inline comments or changes.</p>
   <p>(If you are familiar with older versions of Moodle, this Assignment
   type does the same thing as the old Journal module used to do.)</p>';
$string['helpupload'] = '<p>This type of assignment allows each participant to upload one or more files in any format.
   These might be a Word processor documents, images, a zipped web site, or anything you ask them to submit.</p>
   <p>This type also allows you to upload multiple response files. Response files can be also uploaded before submission which
   can be used to give each participant different file to work with.</p>
   <p>Participants may also enter notes describing the submitted files, progress status or any other text information.</p>
   <p>Submission of this type of assignment must be manually finalised by the participant. You can review the current status
   at any time, unfinished assignments are marked as Draft. You can revert any ungraded assignment back to draft status.</p>';
$string['helpuploadsingle'] = '<p>This type of assignment allows each participant to upload a
   single file, of any type.</p> <p>This might be a Word processor document, an image,
   a zipped web site, or anything you ask them to submit.</p>';
$string['hideintro'] = 'Hide description before available date';
$string['hideintro_help'] = 'If enabled, the assignment description is hidden before the "Available from" date. Only the assignment name is displayed.';
$string['invalidassignment'] = 'Invalid assignment';
$string['invalidfileandsubmissionid'] = 'Missing file or submission ID';
$string['invalidid'] = 'Invalid assignment ID';
$string['invalidsubmissionid'] = 'Invalid submission ID';
$string['invalidtype'] = 'Invalid assignment type';
$string['invaliduserid'] = 'Invalid user ID';
$string['itemstocount'] = 'Count';
$string['lastgrade'] = 'Last grade';
$string['late'] = '{$a} late';
$string['maximumgrade'] = 'Maximum grade';
$string['maximumsize'] = 'Maximum size';
$string['maxpublishstate'] = 'Maximum visibility for blog entry before due date';
>>>>>>> 5c1049f72bfc192420281551af7356cb5ec18ea3
$string['messageprovider:assignment_updates'] = 'Assignment (2.2) notifications';
$string['assignmentdisabled'] = 'The legacy "Assignment 2.2" activity is disabled.';
$string['modulename'] = 'Assignment 2.2 (Disabled)';
$string['modulename_help'] = 'Legacy activity module that has been removed from Moodle.';
$string['modulenameplural'] = 'Assignments 2.2 (Disabled)';
$string['page-mod-assignment-x'] = 'Any assignment module page';
$string['page-mod-assignment-view'] = 'Assignment module main page';
$string['page-mod-assignment-submissions'] = 'Assignment module submission page';
$string['pluginname'] = 'Assignment 2.2 (Disabled)';
$string['upgradenotification'] = 'This activity is based on an older assignment module.';
$string['viewassignmentupgradetool'] = 'View the assignment upgrade tool';
$string['pendingupgrades_message_small'] = 'This plugin has been disabled. All remaining assignments must be upgraded to the new assignment module using the assignment upgrade tool.';
$string['pendingupgrades_message_subject'] = 'Important information regarding Assignment 2.2';
$string['pendingupgrades_message_content'] = 'As part of the upgrade to Moodle 2.7, the legacy Assignment 2.2 activity has been disabled. Backups from the legacy Assignment 2.2 activity will seamlessly restore to the newer Assignment activity. All remaining instances of the legacy Assignment 2.2 activity must be upgraded using the assignment upgrade tool {$a->docsurl}. There are {$a->count} instances of the legacy Assignment 2.2 activity on this site that require upgrading. Users will not be able to access the content of these activities until they have been upgraded.';
$string['pluginadministration'] = 'Assignment 2.2 (Disabled) administration';
