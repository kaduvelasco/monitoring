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
 * @package    report
 * @subpackage monitoring
 * @version    1.0.2
 * @copyright  2022 Kadu Velasco
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * get moodle modules
 *
 * @return array
 *
 * @throws dml_exception|coding_exception
 */
function report_monitoring_get_moodle_modules(): array
{
    global $CFG, $DB;

    $arr_fields = [];

    $sql = 'SELECT id, name FROM ' . $CFG->prefix . 'modules WHERE visible = ?';
    $result = $DB->get_records_sql($sql, [1]);

    foreach ($result as $k) {
        $arr_fields[$k->id] = get_string("modulename", $k->name);
    }

    return $arr_fields;
}

/**
 * @param string $course_id
 *
 * @return array
 *
 * @throws coding_exception
 */
function report_monitoring_get_completion_data(string $course_id): array
{
    $course = new stdClass();
    $course->id = $course_id;

    $info = new completion_info($course);

    $data = $info->get_progress_all();
    $arr_return = [];

    foreach ($data as $k => $v) {
        $arr_return[$k]['userid'] = $v->id;
        $arr_return[$k]['progress'] = [];
        foreach ($v->progress as $j => $m) {
            $arr_return[$k]['progress'][$j]['moduleid'] = $m->coursemoduleid;
            $arr_return[$k]['progress'][$j]['completionstate'] = $m->completionstate;
            $arr_return[$k]['progress'][$j]['viewed'] = $m->viewed;
            $arr_return[$k]['progress'][$j]['overrideby'] = $m->overrideby;
            $arr_return[$k]['progress'][$j]['timemodified'] = report_monitoring_format_date($m->timemodified);
        }
    }
    return $arr_return;
}

/**
 * Selects user access data in the module
 *
 * @param string $course_id
 *
 * @return array
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function report_monitoring_get_access_data(string $course_id): array
{
    global $CFG, $DB;
    $arr_return = [];

    // Select first and last access
    $sql = 'SELECT id, userid, eventname, courseid, MIN(timecreated) as firstaccess, MAX(timecreated) as lastaccess,
    COUNT(timecreated) as total';
    $sql .= ' FROM ' . $CFG->prefix . 'logstore_standard_log';
    $sql .= ' WHERE eventname = ? AND action = ? AND target = ?';
    $sql .= ' AND courseid = ? GROUP BY userid ';

    $data = $DB->get_records_sql($sql, ["\\core\\event\\course_viewed", "viewed", "course", $course_id]);

    foreach ($data as $d) {
        $arr_return[$d->userid]['firstaccess'] = report_monitoring_format_date($d->firstaccess);
        $arr_return[$d->userid]['lastaccess'] = report_monitoring_format_date($d->lastaccess);
        $arr_return[$d->userid]['total'] = $d->total;
    }
    return $arr_return;
}
