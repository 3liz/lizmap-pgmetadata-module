<?php
/**
 * @author    Pierre DRILLIN
 * @author    Laurent Jouanneau
 * @copyright 2020-2022 3liz
 *
 * @see      https://3liz.com
 *
 * @license    Mozilla Public Licence (MPL)
 */
class serviceCtrl extends jController
{
    public function index()
    {
        $rep = $this->getResponse('json');

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

        // Get layer profile
        $profile = $layer->getDatasourceProfile();

        // Check if pgmetadata.dataset exists in the layer database
        $search = new \PgMetadata\HtmlExport($profile);

        $result = $search->checkPgMetadataInstalled();
        if ($result['status'] == 'error') {
            $rep->data = $result;

            return $rep;
        }

        if (empty($result['data'])) {
            $rep->data = array(
                'status' => 'error',
                'message' => 'Table pgmetadata.dataset does not exist in the layer database',
            );

            return $rep;
        }

        // Get currrent locale: en, fr, etc.
        $locale = jLocale::getCurrentLang();
        if (strlen($locale) != 2) {
            $locale = 'en';
        }

        // Check if the database glossary table contains locale label_XX columns
        $result = $search->getTranslatedLocaleColumns();

        // Check if getData doesn't return an error
        if ($result['status'] == 'error') {
            $rep->data = $result;

            return $rep;
        }

        // Check if no locales label_xx columns were returned
        // We then need to use the old get html function
        if (empty($result['data'])) {
            $result = $search->getDatasetHtmlContentDefaultLocale($schema, $tablename);
        } else {
            $result = $search->getDatasetHtmlContent($schema, $tablename, $locale);
        }

        // Check if getData doesn't return an error
        if ($result['status'] == 'error') {
            $rep->data = $result;

            return $rep;
        }

        // Check content and return
        if (empty($result['data'])) {
            $rep->data = array(
                'status' => 'error',
                'message' => 'No line returned by the query',
            );

            return $rep;
        }

        // Get content
        $feature = $result['data'][0];

        // Return  HTML
        $rep->data = array(
            'status' => 'success',
            'html' => $feature->html,
        );

        return $rep;
    }
}
