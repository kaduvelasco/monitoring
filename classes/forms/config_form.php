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

class report_monitoring_config_form extends moodleform
{
    public function definition()
    {
        $mform = $this->_form;

        // user fields
        $mform->addElement(
            'select',
            'user_fields',
            new lang_string('user_fields', 'report_monitoring'),
            $this->_customdata['user_fields']
        );
        $mform->addHelpButton('user_fields', 'user_fields', 'report_monitoring');
        $mform->getElement('user_fields')->setMultiple(true);
        $mform->getElement('user_fields')->setSelected($this->_customdata['selected_fields']);

        // user extra fields
        $user_extrafields = ['' => ''];
        $selected_extrafields = [''];

        if (isset($this->_customdata['user_efields'])) {
            $user_extrafields = $this->_customdata['user_efields'];
            $selected_extrafields = $this->_customdata['selected_efields'];
        }

        $mform->addElement(
            'select',
            'user_extrafields',
            new lang_string('user_extrafields', 'report_monitoring'),
            $user_extrafields
        );
        $mform->addHelpButton('user_extrafields', 'user_extrafields', 'report_monitoring');
        $mform->getElement('user_extrafields')->setMultiple(true);
        $mform->getElement('user_extrafields')->setSelected($selected_extrafields);

        // ignored modules
        $mform->addElement(
            'select',
            'ignored_modules',
            new lang_string('ignored_modules', 'report_monitoring'),
            $this->_customdata['modules']
        );
        $mform->addHelpButton('ignored_modules', 'ignored_modules', 'report_monitoring');
        $mform->getElement('ignored_modules')->setMultiple(true);
        $mform->getElement('ignored_modules')->setSelected($this->_customdata['selected_modules']);

        // filtros extras para usuário
        $mform->addElement(
            'textarea',
            'user_filter',
            new lang_string('user_filter', 'report_monitoring'),
            'wrap="virtual" rows="4" cols="50"'
        );
        $mform->addHelpButton('user_filter', 'user_filter', 'report_monitoring');
        $mform->setDefault('user_filter', $this->_customdata['user_filter']);

        // submit button
        $mform->addElement('submit', 'btn_save_config', new lang_string('save', 'report_monitoring'));
    }

    public function definition_after_data()
    {
        parent::definition_after_data();
    }
}
