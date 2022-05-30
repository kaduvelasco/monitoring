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

try {
    // Informs that you need to be logged in and that it is an administration page
    require_login();
    admin_externalpage_setup('reportmonitoring', '', null, '', array('pagelayout' => 'report'));

    // Plugin urls | Context | Plugin settings
    $url = report_monitoring_get_urls();
    $context = context_system::instance();
    $config = report_monitoring_get_plugin_settings();

    // Here we define the page content, to be shown in the mustache template, it must be stored in the $content object
    $content = new stdClass();
    $content->url = $url['help']->out(false);
    $content->title = new lang_string('pluginname', 'report_monitoring');
    $content->css = $CFG->wwwroot . '/report/monitoring/style.css';

    // Defines the parameters needed for rendering the page
    $PAGE->set_context($context);
    $PAGE->set_url($url['help']);
    $PAGE->set_pagelayout('report');
    $PAGE->set_title($SITE->shortname . ': ' . new lang_string('pluginname', 'report_monitoring'));
    $PAGE->set_heading(new lang_string('pluginname', 'report_monitoring'));
    $PAGE->set_cacheable(false);
    $PAGE->navbar->add(get_string('help', 'report_monitoring'), $url['help']);

    // Render the page
    $tabs = report_monitoring_get_page_tabs($url);

    echo $OUTPUT->header();
    echo $OUTPUT->tabtree($tabs, '5');
    echo $OUTPUT->render_from_template('report_monitoring/help_' . $CFG->lang, $content);
    echo $OUTPUT->footer();

} catch (dml_exception | coding_exception | moodle_exception $e) {
    echo $e->getMessage();
    return false;
}
