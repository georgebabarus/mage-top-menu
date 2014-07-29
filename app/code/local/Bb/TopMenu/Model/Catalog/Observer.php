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
 * @subpackage Model
 * @author     George Babarus <george.babarus@gmail.com>
 */
class Bb_TopMenu_Model_Catalog_Observer extends Mage_Catalog_Model_Observer
{


    /**
     * overwrite
     *
     * Recursively adds categories to top menu
     *
     * @param Varien_Data_Tree_Node_Collection|array $categories
     * @param Varien_Data_Tree_Node $parentCategoryNode
     */
    protected function _addCategoriesToMenu($categories, $parentCategoryNode)
    {
        $counts = Mage::helper('bb_topmenu/menu')->getProductCountforAllCategories();

        foreach ($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $nodeId = 'category-node-' . $category->getId();

            $tree = $parentCategoryNode->getTree();
            $categoryData = array(
                'name'          => $category->getName(),
                'id'            => $nodeId,
                'url'           => Mage::helper('catalog/category')->getCategoryUrl($category),
                'is_active'     => $this->_isActiveMenuCategory($category),
                'product_count' => (!empty($counts[$category->getId()]['count'])?$counts[$category->getId()]['count']:$category->getProductCount()),
                'is_category'   => true

            );
            $categoryNode = new Varien_Data_Tree_Node($categoryData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);

            if (Mage::helper('catalog/category_flat')->isEnabled()) {
                $subcategories = (array)$category->getChildrenNodes();
            } else {
                $subcategories = $category->getChildren();
            }

            $this->_addCategoriesToMenu($subcategories, $categoryNode);
        }
    }

}
