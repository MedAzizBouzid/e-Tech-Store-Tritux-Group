<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230710093843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart ADD product_id INT DEFAULT NULL, ADD commande_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B782EA2E54 FOREIGN KEY (commande_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BA388B74584665A ON cart (product_id)');
        $this->addSql('CREATE INDEX IDX_BA388B782EA2E54 ON cart (commande_id)');
        $this->addSql('CREATE INDEX IDX_BA388B7A76ED395 ON cart (user_id)');
        $this->addSql('ALTER TABLE media ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C4584665A ON media (product_id)');
        $this->addSql('ALTER TABLE product ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B74584665A');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B782EA2E54');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395');
        $this->addSql('DROP INDEX IDX_BA388B74584665A ON cart');
        $this->addSql('DROP INDEX IDX_BA388B782EA2E54 ON cart');
        $this->addSql('DROP INDEX IDX_BA388B7A76ED395 ON cart');
        $this->addSql('ALTER TABLE cart DROP product_id, DROP commande_id, DROP user_id');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C4584665A');
        $this->addSql('DROP INDEX IDX_6A2CA10C4584665A ON media');
        $this->addSql('ALTER TABLE media DROP product_id');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('DROP INDEX IDX_D34A04AD12469DE2 ON product');
        $this->addSql('ALTER TABLE product DROP category_id');
    }
}
