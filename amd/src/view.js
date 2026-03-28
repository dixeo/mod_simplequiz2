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
 * JS code for the simplequiz2 plugin student interface.
 *
 * @module      mod_simplequiz2/view
 * @copyright   2022 Ministère de l'Éducation nationale français; Dixeo (contact@dixeo.com)
 * @author      Céline Hernandez
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/modal_factory', 'core/str'], function($, ModalFactory, str) {
    var modSimplequizView = {

        // Set main attributes.
        instanceId: 0,
        courseId: 0,
        courseModuleId: null,
        attemptId: 0,
        apiUrl: M.cfg.wwwroot + '/mod/simplequiz2/ajax/ajax.php',

        // Intl
        langStrings: [
            'aria_question_text',
            'aria_answer_text',
            'questionsuccess',
            'questionfail',
            'questionpartial',
            'aria_restart',
            'aria_next',
            'aria_video',
            'aria_image',
            'aria_math',
            'aria_audio',
            'aria_no_description',
            'session_expired_title',
            'session_expired_body'
        ],

        init: async function(instanceId, courseId, courseModuleId, attemptId) {

            // Not good. This can be used, else use arrow function.
            var that = this;

            // Set main datas.
            this.instanceId = instanceId;
            this.courseId = courseId;
            this.courseModuleId = courseModuleId;
            this.attemptId = attemptId;

            // Preload language strings before any click handler can use M.util.get_string().
            const modStrings = this.langStrings.map(l => {
                return {
                    key: l,
                    component: 'mod_simplequiz2'
                };
            });
            await str.get_strings(modStrings);

            // Init answer selection
            let answerButtons = document.querySelectorAll('.question-container .answer-container');
            answerButtons.forEach(function(answerButton) {
                answerButton.onclick = function() {
                    // Toggle style for selected answer.
                    answerButton.classList.toggle('selected');
                };
            });

            // Init check answer buttons
            let checkAnswerButtons = document.querySelectorAll('.question-container button.check-answer');
            checkAnswerButtons.forEach(function(checkAnswerButton) {
                checkAnswerButton.onclick = function() {
                    let questionId = this.dataset.questionid;
                    that.check_answers(questionId);
                };
            });

            // Init next question buttons
            let nextQuestionButtons = document.querySelectorAll('.question-container button.next-question');
            nextQuestionButtons.forEach(function(nextQuestionButton) {
                nextQuestionButton.onclick = function() {
                    let questionId = this.dataset.questionid;
                    that.displayNextQuestion(questionId);
                };
            });

            // Init show results button
            document.querySelector('#simplequiz_container button.show-results').onclick = function() {
                that.updateResultspage();
            };

            // Init restart button
            document.querySelector('#simplequiz_container button.restart').onclick = function() {
                location.reload();
            };

            // Fill aria label for the first question.
            that.setAriaLabel(0);

            // Focus on activity title and intro.
            document.querySelector('section#region-main > h2').setAttribute('tabindex', '0');
            if (document.querySelector('.activity-description#intro') !== null) {
                document.querySelector('.activity-description#intro').setAttribute('tabindex', '0');
            }
            document.querySelector('section#region-main > h2').focus();
        },

        /**
         * Set aria-label for a given question id
         * @param {number} questionId Question index
         */
        setAriaLabel: function(questionId) {
            // Get the main question container.
            const questionContainer = document.querySelector(`.question-container[data-questionid="${questionId}"]`);

            // Set aria label for question text element.
            const questionText = questionContainer.querySelector('.question-text');
            questionText.setAttribute('aria-label', questionText.innerText);


            // Set aria label for each answers
            questionContainer.querySelectorAll('.answer-container').forEach((elem) => {
                const answerInfo = this.getAccessibilityInformation(elem.querySelector('.answer-text'));
                elem.setAttribute('aria-label', M.util.get_string('aria_answer_text', 'mod_simplequiz2', {
                    answer: answerInfo
                }));
            });
        },

        /**
         * Ask api to check question user result, display answers status and question status
         * and update navigation buttons
         *
         * @param {string} questionId Question id
         * @returns {Promise<void>}
         */
        check_answers: async function(questionId) {

            // Get selected answers
            const selector = '.question-container[data-questionid="' + questionId + '"] .answer-container.selected';
            let selectedAnswers = document.querySelectorAll(selector);

            let userChoices = [];
            for (let i = 0; i < selectedAnswers.length; i++) {
                userChoices.push(selectedAnswers[i].dataset.answerid);
            }

            // Call to api to get answers corrections
            let data = await this.communicate(this.apiUrl + '?action=check_question&id=' + this.instanceId, {
                'questionid': questionId,
                'attemptid': this.attemptId,
                'answers': userChoices.join(','),
            });

            // Check if at least one correct answers is checked.
            var hasCorrectAnswer = false;

            // Print answers status.
            for (let i = 0; i < selectedAnswers.length; i++) {
                let answerId = selectedAnswers[i].dataset.answerid;
                selectedAnswers[i].classList.remove('selected');
                if (data.results[answerId] === true) {
                    selectedAnswers[i].classList.add('question-success');
                    hasCorrectAnswer = true;
                } else {
                    selectedAnswers[i].classList.add('question-fail');
                }
            }

            // Disabled click on answers
            let answers = document.querySelectorAll('.question-container[data-questionid="' + questionId + '"] .answer-container');
            for (let i = 0; i < answers.length; i++) {
                answers[i].disabled = true;
            }

            // Display question status.
            let status = '';
            if (data.iscorrect === true) {
                status = M.util.get_string('questionsuccess', 'mod_simplequiz2');
                document.querySelector('.question-status[data-questionid="' + questionId + '"]').innerHTML = status;
            } else if (data.iscorrect === false && hasCorrectAnswer === true) {
                status = M.util.get_string('questionpartial', 'mod_simplequiz2');
                document.querySelector('.question-status[data-questionid="' + questionId + '"]').innerHTML = status;
            } else {
                status = M.util.get_string('questionfail', 'mod_simplequiz2');
                document.querySelector('.question-status[data-questionid="' + questionId + '"]').innerHTML = status;
            }

            // Hide check button and display next question or restart button.
            let nextquestion = document.querySelector('.question-container[data-questionid="' + (parseInt(questionId) + 1) + '"]');
            if (!nextquestion) {
                // Is last question, display restart button
                document.querySelector('#simplequiz_container button.show-results').style.display = "block";
                const restartLabel = M.util.get_string('aria_restart', 'mod_simplequiz2', {status});
                document.querySelector('#simplequiz_container button.show-results').setAttribute(
                    'aria-label', restartLabel
                );
                document.querySelector('#simplequiz_container button.show-results').focus();
            } else {
                // Display next question button
                const nextBtn = document.querySelector(
                    'button.next-question[data-questionid="' + questionId + '"]'
                );
                nextBtn.style.display = "block";
                nextBtn.setAttribute(
                    'aria-label', M.util.get_string('aria_next', 'mod_simplequiz2', {status})
                );
                nextBtn.focus();
            }

            // Hide check answers button
            document.querySelector('button.check-answer[data-questionid="' + questionId + '"]').style.display = "none";
        },

        /**
         * Hide question associated to questionId and display the next one (or reload if not exists)
         * @param {string} questionId Question id
         */
        displayNextQuestion: function(questionId) {

            let nextQuestion = document.querySelector('.question-container[data-questionid="' + (parseInt(questionId) + 1) + '"]');

            // If there is no next question, restart
            if (!nextQuestion) {
                location.reload();
            }

            // Hide current question
            document.querySelector(
                '.question-container[data-questionid="' + questionId + '"]'
            ).style.display = "none";

            // Display next question
            nextQuestion.style.display = "block";

            // Init accessibility.
            this.setAriaLabel((parseInt(questionId) + 1));
            nextQuestion.querySelector('.question-text').focus();
        },

        /**
         * Get attempt and best score to print them on attempt result page
         */
        updateResultspage: async function() {

            // Call to api to attempt score and best score.
            let data = await this.communicate(this.apiUrl + '?action=get_attempt_results&id=' + this.instanceId, {
                'attemptid': this.attemptId,
            });

            // Prepare and print attempt score lang str.
            var attemptScoreStr = str.get_string('result-score', 'mod_simplequiz2', {
                score: Math.trunc(data.attemptgrade)
            });
            $.when(attemptScoreStr).done(function(localizedString) {
                const currentScore = document.querySelector('#simplequiz-result .current-score');
                if (currentScore) {
                    currentScore.innerHTML = localizedString;
                }
            });

            // Prepare and print best score lang str.
            var bestScoreStr = str.get_string('result-bestscore', 'mod_simplequiz2', {
                score: Math.trunc(data.bestscore)
            });
            $.when(bestScoreStr).done(function(localizedString) {
                const bestScore = document.querySelector('#simplequiz-result .best-score');
                if (bestScore) {
                    bestScore.innerHTML = localizedString;
                }
            });

            // Hide fireworks if result is under 100%.
            if (data.attemptgrade < 100) {
                document.querySelectorAll('#simplequiz-result .fireworks').forEach(function(elem) {
                    elem.style.visibility = 'hidden';
                });
            }

            // Toggle game/results div.
            const questionsContainer = document.querySelector('#simplequiz-questions');
            const resultContainer = document.querySelector('#simplequiz-result');
            const currentScore = document.querySelector('#simplequiz-result .current-score');
            if (questionsContainer) {
                questionsContainer.style.display = "none";
            }
            if (resultContainer) {
                resultContainer.style.display = "flex";
            }
            if (currentScore) {
                currentScore.focus();
            }

        },

        /**
         * Search description (text, alt or title) in a card to be played by screen readers.
         *
         * @param {HTMLElement} container DOM element containing the content
         * @return {string} Accessibility description
         */
        getAccessibilityInformation: function(container) {
            // Determine aria-label for media content cards.
            // Check only the FIRST element. Considering just one HTML element is authorized.

            // Video
            if (container.querySelector('video') !== null) {
                return M.util.get_string('aria_video', 'mod_simplequiz2', {
                    description: this.findMetaData(container.querySelector('video'))
                });
            }
            // Audio
            if (container.querySelector('audio') !== null) {
                return M.util.get_string('aria_audio', 'mod_simplequiz2', {
                    description: this.findMetaData(container.querySelector('audio'))
                });
            }
            // Image
            if (container.querySelector('img') !== null) {
                return M.util.get_string('aria_image', 'mod_simplequiz2', {
                    description: this.findMetaData(container.querySelector('img'))
                });
            }
            // Equation
            if (container.querySelector('.filter_mathjaxloader_equation') !== null) {
                return M.util.get_string('aria_math', 'mod_simplequiz2');
            }

            return container.innerText;
        },

        /**
         * Just find the good attribute containg meta description of anelement.
         *
         * @param {HTMLElement} elem targeted DOM element
         * @return {string} Meta description
         */
        findMetaData: function(elem) {
            if (elem.title !== '') {
                return elem.title;
            } else if (elem.getAttribute('alt') !== '' && elem.getAttribute('alt') !== null) {
                return elem.getAttribute('alt');
            } else {
                return M.util.get_string('aria_no_description', 'mod_simplequiz2');
            }
        },

        /**
         * Send HTTP async request to the server.
         * @param {string} url Request URL
         * @param {Object} [payload] Data to send to the server
         * @return {Promise<Object>} Deserialized JSON response
         */
        communicate: async function(url, payload = null) {

            let formData = new FormData();

            // These data are for the authentification and enrollment.
            formData.append('sesskey', M.cfg.sesskey);
            formData.append('courseid', this.courseId);
            formData.append('coursemoduleid', this.courseModuleId);

            // Add datas to the formdata before the fetch.
            if (payload !== null) {
                for (const [key, value] of Object.entries(payload)) {
                    formData.append(key, value);
                }
            }

            // Async call to the server with payload in url AND body.
            let response = await fetch(url, {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            // Check if there is an error in the HTTP response.
            this.handleError(response, data);

            // Return the response.
            return data;
        },

        /**
         * Check if there is an HTTP error code, then display modal
         * with tech info, or redirect to login.
         * Based on Moodle Modal, with Jquery
         *
         * @param {Response} response fetch() response object
         * @param {Object} data Parsed response body
         */
        handleError: function(response, data) {

            let modalElement = $('#modal');

            // Redirect to home page if user is not logged, or if its session has expired.
            if (response.status == 401 || data.errorcode == 'requireloginerror') {
                ModalFactory.create({
                    title: M.util.get_string('session_expired_title', 'mod_simplequiz2'),
                    body: M.util.get_string('session_expired_body', 'mod_simplequiz2')
                }, modalElement).done(function(modal) {
                    modal.show();
                    // Redirect to home page after user closed the modal.
                    modal.getRoot().on('modal:hidden', function() {
                        location.href = M.cfg.wwwroot;
                    });
                    // Or redirect auto after 5 secondes.
                    setTimeout(function() {
                        location.href = M.cfg.wwwroot;
                    }, 5000);
                });
            }
            // Show all other errors response code.
            else if (response.ok === false) {
                ModalFactory.create({
                    title: `Error ${response.status}`,
                    body: "<p>Check browser console.</p>"
                }, modalElement).done(function(modal) {
                    modal.show();
                });
            }
        },
    };

    // Add object to window to be called outside require.
    window.modSimplequizView = modSimplequizView;
    return modSimplequizView;
});