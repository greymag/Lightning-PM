<?php
/**
 * Архив scrum досок.
 */
class ScrumStickerSnapshot extends LPMBaseObject
{
    /**
     * Загружает список снепшотов по идентификатору проекта (вначале новые).
     * @param int $projectId
     * @return ScrumStickerSnapshot[]
     * @throws DBException
     * @throws Exception
     */
	public static function loadList($projectId) {
		$db = self::getDB();
        $projectId = (int) $projectId;

        // TODO: нужно ли ограничить как-то?
        // Выбираем (пока все) записи по переданному проекту
        $sql = <<<SQL
        SELECT * FROM `%1\$s` WHERE `%1\$s`.`pid` = '${projectId}'
        ORDER BY `%1\$s`.`created` DESC
SQL;

		return StreamObject::loadObjList($db, [$sql, LPMTables::SCRUM_SNAPSHOT_LIST], __CLASS__);
	}

    /**
     * Загружает данные снепшота по идентификатору
     * @param $snapshotId
     * @return ScrumStickerSnapshot|null
     * @throws DBException
     * @throws Exception
     */
//	public static function load($snapshotId)
//    {
//        $snapshotId = (int) $snapshotId;
//
//        $db = self::getDB();
//
//        /*$sql = <<<SQL
//		SELECT `s`.`issueId` `s_issueId`, `s`.`state` `s_state`, 
//			   'with_issue', `i`.*, `p`.`uid` as `projectUID`
//		  FROM `%1\$s` `s` 
//    INNER JOIN `%2\$s` `i` ON `s`.`issueId` = `i`.`id` 
//    INNER JOIN `%3\$s` `p` ON `i`.`projectId` = `p`.`id`
//     	 WHERE `s`.`issueId` = ${issueId} AND `i`.`deleted` = 0 
//SQL;*/
//        $sql = <<<SQL
//        SELECT * FROM `%1\$s` WHERE `%1\$s`.`sid` = '${$snapshotId}'
//SQL;
//
//        $list = StreamObject::loadObjList($db,
//            [$sql, LPMTables::SCRUM_STICKER, LPMTables::ISSUES, LPMTables::PROJECTS], __CLASS__);
//
//        return empty($list) ? null : $list[0];
//    }

	public static function __log($value){
		GMLog::getInstance()->logIt(
			GMLog::getInstance()->logsPath . 'cmx_log', $value);
	}

	/**
	 * Создает snapshot по текущему состоянию доски для переданного проекта.
     * @param int $projectId
     * @param $userId
     * @throws Exception
     */
	public static function createSnapshot($projectId, $userId) {
		// получаем список всех стикеров на текущей доске
		$stickers = ScrumSticker::loadList($projectId);

		$pid = $projectId;
		$created = DateTimeUtils::mysqlDate();
		$creatorId = $userId;

        $db = self::getDB();

        try {
            // начинаем транзакцию
            $db->begin_transaction();

            // запись о новом снепшоте
            $sql = <<<SQL
                INSERT INTO `%s` (`pid`, `creatorId`, `created`)
                VALUES ('${pid}', '${creatorId}', '${created}')
SQL;

            // если что-то пошло не так
            if (!$db->queryt($sql, LPMTables::SCRUM_SNAPSHOT_LIST))
                throw new Exception("Ошибка при сохранении нового снепшота");

            $sid = $db->insert_id;

            // добавляем всю необходимую информацию по снепшоте
            $sql = <<<SQL
                INSERT INTO `%s` (`sid`, `issue_uid`, `issue_pid`, `issue_name`, `issue_state`, `issue_sp`, `issue_priority`)
                VALUES ('${sid}', ?, ?, ?, ?, ?, ?)
SQL;

            // подготавливаем запрос для вставки данных о стикерах снепшота
            if (!$prepare = $db->preparet($sql, LPMTables::SCRUM_SNAPSHOT))
                throw new Exception("Ошибка при подготовке запроса.");

            foreach ($stickers as $sticker) {
                /* @var $sticker ScrumSticker */

                $issue = $sticker->getIssue();

                $issueUid = $sticker->issueId;
                $issuePid = $issue->idInProject;
                $issueName = $sticker->getName();
                $issueState = $sticker->state;
                $issueSP = $issue->hours;
                $issuePriority = $issue->priority;

                $prepare->bind_param('ddsisi', $issueUid, $issuePid, $issueName, $issueState, $issueSP, $issuePriority);

                if (!$prepare->execute())
                    throw new Exception("Ошибка при вставке данных стикера.");

                // TODO: нужно ли проверить, возможно нет участников?? или такого не может быть?
                // так же сохраняем пользователей
                if (!Member::saveMembers(LPMInstanceTypes::SNAPSHOT_ISSUE_MEMBERS, $issueUid, $issue->getMemberIds()))
                    throw new Exception("Ошибка при сохранении участников.");
            }

            // запрос больше не нужен
            $prepare->close();

            // вроде бы все ок -> завершае транзакцию
            $db->commit();
        }
        catch (Exception $ex) {
            // что-то пошло не так -> отменяем все изменения
            $db->rollback();

            throw $ex;
        }
	}

