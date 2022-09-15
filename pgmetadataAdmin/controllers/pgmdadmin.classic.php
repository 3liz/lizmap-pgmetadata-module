<?php
/**
 * @author    Laurent Jouanneau
 * @copyright 2022 3liz
 *
 * @see      https://3liz.com
 *
 * @license    Mozilla Public Licence (MPL)
 */
class pgmdadminCtrl extends jController
{
    public function index()
    {
        $rep = $this->getResponse('html');
        $rep->body->assign('MAIN', 'Config pgmetadata');
        return $rep;
    }
}