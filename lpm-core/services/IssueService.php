<?php
require_once( dirname( __FILE__ ) . '/../init.inc.php' );

class IssueService extends LPMBaseService
{
	public function complete( $issueId ) {
		// завершать задачу может создатель задачи,
		// исполнитель задачи или модератор
		if (!$issue = Issue::load( (float)$issueId )) return $this->error( 'Нет такой задачи' );
		
		if (!$issue->checkEditPermit( $this->_auth->getUserId() )) 
			return $this->error( 'У Вас нет прав на редактирование этой задачи' );
		
		$issue->status = Issue::STATUS_COMPLETED;
		// выставляем статус - завершена
		$sql = "update `%s` set `status` = '" . $issue->status . "', " .
							   "`completedDate` = '" . DateTimeUtils::mysqlDate() . "'" .
						   "where `id` = '" . $issue->id . "'";
		
		if (!$this->_db->queryt( $sql, LPMTables::ISSUES )) return $this->errorDBSave();

		Project::updateIssuesCount(  $issue->projectId );
		
		// отправка оповещений
		$members = $issue->getMemberIds();
		array_push( $members, $issue->authorId ); 
				
		EmailNotifier::getInstance()->sendMail2Allowed(
			'Завершена задача "' . $issue->name . '"', 
			$this->getUser()->getName() . ' отметил задачу "' .
			$issue->name .  '" как завершённую' . "\n" .
			'Просмотреть задачу можно по ссылке ' .	$issue->getConstURL(), 
			$members,
			EmailNotifier::PREF_ISSUE_STATE
		);
		
		//$this->add2Answer( 'issue', $issue->getClientObject() );
		$this->add2Answer( 'issue', $this->getIssue4Client( $issue ) );
		
		return $this->answer();
	}
	
	/**
	 * Восстанавливаем задачу
	 * @param float $issueId
	 */
	public function restore( $issueId ) {
		// востанавливать задачу может создатель задачи,
		// исполнитель задачи или модератор
		if (!$issue = Issue::load( (float)$issueId )) return $this->error( 'Нет такой задачи' );
	
		if (!$issue->checkEditPermit( $this->_auth->getUserId() ))
			return $this->error( 'У Вас нет прав на редактирование этой задачи' );
	
		$issue->status = Issue::STATUS_IN_WORK;
		// выставляем статус - завершена
		$sql = "update `%s` set `status` = '" . $issue->status . "' " .
						   "where `id` = '" . $issue->id . "'";
	
		if (!$this->_db->queryt( $sql, LPMTables::ISSUES )) return $this->errorDBSave();

		Project::updateIssuesCount(  $issue->projectId );
		
		// отправка оповещений
		$members = $issue->getMemberIds();
		array_push( $members, $issue->authorId );
		
		EmailNotifier::getInstance()->sendMail2Allowed(
			'Открыта задача "' . $issue->name . '"',
			$this->getUser()->getName() . ' заново открыл задачу "' .
			$issue->name .  '"' . "\n" .
			'Просмотреть задачу можно по ссылке ' .	$issue->getConstURL(), 
			$members,
			EmailNotifier::PREF_ISSUE_STATE
		);
		
		//$this->add2Answer( 'issue', $issue->getClientObject() );
		$this->add2Answer( 'issue', $this->getIssue4Client( $issue ) );
	
		return $this->answer();
	}
	
	/**
	 * Загружает информацию о задаче
	 * @param float $issueId
	 */
	public function load( $issueId ) {
		if (!$issue = Issue::load( (float)$issueId )) return $this->error( 'Нет такой задачи' );
		
		// TODO проверка на возможность просмотра
		
		/*$obj = $issue->getClientObject();
		$members = $issue->getMembers();
		$obj['members'] = array();

		foreach ($members as $member) {
			array_push( $obj['members'], $member->getClientObject() );
		}*/				
		
		$this->add2Answer( 'issue', $this->getIssue4Client( $issue ) );
		return $this->answer();
	}
	
	/**
	 * Удаляет задачу
	 * @param float $issueId
	 */
	public function remove( $issueId ) {
		$issueId = (float)$issueId;
		// удалять задачу может создатель задачи или модератор
		if (!$issue = Issue::load( (float)$issueId )) return $this->error( 'Нет такой задачи' );
		
		// TODO проверка прав
		//if (!$issue->checkEditPermit( $this->_auth->getUserId() ))
		//return $this->error( 'У Вас нет прав на редактирование этой задачи' );
		
		// отправка оповещений
		$members = $issue->getMemberIds();
		array_push( $members, $issue->authorId );
		
		EmailNotifier::getInstance()->sendMail2Allowed(
			'Удалена задача "' . $issue->name . '"', 
			$this->getUser()->getName() . ' удалил задачу "' . $issue->name .  '"', 
			$members,
			EmailNotifier::PREF_ISSUE_STATE
		);
		
		$sql = "update `%s` set `deleted` = '1' where `id` = '" . $issueId . "'";
		if (!$this->_db->queryt( $sql, LPMTables::ISSUES )) return $this->errorDBSave();

		Project::updateIssuesCount(  $issue->projectId );
		
		return $this->answer();
	}
	
	public function comment( $issueId, $text ) {
		$issueId = (float)$issueId;
		// удалять задачу может создатель задачи или модератор
		if (!$issue = Issue::load( (float)$issueId )) return $this->error( 'Нет такой задачи' );
		
		// TODO проверка прав
		//if (!$issue->check???Permit( $this->_auth->getUserId() ))
		//return $this->error( 'У Вас нет прав на комментировние задачи' );
		
		if (!$comment = $this->addComment( Issue::ITYPE_ISSUE, $issueId, $text )) 
			return $this->error();				
		
		// отправка оповещений
		$members = $issue->getMemberIds();
		array_push( $members, $issue->authorId );
		
		EmailNotifier::getInstance()->sendMail2Allowed(
			'Новый комментарий к задаче "' . $issue->name . '"',
			$this->getUser()->getName() . ' оставил комментарий к задаче "' .
			$issue->name .  '":' . "\n" .
			strip_tags( $comment->text ) . "\n\n" .
			'Просмотреть все комментарии можно по ссылке ' . $issue->getConstURL(),
			$members,
			EmailNotifier::PREF_ISSUE_COMMENT
		);
		
		// обновляем счетчик коментариев для задачи
		Issue::updateCommentsCounter( $issueId );
		
		$this->add2Answer( 'comment', $comment->getClientObject() );
		return $this->answer();
	}
	
	/**
	 * Загружает html информации о задаче
	 * @param float $issueId
	 */
	/*public function loadHTML( $issueId ) {
		if (!$issue = Issue::load( (float)$issueId )) return $this->error( 'Нет такой задачи' );
		
		$this->add2Answer( 'issue', $issue );
		return $this->answer();
	}*/
	
	protected function getIssue4Client( Issue $issue, $loadMembers = true ) {
		$obj = $issue->getClientObject();
		$members = $issue->getMembers();
		$obj['members'] = array();
		
		foreach ($members as $member) {
			array_push( $obj['members'], $member->getClientObject() );
		}
		
		return $obj;
	}
}
?>