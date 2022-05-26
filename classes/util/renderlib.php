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
 * This file contains functions used by the monitoring reports
 *
 * @package    report
 * @subpackage monitoring
 * @version    1.0.1
 * @copyright  2022 Kadu Velasco
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Create table header with user data
 *
 * @param string $user_fields
 * @param string $user_efields
 * @param string $rowspan
 *
 * @return string
 *
 * @throws coding_exception
 */
function report_monitoring_make_header_user(string $user_fields, string $user_efields, string $rowspan): string
{
    // Handle extra fields, if any
    $arr_efields = [];

    if ('' !== $user_efields) {
        $uef = explode(',',$user_efields);

        foreach ($uef as $u) {
            $uef2 = explode('|', $u);
            $arr_efields[$uef2[1]] = $uef2[2];
        }
    }

    $html = '';
    $fields1 = explode(',', $user_fields);

    foreach ($fields1 as $f1) {
        $fields2 = explode('AS', $f1);

        if (isset($fields2[1])) {
            $efields = $arr_efields[str_replace('"', '', str_replace(' ', '', $fields2[1]))];
            $html .= '<td rowspan="' . $rowspan . '">' . $efields . '</td>';
        } else {
            $fields3 = explode('.', $fields2[0]);
            $html .= '<td rowspan="' . $rowspan . '">' . get_string($fields3[1], 'report_monitoring') . '</td>';
        }
    }
    return $html;
}

/**
 * Create the table header with access data
 *
 * @param $access_data
 * @param string $rowspan
 *
 * @return array
 *
 * @throws coding_exception
 */
function report_monitoring_make_header_access($access_data, string $rowspan): array
{

    $html1 = $html2 = '';

    if (!is_null($access_data)) {
        $html1 .= '<td colspan ="3">' . get_string('course_access_data', 'report_monitoring') . '</td>';
        $html2 .= '<td rowspan="' . $rowspan . '">' . new lang_string('first', 'report_monitoring') . '</td>';
        $html2 .= '<td rowspan="' . $rowspan . '">' . new lang_string('last', 'report_monitoring') . '</td>';
        $html2 .= '<td rowspan="' . $rowspan . '">' . new lang_string('amount', 'report_monitoring') . '</td>';
    }

    return [
        'line1' => $html1,
        'line2' => $html2
    ];
}

/**
 * Create the table header with the course structure data
 *
 * @param array $course_structure
 *
 * @return array
 *
 * @throws coding_exception
 */
function report_monitoring_make_header_course(array $course_structure): array
{
    $html1 = $html2 = $html3 = '';

    foreach ($course_structure as $c) {

        // Verifica se a sessão é a zero, se for e não possuir módulos, não faz nada
        if ( ('0' === $c['section']) && (0 === intval($c['ttl_modules']))) {
            $html1 = '';
        } else {
            $colspan = intval($c['ttl_modules']) * 3;

            $html1 .= '<td colspan="' . $colspan . '">' . $c['name'];
            $html1 .= ('0' === $c['visible'])
                ? '<br/><small>[' . get_string('hidden', 'report_monitoring') . ']</small></td>'
                : '</td>';

            if (isset($c['modules'])) {
                foreach ($c['modules'] as $m) {
                    $html2 .= '<td colspan="3">' . $m['name'] . '<br/><small>(' . $m['type'] . ')</small>';
                    $html2 .= ('0' === $m['visible'])
                        ? '<br/><small>[' . get_string('hidden', 'report_monitoring') . ']</small></td>'
                        : '</td>';
                    $html3 .= '<td>' . get_string('concluded', 'report_monitoring') . '</td>';
                    $html3 .= '<td>' . get_string('visualized', 'report_monitoring') . '</td>';
                    $html3 .= '<td>' . get_string('date', 'report_monitoring') . '</td>';
                }
            } else {
                $html2 .= '<td colspan="3"></td>';
                $html3 .= '<td colspan="3"></td>';
            }
        }
    }

    return [
        'line1' => $html1,
        'line2' => $html2,
        'line3' => $html3
    ];
}

