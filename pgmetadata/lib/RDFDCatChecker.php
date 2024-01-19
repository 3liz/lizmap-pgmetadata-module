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

class RDFDCatChecker extends AbstractSearch
{
    public const STATUS_OK = 0;
    public const STATUS_NO_PROFILE = 1;
    public const STATUS_NO_VIEW = 2;
    public const STATUS_BAD_PROFILE = 3;

    /**
     * Check if the view pgmetadata.v_dataset_as_dcat exists, using the given
     * database profile.
     *
     * @param string $profile
     *
     * @throws DCatSupportException
     *
     * @return int one of STATUS_* constants
     */
    public function checkDCatSupport($profile)
    {
        try {
            $profileParams = \jProfiles::get('jdb', $profile, true);
            if ($profileParams['driver'] != 'pgsql') {
                return self::STATUS_BAD_PROFILE;
            }
        } catch (\jException $e) {
            return self::STATUS_NO_PROFILE;
        }

        $result = $this->doQuery("
            SELECT viewname
            FROM pg_views
            WHERE schemaname = 'pgmetadata'
            AND viewname = 'v_dataset_as_dcat'
        ", array(), $profile, 'checkDCatSupport');

        if ($result['status'] != 'success') {
            // something have failed during the execution of the query
            throw new DCatSupportException($result['message'], $result['code']);
        }
        if (count($result['data'])) {
            return self::STATUS_OK;
        }

        return self::STATUS_NO_VIEW;
    }
}
