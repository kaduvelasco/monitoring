<?php

/**
 * @package    report
 * @subpackage monitoring
 * @version    1.0.2
 * @copyright  2022 Kadu Velasco
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');

class report_monitoring_participation_form extends moodleform
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
        $mform->addRule('course', new lang_string('course_required', 'report_monitoring'), 'required', null, 'client');

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
        $mform->addRule('user_role', new lang_string('role_required', 'report_monitoring'), 'required', null, 'client');

        // submit form
        $mform->addElement('submit', 'btn_generate_report', new lang_string('generate_report', 'report_monitoring'));
    }

    public function definition_after_data()
    {
        parent::definition_after_data();
    }
}
