/**
* @package   lizmap
* @subpackage pgmetadata
* @author    Pierre DRILLIN
* @copyright 2020 3liz
* @link      https://3liz.com
* @license    Mozilla Public Licence
*/
(function () {
    lizMap.events.on({
        'lizmapswitcheritemselected': function (evt) {
            if (evt.selected) {
                // We use the name as written in QGIS layer tree
                // to get the Lizmap layer properties
                // and then use the layer ID to fetch data
                const layername = evt.name;
                if (!(layername in lizMap.config.layers)) {
                    console.log(`No QGIS layer found for ${layername}`);

                    return true;
                }
                const layer = lizMap.config.layers[layername];

                // Get the metadata only for layers, not for groups
                if (layer.type != 'layer') {
                    return true;
                }

                // Get the metadata sheet
                get_metadata_html(layer.id);
            }
        }
    });

    function get_metadata_html(layername) {
        var options = {
            repository: lizUrls.params.repository,
            project: lizUrls.params.project,
            layername: layername
        };

        var url = pgmetadataConfig['urls']['index'];
        url = url + '?' + new URLSearchParams(options);
        fetch(url).then(function (response) {
            return response.json();
        }).then(function (formdata) {
            if (formdata) {
                if (formdata.status == 'error') {
                    console.log(formdata.message);
                } else {
                    set_metadata_in_subdock(formdata.html);
                }
            }
        });
    }

    /**
     * Check if Lizmap uses Bootstrap 5 (Lizmap 3.10 and above) or Bootstrap 2 (before).
     *
     * Bootstrap 5 is loaded as a script which defines the global object bootstrap.
     *
     * @returns {boolean} True for Bootstrap 5
     */
    function isBootstrap5() {
        return typeof window.bootstrap !== 'undefined' && typeof window.bootstrap.Tab !== 'undefined';
    }

    /**
     * Build the HTML of a tab link, for Bootstrap 5 or Bootstrap 2.
     *
     * @param {string}  target The id of the tab pane, like #pg-metadata-content
     * @param {string}  label  The label of the tab
     * @param {boolean} bs5    True for Bootstrap 5
     *
     * @returns {string} The HTML of the list item containing the link
     */
    function tab_link_html(target, label, bs5) {
        if (bs5) {
            return '<li class="nav-item"><a class="nav-link" href="' + target + '" data-bs-toggle="tab">'
                + label + '</a></li>';
        }
        return '<li><a href="' + target + '" data-toggle="tab">' + label + '</a></li>';
    }

    /**
     * Put the Lizmap layer information and the PgMetadata sheet in two tabs of the sub-dock.
     *
     * The list of the Lizmap layer information (style, opacity, export, etc.) is moved,
     * not copied, in the first tab, so that its buttons keep their event handlers.
     * The first tab is displayed.
     *
     * @param {string} html The HTML of the PgMetadata sheet of the layer
     */
    function set_metadata_in_subdock(html) {
        if (!html) {
            return;
        }
        const menuContent = document.querySelector('#sub-dock div.sub-metadata div.menu-content');
        if (!menuContent) {
            return;
        }
        const bs5 = isBootstrap5();

        // Tabs container, with the flat style of Lizmap 3.10 and above (see pgmetadata.css)
        let tabs = '';
        tabs += '<div id="pg-metadata-tabs" class="tabbable' + (bs5 ? ' pgmetadata-bs5' : '') + '">';
        tabs += '  <ul class="nav nav-tabs">';
        tabs += tab_link_html('#lizmap-layer-information', pgmetadataLocales['ui.button.lizmapLayerInformation'], bs5);
        tabs += tab_link_html('#pg-metadata-content', pgmetadataLocales['ui.button.pgmetadataHtml'], bs5);
        tabs += '  </ul>';
        tabs += '  <div class="tab-content">';
        tabs += '    <div class="tab-pane" id="lizmap-layer-information"></div>';
        tabs += '    <div class="tab-pane" id="pg-metadata-content">';
        tabs += html;
        tabs += '    </div>';
        tabs += '  </div>';
        tabs += '</div>';

        // Replace the previous tabs, if any, by the new ones
        const previousTabs = document.getElementById('pg-metadata-tabs');
        if (previousTabs) {
            previousTabs.remove();
        }
        menuContent.insertAdjacentHTML('afterbegin', tabs);

        // Move the Lizmap layer information list in the first tab:
        // with the class dl-vertical before Lizmap 3.10, without it after
        const layerInformation = menuContent.querySelector(':scope > dl');
        if (layerInformation) {
            document.getElementById('lizmap-layer-information').appendChild(layerInformation);
        }

        // Display the first tab
        const firstLink = menuContent.querySelector('#pg-metadata-tabs ul.nav-tabs a');
        if (bs5) {
            window.bootstrap.Tab.getOrCreateInstance(firstLink).show();
        } else {
            $(firstLink).tab('show');
        }
    }

    return {};
})();
