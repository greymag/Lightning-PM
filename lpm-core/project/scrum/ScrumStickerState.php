<?php
/**
 * Состояние стикера на Scrum доске
 */
class ScrumStickerState extends \GMFramework\Enum {
	/**
	 * В общем бэклоге
	 */
	const BACKLOG = 0;
	/**
	 * В колонке на исполнение
	 */
	const TODO = 1;
	/**
	 * В колонке "В работе"
	 */
	const IN_PROGRESS = 2;
	/**
	 * В колонке "Тестировние" или "Проверка"
	 */
	const TESTING = 3;
	/**
	 * В колоке "Выполнено"
	 */
	const DONE = 4;
	/**
	 * Завершенная задача завершена и стикер убран с доски 
	 * (например по окончании спринта)
	 */
	const ARCHIVED = 5;
	/**
	 * Стикер убран (например задача больше не актуальна и была удалена)
	 */
	const DELETED = 6;

	/*private static $_statesOrder = [self::TODO, self::IN_PROGRESS, self::TESTING, self::DONE];

	public static function getNextState($state) {
		$index = array_search($state, self::$_statesOrder);
		return $index !== false && $index < count(self::$_statesOrder) ? 
			self::$_statesOrder[$index + 1] : false;
	}

	public static function getPrevState($state) {
		$index = array_search($state, self::$_statesOrder);
		return $index === false || $index === 0 ? 
			false : self::$_statesOrder[$index - 1];
	}*/
}