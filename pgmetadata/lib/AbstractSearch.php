<?php
/**
 * @author    Pierre DRILLIN
 * @author    Laurent Jouanneau
 * @copyright 2020-2022 3liz
 *
 * @see      https://3liz.com
 *
 * @license    Mozilla Public Licence
 */

namespace PgMetadata;

abstract class AbstractSearch
{
    protected function query($sql, $filterParams, $profile)
    {
        if ($profile) {
            $cnx = \jDb::getConnection($profile);
        } else {
            // Default connection
            $cnx = \jDb::getConnection();
        }

        $resultset = $cnx->prepare($sql);
        if (empty($filterParams)) {
            $resultset->execute();
        } else {
            $resultset->execute($filterParams);
        }

        return $resultset;
    }

    protected function doQuery($sql, $filterParams, $profile, $queryName)
    {
        try {
            $result = $this->query($sql, $filterParams, $profile);
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'message' => 'Error at the query concerning '.$queryName,
            );
        }

        if (!$result) {
            return array(
                'status' => 'error',
                'message' => 'Error at the query concerning '.$queryName,
            );
        }

        return array(
            'status' => 'success',
            'data' => $result->fetchAll(),
        );
    }
}