	/**
	 * Идентификатор snapshot-а.
	 * @var int
	 */
	public $id;
	/**
	 * Идентификатор проекта, для которого сделан snapshot.
	 * @var int
	 */
	public $pid;
	/**
	 * Дата создания snapshot-а.
	 * @var
	 */
	public $created;
	/**
	 * Идентификатор пользователя, создавшего snapshot.
	 * @var
	 */
	public $creatorId;

	function __construct($id = 0) {
		parent::__construct();

		$this->id = $id;

		$this->_typeConverter->addFloatVars('id', 'pid', 'creatorId');
		$this->addDateTimeFields('created');

		// TODO:
//		$this->addClientFields(
//			'id', 'parentId', 'idInProject', 'name', 'desc', 'type', 'authorId', 'createDate',
//			'completeDate','completedDate', 'startDate', 'priority', 'status' ,'commentsCount', 'hours'
//		);
	}

	/**
	 * Возвщает отображаемое имя стикера
	 * @return String
	 */
//	public function getName() {
//	    return $this->_issue === null ?
//	    	'Задача #' . $this->issueId : $this->_issue->getName();
//	}

	/**
	 * Возвращает объект задачи. Если не выставлен - будет загружен
	 * @return Issue
	 */
//	public function getIssue() {
//	    if ($this->_issue === null)
//	    	$this->_issue = Issue::load($this->issueId);
//	    return $this->_issue;
//	}

//	public function isOnBoard() {
//	    // return $this->state !== ScrumStickerState::BACKLOG;
//	    return ScrumStickerState::isActiveState($this->state);
//	}

	/**
	 * Стикер находится в колонке TO DO
	 * @return boolean 
	 */
//	public function isTodo() {
//	    return $this->state === ScrumStickerState::TODO;
//	}

	/**
	 * Стикер находится в колонке В работе
	 * @return boolean 
	 */
//	public function isInProgress() {
//	    return $this->state === ScrumStickerState::IN_PROGRESS;
//	}

	/**
	 * Стикер находится в колонке Тестируется
	 * @return boolean 
	 */
//	public function isTesting() {
//	    return $this->state === ScrumStickerState::TESTING;
//	}

	/**
	 * Стикер находится в колонке Выполнено
	 * @return boolean 
	 */
//	public function isDone() {
//	    return $this->state === ScrumStickerState::DONE;
//	}

	/*public function nextState() {
	    return ScrumStickerState::getNextState($this->state);
	}

	public function prevState() {
	    return ScrumStickerState::getPrevState($this->state);
	}*/

	public function loadStream($raw) {
//	    $data = [];
//	    foreach ($raw as $key => $value) {
//	    	if (strpos($key, 's_') === 0)
//	    		$data[mb_substr($key, 2)] = $value;
//	    }

	    parent::loadStream($raw);

//	    if (isset($raw['with_issue'])) {
//	    	if ($this->_issue === null)
//	    		$this->_issue = new Issue($this->issueId);
//	    	$this->_issue->loadStream($raw);
//	    }
	}
}