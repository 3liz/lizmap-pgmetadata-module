<?php
/**
 * @author    Pierre DRILLIN
 * @copyright 2020 3liz
 *
 * @see      https://3liz.com
 *
 * @license    All rights reserved
 */
class serviceCtrl extends jController
{
    public function index()
    {
        $rep = $this->getResponse('json');
        $result = null;
        $filterParams = array();

        // Get parameters
        $project = $this->param('project');
        $repository = $this->param('repository');
        $layername = $this->param('layername');

        // Check parameters
        if (!$project) {
            $rep->data = array('status' => 'error', 'message' => 'Project not found');

            return $rep;
        }
        if (!$repository) {
            $rep->data = array('status' => 'error', 'message' => 'Repository not found');

            return $rep;
        }
        if (!$layername) {
            $rep->data = array('status' => 'error', 'message' => 'Layer name not found');

            return $rep;
        }

        // Check project
        $p = lizmap::getProject($repository.'~'.$project);
        if (!$p) {
            $rep->data = array('status' => 'error', 'message' => 'A problem occured while loading the project with Lizmap');

            return $rep;
        }

        // Check the user can access this project
        if (!$p->checkAcl()) {
            $rep->data = array('status' => 'error', 'message' => jLocale::get('view~default.repository.access.denied'));

            return $rep;
        }

        // Get layer instance
        $l = $p->findLayerByAnyName($layername);
        if (!$l) {
            $rep->data = array('status' => 'error', 'message' => 'Layer '.$layername.' does not exist');

            return $rep;
        }
        $layer = $p->getLayer($l->id);

        // Check if layer is a PostgreSQL layer
        if (!($layer->getProvider() == 'postgres')) {
            $rep->data = array('status' => 'error', 'message' => 'Layer '.$layername.' is not a PostgreSQL layer');

            return $rep;
        }

        // Get schema and table names
        $layerParameters = $layer->getDatasourceParameters();
        $schema = $layerParameters->schema;
        $tablename = $layerParameters->tablename;
        if (empty($schema)) {
            $schema = 'public';
        }
        $filterParams[] = $schema;
        $filterParams[] = $tablename;

        // Get layer profile
        $profile = $layer->getDatasourceProfile();

        // Check if pgmetadata.dataset exists in the layer database
        $search = jClasses::getService('pgmetadata~search');

        $result = $search->getData('check_pgmetadata_installed', array(), $profile);
        if (isset($result['status'])) {
            $rep->data = $result;

            return $rep;
        }

        if (empty($result)) {
            $rep->data = array('status' => 'error', 'message' => 'Table pgmetadata.dataset does not exist in the layer database');

            return $rep;
        }

        // Get Locale for the html langage
        // Jelix sanitizes the locale. No need to validate the string given by jLocale
        $locale = jLocale::getCurrentLang();

        // Check if the database glossary table contains locale label_XX columns
        $result = $search->getData('get_translated_locale_columns', array(), $profile);

        // Check if getData doesn't return an error
        // If $result['status'] is define there is an error
        if (isset($result['status'])) {
            $rep->data = $result;
            return $rep;
        }

        // Check if no locales label_xx columns were returned
        // We then need to use the old get html function
        if (count($result) == 0) {
            $option = 'get_dataset_html_content_default_locale';
        } else {
            $option = 'get_dataset_html_content';
            $filterParams[] = $locale;
        }

        // Get metadata HTML content for the layer
        $result = $search->getData($option, $filterParams, $profile);

        // Check if getData doesn't return an error
        // If $result['status'] is define there is an error
        if (isset($result['status'])) {
            $rep->data = $result;

            return $rep;
        }

        // Check content and return
        if (count($result) == 0) {
            $rep->data = array('status' => 'error', 'message' => 'No line returned by the query');

            return $rep;
        }
        $feature = $result[0];

        // Return  HTML
        $rep->data = array('status' => 'success', 'html' => $feature->html);

        return $rep;
    }
}
