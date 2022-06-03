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

defined('MOODLE_INTERNAL') || die;

/**
 * Select log information
 *
 * @param string $course_id
 * @param string $user_ids
 *
 * @return array
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function report_monitoring_get_log_info(string $course_id, string $user_ids): array
{
    global $CFG, $DB;

    $sql = 'SELECT id, userid, eventname, component, action, target, timecreated';
    $sql .= ' FROM ' . $CFG->prefix . 'logstore_standard_log';
    $sql .= ' WHERE courseid = ' . $course_id;
    $sql .= ' AND action NOT IN ("failed", "graded")';
    $sql .= ' AND userid IN(' . substr($user_ids, 0, -1) . ')';
    $sql .= ' ORDER BY userid ASC, timecreated ASC';

    $data = $DB->get_records_sql($sql);

    $arr = [];

    // Adjust the array
    foreach ($data as $d) {

        $arr[$d->userid][] = $d->timecreated;
    }

    // Calculate the time estimate
    $arr_final = [];
    foreach ($arr as $k => $v) {
        $ttl = count($v);
        $time = 0;

        if ($ttl > 1) {
            for ($i = 0; $i < $ttl; $i++) {
                if (array_key_exists(($i + 1), $v)) {
                    $time = $time + report_monitoring_calculate_time($v[$i], $v[$i + 1]);
                }
            }
            $arr_final[$k] = gmdate("H:i:s", $time);
        } else {
            $arr_final[$k] = get_string('no_data', 'report_monitoring');
        }
    }
    return $arr_final;
}

function report_monitoring_calculate_time(string $start, string $end)
{
    global $CFG;
    $diference = $end - $start;
    return ($diference > $CFG->sessiontimeout) ? $CFG->sessiontimeout : $diference;
}
