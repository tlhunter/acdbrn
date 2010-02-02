<?php
mustBeAdmin();
runQuery("DELETE FROM textual WHERE section = '" . nukeAlphaNum(urlPath(1)) . "' LIMIT 1");
pingGoogleSitemap();
redirect('admin', 2, "Page has been deleted!");