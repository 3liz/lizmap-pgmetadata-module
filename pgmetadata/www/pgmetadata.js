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

    function set_metadata_in_subdock(html) {
        if (html) {
            // Get initial Lizmap layer information content
            let layer_information = $('#sub-dock div.sub-metadata div.menu-content dl.dl-vertical').html();
            layer_information = ''+layer_information+'';

            // Remove original Lizmap layer information
            $('#sub-dock div.sub-metadata div.menu-content dl.dl-vertical').remove();

            // Create bootstrap v2.3 tabs
            let tabs = '';
            tabs += '<div id="pg-metadata-tabs" class="tabbable">';
            tabs += '  <ul class="nav nav-tabs">';
            tabs += '    <li><a href="#lizmap-layer-information" data-toggle="tab">';
            tabs += pgmetadataLocales['ui.button.lizmapLayerInformation'] + '</a></li>';
            tabs += '    <li class="active"><a href="#pg-metadata-content" data-toggle="tab">';
            tabs += pgmetadataLocales['ui.button.pgmetadataHtml'] + '</a></li>';
            tabs += '  </ul>';
            tabs += '  <div class="tab-content">';
            tabs += '    <div class="tab-pane" id="lizmap-layer-information">';
            tabs += layer_information;
            tabs += '    </div>';
            tabs += '    <div class="tab-pane active" id="pg-metadata-content">';
            tabs += html;
            tabs += '    </div>';
            tabs += '  </div>';
            tabs += '</div>';

            // Add tabs in #sub-dock
            $('#pg-metadata-tabs').remove();
            document.querySelector('#sub-dock div.sub-metadata div.menu-content').insertAdjacentHTML('afterbegin', tabs);

            // Click on first tab
            $('#sub-dock div.sub-metadata div.menu-content ul li a:first').click();


        }
    }

    return {};
})();
