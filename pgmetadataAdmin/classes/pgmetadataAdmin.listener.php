<?php
/**
 * @author    Laurent Jouanneau
 * @copyright 2022 3liz
 *
 * @see      https://3liz.com
 *
 * @license    Mozilla Public Licence
 */
class pgmetadataAdminListener extends jEventListener
{
    public function onmasteradminGetMenuContent($event)
    {
        if (jAcl2::check('lizmap.admin.access')) {
            $bloc = new masterAdminMenuItem(
                'pgmetadataAdmin',
                jLocale::get("pgmetadataAdmin~admin.menu.item.label"),
                '',
                50);

            $bloc->childItems[] = new masterAdminMenuItem(
                'pgmetadata_config',
                jLocale::get("pgmetadataAdmin~admin.menu.configuration.label"),
                jUrl::get('pgmetadataAdmin~pgmdadmin:index'), 122
            );

            // Add the bloc
            $event->add($bloc);
        }
    }

}
