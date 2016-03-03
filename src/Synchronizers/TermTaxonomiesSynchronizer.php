<?php

namespace VersionPress\Synchronizers;

use VersionPress\Database\DbSchemaInfo;
use VersionPress\Storages\Storage;
use VersionPress\Utils\AbsoluteUrlReplacer;
use wpdb;

class TermTaxonomiesSynchronizer extends SynchronizerBase {

    /** @var wpdb */
    private $database;

    function __construct(Storage $storage, $wpdb, DbSchemaInfo $dbSchema, AbsoluteUrlReplacer $urlReplacer) {
        parent::__construct($storage, $wpdb, $dbSchema, $urlReplacer, 'term_taxonomy');
        $this->database = $wpdb;
    }

    function synchronize($task, $entitiesToSynchronize = null) {
        $this->database->query("drop index term_id_taxonomy on {$this->database->term_taxonomy}");
        $result = parent::synchronize($task, $entitiesToSynchronize);
        $this->database->query("create unique index term_id_taxonomy on {$this->database->term_taxonomy}(term_id, taxonomy)");
        return $result;
    }

    protected function doEntitySpecificActions() {
        if ($this->passNumber == 1) {
            return false;
        }

        $this->fixPostsCount();
        return true;
    }

    private function fixPostsCount() {
        $sql = "update {$this->database->term_taxonomy} tt set tt.count =
          (select count(*) from {$this->database->term_relationships} tr where tr.term_taxonomy_id = tt.term_taxonomy_id);";
        $this->database->query($sql);
    }


}
