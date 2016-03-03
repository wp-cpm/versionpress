<?php

namespace VersionPress\ChangeInfos;

use Nette\Utils\Strings;
use VersionPress\Utils\StringUtils;

class BulkThemeChangeInfo extends BulkChangeInfo {

    public function getChangeDescription() {
        return Strings::capitalize(StringUtils::verbToPastTense($this->getAction())) . " $this->count themes";
    }
}