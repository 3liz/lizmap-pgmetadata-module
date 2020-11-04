<?php
/**
 * @author    Pierre DRILLIN
 * @copyright 2020 3liz
 *
 * @see      https://3liz.com
 *
 * @license    Mozilla Public Licence
 */
class search
{
    protected $sql = array(
        'check_dataset' => "SELECT tablename FROM pg_tables WHERE schemaname = 'pgmetadata' AND tablename = 'dataset'",
        'get_html' => 'SELECT pgmetadata.get_dataset_item_html_content($1, $2) AS html',
    );

    protected function getSql($option)
    {
        if (isset($this->sql[$option])) {
            return $this->sql[$option];
        }

        return null;
    }

    public function query($sql, $filterParams, $profile = 'pgmetadata')
    {
        $cnx = jDb::getConnection($profile);
        $resultset = $cnx->prepare($sql);
        if (empty($filterParams)) {
            $resultset->execute();
        } else {
            $resultset->execute($filterParams);
        }

        return $resultset;
    }

    /**
     * Get data from the SQL query.
     *
     * @param mixed $profile
     * @param mixed $filterParams
     * @param mixed $option
     */
    public function getData($profile, $filterParams, $option)
    {
        // Run query
        $sql = $this->getSql($option);
        if (!$sql) {
            return null;
        }

        return $this->query($sql, $filterParams, $profile);
    }
}
