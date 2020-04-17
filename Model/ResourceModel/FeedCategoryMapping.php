<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\ResourceModel;

use Ceneo\Feed\Model\ResourceModel\FeedCategory as FeedCategoryResource;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class FeedCategoryMapping extends AbstractDb
{
    const TABLE_NAME = 'catalog_product_feed_category_mapping';
    const PRIMARY_KEY_NAME = 'feed_category_mapping_id';

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY_NAME);
    }

    /**
     * @param Context $context
     * @param ResourceConnection $resource
     * @param type $connectionName
     */
    public function __construct(
        Context $context,
        ResourceConnection $resource,
        $connectionName = null
    ) {
        parent::__construct($context);
        $this->connection = $this->_resources->getConnection();
        $this->resource = $resource;
    }


    /**
     * Delete all mapped categories from given ID (feed_category_id)
     *
     * @param int $id
     * @throws LocalizedException
     */
    public function deleteCategoryMapping($id)
    {
        $primaryKey = FeedCategoryResource::PRIMARY_KEY_NAME;
        $this->connection->delete(
            $this->getMainTable(),
            ["{$primaryKey} = ?" => $id]
        );
    }

    /**
     * Function insert multiple data into table catalog_product_feed_category_mapping
     *
     * @param array $data
     * @return int
     */
    public function insertMultiple($data)
    {
        try {
            $tableName = $this->resource->getTableName(self::TABLE_NAME);
            return $this->connection->insertMultiple($tableName, $data);
        } catch (\Exception $e) {
            return false;
        }
    }
}
