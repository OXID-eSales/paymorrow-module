<?php

declare(strict_types=1);

namespace OxidProfessionalServices\Paymorrow\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210601132752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        $payments = $schema->getTable('oxpayments');
        if (!$payments->hasColumn('OXPSPAYMORROWACTIVE')) {
            $this->addSql(
                "ALTER TABLE `oxpayments` ADD COLUMN `OXPSPAYMORROWACTIVE` tinyint(1) NOT NULL DEFAULT 0"
            );
        }

        if (!$payments->hasColumn('OXPSPAYMORROWMAP')) {
            $this->addSql(
                "ALTER TABLE `oxpayments` ADD COLUMN `OXPSPAYMORROWMAP` tinyint(1) NOT NULL DEFAULT 0"
            );
        }

        $userPayments = $schema->getTable('oxuserpayments');
        if (!$userPayments->hasColumn('OXPSPAYMORROWBANKNAME')) {
            $this->addSql(
                "ALTER TABLE `oxuserpayments` ADD COLUMN `OXPSPAYMORROWBANKNAME` VARCHAR(255)
                CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL"
            );
        }

        if (!$userPayments->hasColumn('OXPSPAYMORROWIBAN')) {
            $this->addSql(
                "ALTER TABLE `oxuserpayments` ADD COLUMN `OXPSPAYMORROWIBAN` VARCHAR(255)
                CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL"
            );
        }

        if (!$userPayments->hasColumn('OXPSPAYMORROWBIC')) {
            $this->addSql(
                "ALTER TABLE `oxuserpayments` ADD COLUMN `OXPSPAYMORROWBIC` VARCHAR(255)
                CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL"
            );
        }

        if (!$userPayments->hasColumn('OXPSPAYMORROWORDERID')) {
            $this->addSql(
                "ALTER TABLE `oxuserpayments` ADD COLUMN `OXPSPAYMORROWORDERID` VARCHAR(255)
                CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL"
            );
        }

        $this->addSql(
            "INSERT INTO `oxcontents` (`OXID`, `OXLOADID`, `OXSHOPID`, `OXSNIPPET`, `OXTYPE`, `OXACTIVE`, `OXACTIVE_1`, `OXPOSITION`, `OXTITLE`, `OXCONTENT`, `OXTITLE_1`, `OXCONTENT_1`, `OXACTIVE_2`, `OXTITLE_2`, `OXCONTENT_2`, `OXACTIVE_3`, `OXTITLE_3`, `OXCONTENT_3`, `OXCATID`, `OXFOLDER`, `OXTERMVERSION`) VALUES
            ('oxpspm_orderemail_invoice', 'oxpspmuserorderemailinvoice', 1, 1, 0, 1, 1, '', 'Paymorrow Keine Kontodaten Hinweistext Rechnung Email', 'Bitte verwenden Sie ausschließlich die Kontoverbindung, die Ihnen für diesen Kauf per E-Mail mitgeteilt wurde.', 'Paymorrow no bank account info invoice email', 'Please only use the bank account that was given to you for this purchase by e-mail.', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
            ('oxpspm_orderemail_invoice_plain', 'oxpspmuserorderemailinvoiceplain', 1, 1, 0, 1, 1, '', 'Paymorrow Keine Kontodaten Hinweistext Rechnung Email Plain', 'Bitte verwenden Sie ausschließlich die Kontoverbindung, die Ihnen für diesen Kauf per E-Mail mitgeteilt wurde.', 'Paymorrow no bank account info invoice email plain', 'Please only use the bank account that was given to you for this purchase by e-mail.', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
            ('oxpspm_orderemail_sdd', 'oxpspmuserorderemailsdd', 1, 1, 0, 1, 1, '', 'Paymorrow Kontodaten Hinweistext Lastschrift Email', 'Der Rechnungsbetrag wird automatisch von Ihrem Bankkonto eingezogen. Der Zeitpunkt des Einzugs wird Ihnen vorher per E-Mail mitgeteilt.', 'Paymorrow bank account info direct debit email', 'The invoice amount is automatically debited from your bank account. The date of the entry will be communicated by e-mail.', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
            ('oxpspm_orderemail_sdd_plain', 'oxpspmuserorderemailsddplain', 1, 1, 0, 1, 1, '', 'Paymorrow Kontodaten Hinweistext Lastschrift Email Plain', 'Der Rechnungsbetrag wird automatisch von Ihrem Bankkonto eingezogen. Der Zeitpunkt des Einzugs wird Ihnen vorher per E-Mail mitgeteilt.', 'Paymorrow bank account info direct debit email plain', 'The invoice amount is automatically debited from your bank account. The date of the entry will be communicated by e-mail.', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', '');
            ");
    }

    public function down(Schema $schema): void
    {
    }
}
