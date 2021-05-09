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
 * Atto text editor integration version file.
 *
 * @package    atto_cloze
 * @copyright  2016 onward Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/editor/atto/plugins/cloze/locallib.php');

/**
 * Get the list of strings for this plugin.
 */
function atto_cloze_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js(array( 'pluginname' ), 'atto_cloze' );
    $PAGE->requires->strings_for_js(array( 'common:insert' ), 'editor_tinymce' );
    $PAGE->requires->strings_for_js(array( 'answer', 'chooseqtypetoadd', 'defaultmark', 'feedback', 'incorrect' ), 'question' );
    $PAGE->requires->strings_for_js(array( 'multichoice', 'numerical', 'shortanswer' ), 'mod_quiz' );
    $PAGE->requires->strings_for_js(array( 'addmoreanswerblanks', 'tolerance' ), 'qtype_calculated' );
    $PAGE->requires->strings_for_js(array( 'add', 'cancel', 'delete',
            'duplicate', 'down', 'grade', 'previous', 'up' ), 'core' );
}

/**
 * Set params for this plugin.
 *
 * @return array
 * @throws dml_exception
 * @throws coding_exception
 */
function atto_cloze_params_for_js(): array {
    return atto_cloze_get_enabled_question_types();
}
