<?php

namespace Codeception\Module;

/**
 * Additional methods for DB module
 *
 * Save this file as DbHelper.php in _support folder
 * Enable DbHelper in your suite.yml file
 * Execute `codeception build` to integrate this class in your codeception
 * Modified from: https://gist.github.com/agarzon/686e477949311ae215ce
 */
class DbHelper extends \Codeception\Module
{
    /**
     * Delete entries from $table where $criteria conditions
     * Use: $I->deleteFromDatabase('users', ['id' => '111111', 'banned' => 'yes']);
     *
     * @param  string $table    tablename
     * @param  array $criteria conditions. See seeInDatabase() method.
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function deleteFromDatabase($table, $criteria)
    {
        $dbh    = $this->getModule('Db')->_getDbh();
        $query  = "delete from `%s` where %s";
        $params = [];
        foreach ($criteria as $k => $v)
        {
            $params[] = "`$k` = '$v'";
        }
        $params = implode(' AND ', $params);
        $query  = sprintf($query, $table, $params);
        codecept_debug($query);
        $this->debugSection('Query', $query, json_encode($criteria));
        $sth = $dbh->prepare($query);
        return $sth->execute(array_values($criteria));
    }

    /**
     * Execute an arbitrary SQL query
     * Use: $I->executeOnDatabase('UPDATE `users` SET `email` = NULL WHERE `users`.`id` = 1; ');
     *
     * @param  string $sql query
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function executeOnDatabase($sql)
    {
        $dbh = $this->getModule('Db')->_getDbh();
        $this->debugSection('Query', $sql);
        $sth = $dbh->prepare($sql);
        return $sth->execute();
    }

    /**
     * Execute result of an arbitrary SQL SELECT query
     * Use: $I->selectFromDatabase('SELECT * FROM `users`;');
     *
     * @param  string $sql query
     * @return array results of query.
     */
    public function selectFromDatabase($sql)
    {
        $dbh = $this->getModule('Db')->_getDbh();
        $this->debugSection('Query', $sql);
        $sth = $dbh->prepare($sql);
        $sth->execute();
        return $sth->fetchAll();
    }
}
