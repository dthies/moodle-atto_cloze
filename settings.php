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
 * Atto text editor integration settings file.
 *
 * @package    atto_cloze
 * @copyright  2021 Adrian Perez <me@adrianperez.me> {@link https://adrianperez.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/lib/editor/atto/plugins/cloze/locallib.php');

if ($ADMIN->fulltree) {
    $choices = [];

    $qtypes = atto_cloze_get_question_types();
    foreach($qtypes['questiontypes'] as $qtype) {
        $choices[] = $qtype['type'];
    }

    $name = 'atto_cloze/enabledquestiontypes';
    $title = get_string('chooseqtypetoadd', 'question');
    $description = '';
    $default = array_keys($choices);
    $setting = new admin_setting_configmulticheckbox($name, $title, $description, $default, $choices);

    $settings->add($setting);
}
