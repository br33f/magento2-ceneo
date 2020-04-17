<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Setup;

use Ceneo\Feed\Helper\CeneoData;
use Ceneo\Feed\Model\ResourceModel\CeneoCategory;
use Ceneo\Feed\Model\ResourceModel\Feed;
use Ceneo\Feed\Model\ResourceModel\FeedCategory as FeedCategoryResource;
use Ceneo\Feed\Model\ResourceModel\FeedCategoryMapping as FeedCategoryMappingResource;
use Ceneo\Feed\Model\ResourceModel\FeedTemplate as FeedTemplateResource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Zend_Db_Exception;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var CeneoData
     */
    private $ceneoData;

    /**
     * UpgradeSchema constructor.
     *
     * @param CeneoData $ceneoData
     */
    public function __construct(CeneoData $ceneoData)
    {
        $this->ceneoData = $ceneoData;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Zend_Db_Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $tableName = $setup->getTable(Feed::TABLE_NAME);

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->addColumn(
                    $tableName,
                    'conditions_serialized',
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'afters' => 'category_name',
                        'comment' => 'Serialized conditions',
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '1.2') < 0) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable(FeedTemplateResource::TABLE_NAME)
            )->addColumn(
                FeedTemplateResource::PRIMARY_KEY_NAME,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Template Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Name'
            )->addColumn(
                'enable',
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false,
                    'default' => true,
                ],
                'Enable'
            )->addColumn(
                'template',
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true,
                ],
                'Template'
            );
            $setup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.2.8') < 0) {
            $tableName = $setup->getTable(Feed::TABLE_NAME);

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->addColumn(
                    $tableName,
                    'cron_expression',
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'cron expression',
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '1.5.0') < 0) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable(FeedCategoryResource::TABLE_NAME)
            )->addColumn(
                FeedCategoryResource::PRIMARY_KEY_NAME,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Feed Category Id'
            )->addColumn(
                'code',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Code'
            )->addColumn(
                'exclude_categories',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Exclude categories'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Name'
            );
            $setup->getConnection()->createTable($table);

            $table = $setup->getConnection()->newTable(
                $setup->getTable(FeedCategoryMappingResource::TABLE_NAME)
            )->addColumn(
                FeedCategoryMappingResource::PRIMARY_KEY_NAME,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Feed Category Mapping Id'
            )->addColumn(
                FeedCategoryResource::PRIMARY_KEY_NAME,
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                ],
                'Feed Category ID'
            )->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                ],
                'Category ID'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true,
                ],
                'Mapping value'
            );
            $setup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.6.3') < 0) {
            $tableName = $setup->getTable(Feed::TABLE_NAME);

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->addColumn(
                    $tableName,
                    'store_id',
                    [
                        'type' => Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'comment' => 'Store View ID',
                        'unsigned' => true,
                    ]
                );
                $connection->addIndex(
                    $setup->getTable('catalog_product_feed'),
                    $setup->getIdxName('catalog_product_feed', ['store_id']),
                    ['store_id']
                );
                $connection->addForeignKey(
                    $setup->getFkName(
                        $setup->getTable('catalog_product_feed'),
                        'store_id',
                        $setup->getTable('store'),
                        'store_id'
                    ),
                    $setup->getTable('catalog_product_feed'),
                    'store_id',
                    $setup->getTable('store'),
                    'store_id',
                    Table::ACTION_CASCADE
                );
            }
        }

        if (version_compare($context->getVersion(), '1.6.4') < 0) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable(CeneoCategory::TABLE_NAME)
            )->addColumn(
                CeneoCategory::PRIMARY_KEY_NAME,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Ceneo Category Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Name'
            )->addColumn(
                'parent_id',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Category Path'
            )->addColumn(
                'ceneo_id',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Ceneo Id'
            );

            $setup->getConnection()->createTable($table);

            $setup->getConnection()->addIndex(
                $setup->getTable(CeneoCategory::TABLE_NAME),
                $setup->getIdxName(
                    CeneoCategory::TABLE_NAME,
                    [CeneoCategory::PRIMARY_KEY_NAME],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [CeneoCategory::PRIMARY_KEY_NAME],
                AdapterInterface::INDEX_TYPE_UNIQUE
            );

            $this->ceneoData->saveCeneoCategoriesData();
        }

        if (version_compare($context->getVersion(), '1.6.5') < 0) {
            $tableName = $setup->getTable(Feed::TABLE_NAME);

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->addColumn(
                    $tableName,
                    'additional_attributes',
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'afters' => 'conditions_serialized',
                        'comment' => 'Serialized additional attributes',
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
