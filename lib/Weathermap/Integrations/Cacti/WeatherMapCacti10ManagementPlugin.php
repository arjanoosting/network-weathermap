<?php

namespace Weathermap\Integrations\Cacti;

/**
 * The 1.x specific parts of the Cacti 'management' plugin
 *
 * @package Weathermap\Integrations\Cacti
 */
class WeatherMapCacti10ManagementPlugin extends WeatherMapCactiManagementPlugin
{
    public $colours;

    public function __construct($config, $basePath)
    {
        parent::__construct($config, $basePath);
        $this->myURL = "weathermap-cacti10-plugin-mgmt.php";
        $this->editorURL = "weathermap-cacti10-plugin-editor.php";
    }

    /**
     * @param $request
     * @param $appObject
     */
    public function handleManagementMainScreen($request, $appObject)
    {
        $this->cactiHeader();
        print "This will all be replaced.";
        $this->maplistWarnings();
        $this->maplist();
        $this->footerLinks();
        ?>
        <script type='text/javascript'>
            $(function () {
                $('#settings').click(function () {
                    document.location = urlPath + 'settings.php?tab=maps';
                });

                $('#edit').click(function (event) {
                    event.preventDefault();
                    loadPageNoHeader('weathermap-cacti10-plugin-mgmt.php?action=groupadmin&header=false');
                });

                $('.remover').click(function () {
                    var href = $(this).attr('href');
                    loadPageNoHeader(href);
                });
            });
        </script>
        <?php

        $this->cactiFooter();
    }

    public function cactiHeader()
    {
        top_header();
    }

    public function cactiFooter()
    {
        bottom_footer();
    }

    public function cactiRowStart($i)
    {
        form_alternate_row();
    }
}