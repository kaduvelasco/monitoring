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
 * Returns the URLs used in the plugin
 *
 * @return moodle_url[]
 */
function report_monitoring_get_urls(): array
{
    return [
        'index' => new moodle_url('/report/monitoring/index.php'),
        'participation' => new moodle_url('/report/monitoring/participation.php'),
        'grade' => new moodle_url('/report/monitoring/grade.php'),
        'dedication' => new moodle_url('/report/monitoring/dedication.php'),
        'help' => new moodle_url('/report/monitoring/help.php')
    ];
}

/**
 * Returns page tab settings
 *
 * @param array $url
 *
 * @return array
 *
 * @throws moodle_exception
 */
function report_monitoring_get_page_tabs(array $url): array
{
    $tabs = [];

    $tabs[] = new tabobject(
        1,
        new moodle_url($url['index']),
        new lang_string('settings', 'report_monitoring')
    );

    $tabs[] = new tabobject(
        2,
        new moodle_url($url['participation']),
        new lang_string('participation', 'report_monitoring')
    );

    $tabs[] = new tabobject(
        3,
        new moodle_url($url['grade']),
        new lang_string('grade', 'report_monitoring')
    );

    $tabs[] = new tabobject(
        4,
        new moodle_url($url['dedication']),
        new lang_string('dedication', 'report_monitoring')
    );

    $tabs[] = new tabobject(
        5,
        new moodle_url($url['help']),
        new lang_string('help', 'report_monitoring')
    );
    return $tabs;
}

/**
 * Get the plugin settings
 *
 * @return array
 *
 * @throws dml_exception
 */
function report_monitoring_get_plugin_settings(): array
{
    global $DB;

    $arr_return = [];

    $result = $DB->get_records('config_plugins', ['plugin' => 'report_monitoring']);

    foreach ($result as $k) {
        $arr_return[$k->name] = $k->value;
    }
    return $arr_return;
}

/**
 * Analyzes if the informed configuration already exists and inserts or updates depending on the case
 *
 * @param array $config
 * @param string $field_name
 * @param $value
 *
 * @return void
 *
 * @throws dml_exception
 */
function report_monitoring_add_report_settings(array $config, string $field_name, $value)
{
    $formatted_value = (is_array($value)) ? implode(',', $value) : $value;

    if (array_key_exists($field_name, $config)) {
        report_monitoring_update_plugin_settings($field_name, $formatted_value);
    } else {
        report_monitoring_set_plugin_settings($field_name, $formatted_value);
    }
}

/**
 * Update the plugin settings
 *
 * @param string $field
 * @param $value
 *
 * @return bool
 *
 * @throws dml_exception
 */
function report_monitoring_update_plugin_settings(string $field, $value): bool
{
    global $DB;

    // Define the configuration options available in the plugin
    $arr_configs = [
        'user_fields', 'user_extrafields', 'ignored_modules', 'user_filter',
        'participation_options', 'participation_roles',
        'grade_options', 'grade_roles',
        'dedication_options', 'dedication_roles',
    ];

    if (in_array($field, $arr_configs)) {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        // Get id
        $data = $DB->get_record('config_plugins', ['plugin' => 'report_monitoring', 'name' => $field]);

        // Prevent error if configuration does not exist
        if (!$data) {
            return report_monitoring_set_plugin_settings($field, $value);
        } else {

            $data_update = new stdClass();
            $data_update->id = $data->id;
            $data_update->value = $value;

            if ($DB->update_record('config_plugins', $data_update)) {
                return true;
            }
        }
        return false;
    }
    return true;
}

/**
 * Add a configuration item
 *
 * @param string $field
 * @param $value
 *
 * @return bool
 *
 * @throws dml_exception
 */
function report_monitoring_set_plugin_settings(string $field, $value): bool
{
    global $DB;

    if (is_array($value)) {
        $value = implode(',', $value);
    }

    $data = new stdClass();
    $data->plugin = 'report_monitoring';
    $data->name = $field;
    $data->value = $value;

    if ($DB->insert_record('config_plugins', $data)) {
        return true;
    }
    return false;
}

/**
 * Select from the bank the courses available in Moodle
 *
 * @param bool $add_select
 *
 * @return array
 *
 * @throws moodle_exception
 */
function report_monitoring_get_all_courses(bool $add_select = true): array
{
    $data = get_courses();
    $arr_return = [];

    if ($add_select) {
        $arr_return[''] = get_string('select_course', 'report_monitoring');
    }

    foreach ($data as $d) {
        if ('site' !== $d->format) {
            $category = core_course_category::get($d->category);
            $arr_return[$d->id] = '[ ' . $category->name . ' ] ' . $d->fullname;
        }
    }
    return $arr_return;
}

/**
 * Defines available options for the report
 *
 * @param string $report
 *
 * @return array
 */
function report_monitoring_get_report_options(string $report): array
{
    $arr_config = [
        'hide_deleted' => new lang_string('hide_deleted', 'report_monitoring'),
        'hide_suspended' => new lang_string('hide_suspended', 'report_monitoring'),
        'hide_canceled_enrol' =>  new lang_string('hide_canceled_enrol', 'report_monitoring'),
    ];

    switch ($report) {
        case 'participation':
            $arr_config['hide_header'] = new lang_string('hide_header', 'report_monitoring');
            $arr_config['hide_section'] = new lang_string('hide_section', 'report_monitoring');
            $arr_config['show_access'] = new lang_string('show_access', 'report_monitoring');
            break;
        case 'grade':
            $arr_config['show_feedback'] = new lang_string('show_feedback', 'report_monitoring');
            $arr_config['only_passed'] = new lang_string('only_passed', 'report_monitoring');
            $arr_config['only_failed'] = new lang_string('only_failed', 'report_monitoring');
            break;
        case 'dedication':
            $arr_config['test'] = '';
            break;
    }
    return $arr_config;
}

