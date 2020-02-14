<?php

require_once( dirname( __FILE__ ) . '/../init.inc.php' );

class ProjectService extends LPMBaseService
{
	public function addMembers( $projectId, $userIds ) {
		$projectId = (float)$projectId;
		
		if (!$userIds = $this->floatArr($userIds))
		    return $this->error('Неверные входные параметры');
		
		// проверяем права пользователя
		if (!$this->checkRole(User::ROLE_MODERATOR))
		    return $this->error('Недостаточно прав');
		
		// проверим, что существует такой проект
		if (!Project::loadById($projectId))
		    return $this->error('Нет такого проекта');
		
		// пытаемся добавить участников проекта
		$sql = "insert into `%s` ( `userId`, `instanceType`, `instanceId` ) values ";
		
		foreach ($userIds as $i => $userId) {
			if ($i > 0) $sql .= ', ';
			$sql .= "( '" . $userId . "', '" . LPMInstanceTypes::PROJECT . "', '" . $projectId . "' )";
		}
		
		if (!$this->_db->queryt($sql, LPMTables::MEMBERS)) {
			return ($this->_db->errno == 1062) ? $this->error( 'Участники уже добавлены' ) : $this->error();
		}
		
		if (!$members = Member::loadListByProject($projectId))
		    return $this->error();
		
		$this->add2Answer('members', $members);
		return $this->answer();
	}

	public function getSumOpenedIssuesHours($projectId)
	{
		// TODO проверить права доступа для этого проекта
		
	    $count = Project::sumHoursActiveIssues($projectId);

	    if ($count === false) return $this->error('Ошибка получения данных суммы часов');

	    $this->add2Answer('count', $count);

	    return $this->answer();
	}

    /**
     * Устанавливает указанного участника проекта в качестве мастера.
     * @param int $projectId Идентификатор проекта.
     * @param int $masterId  Идентификатор участника, которо надо сделать мастером.
     */
    public function setMaster($projectId, $masterId) {
        $projectId = (int)$projectId;
        $masterId  = (int)$masterId;

        // проверяем права пользователя
        if (!$this->checkRole(User::ROLE_MODERATOR))
            return $this->error('Недостаточно прав');

        $project = Project::loadById($projectId);
        if (!$project)
            return $this->error('Нет такого проекта');

        if ($project->masterId != $masterId) {
            $member = $project->getMember($masterId);

            if (!$member)
                return $this->error('Мастер не найден в участниках проекта');

            if (!Project::updateMaster($project->id, $masterId))
                return $this->error('Не удалось сохранить данные.');
        } else {
            return $this->error('fuck: '. $masterId);
        }

        return $this->answer();
    }

    /**
     * Удаляет мастера проекта.
     * @param  int $projectId Идентификатор проекта.
     */
    public function deleteMaster($projectId) {
        $projectId = (int)$projectId;

        // проверяем права пользователя
        if (!$this->checkRole(User::ROLE_MODERATOR))
            return $this->error('Недостаточно прав');

        $project = Project::loadById($projectId);
        if (!$project)
            return $this->error('Нет такого проекта');

        if ($project->masterId) {
            if (!Project::updateMaster($project->id, 0))
                return $this->error('Не удалось сохранить данные.');
        }

        return $this->answer();
    }

    public function addIssueMemberDefault($projectId, $memberByDefaultId) {
        $projectId = (int)$projectId;
        $memberByDefaultId = (int)$memberByDefaultId;

        // проверяем права пользователя
        if (!$this->checkRole(User::ROLE_MODERATOR ))
            return $this->error('Недостаточно прав');

        // проверим, что существует такой проект
        $project = Project::loadById($projectId);
        if (!$project)
            return $this->error('Нет такого проекта');

        $memberProject = $project->getMember($memberByDefaultId);
        if (!$memberProject)
            return $this->error('Исполнитель не найден в участниках проекта');

        $defaultIssueMemberId = $project->defaultIssueMemberId;

        if ($defaultIssueMemberId)
            return $this->error('Исполнитель уже назначен для проекта');

        if (!Project::updateIssueMemberDefault($projectId, $memberByDefaultId))
            return $this->error('Не удалось сохранить данные.');

        return $this->answer();
    }

    public function addTester( $projectId, $userId ){
        $projectId = (float)$projectId;
        $userId = (float)$userId;

        // проверяем права пользователя
        if (!$this->checkRole(User::ROLE_MODERATOR)) return $this->error('Недостаточно прав');

        // проверим, что существует такой проект
        if (!Project::loadById($projectId)) return $this->error('Нет такого проекта');

        if(empty($userId)) {
            return $this->error("Неверные входные параметры");
        }

        if (Member::hasMember(LPMInstanceTypes::TESTER_FOR_PROJECT, $projectId, $userId )) {
            return $this->error("Тестеровшик уже добавлен");
        }

        Member::saveProjectForTester($projectId, $userId);

        $this->add2Answer("projectId", $projectId);
        $this->add2Answer("userId", $userId);

        return $this->answer();
    }

    public function deleteTester($projectId) {
        $projectId = (int)$projectId;

        // проверяем права пользователя
        if (!$this->checkRole(User::ROLE_MODERATOR)) return $this->error('Недостаточно прав');


        if(!Member::deleteMembers(LPMInstanceTypes::TESTER_FOR_PROJECT, $projectId))
            return $this->error("Ошибка удаления тестера.");

        return $this->answer();
    }

    public function deleteMemberDefault ($projectId) {
        $projectId = (int)$projectId;
        // проверяем права пользователя
        if (!$this->checkRole(User::ROLE_MODERATOR)) return $this->error('Недостаточно прав');

        // проверим, что существует такой проект
        if (!Project::loadById($projectId)) return $this->error('Нет такого проекта');

        $defaultIssueMemberId = Project::loadById($projectId)->defaultIssueMemberId;

        if (!$defaultIssueMemberId) {
            return $this->error('Исполнитель не назначен для проекта');
        }

        $result = Project::deleteMemberDefault($projectId);

        if (!$result) {
            return $this->error('Ошибка удаления.');
        }

         $this->add2Answer('$result', $result);

        return $this->answer();
    }

	public function setProjectSettings($projectId, $scrum, $slackNotifyChannel) {
        $projectId = (int)$projectId;
        $slackNotifyChannel = (string)$slackNotifyChannel;

        if ($scrum !== 0 and $scrum !== 1) {
            return $this->error('Неверные входные параметры');
        }

        $scrum = (bool)$scrum;

        // проверяем права пользователя
        if (!$this->checkRole(User::ROLE_MODERATOR)) return $this->error('Недостаточно прав');

        // проверим, что существует такой проект
        if (!Project::loadById($projectId)) return $this->error('Проект не найден');

        $result = Project::updateProjectSettings($projectId, $scrum, $slackNotifyChannel);

        if (!$result) return $this->error('Ошибка обновления таблицы');

        $this->add2Answer('projectId', $projectId);
        $this->add2Answer('scrum', $scrum);
        $this->add2Answer('slackNotifyChannel', $slackNotifyChannel);

        return $this->answer();
    }
}
