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
 * @version    1.0.1
 * @copyright  2022 Kadu Velasco
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Selects the course's grade structure.
 *
 * @param string $course_id
 * @param string $user_ids
 *
 * @return array
 *
 * @throws dml_exception
 */
function report_monitoring_get_grade_structure(string $course_id, string $user_ids): array
{
    global $CFG, $DB;

    $arr_category = $arr_activities = $arr_return = [];

    // Select available grade categories for the course
    $category_data = $DB->get_records('grade_categories', ['courseid' => $course_id]);

    $sql = 'SELECT gi.id, gi.categoryid, gi.itemname, gi.itemtype, gi.itemmodule, gi.iteminstance, gi.grademax, gi.grademin';
    $sql .= ' FROM ' . $CFG->prefix . 'grade_items gi';
    $sql .= ' INNER JOIN ' . $CFG->prefix . 'course_modules cm ON cm.course = gi.courseid AND cm.instance = gi.iteminstance';
    $sql .= ' INNER JOIN ' . $CFG->prefix . 'modules md ON cm.module = md.id AND md.name = gi.itemmodule';
    $sql .= ' WHERE gi.itemtype = "mod" AND cm.deletioninprogress = 0 AND cm.course = ' . $course_id;
    $sql .= ' ORDER BY sortorder ASC';

    // Seleciona os itens de nota
    //$sql = 'SELECT id, categoryid, itemname, itemtype, itemmodule, iteminstance, grademax, grademin';
    //$sql .= ' FROM ' . $CFG->prefix . 'grade_items WHERE courseid = ' . $course_id;
    //$sql .= ' AND itemtype = "mod" ORDER BY sortorder ASC';

    $course_activities = $DB->get_records_sql($sql);

    // Adjust the categories
    foreach ($category_data as $cat) {

        $fullname = $cat->fullname;

        if ('?' !== $cat->fullname) {
            if ( (!is_null($cat->parent)) && (isset($arr_activities[$cat->parent]))) {
                $fullname = $arr_activities[$cat->parent]['name'] . ' / ' . $cat->fullname;
            }

            $arr_category[$cat->id]['name'] = $fullname;
            $arr_category[$cat->id]['hidden'] = ('1' === $cat->hidden);
        }
    }

    // adjust activities
    $i = 0;
    foreach ($course_activities as $act) {

        $activity_name = (isset($arr_category[$act->categoryid]))
            ? $arr_category[$act->categoryid]['name'] . ' / '
            : '';

        $arr_activities[$i]['name'] = $activity_name . $act->itemname;
        $arr_activities[$i]['type'] = $act->itemtype;
        $arr_activities[$i]['module'] = $act->itemmodule;
        $arr_activities[$i]['instance'] = $act->iteminstance;
        $arr_activities[$i]['grademax'] = str_replace('.00000', ',00', $act->grademax);
        $arr_activities[$i]['grademin'] = str_replace('.00000', ',00', $act->grademin);
        $i++;
    }
    return $arr_activities;
}