/**
 * Select from the bank the profiles available in Moodle
 *
 * @return array
 *
 * @throws dml_exception
 */
function report_monitoring_get_moodle_roles(): array
{
    $systemcontext = context_system::instance();
    $arr_fields = array();

    $roles = role_fix_names(get_all_roles(), $systemcontext, ROLENAME_ORIGINAL);

    foreach ($roles as $r) {
        $arr_fields[$r->id] = $r->localname;
    }
    return $arr_fields;
}

/**
 * Mount SQL for user filter
 *
 * @param $filter
 *
 * @return array
 */
function report_monitoring_get_formatted_filter($filter): array
{
    $query['extra'] = '';
    $query['user'] = '';

    if ('' !== $filter) {
        $filter_val = explode(';', $filter);

        foreach ($filter_val as $v) {
            $value = explode('|', $v);
            $extra_fields = explode('.', $value[1]);
            $type = (isset($extra_fields[1])) ? 'extra' : 'user';
            $operator = strtoupper($value[0]);

            switch ($value[2]) {
                case '=':
                    $query[$type] .= ' ' . $operator . ' ' . $value[1] . ' = "' . $value[3] . '"';
                    break;
                case '<>':
                    $query[$type] .= ' ' . $operator . ' ' . $value[1] . ' != "' . $value[3] . '"';
                    break;
                case 'like':
                    $query[$type] .= ' ' . $operator . ' ' . $value[1];
                    $query[$type] .= ' LIKE "%' . str_replace(' ', '%', $value[3]) . '%"';
                    break;
                case 'notlike':
                    $value_query = str_replace(' ', '%', $value[3]);
                    $query[$type] .= ' ' . $operator . ' ' . $value[1] . ' NOT LIKE "%' . $value_query . '%"';
                    break;
                case 'in':
                    $fields = explode(',', $value[3]);
                    $fields_list = '';

                    foreach ($fields as $f) {
                        $fields_list .= '"' . $f . '",';
                    }

                    $query[$type] .= ' ' . $operator . ' ' . $value[1] . ' IN (' . substr($fields_list, 0, -1) . ')';
                    break;
                case 'notin':
                    $fields = explode(',', $value[3]);
                    $fields_list = '';

                    foreach ($fields as $f) {
                        $fields_list .= '"' . $f . '",';
                    }

                    $query[$type] .= ' ' . $operator . ' ' . $value[1] . ' NOT IN (' . substr($fields_list, 0, -1) . ')';
                    break;
            }
        }
    }
    return $query;
}

/**
 * Adjust user fields
 *
 * @param string $fields
 *
 * @return array
 */
function report_monitoring_format_user_fields(string $fields): array
{
    $arr_fields = [];
    $i = 0;
    $fields1 = explode(',', $fields);

    foreach ($fields1 as $f1) {
        $fields2 = explode('AS', $f1);

        if (isset($fields2[1])) {
            $arr_fields[$i] = str_replace('"', '', str_replace(' ', '', $fields2[1]));
        } else {
            $fields3 = explode('.', $fields2[0]);
            $arr_fields[$i] = $fields3[1];
        }
        $i++;
    }
    return $arr_fields;
}

/**
 * Format the informed date to the default of the logged-in user.
 *
 * @param string $date
 *
 * @return lang_string|string
 *
 * @throws coding_exception
 */
function report_monitoring_format_date(string $date)
{
    $return_value = get_string('default_no_registry', 'report_monitoring');

    if ('0' !== $date) {
        $datetime = new DateTime();
        $datetime->setTimestamp(intval($date));

        $return_value = userdate($datetime->getTimestamp(), get_string('strftimedatetimeshort'));
    }
    return $return_value;
}

/**
 * Clean up a string by removing accents and special characters
 *
 * @param string $text
 *
 * @return string
 */
function report_monitoring_clear_string(string $text): string
{
    $text = str_replace(" ", "_", $text);

    $characters = array(
        'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', '' => 'Z', '' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A',
        'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I',
        'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ń' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
        'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e',
        'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n',
        'ń' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u',
        'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f', 'ă' => 'a', 'ș' => 's', 'ț' => 't',
        'Ă' => 'A', 'Ș' => 'S', 'Ț' => 'T',
    );
    return strtolower(preg_replace("/[^a-zA-Z0-9]/", "_", strtr($text, $characters)));
}

/**
 * Checks if the course format has any parent-child settings
 *
 * @param string $course_id
 * @param string $course_format
 *
 * @return bool
 *
 * @throws dml_exception
 */
function report_monitoring_use_parent(string $course_id, string $course_format):bool
{
    global $CFG, $DB;

    $sql = 'SELECT count(*) as total FROM ' . $CFG->prefix . 'course_format_options';
    $sql .= ' WHERE courseid = ? AND format = ? AND name = ?';

    $data = $DB->get_record_sql($sql, [$course_id, $course_format, 'parent']);

    return (intval($data->total) > 0);
}

/**
 * Transforms a comma-separated string into an array
 *
 * @param string $grade_options
 *
 * @return array
 */
function report_monitoring_get_array(string $string): array
{
    $options_explode = explode(',', $string);

    foreach ($options_explode as $e) {
        $options[$e] = $e;
    }

    return $options;
}
