<?php
/**
 * Псевдонимы методов классов PagePrinter и PageConstructor,
 * для использования в шаблонах
 */

/**
 * Распечатывает title страницы
 */
function lpm_print_title()
{
    PagePrinter::title();
}

/**
* Распечатывает заголовок сайта
*/
function lpm_print_site_title()
{
    PagePrinter::siteTitle();
}

/**
* Распечатывает подзаголовок сайта
*/
function lpm_print_site_subtitle()
{
    PagePrinter::siteSubTitle();
}

/**
 * Распечатывает img логотип сайта
 */
function lpm_print_logo_img()
{
    PagePrinter::logoImg();
}

/**
 * Распечатывает версию
 */
function lpm_print_version()
{
    PagePrinter::version();
}

/**
 * Распечатывает копирайты
 */
function lpm_print_copyrights()
{
    PagePrinter::copyrights();
}

/**
 * Распечатывает название продукта
 */
function lpm_print_product_name()
{
    PagePrinter::productName();
}

/**
 * Распечатывает основной стиль
 */
function lpm_print_css_links()
{
    PagePrinter::cssLinks();
}

/**
 * Распечатывает ссылки на js файлы
 */
function lpm_print_scripts()
{
    PagePrinter::jsScripts();
}

/**
 * Распечатывает Open Graph мету.
 */
function lpm_print_open_graph_meta()
{
    PagePrinter::openGraphMeta();
}

/**
 * Выводит список пользователей
 */
function lpm_print_users_list()
{
    return PagePrinter::usersList();
}


/**
 * Распечатывает заголовок страницы
 */
function lpm_print_header()
{
    PagePrinter::header();
}

/**
 * Распечатывает текущие ошибки
 */
function lpm_print_errors()
{
    PagePrinter::errors();
}

/**
 * Распечатывает основной контент странциы
 */
function lpm_print_page_content()
{
    PagePrinter::pageContent();
}

/**
* Распечатывает задачи
*/
function lpm_print_issues($list)
{
    return PagePrinter::issues($list);
}

/**
* Распечатывает форму добавления/редактирования задачи для текущего проекта
*/
function lpm_print_issue_form($project, $issue = null, $input = null)
{
    return PagePrinter::issueForm($project, $issue, $input);
}

/**
* Распечатывает отображение отдельного комментария.
*/
function lpm_print_comment(Comment $comment)
{
    return PagePrinter::comment($comment);
}

/**
 * Распечатывает текст комментария.
 * @param string $htmlText Форматированный текст для отображения.
 */
function lpm_print_comment_text($htmlText)
{
    return PagePrinter::commentText($htmlText);
}

/**
 * Распечатывает поле ввода текста комментария.
 * @param string $id Идентификатор html элемента.
 */
function lpm_print_comment_input_text($id)
{
    return PagePrinter::commentInputText($id);
}

/**
* Распечатывает задачу
*/
function lpm_print_issue_view()
{
    return PagePrinter::issueView();
}

/**
 * Возвращает JS строку, представляющую объект.
 */
function lpm_get_js_object($data)
{
    return PagePrinter::toJSObject($data);
}

/**
 * Распечатывает JS скрипт с назначением объекта
 * в указанную JS переменную.
 */
function lpm_print_js_object($name, $data, $addScriptTags = true, $defineLet = true)
{
    return PagePrinter::printJSObject($name, $data, $addScriptTags, $defineLet);
}

/**
 * Распечатывает переменную из параметров POST.
 * Если переменной нет - то пустую строку
 */
function lpm_print_post_var($var, $default = '')
{
    return PagePrinter::postVar($var, $default);
}

/**
 * Распечатывает форму выбора пользователей.
 */
function lpm_print_users_chooser()
{
    return PagePrinter::usersChooser();
}

/**
 * Распечатывает список видео.
 */
function lpm_print_video_list($videoLinks)
{
    return PagePrinter::videoList($videoLinks);
}

/**
 * Распечатывает вывод конкретного видео.
 */
function lpm_print_video_item($video)
{
    return PagePrinter::videoItem($video);
}

/**
 * Распечатывает список прикрепленных изобаржений.
 */
function lpm_print_image_list($imageLinks)
{
    return PagePrinter::imageList($imageLinks);
}

/**
 * Распечатывает вывод конкретного прикрепленного изображения.
 */
function lpm_print_image_item($image)
{
    return PagePrinter::imageItem($image);
}

/**
 * Распечатывает форму экспорта задач в Excel.
 */
function lpm_print_issues_export_to_excel()
{
    return PagePrinter::issuesExportToExcel();
}

