<?php

namespace Core\Ams;

class Node {

    /**
     * Magic method to get a key from the registry
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return \Core\Core::$registry->get($key);
    }

    /**
     * Sets a registry key
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        \Core\Core::$registry->set($key, $value);
    }

    /**
     * Set the content node data
     * @param int $page_id
     * @param string $node
     * @param string $value
     */
    public function setNode($page_id, $node, $value) {
        $row = $this->db->fetchRow("select * from #__ams_nodes where ams_page_id = '" . (int) $page_id . "' and node = '" . $this->db->escape($node) . "'");
        if ($row) {
            $this->db->query("update #__ams_nodes set content='" . $this->db->escape($value) . "' where ams_node_id = '" . $row['ams_node_id'] . "'");
        } else {
            $this->db->query("insert into  #__ams_nodes set content='" . $this->db->escape($value) . "', ams_page_id = '" . (int) $page_id . "', node='" . $this->db->escape($node) . "'");
        }
    }

    /**
     * Create CMS Page
     *
     * @param string $name
     * @param string $namespace
     * @param int $parentId
     * @param int $status
     * @return type 
     */
    public function createPage($name, $namespace, $parent_id = 0, $user_id = 0, $status = 0, $layout_id = 0, $public = 1) {
        $query = $this->db->query("Insert into #__ams_pages set "
                . "name='" . $this->db->escape($name) . "',"
                . "namespace = '" . $this->db->escape($namespace) . "', "
                . "parent_id = '" . (int) $parent_id . "', "
                . "layout_id = '" . (int) $layout_id . "', "
                . "user_id= '" . (int) $user_id . "', "
                . "date_created = '" . time() . "',"
                . "status = '" . (int) $status . "',"
                . "public = '" . (int) $public . "'");
        return $this->db->insertId();
    }

    /**
     * Update CMS Page
     *
     * @param int $page_id
     * @param array <strint, string> $data 
     */
    public function updatePage($page_id, $data) {
        $this->db->query("update #__ams_pages set name='" . $this->db->escape($data['name']) . "', "
                . "parent_id = '" . (int) $data['parent_id'] . "', "
                . "layout_id = '" . (int) $data['layout_id'] . "', "
                . "date_modified = '" . time() . "', "
                . "public = '" . $data['public'] . "', "
                . "status = '" . (int) $data['status'] . "' where ams_page_id='" . (int) $page_id . "'");
        unset($data['id'], $data['ams_page_id'], $data['parent_id'], $data['name'], $data['status'], $data['user_id']);
        if (count($data) > 0) {

            foreach ($data as $key => $value) {
                $this->setNode($page_id, $key, $value);
            }
        }
    }

    /**
     * Delete an ams page
     * @param int $page_id
     */
    public function deletePage($page_id) {
        $this->db->query("delete from #__ams_pages where ams_page_id = '" . (int) $page_id . "'");
        $this->db->query("delete from #__ams_nodes where ams_page_id = '" . (int) $page_id . "'");
        $this->db->query("delete from #__ams_meta where ams_page_id = '" . (int) $page_id . "'");
        $this->db->query("DELETE FROM #__allowed_groups WHERE object_id = '" . (int) $page_id . "' AND object_type = 'ams_page'");
        $this->db->query("DELETE FROM #__denied_groups WHERE object_id = '" . (int) $page_id . "' AND object_type = 'ams_page'");
    }

}