/**
 * Create the participation report data
 *
 * @param array $user_data
 * @param array $completion_data
 * @param array $course_structure
 * @param $access_data
 *
 * @return string
 */
function report_monitoring_make_body_participation(
    array $user_data,
    array $completion_data,
    array $course_structure,
    $access_data): string
{
    $html = '';

    // First let's adjust the user fields
    $arr_fields = [];
    $fields1 = explode(',', $user_data['user_fields']);

    foreach ($fields1 as $f1) {
        $fields2 = explode('AS', $f1);

        if (isset($fields2[1])) {
            $arr_fields[] = str_replace('"', '', str_replace(' ', '', $fields2[1]));
        } else {
            $fields3 = explode('.', $fields2[0]);
            $arr_fields[] = $fields3[1];
        }
    }

    // Creating the HTML code
    foreach ($user_data['user_data'] as $v) {
        $html .= '<tr>';

        // User data
        foreach ($arr_fields as $f) {
            $html .= '<td>' . $v[$f] . '</td>';
        }

        // access data
        if (!is_null($access_data)) {
            if (array_key_exists($v['id'], $access_data)) {
                $html .= '<td>' . $access_data[$v['id']]['firstaccess'] . '</td>';
                $html .= '<td>' . $access_data[$v['id']]['lastaccess'] . '</td>';
                $html .= '<td>' . $access_data[$v['id']]['total'] . '</td>';
            } else {
                $html .= '<td>' . new lang_string('default_no_registry', 'report_monitoring') . '</td>';
                $html .= '<td>' . new lang_string('default_no_registry', 'report_monitoring') . '</td>';
                $html .= '<td>' . new lang_string('default_no_registry', 'report_monitoring') . '</td>';
            }
        }

        // Completion data
        foreach ($course_structure as $s) {
            if (isset($s['modules'])) {
                foreach ($s['modules'] as $m) {
                    if (array_key_exists($v['id'], $completion_data)) {
                        $data = $completion_data[$v['id']]['progress'];

                        // There is no data
                        if (empty($data)) {
                            $html .= report_monitoring_get_empty_code();
                        } else {
                            if (!isset($data[$m['id']])) {
                                $html .= report_monitoring_get_empty_code();
                            } else {
                                // Completed
                                switch ($data[$m['id']]['completionstate']) {
                                    case '1':
                                        $html .= '<td>' . new lang_string('completed', 'report_monitoring') . '</td>';
                                        break;
                                    case '2':
                                        $html .= '<td>' . new lang_string('completed_passed', 'report_monitoring') . '</td>';
                                        break;
                                    case '3':
                                        $html .= '<td>' . new lang_string('completed_failed', 'report_monitoring') . '</td>';
                                        break;
                                    case '0':
                                    default:
                                        $html .= '<td>' . new lang_string('not_completed', 'report_monitoring') . '</td>';
                                        break;
                                }

                                // Viewed
                                switch ($data[$m['id']]['viewed']) {
                                    case '0':
                                        $html .= '<td>' . new lang_string('not_viewed', 'report_monitoring') . '</td>';
                                        break;
                                    case '1':
                                        $html .= '<td>' . new lang_string('viewed', 'report_monitoring') . '</td>';
                                        break;
                                    case null:
                                    default:
                                        $html .= '<td>' . new lang_string('null_viewed', 'report_monitoring') . '</td>';
                                        break;
                                }
                                // Data
                                $html .= '<td>' . $data[$m['id']]['timemodified'] . '</td>';
                            }
                        }
                    } else {
                        $html .= report_monitoring_get_empty_code();
                    }
                }
            } else {
                $html .= '<td colspan="3"></td>';
            }

        }
        $html .= '</tr>';
    }
    return $html;
}

/**
 * Create HTML code for blank fields
 *
 * @return string
 */
