<?php

namespace Biz\UserLearnStatistics\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface DailyStatisticsDao extends GeneralDaoInterface
{
    public function findByIds($ids);

    public function updateStorageByIds($ids);
}