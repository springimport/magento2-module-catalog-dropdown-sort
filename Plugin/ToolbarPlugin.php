<?php

namespace SpringImport\CatalogDropdownSort\Plugin;

use Magento\Framework\Api\SortOrder;
//use Magento\Framework\Exception\InputException;

/**
 * Class ToolbarPlugin
 */
class ToolbarPlugin
{
    /**
     * List of available order fields
     *
     * @var array
     */
    protected $_availableOrder = null;

    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * ToolbarPlugin constructor
     *
     * @param \Magento\Catalog\Model\Config $catalogConfig
     */
    public function __construct(
        \Magento\Catalog\Model\Config $catalogConfig
    ) {
        $this->_catalogConfig = $catalogConfig;
    }

    /**
     * Retrieve available Order fields list
     *
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Closure $proceed
     * @return array
     */
    public function aroundGetAvailableOrders(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed
    ) {
        $this->loadAvailableOrders();
        return $this->_availableOrder;
    }

    /**
     * Set Available order fields list
     *
     * @param array $orders
     * @return $this
     */
    public function aroundSetAvailableOrders(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed,
        $orders
    ) {
        $this->loadAvailableOrders();
        //$this->_availableOrder = $orders;
        return $this;
    }

    /**
     * Add order to available orders
     *
     * @param string $order
     * @param string $value
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function aroundAddOrderToAvailableOrders(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed,
        $order,
        $value
    ) {
        //$this->loadAvailableOrders();
        //$this->_availableOrder[$order] = $value;
        return $this;
    }

    /**
     * Remove order from available orders if exists
     *
     * @param string $order
     * @return $this
     */
    public function aroundRemoveOrderFromAvailableOrders(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed,
        $order
    ) {
        $this->loadAvailableOrders();
        if (isset($this->_availableOrder[$order])) {
            unset($this->_availableOrder[$order]);
        }
        return $this;
    }

    /**
     * Load Available Orders
     *
     * @return $this
     */
    private function loadAvailableOrders()
    {
        if ($this->_availableOrder === null) {
            //$this->_availableOrder = $this->_catalogConfig->getAttributeUsedForSortByArray();
            $attributes = $this->_catalogConfig->getAttributesUsedForSortBy();
            foreach ($attributes as $attribute) {
                /* @var $attribute \Magento\Eav\Model\Entity\Attribute\AbstractAttribute */
                $code = $attribute->getAttributeCode();
                $type = $attribute->getBackendType();
                $ascLabel = $this->getSortTitleByType($type, SortOrder::SORT_ASC);
                $descLabel = $this->getSortTitleByType($type, SortOrder::SORT_DESC);
                $options[$code . '-' . SortOrder::SORT_ASC] = $attribute->getStoreLabel() . ' ' . $ascLabel;
                $options[$code . '-' . SortOrder::SORT_DESC] = $attribute->getStoreLabel() . ' ' . $descLabel;
            }
        }

        //print_r($options);exit;

        return $this;
    }

    /**
     * Get sort title by type
     *
     * [varchar, char] => 'A-Z'
     * [int] => 'Low-High'
     *
     * @param $type
     * @param $sort
     * @return null
     * @throws \Exception
     */
    protected function getSortTitleByType($type, $sort)
    {
        if (!in_array($sort, [SortOrder::SORT_ASC, SortOrder::SORT_DESC])) {
            throw new \Exception(
                'Sort option isn\'t allowed.'
            );
        }

        $numberTypes = ['int', 'integer', 'decimal'];
        $stringTypes = ['varchar', 'char', 'static'];
        $ends = [
            'number' => [
                SortOrder::SORT_ASC => __('Low-High'),
                SortOrder::SORT_DESC => __('High-Low'),
            ],
            'string' => [
                SortOrder::SORT_ASC => __('A-Z'),
                SortOrder::SORT_DESC => __('Z-A'),
            ],
        ];

        if (in_array($type, $numberTypes)) {
            $phrases = $ends['number'];
        } elseif (in_array($type, $stringTypes)) {
            $phrases = $ends['string'];
        }

        return (isset($phrases)) ? $phrases[$sort] : null;
    }
}