function report_monitoring_get_empty_code(): string
{
    $html = '';
    $html .= '<td>' . new lang_string('tbl_empty_complete', 'report_monitoring') . '</td>';
    $html .= '<td>' . new lang_string('tbl_empty_visualized', 'report_monitoring') . '</td>';
    $html .= '<td>' . new lang_string('tbl_empty_data', 'report_monitoring') . '</td>';

    return $html;
}

/**
 * Creates the final HTML code for creating the reports
 *
 * @param string $header_user
 * @param array $header_access
 * @param array $header_course
 * @param string $body_participation
 *
 * @return string
 */
function report_monitoring_make_report_participation_code(
    string $header_user,
    array $header_access,
    array $header_course,
    string $body_participation
): string {
    $html = '<table>';
    $html .= '<thead>';
    $html .= '<tr>' . $header_user . $header_access['line1'] . $header_course['line1'] . '</tr>';
    $html .= '<tr>' . $header_access['line2'] . $header_course['line2'] . '</tr>';
    $html .= '<tr>' . $header_course['line3'] . '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    $html .= $body_participation;
    $html .= '</tbody>';
    $html .= '</table>';

    return $html;
}

/**
 * Create the code for the grade header
 *
 * @param array $grade_data
 *
 * @return string
 *
 * @throws coding_exception
 */
function report_monitoring_make_header_grade(array $grade_data, string $grade_options): string
{
    $html = '';

    $options = report_monitoring_get_array($grade_options);

    foreach ($grade_data as $grade) {
        $html .= '<td>' . $grade['name'] . '</td>';
    }

    $html .= '<td>' . get_string('final_grade', 'report_monitoring') . '</td>';

    if (isset($options['show_feedback'])) {
        $html .= '<td>' . get_string('feedback_text', 'report_monitoring') . '</td>';
    }
    return $html;
}

/**
 * Create the code with the contents of the notes body
 *
 * @param array $user_data
 * @param array $grade_structure
 * @param string $course_id
 *
 * @return string
 */
function report_monitoring_make_body_grade(
    array $user_data,
    array $grade_structure,
    string $course_id,
    array $grade_config
): string
{
    // User grade
    $user_ids = explode(',', $user_data['ids']);

    // grade_options
    $options = report_monitoring_get_array($grade_config['grade_options']);

    // Activity grade
    $grades = [];
    $i = 0;

    foreach ($grade_structure as $data) {
        $grades[$i] = $data;
        $grade_grades = grade_get_grades($course_id, $data['type'], $data['module'], $data['instance'], $user_ids);
        $grades[$i]['grades'] = $grade_grades->items[0]->grades;
        $i++;
    }

    // Course grade
    $course_grades = grade_get_course_grades($course_id, $user_ids);
    $total_grades = $course_grades->grades;

    // Assemble the code
    $html = $html_passed = $html_failed = $html_all = '';

    // First let's adjust the user fields
    $arr_fields = [];
    $fields1 = explode(',', $user_data['user_fields']);

    foreach ($fields1 as $f1) {
        $fields2 = explode('AS', $f1);

        if (isset($fields2[1])) {
            $arr_fields[] = str_replace('"', '', str_replace(' ', '', $fields2[1]));
        } else {
            $fields3 = explode('.', $fields2[0]);
            $arr_fields[] = $fields3[1];
        }
    }

    // Creating the HTML code
    foreach ($user_data['user_data'] as $v) {
        $html = '';
        $html .= '<tr>';

        // User data
        foreach ($arr_fields as $f) {
            $html .= '<td>' . $v[$f] . '</td>';
        }

        // Grade data
        foreach ($grades as $g) {
            if (isset($g['grades'][$v['id']])) {
                $html .= '<td>' . $g['grades'][$v['id']]->str_grade . '</td>';
            } else {
                $html .= '<td>-</td>';
            }
        }

        // course total
        if (isset($total_grades[$v['id']])) {
            $html .= '<td>' . $total_grades[$v['id']]->str_grade . '</td>';
        } else {
            $html .= '<td>-</td>';
        }

        // feedback
        // TODO: str_feedback para itens como letra
        if (isset($options['show_feedback'])) {
            if (isset($total_grades[$v['id']])) {

                if (intval($total_grades[$v['id']]->grade) >= intval($grade_config['grade_minimum_grade'])) {
                    $html .= '<td>' . $grade_config['grade_feedback_passed'] . '</td>';
                } else {
                    $html .= '<td>' . $grade_config['grade_feedback_failed'] . '</td>';
                }
            } else {
                $html .= '<td>-</td>';
            }
        }

        $html .= '</tr>';

        // Aprovados
        if (intval($total_grades[$v['id']]->grade) >= intval($grade_config['grade_minimum_grade'])) {
            $html_passed .= $html;
        }

        // Reprovados
        if (intval($total_grades[$v['id']]->grade) < intval($grade_config['grade_minimum_grade'])) {
            $html_failed .= $html;
        }

        // Todos os usuários
        $html_all .= $html;
    }

    // Returns the HTML according to the selected filter option
    if ( (isset($options['only_passed'])) && (isset($options['only_failed'])) ) {
        return $html_all;
    } elseif ( (isset($options['only_passed'])) && (!isset($options['only_failed'])) ) {
        return $html_passed;
    } elseif ( (!isset($options['only_passed'])) && (isset($options['only_failed'])) ) {
        return $html_failed;
    } else {
        return $html_all;
    }
}

