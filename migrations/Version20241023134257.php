<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023134257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quiz_result (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, matiere_id INT DEFAULT NULL, completed_at VARCHAR(255) NOT NULL, INDEX IDX_FE2E314AA76ED395 (user_id), INDEX IDX_FE2E314AF46CD258 (matiere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quiz_result ADD CONSTRAINT FK_FE2E314AA76ED395 FOREIGN KEY (user_id) REFERENCES inscription (id)');
        $this->addSql('ALTER TABLE quiz_result ADD CONSTRAINT FK_FE2E314AF46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE user_completed_quizzes DROP FOREIGN KEY FK_2BCFCFFA5DAC5993');
        $this->addSql('ALTER TABLE user_completed_quizzes DROP FOREIGN KEY FK_2BCFCFFAF46CD258');
        $this->addSql('DROP TABLE user_completed_quizzes');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_completed_quizzes (inscription_id INT NOT NULL, matiere_id INT NOT NULL, INDEX IDX_2BCFCFFA5DAC5993 (inscription_id), INDEX IDX_2BCFCFFAF46CD258 (matiere_id), PRIMARY KEY(inscription_id, matiere_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_completed_quizzes ADD CONSTRAINT FK_2BCFCFFA5DAC5993 FOREIGN KEY (inscription_id) REFERENCES inscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_completed_quizzes ADD CONSTRAINT FK_2BCFCFFAF46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz_result DROP FOREIGN KEY FK_FE2E314AA76ED395');
        $this->addSql('ALTER TABLE quiz_result DROP FOREIGN KEY FK_FE2E314AF46CD258');
        $this->addSql('DROP TABLE quiz_result');
    }
}
