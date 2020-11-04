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
        jLog::log(json_encode($layerParameters), 'error');
        if (empty($schema)) {
            $schema = 'public';
        }
        $filterParams[] = $schema;
        $filterParams[] = $tablename;

        // Get layer profile
        $profile = $layer->getDatasourceProfile();

        // Check if pgmetadata.dataset exists in the layer database
        $autocomplete = jClasses::getService('pgmetadata~search');

        try {
            $result = $autocomplete->getData($profile, array(), 'check_dataset');
            $fetch = $result->fetchAll();
            if (empty($fetch)) {
                $rep->data = array('status' => 'error', 'message' => 'Table pgmetadata.dataset does not exist in the layer database');

                return $rep;
            }
        } catch (Exception $e) {
            $rep->data = array('status' => 'error', 'message' => 'Table pgmetadata.dataset does not exist in the layer database');

            return $rep;
        }

        // Get metadata HTML content for the layer
        try {
            $result = $autocomplete->getData($profile, $filterParams, 'get_html');
        } catch (Exception $e) {
            $rep->data = array('status' => 'error', 'message' => 'Impossible to generate the Html to '.$layername);

            return $rep;
        }

        // Check content and return
        $fetch = $result->fetchAll();
        if (count($fetch) == 0) {
            $rep->data = array('status' => 'error', 'message' => 'No line returned by the query');

            return $rep;
        }
        $feature = $fetch[0];

        // Return  HTML
        $rep->data = array('status' => 'success', 'html' => $feature->html);

        return $rep;
    }
}
