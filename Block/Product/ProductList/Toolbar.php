<?php

namespace SpringImport\CatalogDropdownSort\Block\Product\ProductList;

use Magento\Framework\Api\SortOrder;

/**
 * Class ToolbarPlugin
 */
class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    const SORT_LABEL_TEMPLATE = '%s: %s';

    /**
     * Load Available Orders
     *
     * @return array $options
     */
    public function loadAvailableOrdersForView()
    {
        $attributes = $this->_catalogConfig->getAttributesUsedForSortBy();
        $options = [];
        foreach ($attributes as $attribute) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute\AbstractAttribute */
            $type = $attribute->getBackendType();
            $ascLabel = $this->getSortTitleByType($type, SortOrder::SORT_ASC);
            $descLabel = $this->getSortTitleByType($type, SortOrder::SORT_DESC);
            $keyAsc = $attribute->getAttributeCode() . '-' . SortOrder::SORT_ASC;
            $keyDesc = $attribute->getAttributeCode() . '-' . SortOrder::SORT_DESC;
            $options[$keyAsc] = sprintf(self::SORT_LABEL_TEMPLATE, $attribute->getStoreLabel(), $ascLabel);
            $options[$keyDesc] = sprintf(self::SORT_LABEL_TEMPLATE, $attribute->getStoreLabel(), $descLabel);
        }

        return $options;
    }

    /**
     * Get sort title by type
     *
     * Example:
     * [varchar, char] => 'A-Z'
     * [int] => 'Low-High'
     *
     * @param string $type
     * @param string $sort
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
        $dateTypes = ['date', 'time', 'datetime'];
        $ends = [
            'number' => [
                SortOrder::SORT_ASC => __('Low-High'),
                SortOrder::SORT_DESC => __('High-Low'),
            ],
            'string' => [
                SortOrder::SORT_ASC => __('A-Z'),
                SortOrder::SORT_DESC => __('Z-A'),
            ],
            'date' => [
                SortOrder::SORT_ASC => __('Asc'),
                SortOrder::SORT_DESC => __('Desc'),
            ]
        ];

        if (in_array($type, $stringTypes)) {
            $phrases = $ends['string'];
        } elseif (in_array($type, $dateTypes)) {
            $phrases = $ends['date'];
        } else {
            $phrases = $ends['number'];
        }

        return (isset($phrases)) ? $phrases[$sort] : null;
    }

    /**
     * Compare defined order field with current order field
     *
     * @param string $key
     * @return bool
     */
    public function isOrderAndDirectionCurrent($key)
    {
        if ($key) {
            $parts = explode('-', $key);
            $order = array_shift($parts);
            $direction = strtolower(array_shift($parts));

            return parent::isOrderCurrent($order) && (parent::getCurrentDirection() == $direction);
        }
        return false;
    }
}
