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

class RDFDCat extends AbstractSearch
{
    /**
     * @var string the jDb profile to use
     */
    protected $dbProfile;

    public function __construct()
    {
        // Check pgmetadata needed view exists in profile 'pgmetadata'
        // Else try with defaut
        $ok = false;
        $profiles = array('pgmetadata', null);
        foreach ($profiles as $profile) {
            $result = $this->checkDCatSupport($profile);
            if ($result['status'] == 'success' && !empty($result['status'])) {
                $ok = true;
                $this->dbProfile = $profile;

                break;
            }
        }
        if (!$ok) {
            throw new DCatSupportException('Schema pgmetadata not found');
        }
    }

    protected function checkDCatSupport($profile)
    {
        return $this->doQuery("
            SELECT viewname
            FROM pg_views
            WHERE schemaname = 'pgmetadata'
            AND viewname = 'v_dataset_as_dcat'
        ", array(), $profile, 'checkDCatSupport');
    }

    public function getRDFDcatCatalog($locale)
    {
        return $this->doQuery(
            'SELECT * FROM pgmetadata.get_datasets_as_dcat_xml($1) WHERE True',
            array($locale),
            $this->dbProfile,
            'checkDCatSupport'
        );
    }

    public function getRDFDcatCatalogById($locale, $id)
    {
        return $this->doQuery(
            'SELECT *
            FROM pgmetadata.get_datasets_as_dcat_xml($1, ARRAY[$2::uuid])',
            array($locale, $id),
            $this->dbProfile,
            'checkDCatSupport'
        );
    }

    public function getRDFDcatCatalogByQuery($locale, $query)
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
            $this->dbProfile,
            'checkDCatSupport'
        );
    }
}
