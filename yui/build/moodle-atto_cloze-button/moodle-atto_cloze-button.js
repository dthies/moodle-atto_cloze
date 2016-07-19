YUI.add('moodle-atto_cloze-button', function (Y, NAME) {

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

/*
 * @package    atto_cloze
 * @copyright  2016 onward Daniel Thies <dthies@ccal.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_cloze-button
 */

/**
 * Atto text editor cloze plugin.
 *
 * @namespace M.atto_cloze
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var COMPONENTNAME = 'atto_cloze';

var CSS = {
        ANSWER: 'cloze_answer',
        ADD: 'cloze_add',
        CANCEL: 'cloze_cancel',
        DELETE: 'cloze_delete',
        FEEDBACK: 'cloze_feedback',
        FRACTION: 'cloze_fraction',
        MARKS: 'cloze_marks',
        SUBMIT: 'cloze_submit',
        TYPE: 'cloze_qtype'
    };
var TEMPLATE = {
    FORM: '<div class="atto_cloze">' +
             '<form class="atto_form">' +
             '<p>{{qtype}}' +
                 '<span id="{{elementid}}_mark">{{get_string "defaultmark" "core_question"}}</span>' +
                 '<input aria-labelledby="{{elementid}}_mark" type="text" class="{{CSS.MARKS}}" value="{{marks}}" />' +
             '<ol>{{#answerdata}}' +
             '<li>' +
                 '<button class="{{../CSS.ADD}}" title="{{get_string "addmoreanswerblanks" "qtype_calculated"}}">+</button>' +
                 '<button class="{{../CSS.DELETE}}" title="{{get_string "delete" "core"}}">-</button>' +
                 '<select value="{{fraction}}" class="{{../CSS.FRACTION}}" selected>' +
                     '<option value="{{fraction}}">{{fraction}}%</option>' +
                     '{{#../fractions}}' +
                     '<option value="{{fraction}}">{{fraction}}%</option>' +
                     '{{/../fractions}}' +
                 '</select>' +
                 '<label for="{{elementid}}_answer">{{get_string "answer" "core"}}</label>' +
                 '<input id="{{elementid}}_answer" type="text" class="{{../CSS.ANSWER}}" value="{{answer}}" />' +
                 '<label for="{{elementid}}_feedback">{{get_string "feedback" "core"}}</label>' +
                 '<input id="{{elementid}}_feedback type="text" class="{{../CSS.FEEDBACK}}" value="{{feedback}}" />' +
             '</li>' +
             '{{/answerdata}}</ol>' +
                 '<p><button class="{{CSS.ADD}}" title="{{get_string "addmoreanswerblanks" "qtype_calculated"}}">+</button></p>' +
                 '<p><button type="submit" class="{{CSS.SUBMIT}}">{{get_string "common:insert" "editor_tinymce"}}</button>' +
                 '<button type="submit" class="{{CSS.CANCEL}}">{{get_string "cancel" "core"}}</button></p>' +
             '</form>' +
          '</div>',
    OUTPUT: '&#123;{{marks}}:{{qtype}}:{{#answerdata}}~%{{fraction}}%{{answer}}' +
          '{{#if feedback}}#{{feedback}}{{/if}}{{/answerdata}}&#125;',
    TYPE: '<div class="atto_cloze">' +
             '{{#types}}' +
             '<button class="{{../CSS.TYPE}}" value="{{type}}">{{type}}</button>' +
             '{{/types}}' +
          '</div>'
    },
    FRACTIONS = [{fraction: 100},
        {fraction: 50},
        {fraction: 33.333},
        {fraction: 25},
        {fraction: 20},
        {fraction: 16.667},
        {fraction: 14.2857},
        {fraction: 12.5},
        {fraction: 0},
        {fraction: -12.5},
        {fraction: -14.2857},
        {fraction: -16.667},
        {fraction: -20},
        {fraction: -25},
        {fraction: -33.333},
        {fraction: -50},
        {fraction: -100}];

Y.namespace('M.atto_cloze').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    /**
     * A reference to the currently open form.
     *
     * @param _form
     * @type Node
     * @private
     */
    _form: null,

    /**
     * An array containing the current answers options
     *
     * @param _answerdata
     * @type Array
     * @private
     */
    _answerdata: null,

    /**
     * The sub question type to be edited
     *
     * @param _qtype
     * @type String
     * @private
     */
    _qtype: null,

    /**
     * The maximum marks for the sub question
     *
     * @param _marks
     * @type Integer
     * @private
     */
    _marks: null,

    /**
     * The selection object returned by the browser.
     *
     * @property _currentSelection
     * @type Range
     * @default null
     * @private
     */
    _currentSelection: null,

    initializer: function() {
        this._groupFocus = {};
        // Check whether we are editing a question.
        var form = this.get('host').editor.ancestor('form');
        if (!form || !form.test('[action="question.php"]')) {
            return;
        }

        this.addButton({
            icon: 'icon',
            iconComponent: 'qtype_multianswer',
            callback: this._displayDialogue
        });
        this._marks = 1;
        this._answerdata = [{answer: '', feedback: '', fraction: 100}];
    },

    /**
     * Display form to edit subquestions.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {

        var host = this.get('host');

        // Store the current selection.
        this._currentSelection = host.getSelection();
        if (this._currentSelection === false) {
            return;
        }

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('pluginname', COMPONENTNAME),
            bodyContent: '<div style="height:500px"></div>',
            width: 500
        }, true);

        // Resolve whether cursor is in a subquestion.
        var subquestion = this._resolveSubquestion();
        if (subquestion) {
            this._parseSubquestion(subquestion);
        }
        dialogue.show();

        dialogue.set('bodyContent', this._getDialogueContent());
        this._dialogue = dialogue;
   },

    /**
     * Return the dialogue content for the tool, attaching any required
     * events.
     *
     * @method _getDialogueContent
     * @return {Node} The content to place in the dialogue.
     * @private
     */
    _getDialogueContent: function() {
        var template, content;
        if (!this._qtype) {
            template = Y.Handlebars.compile(TEMPLATE.TYPE);
            content = Y.Node.create(template({CSS: CSS,
                types: [
                    {type: 'MULTICHOICE'},
                    {type: 'MULTICHOICE_H'},
                    {type: 'MULTICHOICE_V'},
                    {type: 'MULTICHOICE_S'},
                    {type: 'MULTICHOICE_HS'},
                    {type: 'MULTICHOICE_VS'},
                    {type: 'NUMERICAL'},
                    {type: 'SHORTANSWER'}
                ]}));
            this._form = content;
            content.delegate('click', function(e) {
                this._qtype = e.target.getAttribute('value');
                this._dialogue.set('bodyContent', this._getDialogueContent());
            }, '.' + CSS.TYPE, this);
            return content;
        }

        template = Y.Handlebars.compile(TEMPLATE.FORM);

        content = Y.Node.create(template({CSS: CSS,
            answerdata: this._answerdata,
            elementid: Y.guid(),
            fractions: FRACTIONS,
            qtype: this._qtype,
            marks: this._marks
        }));

        this._form = content;

        content.one('.' + CSS.SUBMIT).on('click', this._setSubquestion, this);
        content.one('.' + CSS.CANCEL).on('click', this._setSubquestion, this);
        content.delegate('click', this._deleteAnswer, '.' + CSS.DELETE, this);
        content.delegate('click', this._addAnswer, '.' + CSS.ADD, this);

        return content;
    },

    /**
     * Parse question and set properties found
     *
     * @method _parseSubquestion
     * @private
     * @param {String} The question string
     */
    _parseSubquestion: function(question) {
        var re = /\{([0-9]*):([_A-Z]*):(\\.|[^]*?)\}/g,
            parts = re.exec(question);
        if (!parts) {
            return;
        }
        this._marks = parts[1];
        this._qtype = parts[2];
        this._answerdata = [];
        var answers = parts[3].match(/(\\.|[^~])*/g);
        if (!answers) {
            return;
        }
        answers.forEach(function(answer) {
            var options = /^(%(-?[\.0-9]+)%|(=?))([^#]*)#(.*)/.exec(answer);
            if (options) {
                this._answerdata.push({answer: options[4],
                    feedback: options[5],
                    fraction: options[3] ? 100 : options[2] || 0});
            }
        }, this);
    },

    /**
     * Insert a new set of answer blanks before the button.
     *
     * @method _addAnswer
     * @private
     */
    _addAnswer: function(e) {
       e.preventDefault();
       var index = this._form.all('.' + CSS.ADD).indexOf(e.target);
       this._getFormData()
           ._answerdata.splice(index, 0, {answer: '', feedback: '', fraction: 0});
       this._dialogue.set('bodyContent', this._getDialogueContent());
    },

    /**
     * Delete set of answer blanks before the button.
     *
     * @method _deleteAnswer
     * @private
     */
    _deleteAnswer: function(e) {
       e.preventDefault();
       var index = this._form.all('.' + CSS.DELETE).indexOf(e.target);
       this._getFormData()
           ._answerdata.splice(index, 1);
       this._dialogue.set('bodyContent', this._getDialogueContent());
    },

    /**
     * Reset and hide form.
     *
     * @method _cancel
     * @private
     */
    _cancel: function(e) {
        e.preventDefault();
        delete(this._qtype);
    },

    /**
     * Insert content into editor and reset and hide form.
     *
     * @method _setSubquestion
     * @private
     */
    _setSubquestion: function(e) {
        e.preventDefault();
        var template = Y.Handlebars.compile(TEMPLATE.OUTPUT);
        this._getFormData();

        var question = template({
                CSS: CSS,
                answerdata: this._answerdata,
                qtype: this._qtype,
                marks: this._marks}),
            host = this.get('host');

        this._dialogue.hide();
        host.focus();
        host.setSelection(this._currentSelection);

        host.insertContentAtFocusPoint(question);
        delete(this._qtype);
        delete(this._answerdata);

    },

    /**
     * Read and process the current data in the form.
     *
     * @method _setSubquestion
     * @chainable
     * @private
     */
    _getFormData: function() {
        this._answerdata = [];
        var answers = this._form.all('.' + CSS.ANSWER),
            feedbacks = this._form.all('.' + CSS.FEEDBACK),
            fractions = this._form.all('.' + CSS.FRACTION);
        for(var i = 0; i < answers.size(); i++) {
            //this._answerdata.push({answer: answers.item(i).getAttribute('value'),
            this._answerdata.push({answer: answers.item(i).getDOMNode().value,
                feedback: feedbacks.item(i).getDOMNode().value,
                fraction: fractions.item(i).getDOMNode().value});
            this._marks = this._form.one('.' + CSS.MARKS).getDOMNode().value;
        }
        return this;
   },

    /**
     * Locate a node and offset to be used as a end of a range representing an
     * offset in the text value of a node.
     * true.
     *
     * @method _getAnchor
     * @param {DOMNode} Parent node with text value
     * @param {Integer} Position of character with in text of parent node
     * @return {Object} An object with anchor and offset for the character
     * with offset in string.
     * @private
     */
    _getAnchor: function(node, offset) {
      if (!node.hasChildNodes()) {
          return {anchor: node, offset: offset};
      }
      var child = node.firstChild;
      while (offset > child.textContent.length) {
          offset -= child.textContent.length;
          child = child.nextSibling;
      }
      return this._getAnchor(child, offset);
    },

    /**
     * Find the offset for the text of a child with within the text of parent
     *
     * @method _getOffset
     * @param {DOMNode} Parent node with text value
     * @param {DOMNode} Parent node with text value
     * @return {Integer} The offset of the child's text
     * @private
     */
    _getOffset: function(container, node) {
        if (!container.contains(node)) {
            return;
        }
        if (container === node) {
            return 0;
        }
        var offset = 0,
            child = container.firstChild;
        while (!child.contains(node)) {
            offset += child.textContent.length;
            child = child.nextSibling;
        }
        return offset + this._getOffset(node, child);
    },

    /**
     * Check whether cursor is in a subquestion and return subquestion text if
     * true.
     *
     * @method _resolveSubquestion
     * @return {String} The substring describing subquestion
     * @private
     */
    _resolveSubquestion: function() {
        var host = this.get('host'),
            selectedNode =  host.getSelectionParentNode(),
            re = /\{[^]*?\}/g;

        if (!selectedNode) {
            return;
        }
        var subquestions = selectedNode.textContent.match(re);
        if (!subquestions) {
            return;
        }

        var index,
            selection = this.get('host').getSelection(),
            result = '',
            questionEnd = 0;

        if (!selection || selection.length === 0) {
            return false;
        }

        var startIndex = this._getOffset(selectedNode, selection[0].startContainer) + selection[0].startOffset,
            endIndex = this._getOffset(selectedNode, selection[0].endContainer) + selection[0].endOffset;

        subquestions.forEach(function(subquestion) {
            index = selectedNode.textContent.indexOf(subquestion, questionEnd);
            questionEnd = index + subquestion.length;
            if (index < startIndex && endIndex < questionEnd) {
               result = subquestion;
               var startRange = this._getAnchor(selectedNode, index);
               var endRange = this._getAnchor(selectedNode, questionEnd);
               selection[0].setStart(startRange.anchor, startRange.offset);
               selection[0].setEnd(endRange.anchor, endRange.offset);
               this.get('host').setSelection(selection);
               this._currentSelection = host.getSelection();
            }
        }, this);

        return result;
    }
});


}, '@VERSION@', {"requires": ["escape", "moodle-tinymce_cloze-editor"]});
