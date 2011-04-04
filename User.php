<?php
class User
{
    private $_Dbh;

    private $_userId;

    public function __construct($Dbh)
    {
        $this->_Dbh = $Dbh;
        $Namespace = new Zend_Session_Namespace('Zend_Auth');
        foreach ($Namespace as $index => $value) {
            $this->_userId = $value->user_id;
        }
    }

    /**
     * @param SortOrder $SortOrder
     */
    public function setSortOrderTo(SortOrder $SortOrder)
    {
        $sql = "
            REPLACE INTO
                user_prefs (sort_id, user_id)
            VALUES (?, ?)
        ";
        $stmt = $this->_Dbh->prepare($sql);
        $stmt->execute(array($SortOrder->getId(), $this->_userId));
    }

    /**
     * @return SortOrder
     */
    public function getSortOrder()
    {
        $sql = "
            SELECT
                sort_id
            FROM
                user_prefs
            WHERE user_id = ?
        ";
        $stmt = $this->_Dbh->prepare($sql);
        $stmt->execute(array($this->_userId));
        while ($row = $stmt->fetch()) {
            $sortId = $row['sort_id'];
        }

        if ($sortId == 1) {
            return new SortOrder_Snowball();
        } else {
            return new SortOrder_Default();
        }
    }
}
?>