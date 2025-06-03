<?php

use PgMetadata\DCatSupportException;
use PgMetadata\RDFDCat;

/**
 * @author    Michaël DOUCHIN
 * @author    Laurent Jouanneau
 * @copyright 2020-2022 3liz
 *
 * @see      https://3liz.com
 *
 * @license    Mozilla Public Licence (MPL)
 */
class dcatCtrl extends jController
{
    protected $blockSqlWords = array(
        ';',
        'select',
        'delete',
        'insert',
        'update',
        'drop',
        'alter',
        '--',
        'truncate',
        'vacuum',
        'create',
    );

    /**
     * Check if a given string is a valid UUID.
     *
     * @param string $uuid The string to check
     *
     * @return bool
     */
    private function isValidUuid($uuid)
    {
        if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a given string is a search item.
     *
     * @param mixed $query
     *
     * @return bool
     */
    private function isValidQueryString($query)
    {
        // Must contain only alphanumeric chars
        if (!preg_match('/^([\p{L}a-zA-Z0-9 \-]*)$/ui', $query)) {
            return false;
        }

        // Must not contains any SQL block words
        $query = preg_replace('/ +/', ' ', $query);
        foreach (explode(' ', $query) as $word) {
            if (preg_match('#'.implode('|', $this->blockSqlWords).'#i', trim($word))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Main entry point for DCAT RDF catalog.
     */
    public function index()
    {
        $rep = $this->getResponse('xml');

        // Generate empty content from template
        $tpl = new jTpl();
        $assign = array();
        $locale = jLocale::getCurrentLang();
        $assign['locale'] = $locale;
        $assign['content'] = '';
        $tpl->assign($assign);
        $empty_content = $tpl->fetch('pgmetadata~dcat_catalog');

        try {
            $search = new RDFDCat();
        } catch (DCatSupportException $e) {
            $rep->content = $empty_content;

            return $rep;
        }

        // Get current locale: en, fr, etc.
        $locale = jLocale::getCurrentLang();
        if (strlen($locale) != 2) {
            $locale = 'en';
        }

        // If id is passed as parameter, filter by this UUID
        $id = trim($this->param('id', ''));

        if (!empty($id)) {
            if ($this->isValidUuid($id)) {
                $result = $search->getRDFDcatCatalogById($locale, $id);
            } else {
                $rep->content = $empty_content;

                return $rep;
            }
        } else {
            // If query string is passed
            $query = trim($this->param('q', ''));

            // Must not be empty
            if (!empty($query)) {
                if (!$this->isValidQueryString($query)) {
                    // We use a fake UID to get no data
                    $fake_id = 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11';
                    $result = $search->getRDFDcatCatalogById($locale, $fake_id);
                } else {
                    $result = $search->getRDFDcatCatalogByQuery($locale, $query);
                }
            } else {
                $result = $search->getRDFDcatCatalog($locale);
            }
        }

        // Check if getData doesn't return an error
        if ($result['status'] == 'error') {
            $rep->content = $empty_content;

            return $rep;
        }
        $datasets = $result['data'];
        $content = '';

        foreach ($datasets as $dataset) {
            $dataset_url = jUrl::getFull(
                'pgmetadata~dcat:index',
                array('id' => $dataset->uid)
            );
            $content .= str_replace(
                '<dcat:Dataset>',
                '<dcat:Dataset rdf:about="'.$dataset_url.'">',
                $dataset->dataset
            );
        }

        $assign['content'] = $content;
        $tpl->assign($assign);
        $rep->content = $tpl->fetch('pgmetadata~dcat_catalog');

        // Default xml response content-type is: text/xml;charset=UTF-8
        // Since some harversters need application/xml, override default
        $rep->addHttpHeader('Content-Type', 'application/xml;charset=UTF-8');

        return $rep;
    }
}
