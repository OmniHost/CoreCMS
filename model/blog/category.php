<?php

class ModelBlogCategory extends \Core\Ams\Page {

    /**
     * Sets the namespace of each content page type. this one is the core version - ie the simplest!
     * @var string
     */
    protected $_namespace = 'blog.category';
    public $content;

    public function countLatestPosts() {
        $group_id = $this->_customer->getGroupId();
        if ($this->_customer->getId()) {
            $groups = "-1,-3," . $group_id;
        } else {
            $groups = "-1,-2";
        }

        $sql = "select count(*) as total from #__ams_pages a inner join #__ams_nodes n on a.ams_page_id = n.ams_page_id and n.node='publish_date' where a.status='1' and a.public='1' and a.namespace='blog.post' and a.ams_page_id in (select object_id from #__allowed_groups where object_type='ams_page' and group_id in ($groups)) and a.ams_page_id not in(select object_id from #__denied_groups where object_type='ams_page' and group_id in ($groups)) and n.content < '" . time() . "'";

        $row = $this->_db->query($sql);
        return $row->row['total'];
    }

    public function getLatestPosts($data = array()) {

        $group_id = $this->_customer->getGroupId();
        if ($this->_customer->getId()) {
            $groups = "-1,-3," . $group_id;
        } else {
            $groups = "-1,-2";
        }

        $sql = "select a.ams_page_id, n.content as publish_date from #__ams_pages a "
                . " inner join #__ams_nodes n on a.ams_page_id = n.ams_page_id and n.node='publish_date' "
                . " where a.status='1' and a.public='1' and a.namespace='blog.post' "
                . " and a.ams_page_id in (select object_id from #__allowed_groups where object_type='ams_page' and group_id in ($groups)) "
                . " and a.ams_page_id not in(select object_id from #__denied_groups where object_type='ams_page' and group_id in ($groups))"
                . " and n.content < '" . time() . "'";


        $sorts = array(
            'a.name',
            'n.content',
            'a.date_modified',
            'publish_date'
        );

        if (empty($data['sort']) || !in_array($data['sort'], $sorts)) {
            $sort = 'publish_date';
        } else {

            $sort = $data['sort'];
        }




        if (empty($data['order']) || $data['order'] == 'DESC') {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }

        if (empty($data['limit'])) {
            $limit = 5;
        } else {
            $limit = $data['limit'];
        }

        if (empty($data['start'])) {
            $start = 0;
        } else {
            $start = $data['start'];
        }

        $sql .= " order by $sort $order limit $start, $limit";

        $query = $this->_db->query($sql);

        return $query->rows;
        // return $query->rows;
    }

    public function getActivePosts($data = array()) {


        $group_id = $this->_customer->getGroupId();
        if ($this->_customer->getId()) {
            $groups = "-1,-3," . $group_id;
        } else {
            $groups = "-1,-2";
        }


        if (!empty($data['ams_page_id'])) {
            $id = $data['ams_page_id'];
        } else {
            $id = $this->id;
        }

        if (!$id) {
            return false;
        }

        $sql = "Select p.ams_page_id, d.content as publish_date from #__ams_pages p "
                . " inner join #__ams_nodes n on p.ams_page_id = n.ams_page_id "
                . " inner join #__ams_nodes d on p.ams_page_id = d.ams_page_id and d.node='publish_date' "
                . " where"
                . " p.status = 1 and p.namespace='blog.post' and n.node='categories' and n.content like '%\"" . $id . "\"%'"
                . " and p.ams_page_id in (select object_id from #__allowed_groups where object_type='ams_page' and group_id in ($groups)) "
                . " and p.ams_page_id not in(select object_id from #__denied_groups where object_type='ams_page' and group_id in ($groups))"
                . " and d.content < '" . time() . "'";


      
        $sorts = array(
            'p.name',
            'd.content',
            'p.date_modified',
            'publish_date'
        );
        

        if (empty($data['sort']) || !in_array($data['sort'], $sorts)) {
            $sort = 'publish_date';
        } else {

            $sort = $data['sort'];
        }




        if (empty($data['order']) || $data['order'] == 'DESC') {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }
        

        if (empty($data['limit'])) {
            $limit = 5;
        } else {
            $limit = $data['limit'];
        }

        if (empty($data['start'])) {
            $start = 0;
        } else {
            $start = $data['start'];
        }

        $sql .= " order by $sort $order limit $start, $limit";

        $query = $this->_db->query($sql);

        return $query->rows;
        // return $query->rows;
    }

    public function countActivePosts($data = array()) {

        if (!empty($data['ams_page_id'])) {
            $id = $data['ams_page_id'];
        } else {
            $id = $this->id;
        }

        if (!$id) {
            return false;
        }

        $sql = "Select count(p.ams_page_id) as total from #__ams_pages p inner join #__ams_nodes n on p.ams_page_id = n.ams_page_id "
                . " inner join #__ams_nodes d on p.ams_page_id = d.ams_page_id and d.node='publish_date' "
                . " where"
                . " p.status = 1 and p.namespace='blog.post' and n.node='categories' and n.content like '%\"" . $id . "\"%'"
                . " and d.content < '" . time() . "'";


        $query = $this->_db->query($sql);
        return $query->row['total'];
    }

}
