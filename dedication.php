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

global $CFG, $PAGE, $SITE, $OUTPUT;

require_once dirname(__FILE__) . '/../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->libdir . '/completionlib.php';
require_once dirname(__FILE__) . '/locallib.php';
require_once dirname(__FILE__) . '/classes/util/user.php';
require_once dirname(__FILE__) . '/classes/util/course.php';
require_once dirname(__FILE__) . '/classes/util/moodle.php';
require_once dirname(__FILE__) . '/classes/util/dedication.php';
require_once dirname(__FILE__) . '/classes/util/exportlib.php';
require_once dirname(__FILE__) . '/classes/util/renderlib.php';
require_once dirname(__FILE__) . '/classes/forms/dedication_form.php';

raise_memory_limit(MEMORY_HUGE);
set_time_limit(600);

try {
    // Informs that you need to be logged in and that it is an administration page
    require_login();
    admin_externalpage_setup('reportmonitoring', '', null, '', array('pagelayout' => 'report'));

    // Plugin urls | Context | Plugin settings
    $url = report_monitoring_get_urls();
    $context = context_system::instance();
    $config = report_monitoring_get_plugin_settings();

    // Data for the forms
    $course_list    = report_monitoring_get_all_courses();
    $report_options = report_monitoring_get_report_options('dedication');
    $moodle_roles   = report_monitoring_get_moodle_roles();

    $custom_data = [
        'course_list'    => $course_list,
        'report_options' => $report_options,
        'moodle_roles'   => $moodle_roles,
        'report_options_sel' => (isset($config['dedication_options']))
            ? $config['dedication_options']
            : [],
        'moodle_roles_sel' => (isset($config['dedication_roles']))
            ? $config['dedication_roles']
            : [],
    ];

    // Form definition
    $dform = new report_monitoring_dedication_form(null, $custom_data);

    // Check if the form has been submitted
    // Check if the form has been submitted
    if ($dform->is_cancelled()) {
        redirect($url['dedication']);
    } elseif ($form_data = $dform->get_data()) {
        // Update or configure report settings in the database
        report_monitoring_add_report_settings($config, 'dedication_options', $form_data->report_options);
        report_monitoring_add_report_settings($config, 'dedication_roles', $form_data->user_role);
        $config = report_monitoring_get_plugin_settings();

        // Data for the report. Here we will select the data:
        // From users
        // From the log (logs of user actions)
        $course_data = report_monitoring_get_course_data($form_data->course);
        $user_data = report_monitoring_get_user_data(
            $course_data['id'],
            $config['dedication_roles'],
            $config['user_fields'],
            $config['user_extrafields'],
            $config['dedication_options'],
            $config['user_filter']);
        $log_info = report_monitoring_get_log_info($course_data['id'], $user_data['ids']);

        // Assemble the report (html code)
        $header_user = report_monitoring_make_header_user($user_data['user_fields'], $config['user_extrafields'], '1');
        $header_dedication = report_monitoring_make_header_dedication();

        $body_dedication = report_monitoring_make_body_dedication($user_data, $log_info);

        $final_html = report_monitoring_make_report_dedication_code($header_user, $header_dedication, $body_dedication);

        // Create the file for download
        $filename = new lang_string('dedication_file', 'report_monitoring');
        $filename .= report_monitoring_clear_string(strtolower($course_data['shortname']));
        $filename .= '_' . date('dmYHis');

        report_monitoring_get_report($final_html, $filename);
    }

    // Here we define the page content, to be shown in the mustache template, it must be stored in the $content object
    $content = new stdClass();
    $content->url = $url['dedication']->out(false);
    $content->title = new lang_string('pluginname', 'report_monitoring');
    $content->css = $CFG->wwwroot . '/report/monitoring/style.css';

    // Add the form code to a variable to be rendered by mustache
    $content->report_form = $dform->render();

    // Defines the parameters needed for rendering the page
    $PAGE->set_context($context);
    $PAGE->set_url($url['dedication']);
    $PAGE->set_pagelayout('report');
    $PAGE->set_title($SITE->shortname . ': ' . new lang_string('pluginname', 'report_monitoring'));
    $PAGE->set_heading(new lang_string('pluginname', 'report_monitoring'));
    $PAGE->set_cacheable(false);
    $PAGE->navbar->add(get_string('dedication', 'report_monitoring'), $url['dedication']);

    // Render the page
    $tabs = report_monitoring_get_page_tabs($url);

    echo $OUTPUT->header();
    echo $OUTPUT->tabtree($tabs, '4');
    echo $OUTPUT->render_from_template('report_monitoring/dedication', $content);
    echo $OUTPUT->footer();
} catch (dml_exception | coding_exception | moodle_exception | \PhpOffice\PhpSpreadsheet\Reader\Exception | \PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
    echo $e->getMessage();
    return false;
}