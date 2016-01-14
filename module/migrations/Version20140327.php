<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-image
 * @license    LGPL-3.0+
 * @filesource
 */

namespace DoctrineMigrations\AvisotaMessageElementGallery;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20140327 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('orm_avisota_message_content')) {
            return;
        }

        $table = $schema->getTable('orm_avisota_message_content');

        if ($table->hasColumn('multiSRC')) {
            $this->addSql('ALTER TABLE orm_avisota_message_content CHANGE multiSRC imageSources BLOB DEFAULT NULL COMMENT \'(DC2Type:serializedBinary)\'');
        }
    }

    public function down(Schema $schema)
    {
        if (!$schema->hasTable('orm_avisota_message_content')) {
            return;
        }

        $table = $schema->getTable('orm_avisota_message_content');

        if ($table->hasColumn('imageSources')) {
            $this->addSql('ALTER TABLE orm_avisota_message_content CHANGE imageSources multiSRC BLOB DEFAULT NULL COMMENT \'(DC2Type:serializedBinary)\'');
        }
    }
}
