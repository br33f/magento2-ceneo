<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Setup;

use Ceneo\Feed\Model\ResourceModel\Feed as FeedResource;
use Ceneo\Feed\Model\ResourceModel\FeedCategory as FeedCategoryResource;
use Ceneo\Feed\Model\ResourceModel\FeedTemplate as FeedTemplateResource;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * Module uninstall code
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $connection = $setup->getConnection();

        if ($connection->isTableExists($connection->getTableName(FeedResource::TABLE_NAME))) {
            $connection->dropTable($connection->getTableName(FeedResource::TABLE_NAME));
        }
        if ($connection->isTableExists($connection->getTableName(FeedTemplateResource::TABLE_NAME))) {
            $connection->dropTable($connection->getTableName(FeedTemplateResource::TABLE_NAME));
        }
        if ($connection->isTableExists($connection->getTableName(FeedCategoryResource::TABLE_NAME))) {
            $connection->dropTable($connection->getTableName(FeedCategoryResource::TABLE_NAME));
        }

        $setup->endSetup();
    }
}
