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

    /**
     * @throws DCatSupportException
     */
    public function __construct()
    {
        // Check pgmetadata needed view exists in profile 'pgmetadata'
        // Else try with default
        $ok = false;
        $profiles = array('pgmetadata', '');
        $checker = new RDFDCatChecker();

        foreach ($profiles as $profile) {
            try {
                $result = $checker->checkDCatSupport($profile);
                if ($result == $checker::STATUS_OK) {
                    $ok = true;
                    $this->dbProfile = $profile;

                    break;
                }
            } catch (DCatSupportException $e) {
                // ignore connection errors
            }
        }
        if (!$ok) {
            throw new DCatSupportException('Schema pgmetadata not found');
        }
    }

    public function getProfile()
    {
        return $this->dbProfile;
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
