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
 * lang strings
 *
 * @package    report
 * @subpackage monitoring
 * @version    1.0.2
 * @copyright  2022 Kadu Velasco
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Moodle Monitoring';

/**
 * Tabs available in the interface
 */
$string['settings'] = 'Settings';
$string['participation'] = 'Participation';
$string['grade'] = 'Grade';
$string['dedication'] = 'Dedication';
$string['help'] = 'Help';

/**
 * Base name for the generated report files
 */
$string['participation_file'] = 'participation_';
$string['grade_file'] = 'grade_';
$string['dedication_file'] = 'dedication_';

/**
 * Name of form fields
 */
$string['user_fields'] = 'User table fields';
$string['user_extrafields'] = 'User Profile Fields';
$string['ignored_modules'] = 'Ignored modules in report';
$string['user_filter'] = 'Filter for users';
$string['select_course'] = 'Select the course';
$string['report_options'] = 'Report options';
$string['user_role'] = 'User Profile';
$string['feedback_passed'] = 'Feedback for approved users';
$string['feedback_failed'] = 'Feedback for failed users';
$string['minimum_grade'] = 'Minimum pass grade';

/**
 * Help texts
 */
$string['user_fields_help'] = 'Select the user fields that will be shown in reports. Use the Ctrl key to select more than one or to deselect it.';
$string['user_extrafields_help'] = 'Select the user profile fields that will be shown in reports. Use the Ctrl key to select more than one or to deselect it.';
$string['ignored_modules_help'] = 'Select modules that will not be shown in reports. Use the Ctrl key to select more than one or to deselect it.';
$string['user_filter_help'] = 'Define rules to filter the users present in the report. See help for more information.';
$string['select_course_help'] = 'Select the course that will be used to generate the report.';
$string['report_options_help'] = 'Select the options that will be activated when generating the report. Use the Ctrl key to select more than one or to deselect it.';
$string['user_role_help'] = 'Select the user profiles that will be shown in reports. Use the Ctrl key to select more than one or to deselect it.';
$string['feedback_passed_help'] = 'Enter the text that will be shown to users who have reached the minimum grade in the total course.';
$string['feedback_failed_help'] = 'Enter the text that will be shown to users who have reached the minimum grade in the total course.';
$string['minimum_grade_help'] = 'Enter the minimum grade for the user to be considered approved (in relation to the course total).';

/**
 * Options available in form fields
 */
$string['hide_deleted'] = 'Do not show users defined as deleted in the database.';
$string['hide_suspended'] = 'Do not show suspended users in Moodle.';
$string['hide_canceled_enrol'] = 'Only show users with active enrollment in the course.';
$string['hide_header'] = 'Do not show course header (session 0).';
$string['hide_section'] = 'Do not show hidden sessions and modules.';
$string['show_access'] = 'Show course access data.';
$string['only_passed'] = 'Display only users who have reached the minimum grade in the total course.';
$string['only_failed'] = 'Display only users who have not reached the minimum grade in the total course.';
$string['show_feedback'] = 'Show feedback (pass/fail).';

/**
 * Miscellaneous texts
 */
$string['error_update_config'] = 'There was an error saving report options. Try again more trade';
$string['save'] = 'Save';
$string['header_definitions'] = 'Definitions';
$string['generate_report'] = 'Generate report';
$string['default_no_registry'] = '';
$string['course_access_data'] = 'Course access data';
$string['first'] = 'First';
$string['last'] = 'Last';
$string['amount'] = 'Amount';
$string['not_completed'] = 'No';
$string['completed'] = 'Yes';
$string['completed_passed'] = 'Yes (graded)';
$string['completed_failed'] = 'Yes (no grade)';
$string['not_viewed'] = 'No';
$string['viewed'] = 'Yes';
$string['null_viewed'] = 'Unmonitored view';
$string['tbl_empty_complete'] = 'No';
$string['tbl_empty_visualized'] = 'No';
$string['tbl_empty_data'] = '';
$string['hidden'] = 'Hidden';
$string['concluded'] = 'Completed?';
$string['visualized'] = 'Visualized?';
$string['date'] = 'Date';
$string['final_grade'] = 'COURSE TOTAL';
$string['no_data'] = 'No data to calculate estimate';
$string['estimate_time'] = 'Time (estimate)';
$string['feedback_text'] = 'FEEDBACK';

/**
 * Messages
 */
$string['course_required'] = 'You must select a course.';
$string['role_required'] = 'You must select at least one user profile.';
$string['no_extra_fields'] = 'No extra fields found on the platform';

/**
 * User table fields
 */
$string['id'] = 'ID';
$string['auth'] = 'Authentication method';
$string['confirmed'] = 'Registration confirmed';
$string['policyagreed'] = 'Policy agreed';
$string['deleted'] = 'Account deleted';
$string['suspended'] = 'There is suspended';
$string['username'] = 'Username';
$string['idnumber'] = 'ID number';
$string['firstname'] = 'First Name';
$string['lastname'] = 'Last Name';
$string['email'] = 'Email';
$string['phone1'] = 'Phone 1';
$string['phone2'] = 'Phone 2';
$string['institution'] = 'Institution';
$string['department'] = 'Department';
$string['address'] = 'Address';
$string['city'] = 'City';
$string['country'] = 'Country';
$string['firstlogin'] = 'First login';
$string['lang'] = 'Language';
$string['lastlogin'] = 'Last login';
$string['currentlogin'] = 'Current login';
$string['description'] = 'Description';
$string['timecreated'] = 'Date created';
$string['timemodified'] = 'Last modified';
$string['firstaccess'] = 'First access';
$string['lastaccess'] = 'Last access';
$string['lastip'] = 'Last registered IP';
$string['icq'] = 'ICQ';
$string['skype'] = 'Skype';
$string['yahoo'] = 'Yahoo';
$string['aim'] = 'AIM';
$string['msn'] = 'MSN';
$string['url'] = 'URL';
