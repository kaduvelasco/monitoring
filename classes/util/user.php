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
 * Select available fields in the users table
 *
 * @return array
 *
 * @throws dml_exception|coding_exception
 */
function report_monitoring_get_user_fields(): array
{
    global $CFG, $DB;

    $result = $DB->get_records_sql('DESCRIBE ' . $CFG->prefix . 'user');
    $ignored_fields = report_monitoring_get_ignored_user_fields();
    $arr_fields = [];

    foreach ($result as $r) {
        if (!in_array($r->field, $ignored_fields)) {
            $arr_fields[$r->field] = new lang_string($r->field, 'report_monitoring') . ' (' . $r->field . ')';
        }
    }
    return $arr_fields;
}

/**
 * Defines the fields in the users table that will not be shown in the settings.
 *
 * @return array
 */
function report_monitoring_get_ignored_user_fields(): array
{
    return [
        'mnethostid', 'password', 'emailstop', 'calendartype', 'theme', 'timezone', 'lastip', 'secret', 'picture',
        'descriptionformat', 'mailformat', 'maildigest', 'maildisplay', 'autosubscribe', 'trackforums', 'trustbitmask',
        'imagealt', 'lastnamephonetic', 'firstnamephonetic', 'middlename', 'alternatename', 'moodlenetprofile'
    ];
}

/**
 * Selects the extra fields of registered users in Moodle
 *
 * @return array
 *
 * @throws dml_exception
 */
function report_monitoring_get_user_extra_fields(): array
{
    global $CFG, $DB;

    $arr_fields = [];

    $sql = 'SELECT id, shortname, name FROM ' . $CFG->prefix . 'user_info_field ORDER BY sortorder ASC';
    $result = $DB->get_records_sql($sql);
    $i = 1;

    foreach ($result as $k) {
        $key = $k->id . '|' . $k->shortname . '|' . $k->name . '|uid' . $i;

        $arr_fields[$key] = $k->name . ' (uid' . $i . '.data)';
        $i++;
    }

    // Checks if the platform has extra fields
    $ttl = count($arr_fields);

    if (0 === $ttl) {
        $arr_fields = ['' => new lang_string('no_extra_fields', 'report_monitoring')];
    }
    return $arr_fields;
}

/**
 * Select user data as per report settings
 *
 * @param string $course_id
 * @param string $roles
 * @param string $user_fields
 * @param string $user_extrafields
 * @param string $report_options
 * @param string $report_filter
 *
 * @return array
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function report_monitoring_get_user_data(
    string $course_id,
    string $roles,
    string $user_fields,
    string $user_extrafields,
    string $report_options,
    string $report_filter
): array
{
    global $CFG, $DB;

    // We need the enrollment methods available for the course. We will filter by the profiles informed.
    $enrol_ids = report_monitoring_get_enrol_ids($course_id, $roles);

    // Now we will select the user data.
    $arr_field_datetime = ['firstaccess', 'lastaccess', 'lastlogin', 'currentlogin', 'timecreated', 'timemodified'];

    // Query SQL (for user data)
    // User fields
    $user_fields = ('' === $user_fields) ? 'id' : $user_fields;
    $user_fields2 = explode(',', $user_fields);
    $fields = $where = $where_extra = $left_join = '';
    $add_id = true;

    foreach ($user_fields2 as $f) {
        if ('id' === $f) { $add_id = false; }
        $fields .= 'u.' . $f . ',';
    }

    // Extra User Fields
    $use_extra_fields = false;

    if ('' !== $user_extrafields) {
        $use_extra_fields = true;

        $user_efields = explode(',', $user_extrafields);

        foreach ($user_efields as $ef) {
            $efields = explode('|', $ef);

            $fields .= $efields[3] . '.data AS "' . $efields[1] . '",';
            $left_join .= ' LEFT JOIN ' . $CFG->prefix . 'user_info_data ' . $efields[3];
            $left_join .= ' ON ' . $efields[3] . '.userid = u.id';
            $where_extra .= ' AND ' . $efields[3] . '.fieldid = ' . $efields[0];
        }
    }

    // Adjust the fields
    $fields = ($add_id) ? 'u.id,' . substr($fields, 0, -1) : substr($fields, 0, -1);

    // Where (for options defined in Report Options and User Filter)
    $report_options = explode(',', $report_options);
    $report_filter = report_monitoring_get_formatted_filter($report_filter);

    $where .= $where_extra;

    $where .= (in_array('hide_deleted', $report_options)) ? ' AND u.deleted = 0' : '';
    $where .= (in_array('hide_suspended', $report_options)) ? ' AND u.suspended = 0' : '';
    $where .= (in_array('hide_canceled_enrol', $report_options)) ? ' AND ue.status = 0' : '';
    $where .= ('' !== $report_filter['user']) ? ' ' . $report_filter['user'] : '';
    $where .= ('' !== $report_filter['extra']) ? ' ' . $report_filter['extra'] : '';

    // Final SQL
    $sql = 'SELECT ' . $fields . ' FROM ' . $CFG->prefix . 'role_assignments rs';
    $sql .= ' INNER JOIN ' . $CFG->prefix . 'user u ON u.id = rs.userid';
    $sql .= ' INNER JOIN ' . $CFG->prefix . 'context e ON rs.contextid = e.id';
    $sql .= ' INNER JOIN ' . $CFG->prefix . 'user_enrolments ue ON ue.userid = rs.userid';
    $sql .= ($use_extra_fields) ? $left_join : '';
    $sql .= ' WHERE e.contextlevel = 50 AND rs.roleid IN (' . $roles . ')';
    $sql .= ' AND e.instanceid = ' . $course_id . ' AND ue.enrolid IN (' . $enrol_ids . ')';
    $sql .= $where;

    // Retrieve the data in the database
    $data = $DB->get_records_sql($sql);

    // Handles user data
    $ids = '';
    $user_fields = report_monitoring_format_user_fields($fields);
    $users =[];

    foreach ($data as $d) {
        $ids .= $d->id . ',';

        foreach ($user_fields as $u) {
            $field = $u;

            if (in_array($field, $arr_field_datetime)) {
                $users[$d->id][$field] = report_monitoring_format_date($d->$field);
            } else {
                $users[$d->id][$field] = $d->$field;
            }
        }
    }
    // Data return
    return [
        'ids' => $ids,
        'user_fields' =>$fields,
        'user_data' => $users,
    ];
}

/**
 * Select the enrollment methods available for the course and for the profiles provided.
 *
 * @param string $course_id
 * @param string $roles
 *
 * @return false|string
 *
 * @throws dml_exception
 */
function report_monitoring_get_enrol_ids(string $course_id, string $roles)
{
    global $CFG, $DB;

    $sql = 'SELECT id FROM ' . $CFG->prefix . 'enrol WHERE courseid = ? AND roleid IN(?)';
    $data = $DB->get_records_sql($sql, [$course_id, $roles]);
    $ids = '';

    foreach ($data as $d) {
        $ids .= $d->id . ',';
    }
    return substr($ids, 0, -1);
}