<?php
/**
 * George Babarus extension for Magento
 *
 * Long description of this file (if any...)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Bb TopMenu module to newer versions in the future.
 * If you wish to customize the Bb TopMenu module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Bb
 * @package    Bb_TopMenu
 * @copyright  Copyright (C) 2014 http://www.babarus.ro
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   Bb
 * @package    Bb_TopMenu
 * @subpackage Helper
 * @author     George Babarus <george.babarus@gmail.com>
 */
class Bb_TopMenu_Helper_Menu extends Mage_Core_Helper_Data
{





    /**
     * extract product count for all store categories
     *
     * @return assoc array with category id on array key
     */
    public function getProductCountforAllCategories()
    {
        $counts = Mage::registry('getProductCountforAllCategories');
        if (!empty($counts)){
            return $counts;
        }

        $counts = $this->getProductCountForCategories();
        Mage::register('getProductCountforAllCategories', $counts);
        return $counts;
    }



    /**
     * Get products count in category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return int
     */
    public function getProductCountForCategories( $categoryCollection = null)
    {
        try{
            $categoryIds = $this->extractCategoriesIdsFromCollection($categoryCollection);

            $productTable = Mage::getSingleton('core/resource')->getTableName('catalog/category_product_index');

            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');

            $currentStoreId = Mage::app()->getStore()->getStoreId();
            $select = $readConnection->select()
                ->from(
                    array('main_table' => $productTable),
                    array('category_id','count' => new Zend_Db_Expr('COUNT(main_table.product_id)'))
                );

            if (is_array($categoryIds)){
                $select->where('main_table.category_id IN (?)', $categoryIds);
            }

            if (!empty($currentStoreId)) {
                $select->where('main_table.store_id = ? ', $currentStoreId);
            }

            $visibility = Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds();

            $select->where(' main_table.visibility IN (?) ', $visibility);
            $select->group('category_id');

            $counts = $readConnection->fetchAssoc($select);
        } catch ( Exception $e ) {
            //if catalog/category_product_index table is not created we will count on category_products table
            $counts = array();
        }
        return $counts;
    }




    /**
     * extract all categories ids form list of category
     *
     * @param $categoryCollection - category collection or array of Mage_Catalog_Model_Category
     */
    public function extractCategoriesIdsFromCollection ( $categoryCollection ) {
        if (empty($categoryCollection)){
            return false;
        }
        $categoryIds = array();
        foreach ($categoryCollection as $category){
            $categoryIds[] = $category->getId();
        }

        return $categoryIds;
    }





}
