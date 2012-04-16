<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120416101926 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("DROP TABLE club_ranking_game_administrators");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("CREATE TABLE club_ranking_game_administrators (game_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_18D79062E48FD905 (game_id), INDEX IDX_18D79062A76ED395 (user_id), PRIMARY KEY(game_id, user_id)) ENGINE = InnoDB");
        $this->addSql("ALTER TABLE club_ranking_game_administrators ADD CONSTRAINT FK_18D79062A76ED395 FOREIGN KEY (user_id) REFERENCES club_user_user(id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE club_ranking_game_administrators ADD CONSTRAINT FK_18D79062E48FD905 FOREIGN KEY (game_id) REFERENCES club_ranking_game(id) ON DELETE CASCADE");
    }
}
