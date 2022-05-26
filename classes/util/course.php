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
 * Returns information for the selected course.
 *
 * @param string $course_id
 *
 * @return array
 *
 * @throws dml_exception
 */
function report_monitoring_get_course_data(string $course_id): array
{
    $data = get_course($course_id);

    return [
        'id' => $data->id,
        'category' => $data->category,
        'fullname' => $data->fullname,
        'shortname' => $data->shortname,
        'idnumber' => $data->idnumber,
        'visible' => $data->visible,
        'format' => $data->format
    ];
}

/**
 * Build the course structure
 *
 * @param string $course_id
 * @param array $config
 * @param null $course_format
 *
 * @return array
 *
 * @throws dml_exception|coding_exception
 */
function report_monitoring_get_course_structure(string $course_id, array $config, $course_format = null): array
{
    $course_sections = report_monitoring_get_course_sections($course_id, $config, $course_format);
    $course_modules = report_monitoring_get_course_modules($course_id, $config);

    $arr_base = $arr_parent = $arr_child = $arr_return = [];

    // Adjust sessions and modules
    // Step one associates the modules with the corresponding session
    foreach ($course_sections as $s) {
        $arr_base[$s['section']]['id'] = $s['id'];
        $arr_base[$s['section']]['section'] = $s['section'];
        $arr_base[$s['section']]['name'] = $s['name'];
        $arr_base[$s['section']]['visible'] = $s['visible'];
        $arr_base[$s['section']]['parent'] = $s['parent'];

        $modules = explode(',', $s['sequence']);
        $k = 0;

        foreach ($modules as $m) {
            if (isset($course_modules[$m])) {
                $arr_base[$s['section']]['modules'][$k]['id'] = $course_modules[$m]['id'];
                $arr_base[$s['section']]['modules'][$k]['name'] = ('0' !== $s['parent'])
                    ? $s['name'] . ' - ' . $course_modules[$m]['data']['name']
                    : $course_modules[$m]['data']['name'];
                $arr_base[$s['section']]['modules'][$k]['type'] = $course_modules[$m]['data']['type'];
                $arr_base[$s['section']]['modules'][$k]['visible'] = $course_modules[$m]['visible'];
                $k++;
            }
        }
    }

    // Adjust sessions and modules
    // Separates parent elements from child elements
    foreach ($arr_base as $b) {
        if ('0' === $b['parent']) {
            $arr_parent[$b['section']] = $b;
        } else {
            $arr_child[$b['section']] = $b;
        }
    }

    // Adjust sessions and modules
    // Let's associate children with grandchild until only children remain
    $ttl_child = count($arr_child);

    if ($ttl_child > 0) {
        $continue = true;
        while ($continue) {
            $continue = false;

            foreach ($arr_child as $c) {
                if (isset($arr_child[$c['parent']])) {
                    foreach ($c['modules'] as $m) {
                        $arr_child[$c['parent']]['modules'][] = $m;
                    }
                    unset($arr_child[$c['section']]);
                    $continue = true;
                }
            }
        }

        // Adjust sessions and modules
        // Add children to parents
        foreach ($arr_child as $c) {
            if (isset($arr_parent[$c['parent']])) {
                foreach ($c['modules'] as $m) {
                    $arr_parent[$c['parent']]['modules'][] = $m;
                }
                unset($arr_child[$c['section']]);
            }
        }
    }

    // Adjust sessions and modules
    // Create the final array
    $i = 0;
    foreach ($arr_parent as $p) {
        $arr_return[$i]['id'] = $p['id'];
        $arr_return[$i]['name'] = $p['name'];
        $arr_return[$i]['section'] = $p['section'];
        $arr_return[$i]['visible'] = $p['visible'];
        $arr_return[$i]['ttl_modules'] = isset($p['modules']) ? count($p['modules']) : 0;
        $arr_return[$i]['modules'] = isset($p['modules']) ? $p['modules'] : [];
        $i++;
    }
    return $arr_return;
}

/**
 * Returns information from existing sessions in the course
 *
 * @param string $course_id
 * @param array $config
 * @param null $course_format
 *
 * @return array
 *
 * @throws dml_exception
 */