/**
 * Распечатывает вывод таблици Scrum доски.
 * @param $stickers
 * @param bool $addProjectName
 * @param bool $addClearBoard
 */
function lpm_print_table_scrum_board($stickers, $addProjectName = false, $addClearBoard = false)
{
    return PagePrinter::tableScrumBoard($stickers, $addProjectName, $addClearBoard);
}


/**
 * Распечатывает элемент исполнителя задачи в стикере на Scrum доске.
 * @param $member
 */
function lpm_print_table_scrum_board_issue_member(User $member)
{
    return PagePrinter::tableScrumBoardIssueMember($member);
}



/**
*   Возвращает текущую страницу
*/
function lpm_get_current_page()
{
    return PageConstructor::getCurrentPage();
}

/**
 * Возвращает url приложения
 * @return string
 */
function lpm_get_site_url()
{
    return PageConstructor::getSiteURL();
}

/**
 * Возвращает url базовай текущей страницы
 * @return string
 */
function lpm_get_base_page_url()
{
    return PageConstructor::getBasePageURL();
}

/**
 * Возвращает массив ссылок для главного меню
 * @return array
 */
function lpm_get_main_menu()
{
    return PageConstructor::getMainMenu();
}

/**
 * Возвращает массив ссылок для подменю страницы
 * @return array
 */
function lpm_get_sub_menu()
{
    return PageConstructor::getSubMenu();
}

/**
 * Возвращает массив ссылок для меню пользователя
 * @return array
 */
function lpm_get_user_menu()
{
    return PageConstructor::getUserMenu();
}

/**
 * Возвращает список проектов
 */
function lpm_get_projects_list($achive = false)
{
    return PageConstructor::getProjectsList($achive);
}

/**
 * Возвращает список задач для текущего проекта
 */
function lpm_get_issues_list()
{
    return PageConstructor::getIssuesList();
}

/**
* Возвращает список сотрудников
*/
function lpm_get_workers_list()
{
    return PageConstructor::getWorkersList();
}

/**
 * Возвращает текущий проект
 */
function lpm_get_project()
{
    return PageConstructor::getProject();
}

/**
 * Возвращает список участников проекта
 */
function lpm_get_project_members()
{
    return PageConstructor::getProjectMembers();
}

/**
 * Возврашает тестера проекта
 */
function lpm_get_project_tester()
{
    return PageConstructor::getProjectTester();
}

/**
 * Возвращает список меток для задачи.
 */
function lpm_get_issue_labels()
{
    return PageConstructor::getIssueLabels();
}

/**
 * Возвращает список пользователей
 */
function lpm_get_users_list()
{
    return PageConstructor::getUsersList();
}
/**
 * Возвращает список пользователей
 */
function lpm_get_user_issues()
{
    return PageConstructor::getUserIssues();
}
/**
 * Возвращает список пользователей для выбора
 */
function lpm_get_users_choose_list()
{
    return PageConstructor::getUsersChooseList();
}
/**
* Возвращает список пользователей для добавления в работники
*/
function lpm_get_add_worker_list()
{
    return PageConstructor::getAddWorkerList();
}
/**
* Возвращает массив ссылок-дат
*/
function lpm_get_date_links()
{
    return PageConstructor::getDateLinks();
}
/**
* Возвращает массив ссылок-недель
*/
function lpm_get_week_links()
{
    return PageConstructor::getWeekLinks();
}
/**
* Возвращает массив дней текущей недели
*/
function lpm_get_week_dates()
{
    return PageConstructor::getWeekDates();
}
/**
* Возвращает массив работников со статистикой по неделе
*/
function lpm_get_week_stat()
{
    return PageConstructor::getWeekStat();
}
/**
 * Возвращает текущего пользователя
 */
function lpm_get_user()
{
    return PageConstructor::getUser();
}
/**
 * Определяет, может ли текущий пользователь создавать проекты
 */
function lpm_can_create_project()
{
    return PageConstructor::canCreateProject();
}
/**
 * Определяет, является ли пользователь модератором
 */
function lpm_is_moderator()
{
    return PageConstructor::isModerator();
}

/**
 * Определяет, авторизован ли в данный момент пользователь
 */
function lpm_is_auth()
{
    return PageConstructor::isAuth();
}

/**
 * Возвращает текущие ошибки и очищает список
 */
function lpm_get_errors()
{
    return PageConstructor::getErrors();
}
/**
 * Проверяет кто удаляет комментарий.
 */
function lpm_check_delete_comment($authorId, $commentId)
{
    return PageConstructor::checkDeleteComment($authorId, $commentId);
}
