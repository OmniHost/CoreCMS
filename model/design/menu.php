<?php

class ModelDesignMenu extends \Core\Model {

    public function getMenu($menu_id) {
        $group_id = $this->customer->getGroupId();
        $cacheKey = 'menus.menu.' . (int) $menu_id . '_' . (int) $group_id;
        $tree = $this->cache->get($cacheKey);
        if (!$tree) {
            $query = $this->db->query("select mi.* from #__menu_items mi inner join #__menus m on m.menu_id = mi.menu_id where m.menu_id='" . (int) $menu_id . "' order by sort_order asc");
            $items = array();
            $this->load->model('setting/rights');
            foreach ($query->rows as $row) {
                $items[$row['parent_id']][] = $row;
            }
            if ($items) {
                $tree = $this->_createTree($items, $items[0]); // changed
            } else {
                $tree = false;
            }
            $this->cache->set($cacheKey, $tree);
        }
        return $tree;
    }

    public function getHeaderMenu() {
        $group_id = $this->customer->getGroupId();
        $cacheKey = 'header.menu.' . (int) $group_id;
        $tree = $this->cache->get($cacheKey);
        if (!$tree) {
            $query = $this->db->query("select mi.* from #__menu_items mi inner join #__menus m on m.menu_id = mi.menu_id where m.header=1 order by sort_order asc");
            $items = array();
            $this->load->model('setting/rights');
            foreach ($query->rows as $row) {
                $items[$row['parent_id']][] = $row;
            }
            if ($items) {
                $tree = $this->_createTree($items, $items[0]); // changed
            } else {
                $tree = false;
            }
            $this->cache->set($cacheKey, $tree);
        }
        return $tree;
    }

    public function getFooterMenu() {
        $group_id = $this->customer->getGroupId();
        $cacheKey = 'footer.menu.' . (int) $group_id;
        $tree = $this->cache->get($cacheKey);
        if (!$tree) {
            $query = $this->db->query("select mi.* from #__menu_items mi inner join #__menus m on m.menu_id = mi.menu_id where m.footer=1 order by sort_order asc");
            $items = array();
            $this->load->model('setting/rights');
            foreach ($query->rows as $row) {
                $items[$row['parent_id']][] = $row;
            }
            if ($items) {
                $tree = $this->_createTree($items, $items[0]); // changed
            } else {
                $tree = false;
            }
            $this->cache->set($cacheKey, $tree);
        }
        return $tree;
    }

    private function _createTree(&$list, $parent) {
        $tree = array();
        foreach ($parent as $k => $l) {
            if ($this->model_setting_rights->getRight($l['menu_item_id'], 'menu_item')) {

                if (isset($list[$l['menu_item_id']])) {
                    $l['children'] = $this->_createTree($list, $list[$l['menu_item_id']]);
                }
                $tree[] = $l;
            }
        }
        return $tree;
    }

}
