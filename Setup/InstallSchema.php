<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Setup;

use Ceneo\Feed\Model\ResourceModel\Feed as FeedResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable(FeedResource::TABLE_NAME)
        )->addColumn(
            FeedResource::PRIMARY_KEY_NAME,
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
            ],
            'Tab content Id'
        )->addColumn(
            'enable',
            Table::TYPE_BOOLEAN,
            null,
            [
                'nullable' => false,
                'default' => false,
            ],
            'Enable'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            [
                'nullable' => false,
            ],
            'Name'
        )->addColumn(
            'filename',
            Table::TYPE_TEXT,
            50,
            [
                'nullable' => false,
            ],
            'Filename'
        )->addColumn(
            'ceneo_mapping_id',
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
            ],
            'Ceneo Mapping Id'
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
