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
        'check_pgmetadata_installed' => "SELECT tablename FROM pg_tables WHERE schemaname = 'pgmetadata' AND tablename = 'dataset'",
        'get_translated_locale_columns' => "SELECT column_name FROM information_schema.columns WHERE table_schema = 'pgmetadata' AND table_name = 'glossary' AND column_name LIKE 'label_%';",
        'get_dataset_html_content' => 'SELECT pgmetadata.get_dataset_item_html_content($1, $2, $3) AS html',
        'get_dataset_html_content_default_locale' => 'SELECT pgmetadata.get_dataset_item_html_content($1, $2) AS html',
    );

    protected function getSql($option)
    {
        if (isset($this->sql[$option])) {
            return $this->sql[$option];
        }

        return null;
    }

    public function query($sql, $filterParams, $profile)
    {
        if ($profile) {
            $cnx = jDb::getConnection($profile);
        } else {
            // Default connection
            $cnx = jDb::getConnection();
        }

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
    public function getData($option='check_pgmetadata_installed', $filterParams=array(), $profile=null)
    {
        // Run query
        $sql = $this->getSql($option);
        if (!$sql) {
            return null;
        }

        try {
            $result = $this->query($sql, $filterParams, $profile);
        } catch (Exception $e) {
            return array('status' => 'error', 'message' => 'Error at the query concerning '.$option);
        }

        return $result->fetchAll();
    }
}