/**
 * Creates the final HTML code for creating the reports
 *
 * @param string $header_user
 * @param string $header_grade
 * @param string $body_grades
 *
 * @return string
 */
function report_monitoring_make_report_grade_code(
    string $header_user,
    string $header_grade,
    string $body_grades
): string {

    $html = '<table>';
    $html .= '<thead>';
    $html .= '<tr>' . $header_user . $header_grade . '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    $html .= $body_grades;
    $html .= '</tbody>';
    $html .= '</table>';

    return $html;
}

/**
 * Create the table header with access data
 *
 * @return string
 */
function report_monitoring_make_header_dedication(): string
{
    return '<td>' . new lang_string('estimate_time', 'report_monitoring') . '</td>';
}

function report_monitoring_make_body_dedication(array $user_data, array $log_info)
{
    // 1. Assemble the code
    $html = '';

    // First let's adjust the user fields
    $arr_fields = [];
    $fields1 = explode(',', $user_data['user_fields']);

    foreach ($fields1 as $f1) {
        $fields2 = explode('AS', $f1);

        if (isset($fields2[1])) {
            $arr_fields[] = str_replace('"', '', str_replace(' ', '', $fields2[1]));
        } else {
            $fields3 = explode('.', $fields2[0]);
            $arr_fields[] = $fields3[1];
        }
    }

    // Creating the HTML code
    foreach ($user_data['user_data'] as $v) {
        $html .= '<tr>';

        // User data
        foreach ($arr_fields as $f) {
            $html .= '<td>' . $v[$f] . '</td>';
        }

        // Dedication data
        if (isset($log_info[$v['id']])) {
            $html .= '<td>' . $log_info[$v['id']] . '</td>';
        } else {
            $html .= '<td>' . get_string('no_data', 'report_monitoring') . '</td>';
        }

        $html .= '</tr>';
    }
    return $html;
}

/**
 * Creates the final HTML code for creating the reports
 *
 * @param string $header_user
 * @param string $header_grade
 * @param string $body_grades
 *
 * @return string
 */
function report_monitoring_make_report_dedication_code(
    string $header_user,
    string $header_dedication,
    string $body_dedication
): string {

    $html = '<table>';
    $html .= '<thead>';
    $html .= '<tr>' . $header_user . $header_dedication . '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    $html .= $body_dedication;
    $html .= '</tbody>';
    $html .= '</table>';

    return $html;
}
