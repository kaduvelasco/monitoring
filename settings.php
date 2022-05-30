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
 * Report monitoring plugin installation page
 *
 * @package    report
 * @subpackage monitoring
 * @version    1.0.2
 * @copyright  2022 Kadu Velasco
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $ADMIN;

$ADMIN->add(
    'reports',
    new admin_externalpage(
        'reportmonitoring',
        new lang_string('pluginname', 'report_monitoring'),
        new moodle_url('/report/monitoring/index.php'),
        "report/monitoring:view"
    )
);

// no report settings
$settings = null;
