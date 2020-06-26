/**
 * Страница просмотра проекта (просмотр задач, добавлени задачи)
 */

// по открытию страницы сразу убираем форму регистрации
$(document).ready(
    function () {
        states.addState($("#projectView"));
        states.addState($("#projectView"), 'only-my', issuePage.showIssues4Me);
        states.addState($("#projectView"), 'last-created', issuePage.showLastCreated);
        states.addState($("#projectView"), 'by-user:#', issuePage.showIssuesByUser);
        states.addState($("#issueForm"), 'add-issue', issueForm.handleAddState);
        states.addState($("#issueForm"), 'copy-issue:#', issueForm.addIssueBy);
        states.addState($("#issueForm"), 'finished-issue:#', issueForm.finishedIssueBy);

        if (window.location.hash == '#issue-view')
            window.location.hash = '';

        states.updateView();
        if ($('#issueForm > div.validateError').html() != '') {
            $('#issueForm > div.validateError').show();
        }

        issuePage.updateStat();
    }
);