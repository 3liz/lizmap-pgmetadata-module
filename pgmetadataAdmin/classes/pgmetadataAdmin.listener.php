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
            $item = new masterAdminMenuItem(
                'pgmetadata_config',
                jLocale::get('pgmetadataAdmin~admin.menu.item.label'),
                jUrl::get('pgmetadataAdmin~pgmdadmin:index'),
                119,
                'lizmap'
            );

            // Add the bloc
            $event->add($item);


        }
    }

}
