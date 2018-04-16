<?php

/**
 * afoipalasi.gr custom effect: inherit widgets from theme's parent(s).
 * https://magento.stackexchange.com/questions/221255/inherit-widgets-from-theme-parent
 *
 * note: self and parent's packages may differ.
 * logic: discover all package / theme
 * use a foreach on the package
 *
 * Problem: prepared statements do not accept arrays as params,
 * Solution: bind each individual theme to a (generated) variable number of placeholders.
 *
 */

/**
 * Core layout update resource model
 *
 * @category    Palasis
 * @package     Palasis_Inheritwidgets
 * @author      afoipalasi
 */
class Palasis_Inheritwidgets_Model_Core_Resource_Layout
  extends Mage_Core_Model_Resource_Layout {

  /**
   * Define main table
   *
   */
  protected function _construct() {
    $this->_init('core/layout_update', 'layout_update_id');
  }

  /**
   * Retrieve layout updates by handle
   *
   * @param string $handle
   * @param array $params
   * @return string
   */
  public function fetchUpdatesByHandle($handle, $params = array()) {
    $bind = array(
        'store_id' => Mage::app()->getStore()->getId(),
        'area'     => Mage::getSingleton('core/design_package')->getArea(),
        'package'  => Mage::getSingleton('core/design_package')->getPackageName(),
        'theme'    => Mage::getSingleton('core/design_package')->getTheme('layout'),
    );

    foreach ($params as $key => $value) {
      if (isset($bind[$key])) {
        $bind[$key] = $value;
      }
    }
    $bind['layout_update_handle'] = $handle;
    $result = '';

    $designPackage = Mage::getSingleton('core/design_package');
    //discover all package/theme ancenstry
    $allParents = Mage::getModel('core/design_fallback')->getFallbackScheme(
            $designPackage->getArea(), $designPackage->getPackageName(), $designPackage->getTheme('layout')
    );
    //add self
    array_push($allParents, array(
        '_package' => $bind['package'],
        '_theme'   => $bind['theme']
    ));

    $allPackages = array();
    foreach ($allParents as $parent) {
      if (!$parent) {
        continue;
      }
      $_package = isset($parent['_package']) ? $parent['_package'] : null;
      $_theme = isset($parent['_theme']) ? $parent['_theme'] : null;
      if ($_package != null && $_theme != null) {
        $allPackages[$_package][$_theme] = true;
      }
    }


    foreach ($allPackages as $package => $themes) {
      $bind['package'] = $package;
      if (count($themes) === 1) {
        //only one theme in this package
        $themeStatement = 'link.theme = :theme';
        $tKeys = array_keys($themes);
        $bind['theme'] = array_shift($tKeys);
        if (!$themes[$bind['theme']]) { //$flag
          continue;
        }
      } else {
        //more than one theme in this package
        $in = '';
        $i = 0;
        unset($bind['theme']);
        foreach ($themes as $theme => $flag) {
          if (!$flag) {
            continue;
          }
          $themeKey = 'theme' . $i++;
          $in .= ':' . $themeKey . ',';
          $bind[$themeKey] = $theme;
        }
        $themeStatement = 'link.theme IN (' . rtrim($in, ',') . ')';
      }
      $readAdapter = $this->_getReadAdapter();
      if ($readAdapter) {
        $select = $readAdapter->select()
                ->from(array('layout_update' => $this->getMainTable()), array('xml'))
                ->join(array('link' => $this->getTable('core/layout_link')),
                                                       'link.layout_update_id=layout_update.layout_update_id', '')
                ->where('link.store_id IN (0, :store_id)')
                ->where('link.area = :area')
                ->where('link.package = :package')
                ->where($themeStatement)
                ->where('layout_update.handle = :layout_update_handle')
                ->order('layout_update.sort_order ' . Varien_Db_Select::SQL_ASC);

        $result .= join('', $readAdapter->fetchCol($select, $bind));
      }
    }
    return $result;
  }

}
