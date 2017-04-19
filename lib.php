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
 * @copyright  2016 onward Daniel Thies <dthies@ccal.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get the list of strings for this plugin.
 * @param string $elementid
 */
function atto_cloze_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js(array( 'pluginname', 'add', 'addmoreanswerblanks', 'answer', 'cancel',
            'chooseqtypetoadd', 'insert', 'defaultmark', 'delete', 'down', 'duplicate', 'feedback',
            'grade', 'multichoice', 'none', 'numerical', 'shortanswer', 'tolerance', 'up',), 'atto_cloze' );
}

/**
 * Set params for this plugin.
 *
 * @return array
 */
function atto_cloze_params_for_js() {
    global $CFG;

    $singleno = array('option' => get_string('answersingleno', 'atto_cloze'));
    $singleyes = array('option' => get_string('answersingleyes', 'atto_cloze'));
    $selectinline = array('option' => get_string('layoutselectinline', 'atto_cloze'));
    $horizontal = array('option' => get_string('layouthorizontal', 'atto_cloze'));
    $vertical = array('option' => get_string('layoutvertical', 'atto_cloze'));
    $qtypes = array(
        array('type' => 'MULTICHOICE', 'name' => get_string('multichoice', 'atto_cloze'),
            'summary' => get_string('multichoicesummary', 'atto_cloze'),
            'options' => array($selectinline, $singleyes)
        ),
        array('type' => 'MULTICHOICE_H', 'name' => get_string('multichoice', 'atto_cloze'),
            'summary' => get_string('multichoicesummary', 'atto_cloze'),
            'options' => array($horizontal, $singleyes)
        ),
        array('type' => 'MULTICHOICE_V', 'name' => get_string('multichoice', 'atto_cloze'),
            'summary' => get_string('multichoicesummary', 'atto_cloze'),
            'options' => array($vertical, $singleyes)
        ),
    );
    // Check whether shuffled multichoice is supported yet.
    if ($CFG->version >= 2015111604) {
        $shuffle = array('option' => get_string('shufflewithin', 'atto_cloze'));
        $qtypes = array_merge($qtypes, array(

            array('type' => 'MULTICHOICE_S', 'name' => get_string('multichoice', 'atto_cloze'),
                'summary' => get_string('multichoicesummary', 'atto_cloze'),
                'options' => array($selectinline, $shuffle, $singleyes)
            ),
            array('type' => 'MULTICHOICE_HS', 'name' => get_string('multichoice', 'atto_cloze'),
                'summary' => get_string('multichoicesummary', 'atto_cloze'),
                'options' => array($horizontal, $shuffle, $singleyes)
            ),
            array('type' => 'MULTICHOICE_VS', 'name' => get_string('multichoice', 'atto_cloze'),
                'summary' => get_string('multichoicesummary', 'atto_cloze'),
                'options' => array($vertical, $shuffle, $singleyes)
            ),
        ));
    }

    // Check whether shuffled multichoice is supported yet.
    if ($CFG->version >= 2016080400) {
        $multihorizontal = array('option' => get_string('layoutmultiple_horizontal', 'atto_cloze'));
        $multivertical = array('option' => get_string('layoutmultiple_vertical', 'atto_cloze'));
        $qtypes = array_merge($qtypes, array(
            array('type' => 'MULTIRESPONSE', 'name' => get_string('multichoice', 'atto_cloze'),
                'summary' => get_string('multichoicesummary', 'atto_cloze'),
                'options' => array($multivertical, $singleno)
            ),
            array('type' => 'MULTIRESPONSE_H', 'name' => get_string('multichoice', 'atto_cloze'),
                'summary' => get_string('multichoicesummary', 'atto_cloze'),
                'options' => array($multihorizontal, $singleno)
            ),
            array('type' => 'MULTIRESPONSE_S', 'name' => get_string('multichoice', 'atto_cloze'),
                'summary' => get_string('multichoicesummary', 'atto_cloze'),
                'options' => array($multivertical, $shuffle, $singleno)
            ),
            array('type' => 'MULTIRESPONSE_HS', 'name' => get_string('multichoice', 'atto_cloze'),
                'summary' => get_string('multichoicesummary', 'atto_cloze'),
                'options' => array($multihorizontal, $shuffle, $singleno)
            ),
        ));
    }
    $qtypes = array_merge($qtypes, array(
        array('type' => 'NUMERICAL', 'name' => get_string('numerical', 'atto_cloze'),
        'summary' => get_string('numericalsummary', 'atto_cloze')),
        array('type' => 'SHORTANSWER', 'name' => get_string('shortanswer', 'atto_cloze'),
        'summary' => get_string('shortanswersummary', 'atto_cloze'),
        'options' => array('option' => get_string('caseno', 'atto_cloze'))),
        array('type' => 'SHORTANSWER_C', 'name' => get_string('shortanswer', 'atto_cloze'),
        'summary' => get_string('shortanswersummary', 'atto_cloze'),
        'options' => array('option' => get_string('caseyes', 'atto_cloze'))),
    ));
    return array('questiontypes' => $qtypes);
}