function report_monitoring_get_course_sections(string $course_id, array $config, $course_format = null): array
{
    global $CFG, $DB;

    $report_options = explode(',', $config['participation_options']);

    $sql = 'SELECT id, course, section, name, sequence, visible FROM ' . $CFG->prefix . 'course_sections';
    $sql .= ' WHERE course = ' . $course_id;
    $sql .= (in_array('hide_header', $report_options)) ? ' AND section > 0' : '';
    $sql .= (in_array('hide_section', $report_options)) ? ' AND visible = 1' : '';

    $data = $DB->get_records_sql($sql);
    $arr_return = [];
    $section_parent = [];
    $i = 0;

    // We will check if the theme used has any parent-child configuration
    $use_parent = false;

    if (!is_null($course_format)) {
        if (report_monitoring_use_parent($course_id, $course_format)) {
            $use_parent = true;
            $section_parent = report_monitoring_get_section_parent($course_id, $course_format);
        }
    }

    foreach ($data as $d) {
        $arr_return[$i]['id'] = $d->id;
        $arr_return[$i]['course'] = $d->course;
        $arr_return[$i]['section'] = $d->section;
        $arr_return[$i]['name'] = $d->name;
        $arr_return[$i]['sequence'] = $d->sequence;
        $arr_return[$i]['visible'] = $d->visible;
        $arr_return[$i]['parent'] = ($use_parent) ? $section_parent[$d->id] : '0';
        $i++;
    }
    return $arr_return;
}

/**
 * Select parent-child structure of the session if necessary
 *
 * @param string $course_id
 * @param string $course_format
 *
 * @return array
 *
 * @throws dml_exception
 */
function report_monitoring_get_section_parent(string $course_id, string $course_format): array
{
    global $CFG, $DB;
    $arr_return = [];

    $sql = 'SELECT id, sectionid, value FROM ' . $CFG->prefix . 'course_format_options';
    $sql .= ' WHERE format = ? AND courseid = ? AND name = ?';

    $data = $DB->get_records_sql($sql, [$course_format, $course_id, 'parent']);

    foreach ($data as $d) {
        $arr_return[$d->sectionid] = $d->value;
    }
    return $arr_return;
}

/**
 * Select from the database the modules registered for the informed course.
 *
 * @param string $course_id
 * @param array $config
 *
 * @return array
 *
 * @throws dml_exception|coding_exception
 */
function report_monitoring_get_course_modules(string $course_id, array $config): array
{
    global $CFG, $DB;

    $report_options = explode(',', $config['participation_options']);

    $sql = 'SELECT id, course, module, instance, section, visible FROM ' . $CFG->prefix . 'course_modules';
    $sql .= ' WHERE course = ' . $course_id . ' AND deletioninprogress = 0';
    $sql .= ('' !== $config['ignored_modules']) ? ' AND module NOT IN (' . $config['ignored_modules'] . ')' : '';
    $sql .= (in_array('hide_section', $report_options)) ? ' AND visible = 1' : '';

    $data = $DB->get_records_sql($sql);
    $arr_return = [];

    foreach ($data as $d) {
        $arr_return[$d->id]['id'] = $d->id;
        $arr_return[$d->id]['course'] = $d->course;
        $arr_return[$d->id]['module'] = $d->module;
        $arr_return[$d->id]['instance'] = $d->instance;
        $arr_return[$d->id]['data'] = report_monitoring_get_module_data($d->module, $d->instance);
        $arr_return[$d->id]['section'] = $d->section;
        $arr_return[$d->id]['visible'] = $d->visible;
    }
    return $arr_return;
}

/**
 * Select the data of the informed module
 *
 * @param string $module
 * @param string $module_id
 *
 * @return array
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function report_monitoring_get_module_data(string $module, string $module_id): array
{
    global $CFG, $DB;

    // Modules available in moodle
    $sql = 'SELECT id, name FROM ' . $CFG->prefix . 'modules WHERE visible = 1';
    $result = $DB->get_records_sql($sql);
    $modules = [];

    foreach ($result as $k) {
        $modules[$k->id] = $k->name;
    }

    // Select module data
    $sql = 'SELECT * FROM ' . $CFG->prefix . $modules[$module] . ' WHERE id =' . $module_id;
    $module_data = $DB->get_record_sql($sql);

    return [
        'name' => strip_tags($module_data->name),
        'type' => get_string("modulename", $modules[$module])
    ];
}
