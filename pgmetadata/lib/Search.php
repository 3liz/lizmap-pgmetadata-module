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

class Search
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

    public function checkDCatSupport($profile)
    {
        return $this->doQuery("
            SELECT viewname
            FROM pg_views
            WHERE schemaname = 'pgmetadata'
            AND viewname = 'v_dataset_as_dcat'
        ", array(), $profile, 'checkDCatSupport');
    }

    public function checkPgMetadataInstalled($profile)
    {
        return $this->doQuery("
            SELECT tablename
            FROM pg_tables
            WHERE schemaname = 'pgmetadata'
            AND tablename = 'dataset'
        ", array(), $profile, 'checkPgMetadataInstalled');
    }

    public function getTranslatedLocaleColumns($profile)
    {
        return $this->doQuery("
           SELECT column_name
            FROM information_schema.columns
            WHERE table_schema = 'pgmetadata'
            AND table_name = 'glossary'
            AND column_name LIKE 'label_%'
        ", array(), $profile, 'getTranslatedLocaleColumns');
    }

    public function getDatasetHtmlContent($schema, $tablename, $locale, $profile)
    {
        return $this->doQuery('SELECT pgmetadata.get_dataset_item_html_content($1, $2, $3) AS html', array($schema, $tablename, $locale), $profile, 'getDatasetHtmlContent');
    }

    public function getDatasetHtmlContentDefaultLocale($schema, $tablename, $profile)
    {
        return $this->doQuery('SELECT pgmetadata.get_dataset_item_html_content($1, $2) AS html', array($schema, $tablename), $profile, 'getDatasetHtmlContentDefaultLocale');
    }

    public function getRDFDcatCatalog($locale, $profile)
    {
        return $this->doQuery(
            'SELECT * FROM pgmetadata.get_datasets_as_dcat_xml($1) WHERE True',
            array($locale),
            $profile,
            'checkDCatSupport'
        );
    }

    public function getRDFDcatCatalogById($locale, $id, $profile)
    {
        return $this->doQuery(
            'SELECT *
            FROM pgmetadata.get_datasets_as_dcat_xml($1, ARRAY[$2::uuid])',
            array($locale, $id),
            $profile,
            'checkDCatSupport'
        );
    }

    public function getRDFDcatCatalogByQuery($locale, $query, $profile)
    {
        return $this->doQuery(
            "
            WITH u AS (
                SELECT Coalesce(array_agg(d.uid), ARRAY[]::uuid[]) AS uids
                FROM pgmetadata.dataset AS d
                WHERE True
                AND unaccent($2) ILIKE ANY (regexp_split_to_array(regexp_replace(unaccent(d.keywords), '( *)?,( *)?', ',', 'g'), ','))
            )
            SELECT dc.*
            FROM u,
            pgmetadata.get_datasets_as_dcat_xml($1, u.uids) AS dc
        ",
            array($locale, $query),
            $profile,
            'checkDCatSupport'
        );
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
