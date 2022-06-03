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

defined('MOODLE_INTERNAL') || die;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Generate the xsl file
 *
 * @param string $report_content
 * @param $filename
 *
 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
 */
function report_monitoring_get_report(string $report_content, $filename)
{
    $filename .= '.xls';

    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
    $spreadsheet = $reader->loadFromString($report_content);
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment;filename=\"{$filename}\"");
    header("Cache-Control: max-age=0");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: cache, must-revalidate");
    header("Pragma: public");

    $writer->save("php://output");
}
