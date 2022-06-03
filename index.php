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
require_once dirname(__FILE__) . '/locallib.php';
require_once dirname(__FILE__) . '/classes/util/user.php';
require_once dirname(__FILE__) . '/classes/util/moodle.php';
require_once dirname(__FILE__) . '/classes/forms/config_form.php';

try {
    // Informs that you need to be logged in and that it is an administration page
    require_login();
    admin_externalpage_setup('reportmonitoring', '', null, '', array('pagelayout' => 'report'));

    // Plugin urls | Context | Plugin settings
    $url = report_monitoring_get_urls();
    $context = context_system::instance();
    $config = report_monitoring_get_plugin_settings();

    // Data needed to create the form
    $user_fields = report_monitoring_get_user_fields();
    $user_extrafields = report_monitoring_get_user_extra_fields();
    $modules = report_monitoring_get_moodle_modules();

    $custom_data =  [
        'user_fields'      => $user_fields,
        'selected_fields'  => isset($config['user_fields']) ? explode(',', $config['user_fields']) : [],
        'user_efields'     => $user_extrafields,
        'selected_efields' => isset($config['user_extrafields']) ? explode(',', $config['user_extrafields']) : [],
        'modules'          => $modules,
        'selected_modules' => isset($config['ignored_modules']) ? explode(',', $config['ignored_modules']) : [],
        'user_filter' => isset($config['user_filter']) ? $config['user_filter'] : ''
    ];

    // Form definition
    $cform = new report_monitoring_config_form(null, $custom_data);

    // Checks if the form has been submitted
    if ($cform->is_cancelled()) {
        redirect($url['index']);
    } elseif ($form_data = $cform->get_data()) {
        // Update plugin data
        foreach ($form_data as $k => $v) {
            if (!report_monitoring_update_plugin_settings($k, $v)) {
                new coding_exception(new lang_string('error_update_config', 'report_monitoring'));
            }
        }

        // Reset the form
        $config = report_monitoring_get_plugin_settings();

        $custom_data =  [
            'user_fields'      => $user_fields,
            'selected_fields'  => explode(',', $config['user_fields']),
            'user_efields'     => $user_extrafields,
            'selected_efields' => explode(',', $config['user_extrafields']),
            'modules'          => $modules,
            'selected_modules' => explode(',', $config['ignored_modules']),
            'user_filter'      => $config['user_filter']
        ];

        $cform = new report_monitoring_config_form(null, $custom_data);

    }

    // Here we define the page content, to be shown in the mustache template, it must be stored in the $content object
    // To use an image: new moodle_url('/pix/y/loading.gif');
    $content = new stdClass();
    $content->url = $url['index']->out(false);
    $content->title = new lang_string('pluginname', 'report_monitoring');
    $content->css = $CFG->wwwroot . '/report/monitoring/style.css';

    // Add the form code to a variable to be rendered by mustache
    $content->config_form = $cform->render();

    // Defines the parameters needed for rendering the page
    $PAGE->set_context($context);
    $PAGE->set_url($url['index']);
    $PAGE->set_pagelayout('report');
    $PAGE->set_title($SITE->shortname . ': ' . new lang_string('pluginname', 'report_monitoring'));
    $PAGE->set_heading(new lang_string('pluginname', 'report_monitoring'));
    $PAGE->set_cacheable(false);
    //$PAGE->navbar->ignore_active(); // breadcrumb hides all navigation
    //$PAGE->navbar->add(get_string('pluginname', 'report_monitoring'), $url); // breadcrumb add

    // Render the page
    $tabs = report_monitoring_get_page_tabs($url);

    echo $OUTPUT->header();
    echo $OUTPUT->tabtree($tabs, '1');
    //echo $OUTPUT->heading(get_string('userfields', 'report_monitoring')); // Title shown on page, block 2
    echo $OUTPUT->render_from_template('report_monitoring/index', $content);
    echo $OUTPUT->footer();
}  catch (dml_exception | coding_exception | moodle_exception $e) {
    echo $e->getMessage();
    return false;
}