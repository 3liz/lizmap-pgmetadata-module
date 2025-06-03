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

class HtmlExport extends AbstractSearch
{
    /**
     * @var string the jDb profile to use
     */
    protected $dbProfile;

    public function __construct($dbProfile)
    {
        $this->dbProfile = $dbProfile;
    }

    public function checkPgMetadataInstalled()
    {
        return $this->doQuery("
            SELECT tablename
            FROM pg_tables
            WHERE schemaname = 'pgmetadata'
            AND tablename = 'dataset'
        ", array(), $this->dbProfile, 'checkPgMetadataInstalled');
    }

    public function getTranslatedLocaleColumns()
    {
        return $this->doQuery("
           SELECT column_name
            FROM information_schema.columns
            WHERE table_schema = 'pgmetadata'
            AND table_name = 'glossary'
            AND column_name LIKE 'label_%'
        ", array(), $this->dbProfile, 'getTranslatedLocaleColumns');
    }

    public function getDatasetHtmlContent($schema, $tablename, $locale)
    {
        return $this->doQuery(
            'SELECT pgmetadata.get_dataset_item_html_content($1, $2, $3) AS html',
            array($schema, $tablename, $locale),
            $this->dbProfile,
            'getDatasetHtmlContent'
        );
    }

    public function getDatasetHtmlContentDefaultLocale($schema, $tablename)
    {
        return $this->doQuery(
            'SELECT pgmetadata.get_dataset_item_html_content($1, $2) AS html',
            array($schema, $tablename),
            $this->dbProfile,
            'getDatasetHtmlContentDefaultLocale'
        );
    }
}
