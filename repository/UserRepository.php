<?php

namespace SMU\Repository;

use SMU\Core\DataBaseHandler;
use SMU\Constants\UserConstants;

class UserRepository
{
    public static function findById(int $id)
    {
        $db   = DataBaseHandler::getInstance();
        $conn = $db->getConnection();        

        $stmt = $conn->prepare();
        
        $db->closeConnection();
    }
}
