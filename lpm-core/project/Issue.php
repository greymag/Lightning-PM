<?php
class Issue extends MembersInstance
{
	public static $currentIssue;
	private static $_listByProjects = array();
	
	protected static function loadList( $where ) {
		//return StreamObject::loadListDefault( $where, LPMTables::PROJECTS, __CLASS__ );
		$sql = "SELECT `%1\$s`.*, " .
					  //"IF(`%1\$s`.`status` <> 2, `%1\$s`.`priority`, 0) AS `realPriority`, " .
					  "IF(`%1\$s`.`status` = 2, `%1\$s`.`completedDate`, NULL) AS `realCompleted`, " .
					  "`%2\$s`.*, `%3\$s`.*, `%4\$s`.`uid` as `projectUID` " .
		         "FROM `%2\$s`, `%4\$s`, `%1\$s` " .
		         "LEFT JOIN `%3\$s` ON `%1\$s`.`id` = `%3\$s`.`issueId` " .
				"WHERE `%1\$s`.`projectId` = `%4\$s`.`id` " .
				  "AND `%1\$s`.`deleted` = '0'";
		if ($where != '') $sql  .= " AND " . $where;
		$sql .= " AND `%1\$s`.`authorId` = `%2\$s`.`userId` ".
				"ORDER BY `%1\$s`.`status` ASC, `realCompleted` DESC, `%1\$s`.`priority` DESC, `%1\$s`.`completeDate` ASC";
		return StreamObject::loadObjList(
					self::getDB(),  
					array( 
						$sql, 
						LPMTables::ISSUES, 
						LPMTables::USERS,
						LPMTables::ISSUE_COUNTERS,
						LPMTables::PROJECTS
					), 
					__CLASS__ 
			   );
	}
	
	public static function getListByProject( $projectId, $type = -1 ) {
		if (!isset( self::$_listByProjects[$projectId] )) {
			if (LightningEngine::getInstance()->isAuth()) {
				$where = "`%1\$s`.`projectId` = '" . $projectId . "'";
				if ($type != -1) $where .= "AND `%1\$s`.`type` = '" . $type . "'";
				self::$_listByProjects[$projectId] = self::loadList( $where );
			} else self::$_listByProjects[$projectId] = array();
		}
	
		return self::$_listByProjects[$projectId];
	}
	
	public static function getCurrentList() {
		/*foreach (self::$_listByProjects as $list) {
			return $list;
		}
		
		return array();*/
		//return Project::
		return Project::$currentProject != null 
					? self::getListByProject( Project::$currentProject->id )
					: array();
	}
	
	/**
	 * 
	 * @param float $issueId
	 * @return Issue
	 */
	public static function load( $issueId ) {
		return StreamObject::singleLoad( $issueId, __CLASS__, '', "%1\$s`.`id" );
	}
	
	public function updateCommentsCounter( $issueId ) {
		$sql = "INSERT INTO `%1\$s` (`issueId`, `commentsCount`) " .
									"VALUES ('" . $issueId . "', '1') " .
					   "ON DUPLICATE KEY UPDATE `commentsCount` = " . 
							"(SELECT COUNT(*) FROM `%2\$s` " .
							  "WHERE `%2\$s`.`instanceType` = '" . Issue::ITYPE_ISSUE . "' " .
								"AND `%2\$s`.`instanceId` = '" . $issueId . "')";
		Globals::getDBConnect()->queryt( $sql, LPMTables::ISSUE_COUNTERS, LPMTables::COMMENTS );
	} 
	
	const ITYPE_ISSUE      = 1;
	
	const TYPE_DEVELOP     = 0;
	const TYPE_BUG         = 1;
	const TYPE_SUPPORT     = 2;
	
	const STATUS_IN_WORK   = 0;
	const STATUS_WAIT      = 1;
	const STATUS_COMPLETED = 2;
	
