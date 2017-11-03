<?php
class LPMTables {
	/**
	* Таблица настроек
	* @var string
	*/
	const OPTIONS = 'options';
	/**
	 * Таблица проектов
	 * @var string
	 */
	const PROJECTS = 'projects';
	/**
	 * Таблица изображений
	 * @var string
	 */
	const IMAGES = 'images';
	/**
	 * Задачи
	 * @var string
	 */
	const ISSUES = 'issues';
	/**
	 * Счётчики для задач
	 * @var string
	 */
	const ISSUE_COUNTERS = 'issue_counters';	
	/**
	 * Стикеры для Scrum доски
	 * @var string
	 */
	const SCRUM_STICKER = 'scrum_sticker';
	/**
	 * Участия пользователей
	 * @var string
	 */
	const MEMBERS = 'members';	
	/**
	 * Теги для объектов
	 * @var string
	 */
	const TAGS = 'tags';
	/**
	 * Список существующих тегов
	 * @var string
	 */
	const TAGS_LIST = 'tags_list';
	/**
	 * Список работников для учёта времени
	 * @var string
	 */
	const WORKERS = 'workers';
	/**
	 * Записи о времени
	 * @var string
	 */
	const WORK_STUDY = 'work_study';
	/**
	 * Комментарии
	 * @var string
	 */
	const COMMENTS = 'comments';
	/**
	 * Данные авторизации
	 * @var string
	 */
	const USER_AUTH = 'user_auth';
	/**
	 * Таблица пользователей
	 * @var string
	 */
	const USERS = 'users';
	/**
	 * Таблица настроек пользователей
	 * @var string
	 */
	const USERS_PREF = 'users_pref';
    /**
     *  Записи о отправленных письмах для восстановления пароля
     */
    const RECOVERY_EMAILS = 'recovery_emails';
}
?>