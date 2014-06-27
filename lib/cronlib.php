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
 * Cron functions.
 *
 * @package    core
 * @subpackage admin
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute cron tasks
 */
function cron_run() {
    global $DB, $CFG, $OUTPUT;

    if (CLI_MAINTENANCE) {
        echo "CLI maintenance mode active, cron execution suspended.\n";
        exit(1);
    }

    if (moodle_needs_upgrading()) {
        echo "Moodle upgrade pending, cron execution suspended.\n";
        exit(1);
    }

    require_once($CFG->libdir.'/adminlib.php');

    if (!empty($CFG->showcronsql)) {
        $DB->set_debug(true);
    }
    if (!empty($CFG->showcrondebugging)) {
        set_debugging(DEBUG_DEVELOPER, true);
    }

    core_php_time_limit::raise();
    $starttime = microtime();

    // Increase memory limit
    raise_memory_limit(MEMORY_EXTRA);

    // Emulate normal session - we use admin accoutn by default
    cron_setup_user();

    // Start output log
    $timenow  = time();
    mtrace("Server Time: ".date('r', $timenow)."\n\n");

<<<<<<< HEAD
    // Run all scheduled tasks.
    while (!\core\task\manager::static_caches_cleared_since($timenow) &&
           $task = \core\task\manager::get_next_scheduled_task($timenow)) {
        mtrace("Execute scheduled task: " . $task->get_name());
        cron_trace_time_and_memory();
        $predbqueries = null;
        $predbqueries = $DB->perf_get_queries();
        $pretime      = microtime(1);
        try {
            $task->execute();
            if (isset($predbqueries)) {
                mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
                mtrace("... used " . (microtime(1) - $pretime) . " seconds");
=======

    // Run cleanup core cron jobs, but not every time since they aren't too important.
    // These don't have a timer to reduce load, so we'll use a random number
    // to randomly choose the percentage of times we should run these jobs.
    $random100 = rand(0,100);
    if ($random100 < 20) {     // Approximately 20% of the time.
        mtrace("Running clean-up tasks...");
        cron_trace_time_and_memory();

        // Delete users who haven't confirmed within required period
        if (!empty($CFG->deleteunconfirmed)) {
            $cuttime = $timenow - ($CFG->deleteunconfirmed * 3600);
            $rs = $DB->get_recordset_sql ("SELECT *
                                             FROM {user}
                                            WHERE confirmed = 0 AND firstaccess > 0
                                                  AND firstaccess < ? AND deleted = 0", array($cuttime));
            foreach ($rs as $user) {
                delete_user($user); // we MUST delete user properly first
                $DB->delete_records('user', array('id'=>$user->id)); // this is a bloody hack, but it might work
                mtrace(" Deleted unconfirmed user for ".fullname($user, true)." ($user->id)");
>>>>>>> 5c1049f72bfc192420281551af7356cb5ec18ea3
            }
            mtrace("Scheduled task complete: " . $task->get_name());
            \core\task\manager::scheduled_task_complete($task);
        } catch (Exception $e) {
            if ($DB && $DB->is_transaction_started()) {
                error_log('Database transaction aborted automatically in ' . get_class($task));
                $DB->force_transaction_rollback();
            }
<<<<<<< HEAD
            if (isset($predbqueries)) {
                mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
                mtrace("... used " . (microtime(1) - $pretime) . " seconds");
=======
            $rs->close();
        }


        // Delete old logs to save space (this might need a timer to slow it down...)
        if (!empty($CFG->loglifetime)) {  // value in days
            $loglifetime = $timenow - ($CFG->loglifetime * 3600 * 24);
            $DB->delete_records_select("log", "time < ?", array($loglifetime));
            mtrace(" Deleted old log records");
        }


        // Delete old backup_controllers and logs.
        $loglifetime = get_config('backup', 'loglifetime');
        if (!empty($loglifetime)) {  // Value in days.
            $loglifetime = $timenow - ($loglifetime * 3600 * 24);
            // Delete child records from backup_logs.
            $DB->execute("DELETE FROM {backup_logs}
                           WHERE EXISTS (
                               SELECT 'x'
                                 FROM {backup_controllers} bc
                                WHERE bc.backupid = {backup_logs}.backupid
                                  AND bc.timecreated < ?)", array($loglifetime));
            // Delete records from backup_controllers.
            $DB->execute("DELETE FROM {backup_controllers}
                          WHERE timecreated < ?", array($loglifetime));
            mtrace(" Deleted old backup records");
        }


        // Delete old cached texts
        if (!empty($CFG->cachetext)) {   // Defined in config.php
            $cachelifetime = time() - $CFG->cachetext - 60;  // Add an extra minute to allow for really heavy sites
            $DB->delete_records_select('cache_text', "timemodified < ?", array($cachelifetime));
            mtrace(" Deleted old cache_text records");
        }


        if (!empty($CFG->usetags)) {
            require_once($CFG->dirroot.'/tag/lib.php');
            tag_cron();
            mtrace(' Executed tag cron');
        }


        // Context maintenance stuff
        context_helper::cleanup_instances();
        mtrace(' Cleaned up context instances');
        context_helper::build_all_paths(false);
        // If you suspect that the context paths are somehow corrupt
        // replace the line below with: context_helper::build_all_paths(true);
        mtrace(' Built context paths');


        // Remove expired cache flags
        gc_cache_flags();
        mtrace(' Cleaned cache flags');


        // Cleanup messaging
        if (!empty($CFG->messagingdeletereadnotificationsdelay)) {
            $notificationdeletetime = time() - $CFG->messagingdeletereadnotificationsdelay;
            $DB->delete_records_select('message_read', 'notification=1 AND timeread<:notificationdeletetime', array('notificationdeletetime'=>$notificationdeletetime));
            mtrace(' Cleaned up read notifications');
        }

        mtrace(' Deleting temporary files...');
        cron_delete_from_temp();

        // Cleanup user password reset records
        // Delete any reset request records which are expired by more than a day.
        // (We keep recently expired requests around so we can give a different error msg to users who
        // are trying to user a recently expired reset attempt).
        $pwresettime = isset($CFG->pwresettime) ? $CFG->pwresettime : 1800;
        $earliestvalid = time() - $pwresettime - DAYSECS;
        $DB->delete_records_select('user_password_resets', "timerequested < ?", array($earliestvalid));
        mtrace(' Cleaned up old password reset records');

        mtrace("...finished clean-up tasks");

    } // End of occasional clean-up tasks


    // Send login failures notification - brute force protection in moodle is weak,
    // we should at least send notices early in each cron execution
    if (notify_login_failures()) {
        mtrace(' Notified login failures');
    }


    // Make sure all context instances are properly created - they may be required in auth, enrol, etc.
    context_helper::create_instances();
    mtrace(' Created missing context instances');


    // Session gc.
    mtrace("Running session gc tasks...");
    \core\session\manager::gc();
    mtrace("...finished stale session cleanup");


    // Run the auth cron, if any before enrolments
    // because it might add users that will be needed in enrol plugins
    $auths = get_enabled_auth_plugins();
    mtrace("Running auth crons if required...");
    cron_trace_time_and_memory();
    foreach ($auths as $auth) {
        $authplugin = get_auth_plugin($auth);
        if (method_exists($authplugin, 'cron')) {
            mtrace("Running cron for auth/$auth...");
            $authplugin->cron();
            if (!empty($authplugin->log)) {
                mtrace($authplugin->log);
            }
        }
        unset($authplugin);
    }
    // Generate new password emails for users - ppl expect these generated asap
    if ($DB->count_records('user_preferences', array('name'=>'create_password', 'value'=>'1'))) {
        mtrace('Creating passwords for new users...');
        $usernamefields = get_all_user_name_fields(true, 'u');
        $newusers = $DB->get_recordset_sql("SELECT u.id as id, u.email,
                                                 $usernamefields, u.username, u.lang,
                                                 p.id as prefid
                                            FROM {user} u
                                            JOIN {user_preferences} p ON u.id=p.userid
                                           WHERE p.name='create_password' AND p.value='1' AND u.email !='' AND u.suspended = 0 AND u.auth != 'nologin' AND u.deleted = 0");

        // note: we can not send emails to suspended accounts
        foreach ($newusers as $newuser) {
            // Use a low cost factor when generating bcrypt hash otherwise
            // hashing would be slow when emailing lots of users. Hashes
            // will be automatically updated to a higher cost factor the first
            // time the user logs in.
            if (setnew_password_and_mail($newuser, true)) {
                unset_user_preference('create_password', $newuser);
                set_user_preference('auth_forcepasswordchange', 1, $newuser);
            } else {
                trigger_error("Could not create and mail new user password!");
>>>>>>> 5c1049f72bfc192420281551af7356cb5ec18ea3
            }
            mtrace("Scheduled task failed: " . $task->get_name() . "," . $e->getMessage());
            \core\task\manager::scheduled_task_failed($task);
        }
        unset($task);
    }

    // Run all adhoc tasks.
    while (!\core\task\manager::static_caches_cleared_since($timenow) &&
           $task = \core\task\manager::get_next_adhoc_task($timenow)) {
        mtrace("Execute adhoc task: " . get_class($task));
        cron_trace_time_and_memory();
        $predbqueries = null;
        $predbqueries = $DB->perf_get_queries();
        $pretime      = microtime(1);
        try {
            $task->execute();
            if (isset($predbqueries)) {
                mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
                mtrace("... used " . (microtime(1) - $pretime) . " seconds");
            }
            mtrace("Adhoc task complete: " . get_class($task));
            \core\task\manager::adhoc_task_complete($task);
        } catch (Exception $e) {
            if ($DB && $DB->is_transaction_started()) {
                error_log('Database transaction aborted automatically in ' . get_class($task));
                $DB->force_transaction_rollback();
            }
            if (isset($predbqueries)) {
                mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
                mtrace("... used " . (microtime(1) - $pretime) . " seconds");
            }
            mtrace("Adhoc task failed: " . get_class($task) . "," . $e->getMessage());
            \core\task\manager::adhoc_task_failed($task);
        }
        unset($task);
    }

    mtrace("Cron script completed correctly");

    gc_collect_cycles();
    mtrace('Cron completed at ' . date('H:i:s') . '. Memory used ' . display_size(memory_get_usage()) . '.');
    $difftime = microtime_diff($starttime, microtime());
    mtrace("Execution took ".$difftime." seconds");
}

/**
 * Output some standard information during cron runs. Specifically current time
 * and memory usage. This method also does gc_collect_cycles() (before displaying
 * memory usage) to try to help PHP manage memory better.
 */
function cron_trace_time_and_memory() {
    gc_collect_cycles();
    mtrace('... started ' . date('H:i:s') . '. Current memory use ' . display_size(memory_get_usage()) . '.');
}

/**
 * Executes cron functions for a specific type of plugin.
 *
 * @param string $plugintype Plugin type (e.g. 'report')
 * @param string $description If specified, will display 'Starting (whatever)'
 *   and 'Finished (whatever)' lines, otherwise does not display
 */
function cron_execute_plugin_type($plugintype, $description = null) {
    global $DB;

    // Get list from plugin => function for all plugins
    $plugins = get_plugin_list_with_function($plugintype, 'cron');

    // Modify list for backward compatibility (different files/names)
    $plugins = cron_bc_hack_plugin_functions($plugintype, $plugins);

    // Return if no plugins with cron function to process
    if (!$plugins) {
        return;
    }

    if ($description) {
        mtrace('Starting '.$description);
    }

    foreach ($plugins as $component=>$cronfunction) {
        $dir = core_component::get_component_directory($component);

        // Get cron period if specified in version.php, otherwise assume every cron
        $cronperiod = 0;
        if (file_exists("$dir/version.php")) {
            $plugin = new stdClass();
            include("$dir/version.php");
            if (isset($plugin->cron)) {
                $cronperiod = $plugin->cron;
            }
        }

        // Using last cron and cron period, don't run if it already ran recently
        $lastcron = get_config($component, 'lastcron');
        if ($cronperiod && $lastcron) {
            if ($lastcron + $cronperiod > time()) {
                // do not execute cron yet
                continue;
            }
        }

        mtrace('Processing cron function for ' . $component . '...');
        cron_trace_time_and_memory();
        $pre_dbqueries = $DB->perf_get_queries();
        $pre_time = microtime(true);

        $cronfunction();

        mtrace("done. (" . ($DB->perf_get_queries() - $pre_dbqueries) . " dbqueries, " .
                round(microtime(true) - $pre_time, 2) . " seconds)");

        set_config('lastcron', time(), $component);
        core_php_time_limit::raise();
    }

    if ($description) {
        mtrace('Finished ' . $description);
    }
}

/**
 * Used to add in old-style cron functions within plugins that have not been converted to the
 * new standard API. (The standard API is frankenstyle_name_cron() in lib.php; some types used
 * cron.php and some used a different name.)
 *
 * @param string $plugintype Plugin type e.g. 'report'
 * @param array $plugins Array from plugin name (e.g. 'report_frog') to function name (e.g.
 *   'report_frog_cron') for plugin cron functions that were already found using the new API
 * @return array Revised version of $plugins that adds in any extra plugin functions found by
 *   looking in the older location
 */
function cron_bc_hack_plugin_functions($plugintype, $plugins) {
    global $CFG; // mandatory in case it is referenced by include()d PHP script

    if ($plugintype === 'report') {
        // Admin reports only - not course report because course report was
        // never implemented before, so doesn't need BC
        foreach (core_component::get_plugin_list($plugintype) as $pluginname=>$dir) {
            $component = $plugintype . '_' . $pluginname;
            if (isset($plugins[$component])) {
                // We already have detected the function using the new API
                continue;
            }
            if (!file_exists("$dir/cron.php")) {
                // No old style cron file present
                continue;
            }
            include_once("$dir/cron.php");
            $cronfunction = $component . '_cron';
            if (function_exists($cronfunction)) {
                $plugins[$component] = $cronfunction;
            } else {
                debugging("Invalid legacy cron.php detected in $component, " .
                        "please use lib.php instead");
            }
        }
    } else if (strpos($plugintype, 'grade') === 0) {
        // Detect old style cron function names
        // Plugin gradeexport_frog used to use grade_export_frog_cron() instead of
        // new standard API gradeexport_frog_cron(). Also applies to gradeimport, gradereport
        foreach(core_component::get_plugin_list($plugintype) as $pluginname=>$dir) {
            $component = $plugintype.'_'.$pluginname;
            if (isset($plugins[$component])) {
                // We already have detected the function using the new API
                continue;
            }
            if (!file_exists("$dir/lib.php")) {
                continue;
            }
            include_once("$dir/lib.php");
            $cronfunction = str_replace('grade', 'grade_', $plugintype) . '_' .
                    $pluginname . '_cron';
            if (function_exists($cronfunction)) {
                $plugins[$component] = $cronfunction;
            }
        }
    }

    return $plugins;
}
<<<<<<< HEAD
=======

/**
 * Output some standard information during cron runs. Specifically current time
 * and memory usage. This method also does gc_collect_cycles() (before displaying
 * memory usage) to try to help PHP manage memory better.
 */
function cron_trace_time_and_memory() {
    gc_collect_cycles();
    mtrace('... started ' . date('H:i:s') . '. Current memory use ' . display_size(memory_get_usage()) . '.');
}

/**
 * Notify admin users or admin user of any failed logins (since last notification).
 *
 * Note that this function must be only executed from the cron script
 * It uses the cache_flags system to store temporary records, deleting them
 * by name before finishing
 *
 * @return bool True if executed, false if not
 */
function notify_login_failures() {
    global $CFG, $DB, $OUTPUT;

    if (empty($CFG->notifyloginfailures)) {
        return false;
    }

    $recip = get_users_from_config($CFG->notifyloginfailures, 'moodle/site:config');

    if (empty($CFG->lastnotifyfailure)) {
        $CFG->lastnotifyfailure=0;
    }

    // If it has been less than an hour, or if there are no recipients, don't execute.
    if (((time() - HOURSECS) < $CFG->lastnotifyfailure) || !is_array($recip) || count($recip) <= 0) {
        return false;
    }

    // we need to deal with the threshold stuff first.
    if (empty($CFG->notifyloginthreshold)) {
        $CFG->notifyloginthreshold = 10; // default to something sensible.
    }

    // Get all the IPs with more than notifyloginthreshold failures since lastnotifyfailure
    // and insert them into the cache_flags temp table
    $sql = "SELECT ip, COUNT(*)
              FROM {log}
             WHERE module = 'login' AND action = 'error'
                   AND time > ?
          GROUP BY ip
            HAVING COUNT(*) >= ?";
    $params = array($CFG->lastnotifyfailure, $CFG->notifyloginthreshold);
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $iprec) {
        if (!empty($iprec->ip)) {
            set_cache_flag('login_failure_by_ip', $iprec->ip, '1', 0);
        }
    }
    $rs->close();

    // Get all the INFOs with more than notifyloginthreshold failures since lastnotifyfailure
    // and insert them into the cache_flags temp table
    $sql = "SELECT info, count(*)
              FROM {log}
             WHERE module = 'login' AND action = 'error'
                   AND time > ?
          GROUP BY info
            HAVING count(*) >= ?";
    $params = array($CFG->lastnotifyfailure, $CFG->notifyloginthreshold);
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $inforec) {
        if (!empty($inforec->info)) {
            set_cache_flag('login_failure_by_info', $inforec->info, '1', 0);
        }
    }
    $rs->close();

    // Now, select all the login error logged records belonging to the ips and infos
    // since lastnotifyfailure, that we have stored in the cache_flags table
    $sql = "SELECT * FROM (
        SELECT l.*, u.firstname, u.lastname
              FROM {log} l
              JOIN {cache_flags} cf ON l.ip = cf.name
         LEFT JOIN {user} u         ON l.userid = u.id
             WHERE l.module = 'login' AND l.action = 'error'
                   AND l.time > ?
                   AND cf.flagtype = 'login_failure_by_ip'
        UNION ALL
            SELECT l.*, u.firstname, u.lastname
              FROM {log} l
              JOIN {cache_flags} cf ON l.info = cf.name
         LEFT JOIN {user} u         ON l.userid = u.id
             WHERE l.module = 'login' AND l.action = 'error'
                   AND l.time > ?
                   AND cf.flagtype = 'login_failure_by_info') t
        ORDER BY t.time DESC";
    $params = array($CFG->lastnotifyfailure, $CFG->lastnotifyfailure);

    // Init some variables
    $count = 0;
    $messages = '';
    // Iterate over the logs recordset
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $log) {
        $log->time = userdate($log->time);
        $messages .= get_string('notifyloginfailuresmessage','',$log)."\n";
        $count++;
    }
    $rs->close();

    // If we have something useful to report.
    if ($count > 0) {
        $site = get_site();
        $subject = get_string('notifyloginfailuressubject', '', format_string($site->fullname));
        // Calculate the complete body of notification (start + messages + end)
        $body = get_string('notifyloginfailuresmessagestart', '', $CFG->wwwroot) .
                (($CFG->lastnotifyfailure != 0) ? '('.userdate($CFG->lastnotifyfailure).')' : '')."\n\n" .
                $messages .
                "\n\n".get_string('notifyloginfailuresmessageend','',$CFG->wwwroot)."\n\n";

        // For each destination, send mail
        mtrace('Emailing admins about '. $count .' failed login attempts');
        foreach ($recip as $admin) {
            //emailing the admins directly rather than putting these through the messaging system
            email_to_user($admin, core_user::get_support_user(), $subject, $body);
        }
    }

    // Update lastnotifyfailure with current time
    set_config('lastnotifyfailure', time());

    // Finally, delete all the temp records we have created in cache_flags
    $DB->delete_records_select('cache_flags', "flagtype IN ('login_failure_by_ip', 'login_failure_by_info')");

    return true;
}

/**
 * Delete files and directories older than one week from directory provided by $CFG->tempdir.
 *
 * @exception Exception Failed reading/accessing file or directory
 * @return bool True on successful file and directory deletion; otherwise, false on failure
 */
function cron_delete_from_temp() {
    global $CFG;

    $tmpdir = $CFG->tempdir;
    // Default to last weeks time.
    $time = strtotime('-1 week');

    try {
        $dir = new RecursiveDirectoryIterator($tmpdir);
        // Show all child nodes prior to their parent.
        $iter = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);

        for ($iter->rewind(); $iter->valid(); $iter->next()) {
            $node = $iter->getRealPath();
            if (!is_readable($node)) {
                continue;
            }
            // Check if file or directory is older than the given time.
            if ($iter->getMTime() < $time) {
                if ($iter->isDir() && !$iter->isDot()) {
                    // Don't attempt to delete the directory if it isn't empty.
                    if (!glob($node. DIRECTORY_SEPARATOR . '*')) {
                        if (@rmdir($node) === false) {
                            mtrace("Failed removing directory '$node'.");
                        }
                    }
                }
                if ($iter->isFile()) {
                    if (@unlink($node) === false) {
                        mtrace("Failed removing file '$node'.");
                    }
                }
            }
        }
    } catch (Exception $e) {
        mtrace('Failed reading/accessing file or directory.');
        return false;
    }

    return true;
}
>>>>>>> 5c1049f72bfc192420281551af7356cb5ec18ea3