	public $id            =  0;
	public $parentId      =  0;
	public $projectId     =  0;
	public $projectUID    =  '';
	public $name          = '';
	public $desc          = '';
	public $type          = -1;
	public $authorId      =  0;
	public $createDate    =  0;
	public $startDate     =  0;
	public $completeDate  =  0;
	public $completedDate =  0;
	public $priority      = 49;
	public $status        = -1;
	public $commentsCount = 0;
	
	/**
	 * 
	 * @var User
	 */
	public $author;
	
	//public $baseURL = '';
	
	function __construct( $id = 0 )
	{
		parent::__construct();
		
		$this->id = $id;
		
		$this->_typeConverter->addFloatVars( 
			'id', 'parentId', 'authorId', 'type', 'status', 'commentsCount' 
		);
		$this->_typeConverter->addIntVars( 'priority' );
		$this->addDateTimeFields( 'createDate', 'startDate', 'completeDate', 'completedDate' );
		
		$this->author = new User();
	}
	
	public function checkEditPermit( $userId ) {
		if ($userId == $this->authorId) return true;
		
		// TODO проверку прав
		return true;
	}
	
	public function getID() {
		return $this->id;
	}
	
	/**
	 * относительно текущей страницы
	 */
	public function getURL4View() {
		//return $this->baseURL;
		$curPage = LightningEngine::getInstance()->getCurrentPage();
		return $curPage->getBaseUrl( ProjectPage::PUID_ISSUE, $this->id );
	}
	
	public function getPriorityStr() {
		if ($this->priority < 33) return 'низкий';
		else if ($this->priority < 66) return 'нормальный';
		else return 'высокий';
	}
	
	/**
	 * Чтобы этот метод корректно работал, необходимо, 
	 * чтобы был загружен uid проекта
	 */
	public function getConstURL() {
		if ($this->projectUID == '') return SITE_URL;
		else return Link::getUrlByUid( 
						ProjectPage::UID, 
						$this->projectUID,
						ProjectPage::PUID_ISSUE, 
						$this->id
					);
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDesc() {
		return nl2br( $this->desc );
	}
	
	public function isCompleted() {
		return $this->status == self::STATUS_COMPLETED;
	} 
	
	public function getShortDesc() {
		return parent::getRich( parent::getShort( $this->desc ) );
	}
	
	public function getCreateDate() {
		return self::getDateStr( $this->createDate );
	}
	
	public function getCompleteDate() {
		return self::getDateStr( $this->completeDate );
	}
	
	
	public function getCompleteDate4Input() {
		return self::getDate4Input( $this->completeDate );
	}
	
	public function getAuthorLinkedName() {
		return ($this->author) ? $this->author->getLinkedName() : '';
	}
	
	/*public function getMembersLinkedName() {
		if (!$this->_members) return '';
		$names = array();
		foreach ($this->_members as /*@var $member Member * /$member) {
			array_push( $names, $member->getLinkedName() );
		}
		
		return implode( ', ', $names );
	}*/
	
	public function getType() {
		switch ($this->type) {
			case self::TYPE_BUG     : return 'Ошибка';
			case self::TYPE_DEVELOP : return 'Разработка';
			case self::TYPE_SUPPORT : return 'Поддержка';
			default 			    : return '';
		}
	}
	
	public function getStatus() {
		switch ($this->status) {
			case self::STATUS_IN_WORK   : return 'В работе';
			case self::STATUS_WAIT      : return 'Ожидает';
			case self::STATUS_COMPLETED : return 'Завершена';
			default 			        : return '';
		}
	}
	
	public function parseData( $hash ) {
		if (!parent::parseData( $hash ) || !$this->author->parseData( $hash )) return false;
		else return true;		
	}
	
	protected function loadMembers() {
		$this->_members = Member::loadListByIssue( $this->id );
		if ($this->_members === false) 
			throw new Exception( 'Ошибка при загрузке списка исполнителей задачи' );
		return true;
	}		
}
?>