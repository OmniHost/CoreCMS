<?php

class ModelDesignMenu extends \Core\Model {

    protected $_sort_order = 0;

    public function getMenus() {
        $query = $this->db->query("select * from #__menus order by name asc");
        return $query->rows;
    }

    public function getMenu($menu_id) {
        $query = $this->db->query("select * from #__menus where menu_id = '" . (int) $menu_id . "'");
        $menu = $query->row;
        $menu['items'] = $this->getMenuItems($menu_id);
        return $menu;
    }

    public function addMenu($data) {

        if ($data['header']) {
            $this->db->query("update #__menus set header='0'");
        }
        if ($data['footer']) {
            $this->db->query("update #__menus set footer='0'");
        }

        $query = $this->db->query("insert into #__menus set name='" . $this->db->escape($data['name']) . "', header='" . (int) $data['header'] . "',"
                . "footer = '" . (int) $data['footer'] . "'");
        $menu_id = $this->db->getLastId();
        //  if ($data['items']) {
        $this->updateItems($menu_id, $data['items']);
        //  }
        return $menu_id;
    }
    
    public function editMenu($menu_id, $data) {

        if ($data['header']) {
            $this->db->query("update #__menus set header='0'");
        }
        if ($data['footer']) {
            $this->db->query("update #__menus set footer='0'");
        }

        $query = $this->db->query("update #__menus set name='" . $this->db->escape($data['name']) . "', header='" . (int) $data['header'] . "',"
                . "footer = '" . (int) $data['footer'] . "' where menu_id = '" . (int)$menu_id . "'");
        //  if ($data['items']) {
        $this->updateItems($menu_id, $data['items']);
        //  }
        return $menu_id;
    }

    protected function insertItem($menu_id, $item, $parent_id = 0) {
        $this->db->query("insert into #__menu_items set menu_id='" . (int) $menu_id . "',"
                . "parent_id = '" . (int) $parent_id . "', "
                . "title = '" . $this->db->escape($item['title']) . "', "
                . "ams_page_id = '" . (int) $item['ams_page_id'] . "', "
                . "link = '" . $this->db->escape($item['link']) . "', "
                . "linklabel = '" . $this->db->escape($item['linklabel']) . "', "
                . "sort_order = '" . (int) $this->_sort_order++ . "'");
        $item_id = $this->db->getLastId();
  
        if($item['allowed_groups']){
        $this->model_setting_rights->setAllowedGroups($item_id, 'menu_item', explode(",", $item['allowed_groups']));
        }
        if($item['denied_groups']){
        $this->model_setting_rights->setDeniedGroups($item_id, 'menu_item', explode(",", $item['denied_groups']));
        }
        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                $this->insertItem($menu_id, $child, $item_id);
            }
        }
    }

    public function updateItems($menu_id, $items) {

        $this->db->query("DELETE FROM #__allowed_groups WHERE object_id in (select menu_item_id from #__menu_items where menu_id = '" . (int) $menu_id . "') AND object_type = 'menu_item'");
        $this->db->query("DELETE FROM #__denied_groups WHERE object_id in (select menu_item_id from #__menu_items where menu_id = '" . (int) $menu_id . "') AND object_type = 'menu_item'");
        $this->db->query("delete from #__menu_items where menu_id = '" . (int) $menu_id . "'");
        $this->load->model('setting/rights');
        foreach ($items as $item) {
            $this->insertItem($menu_id, $item);
        }
        $this->cache->delete('header.menu');
        $this->cache->delete('footer.menu');
        $this->cache->delete('menus.menu');
    }

    public function deleteMenu($menu_id) {
        $this->db->query("DELETE FROM #__allowed_groups WHERE object_id in (select menu_item_id from #__menu_items where menu_id = '" . (int) $menu_id . "') AND object_type = 'menu_item'");
        $this->db->query("DELETE FROM #__denied_groups WHERE object_id in (select menu_item_id from #__menu_items where menu_id = '" . (int) $menu_id . "') AND object_type = 'menu_item'");
        $this->db->query("delete from #__menu_items where menu_id = '" . (int) $menu_id . "'");
        $this->db->query("delete from #__menus where menu_id = '" . (int) $menu_id . "'");
    }

    public function countMenuItems($menu_id) {
        $query = $this->db->query("select count(*) as total from #__menu_items where menu_id = '" . (int) $menu_id . "'");
        return $query->row['total'];
    }

    public function getMenuItems($menu_id) {
        $query = $this->db->query("select * from #__menu_items where menu_id = '" . (int) $menu_id . "' order by parent_id asc, sort_order asc");

        $new = array();
        foreach ($query->rows as $a) {

            $query = $this->db->query("Select * FROM #__allowed_groups WHERE object_id ='" . $a['menu_item_id'] . "' AND object_type = 'menu_item'");
            $allowed = array();
            foreach ($query->rows as $r) {
                $allowed[] = $r['group_id'];
            }
            $query = $this->db->query("Select * FROM #__denied_groups WHERE object_id ='" . $a['menu_item_id'] . "' AND object_type = 'menu_item'");
            $denied = array();
            foreach ($query->rows as $r) {
                $denied[] = $r['group_id'];
            }
          
            $a['allowed_groups'] = implode(",", $allowed);
            $a['denied_groups'] = implode(",", $denied);
            //Get allowed
            //Get Denied
            $new[$a['parent_id']][] = $a;
        }
        return $this->_createTree($new, $new[0]); // changed
    }

    private function _createTree(&$list, $parent) {
        $tree = array();
        foreach ($parent as $k => $l) {
            if (isset($list[$l['menu_item_id']])) {
                $l['children'] = $this->_createTree($list, $list[$l['menu_item_id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }

}
