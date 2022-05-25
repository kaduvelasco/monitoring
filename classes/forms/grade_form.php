<?php

/**
 * @package    report
 * @subpackage monitoring
 * @version    1.0.1
 * @copyright  2022 Kadu Velasco
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');

class report_monitoring_grade_form extends moodleform
{
    public function definition()
    {
        $mform = $this->_form;

        // config group header
        $mform->addElement('header', 'report_header', new lang_string('header_definitions', 'report_monitoring'));
        $mform->setExpanded('report_header');
        $mform->closeHeaderBefore('btn_generate_report');

        // select course
        $mform->addElement(
            'select',
            'course',
            new lang_string('select_course', 'report_monitoring'),
            $this->_customdata['course_list']
        );
        $mform->addHelpButton('course', 'select_course', 'report_monitoring');
        $mform->addRule('course', new lang_string('course_required', 'report_monitoring'), 'required');

        // report options
        $mform->addElement(
            'select',
            'report_options',
            new lang_string('report_options', 'report_monitoring'),
            $this->_customdata['report_options']
        );
        $mform->addHelpButton('report_options', 'report_options', 'report_monitoring');
        $mform->getElement('report_options')->setMultiple(true);
        $mform->getElement('report_options')->setSelected($this->_customdata['report_options_sel']);

        // user roles
        $mform->addElement(
            'select',
            'user_role',
            new lang_string('user_role', 'report_monitoring'),
            $this->_customdata['moodle_roles']
        );
        $mform->addHelpButton('user_role', 'user_role', 'report_monitoring');
        $mform->getElement('user_role')->setMultiple(true);
        $mform->getElement('user_role')->setSelected($this->_customdata['moodle_roles_sel']);
        $mform->addRule('user_role', get_string('role_required', 'report_monitoring'), 'required', null, 'client');

        // Text for approved users
        $mform->addElement(
            'text',
            'feedback_passed',
            new lang_string('feedback_passed', 'report_monitoring'),
            ['value' => $this->_customdata['feedback_passed']]
        );
        $mform->addHelpButton('feedback_passed', 'feedback_passed', 'report_monitoring');
        $mform->setType('feedback_passed', PARAM_TEXT);

        // Text for disapproved users
        $mform->addElement(
            'text',
            'feedback_failed',
            new lang_string('feedback_failed', 'report_monitoring'),
            ['value' => $this->_customdata['feedback_failed']]
        );
        $mform->addHelpButton('feedback_failed', 'feedback_failed', 'report_monitoring');
        $mform->setType('feedback_failed', PARAM_TEXT);

        // Minimum grade for approval
        $mform->addElement(
            'text',
            'minimum_grade',
            new lang_string('minimum_grade', 'report_monitoring'),
            ['value' => $this->_customdata['minimum_grade']]
        );
        $mform->addHelpButton('minimum_grade', 'minimum_grade', 'report_monitoring');
        $mform->setType('minimum_grade', PARAM_TEXT);

        // submit form
        $mform->addElement('submit', 'btn_generate_report', new lang_string('generate_report', 'report_monitoring'));
    }

    public function definition_after_data()
    {
        parent::definition_after_data();
    }
}
