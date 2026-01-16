<?php
// routes/modules.php
foreach (glob(app_path('Modules/*/routes/web.php')) as $routeFile) {
    require $routeFile;
}
