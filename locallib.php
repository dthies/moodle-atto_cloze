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

/**
 * Get the question types for this plugin.
 *
 * @return array
 * @throws coding_exception
 */
function atto_cloze_get_question_types(): array {
    global $CFG;

    $singleno = array('option' => get_string('answersingleno', 'qtype_multichoice'));
    $singleyes = array('option' => get_string('answersingleyes', 'qtype_multichoice'));
    $selectinline = array('option' => get_string('layoutselectinline', 'qtype_multianswer'));
    $horizontal = array('option' => get_string('layouthorizontal', 'qtype_multianswer'));
    $vertical = array('option' => get_string('layoutvertical', 'qtype_multianswer'));
    $qtypes = array(
        array('type' => 'MULTICHOICE', 'name' => get_string('multichoice', 'mod_quiz'),
            'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
            'options' => array($selectinline, $singleyes)
        ),
        array('type' => 'MULTICHOICE_H', 'name' => get_string('multichoice', 'mod_quiz'),
            'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
            'options' => array($horizontal, $singleyes)
        ),
        array('type' => 'MULTICHOICE_V', 'name' => get_string('multichoice', 'mod_quiz'),
            'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
            'options' => array($vertical, $singleyes)
        ),
    );

    // Check whether shuffled multichoice is supported yet.
    if ($CFG->version >= 2015111604) {
        $shuffle = array('option' => get_string('shufflewithin', 'mod_quiz'));
        $qtypes = array_merge($qtypes, array(

            array('type' => 'MULTICHOICE_S', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($selectinline, $shuffle, $singleyes)
            ),
            array('type' => 'MULTICHOICE_HS', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($horizontal, $shuffle, $singleyes)
            ),
            array('type' => 'MULTICHOICE_VS', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($vertical, $shuffle, $singleyes)
            ),
        ));
    }

    // Check whether shuffled multichoice is supported yet.
    if ($CFG->version >= 2016080400) {
        $multihorizontal = array('option' => get_string('layoutmultiple_horizontal', 'qtype_multianswer'));
        $multivertical = array('option' => get_string('layoutmultiple_vertical', 'qtype_multianswer'));
        $qtypes = array_merge($qtypes, array(
            array('type' => 'MULTIRESPONSE', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($multivertical, $singleno)
            ),
            array('type' => 'MULTIRESPONSE_H', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($multihorizontal, $singleno)
            ),
            array('type' => 'MULTIRESPONSE_S', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($multivertical, $shuffle, $singleno)
            ),
            array('type' => 'MULTIRESPONSE_HS', 'name' => get_string('multichoice', 'mod_quiz'),
                'summary' => get_string('pluginnamesummary', 'qtype_multichoice'),
                'options' => array($multihorizontal, $shuffle, $singleno)
            ),
        ));
    }

    $qtypes = array_merge($qtypes, array(
        array('type' => 'NUMERICAL', 'name' => get_string('numerical', 'mod_quiz'),
        'summary' => get_string('pluginnamesummary', 'qtype_numerical')),
        array('type' => 'SHORTANSWER', 'name' => get_string('shortanswer', 'mod_quiz'),
        'summary' => get_string('pluginnamesummary', 'qtype_shortanswer'),
        'options' => array('option' => get_string('caseno', 'mod_quiz'))),
        array('type' => 'SHORTANSWER_C', 'name' => get_string('shortanswer', 'mod_quiz'),
        'summary' => get_string('pluginnamesummary', 'qtype_shortanswer'),
        'options' => array('option' => get_string('caseyes', 'mod_quiz'))),
    ));

    return array('questiontypes' => $qtypes);
}

/**
 * Get the enabled question types for this plugin.
 *
 * @return array
 * @throws dml_exception
 * @throws coding_exception
 */
function atto_cloze_get_enabled_question_types(): array {
    $enabledqtypes = [];

    $enabledqtypeskeys = explode(',', get_config('atto_cloze', 'enabledquestiontypes'));

    $qtypes = atto_cloze_get_question_types();
    foreach ($enabledqtypeskeys as $enabledqtypeskey) {
        $enabledqtypes['questiontypes'][] = $qtypes['questiontypes'][$enabledqtypeskey];
    }

    return $enabledqtypes;
}
