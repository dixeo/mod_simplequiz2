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
 * Activity form UI for mod_simplequiz2 (question fieldsets, reorder, warnings).
 *
 * @module      mod_simplequiz2/edit
 * @copyright   2022 Ministère de l'Éducation nationale français; Dixeo (contact@dixeo.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {
    var modSimplequizEdit = {

        preparedFieldsets: {},

        init: function() {

            var that = this;

            // Hide empty questions container
            let questionsHeaders = document.querySelectorAll("fieldset[id^='id_question_header_']");
            for (let i = 0; i < questionsHeaders.length; i++) {
                let questionHeader = questionsHeaders[i];

                that.updateQuestionsBackground([i]);

                // The first container is always visible
                if (i == 0) {

                    // This question can be visible and empty if this is a new activity, check his warning
                    if (document.querySelector('input[name="coursemodule"]').value == '') {
                        that.checkQuestionsAnswersWarnings([i]);
                    }

                    continue;
                }

                // If the container has no content, hide it
                if (questionHeader.querySelector('.question-text-editor:not(.has-content')) {
                    questionHeader.hidden = true;
                }

            }

            // Question fieldset contains question text at any moment
            that.prepareQuestionHeader();

            // If the max questions number is reach, disable all add buttons
            that.checkIfCanAddQuestion();

            // Add question buttons
            that.initAddQuestionButtons();

            // Add delete question buttons
            that.initDeleteQuestionButtons();

            let fieldsets = document.querySelectorAll("fieldset[id^='id_question_header_']");
            fieldsets.forEach(function(fieldset) {
                fieldset.onclick = function() {

                    let questionId = fieldset.id.replace('id_question_header_', '');

                    // If the fieldset is already init, stop
                    if (that.preparedFieldsets[questionId] === 1) {
                        return;
                    }

                    // Check if rich editor are loaded
                    let questionText = document.getElementById('id_questions' + questionId + '_texteditable');
                    if (questionText === null) {
                        // If not, exit
                        return;
                    }

                    that.preparedFieldsets[questionId] = 1;

                    that.updateQuestionHeader(questionId);

                    let answersEditor = document.querySelectorAll(
                        "div[id^='id_questions" + questionId + "_answers_'].editor_atto_content, " +
                        "textarea[id^='id_questions" + questionId + "_answers_']"
                    );
                    for (const editor of answersEditor) {

                        // On change of editor content (work when audio/img are added), update displayed question info
                        editor.addEventListener("change", () => {
                            that.checkQuestionsAnswersWarnings(false);
                            that.updateQuestionsBackground([questionId]);
                        });
                    }

                    let checkboxs = document.querySelectorAll("input[id^='id_questions" + questionId + "_correctanswers_']");
                    for (const checkbox of checkboxs) {
                        checkbox.addEventListener('change', function() {
                            that.checkQuestionsAnswersWarnings([questionId]);
                            that.updateQuestionsBackground([questionId]);
                        });
                    }

                };
            });

        },

        // Prepare add question buttons
        initAddQuestionButtons: function() {
            var that = this;

            let addQuestionButtons = document.querySelectorAll('form input[type="button"].add-simplequestion');
            addQuestionButtons.forEach(function(addQuestionButton) {
                addQuestionButton.onclick = function() {

                    let currentFieldset = document.getElementById('id_question_header_' + addQuestionButton.dataset.questionid);

                    // Move the next hidden field at the correct place and unhide it
                    let fieldsets = document.querySelectorAll("fieldset[id^='id_question_header_']");
                    for (const fieldset of fieldsets) {

                        if (fieldset.hidden === true) {
                            // Move the element after sur selected field
                            currentFieldset.after(fieldset);

                            // Unhide the fieldset
                            fieldset.hidden = false;

                            // Open the fieldset
                            let header = fieldset.querySelector('a[aria-expanded="false"][aria-controls^="id_question_header_"]');
                            if (header) {
                                header.click();
                            }

                            // Display warning message
                            let questionId = fieldset.id.replace('id_question_header_', '');
                            that.checkQuestionsAnswersWarnings([questionId]);

                            break;
                        }
                    }

                    // Check if the order are still correct and update title
                    that.fixQuestionOrder();

                    // If there is only one question, hide delete buttons
                    that.checkIfCanAddQuestion();
                };
            });
        },

        // Prepare delete question buttons
        initDeleteQuestionButtons: function() {
            var that = this;

            let deleteQuestionButtons = document.querySelectorAll('form input[type="button"].delete-simplequestion');
            deleteQuestionButtons.forEach(function(deleteQuestionButton) {
                deleteQuestionButton.onclick = function() {

                    // Empty all fields, hide the container, fix order and title
                    let questionId = deleteQuestionButton.dataset.questionid;

                    // Hide the container
                    document.getElementById('id_question_header_' + questionId).hidden = true;

                    // Empty question text editor adn header
                    document.getElementById('id_questions' + questionId + '_texteditable').innerHTML = '';
                    document.getElementById('id_questions' + questionId + '_text').innerHTML = '';
                    document.querySelector('.header-questiontext[data-questionid="' + questionId + '"]').textContent = '';

                    // Empty answers editor
                    let answersEditors = document.querySelectorAll(
                        "div[id^='id_questions" + questionId + "_answers_'].editor_atto_content, " +
                        "textarea[id^='id_questions" + questionId + "_answers_']"
                    );
                    answersEditors.forEach(answersEditor => {
                        answersEditor.innerHTML = '';
                    });

                    // Empty answers checkbox
                    let checkboxs = document.querySelectorAll("input[id^='id_questions" + questionId + "_correctanswers_']");
                    checkboxs.forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Remove answers state
                    let questionsState = document.querySelectorAll(
                        "div[id^='fitem_id_questions" + questionId + "_answers_'].simplequiz2-answer"
                    );
                    questionsState.forEach(function(questionState) {
                        questionState.classList.remove("simplequiz2-right-answer");
                        questionState.classList.remove("simplequiz2-wrong-answer");
                    });

                    // Fix order of other question
                    that.fixQuestionOrder();

                    // Check add and question question status
                    that.checkIfCanAddQuestion();

                    // Check state of all visible question to enable save button if only the deleted question has error
                    that.checkQuestionsAnswersWarnings(false);
                };
            });

        },

        // Move elements in fieldset header to better view
        prepareQuestionHeader: function() {
            let questionHeaders = document.querySelectorAll("fieldset[id^='id_question_header_']");
            questionHeaders.forEach(function(questionHeader) {

                // Get the main div questions/answer container.
                let legend = questionHeader.querySelector('legend ~ div.d-flex');
                legend.insertAdjacentHTML('afterend', '<div class="header-info-container"></div>');

                let legendContainer = questionHeader.querySelector('.header-info-container');
                let title = questionHeader.querySelector('div.ftoggler');

                // Move question text
                let questionText = questionHeader.querySelector('.header-questiontext');
                title.appendChild(questionText);

                // Move answers warning
                let warnings = questionHeader.querySelectorAll('.error_not_enough_answers, .error_no_right_answer');
                warnings.forEach(warning => legendContainer.appendChild(warning));

                // Move add/delete buttons
                let buttons = questionHeader.querySelectorAll('.header-btn');
                legendContainer.insertAdjacentHTML('beforeend', '<div class="header-buttons-container"></div>');
                let buttonsContainer = legendContainer.querySelector('.header-buttons-container');
                buttons.forEach(button => buttonsContainer.appendChild(button));
            });
        },

        // Question fieldset header contains question text at any moment
        updateQuestionHeader: function(questionId) {
            let questionTextElement = document.getElementById('id_questions' + questionId + '_texteditable');
            questionTextElement.addEventListener("keyup", () => {
                let questionText = document.getElementById('id_questions' + questionId + '_texteditable').innerText;
                document.querySelector('.header-questiontext[data-questionid="' + questionId + '"]').textContent = questionText;
            });
        },

        // Check if hidden order field and question fieldset match to what to user sees
        fixQuestionOrder: function() {
            let fieldsets = document.querySelectorAll("fieldset[id^='id_question_header_']");

            var visibleIndex = 1;
            for (const fieldset of fieldsets) {

                if (fieldset.hidden === true) {
                    continue;
                }

                // Get question real id
                let questionId = fieldset.id.replace('id_question_header_', '');

                // Change title
                let title = fieldset.querySelector(".fheader[aria-controls^='id_question_header_'] ~ h3");
                title.innerHTML = "Question " + visibleIndex;

                // Change order input
                document.querySelector('input[name="questions' + questionId + '[questionorder]"]').value = visibleIndex - 1;

                visibleIndex++;
            }
        },

        // If there is no hidden question, disable all "add question" buttons
        checkIfCanAddQuestion: function() {

            // Get if at least one fieldset is hidden (not used)
            let fieldsets = document.querySelectorAll("fieldset[id^='id_question_header_']");
            var hasHiddenQuestions = false;
            for (const fieldset of fieldsets) {
                if (fieldset.hidden === true) {
                    hasHiddenQuestions = true;
                    break;
                }
            }

            // If no fieldset is hidden, disable "add question" button, else, enable them
            let addButtons = document.querySelectorAll('input.add-simplequestion');
            addButtons.forEach(function(addButton) {
                if (hasHiddenQuestions === false) {
                    addButton.disabled = true;
                } else {
                    addButton.disabled = false;
                }
            });

        },

        // Check if there is enough answers and correct answers, else display warning message and block save buttons
        checkQuestionsAnswersWarnings: function(questionIds) {
            var formHasError = false;

            if (questionIds == false) {
                let visibleQuestionIds = [];
                let fieldsets = document.querySelectorAll("fieldset[id^='id_question_header_']");
                for (const fieldset of fieldsets) {
                    if (fieldset.hidden === true) {
                        continue;
                    }
                    visibleQuestionIds.push(fieldset.id.replace('id_question_header_', ''));
                }
                questionIds = visibleQuestionIds;
            }

            for (const questionId of questionIds) {

                let fieldset = document.querySelector("fieldset#id_question_header_" + questionId);
                let questionHasError = false;

                // If question is hidden, save buttons will not be affected by its errors
                let isHidden = fieldset.hidden;

                // Check if there is enough answers
                let answersEditor = fieldset.querySelectorAll(
                    "div[id^='id_questions" + questionId + "_answers_'].editor_atto_content"
                );
                let nbContent = 0;
                for (const answerEditor of answersEditor) {

                    // Remove all useless tags from content to check if content is really empty
                    let stripContent = answerEditor.innerHTML;
                    stripContent = stripContent.replace(/<\/?p[^>]*>/g, "");
                    stripContent = stripContent.replace(/<\/?br[^>]*>/g, "");
                    stripContent = stripContent.trim();

                    if (stripContent.trim() != '') {
                        nbContent++;
                    }
                }

                // Display warning if there is less then two answers with content
                if (nbContent < 2) {
                    fieldset.querySelector('.error_not_enough_answers').style.display = "inline";
                    questionHasError = true;
                    if (isHidden === false) {
                        formHasError = true;
                    }

                } else {
                    fieldset.querySelector('.error_not_enough_answers').style.display = "none";
                }

                // Check if there is at least one correct answers, else display the error message
                if (!questionHasError) {
                    let checkboxs = document.querySelectorAll(
                        "input[id^='id_questions" + questionId + "_correctanswers_']"
                    );
                    let checked = false;
                    for (const checkbox of checkboxs) {
                        if (checkbox.checked === true) {

                            // Check if checked checkbox editor has content (empty answer is not correct answer)
                            let answerId = checkbox.dataset.answerid;

                            let editor = document.querySelector(
                                "#id_questions" + questionId + "_answers_" + answerId +
                                "editable.editor_atto_content"
                            );
                            let stripContent = '';
                            if (editor === null) {

                                // Editor are not init, use textarea
                                const ta = document.querySelector(
                                    '#id_questions' + questionId + '_answers_' + answerId
                                );
                                stripContent = ta ? ta.textContent : '';
                            } else {
                                stripContent = editor.innerHTML;
                            }

                            // Remove all useless tags from content to check if content is really empty
                            stripContent = stripContent.replace(/<\/?p[^>]*>/g, "");
                            stripContent = stripContent.replace(/<\/?br[^>]*>/g, "");
                            stripContent = stripContent.trim();

                            if (stripContent != '') {
                                checked = true;
                                break;
                            }

                        }
                    }

                    if (!checked) {
                        fieldset.querySelector('.error_no_right_answer').style.display = "inline";
                        questionHasError = true;

                        if (isHidden === false) {
                            formHasError = true;
                        }
                    } else {
                        fieldset.querySelector('.error_no_right_answer').style.display = "none";
                    }
                }

            }

            // If there is some error, disable save buttons
            if (formHasError === true) {
                document.getElementById('id_submitbutton').disabled = true;
                if (document.getElementById('id_submitbutton2')) {
                    document.getElementById('id_submitbutton2').disabled = true;
                }
            } else {
                document.getElementById('id_submitbutton').disabled = false;
                if (document.getElementById('id_submitbutton2')) {
                    document.getElementById('id_submitbutton2').disabled = false;
                }
            }

        },

        // Display red or green background on right/wrong answers
        updateQuestionsBackground: function(questionIds) {

            questionIds.forEach(function(questionId) {
                let checkboxs = document.querySelectorAll(
                    "input[id^='id_questions" + questionId + "_correctanswers_']"
                );
                for (const checkbox of checkboxs) {

                    let answerId = checkbox.dataset.answerid;

                    let container = document.querySelector('#fitem_id_questions' + questionId + '_answers_' + answerId);
                    container.classList.add('simplequiz2-answer');

                    // Check if answer has content
                    let editor = document.querySelector(
                        "#id_questions" + questionId + "_answers_" + answerId +
                        "editable.editor_atto_content"
                    );
                    let stripContent = '';
                    if (editor === null) {

                        // Editor are not init, use textarea
                        stripContent = document.querySelector('#id_questions' + questionId + '_answers_' + answerId).textContent;
                    } else {
                        stripContent = editor.innerHTML;
                    }

                    // Remove all useless tags from content to check if content is really empty
                    stripContent = stripContent.replace(/<\/?p[^>]*>/g, "");
                    stripContent = stripContent.replace(/<\/?br[^>]*>/g, "");
                    stripContent = stripContent.trim();

                    let hasContent = false;
                    if (stripContent != '') {
                        hasContent = true;
                    }

                    // If answer has no content, remove all color, else add right/wrong class
                    if (!hasContent) {
                        container.classList.remove('simplequiz2-wrong-answer');
                        container.classList.remove('simplequiz2-right-answer');
                    } else if (checkbox.checked === true) {
                        container.classList.add('simplequiz2-right-answer');
                        container.classList.remove('simplequiz2-wrong-answer');
                    } else {
                        container.classList.add('simplequiz2-wrong-answer');
                        container.classList.remove('simplequiz2-right-answer');
                    }

                }
            });
        },

    };

// Add object to window to be called outside require.
    window.modSimplequizEdit = modSimplequizEdit;
    return modSimplequizEdit;
});