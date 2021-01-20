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
                var layername = lizMap.getLayerNameByCleanName(evt.name);
                get_metadata_html(layername);
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
            // Create bootstrap v2.3 tabs
            let tabs = '';
            tabs += '<div class="tabbable">';
            tabs += '  <ul class="nav nav-tabs">';
            tabs += '    <li class="active"><a href="#pg-metadata-content" data-toggle="tab">';
            tabs += pgmetadataLocales['ui.button.pgmetadataHtml'] + '</a></li>';
            tabs += '    <li><a href="#lizmap-layer-information" data-toggle="tab">';
            tabs += pgmetadataLocales['ui.button.lizmapLayerInformation'] + '</a></li>';
            tabs += '  </ul>';
            tabs += '  <div class="tab-content">';
            tabs += '    <div class="tab-pane active" id="pg-metadata-content">';
            tabs += html;
            tabs += '    </div>';
            tabs += '    <div class="tab-pane" id="lizmap-layer-information">';
            tabs += '    </div>';
            tabs += '  </div>';
            tabs += '</div>';

            // Add tabs in #sub-dock
            document.querySelector('#sub-dock .menu-content').insertAdjacentHTML('afterbegin', tabs);

            // Move original Lizmap Web Client layer tools to the second tab
            $('#sub-dock dl.dl-vertical').appendTo('#lizmap-layer-information');
        }
    }

    return {};
})();
