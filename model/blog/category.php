<?php

class ModelBlogCategory extends \Core\Ams\Page {

    /**
     * Sets the namespace of each content page type. this one is the core version - ie the simplest!
     * @var string
     */
    protected $_namespace = 'blog.category';
    public $content;

    public function getActivePosts($data = array()) {


        if ($data['ams_page_id']) {
            $id = $data['ams_page_id'];
        } else {
            $id = $this->id;
        }

        if (!$id) {
            return false;
        }

        $sql = "Select p.ams_page_id from #__ams_pages p inner join #__ams_nodes n on p.ams_page_id = n.ams_page_id where"
                . " p.status = 1 and p.namespace='blog.post' and n.node='categories' and n.content like '%\"" . $id . "\"%'";

        $sorts = array(
            'p.name',
            'p.date_created',
            'p.date_modified',
        );
        if (empty($data['sort']) || !in_array($data['sort'], $sorts)) {
            $sort = 'p.name';
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

        if ($data['ams_page_id']) {
            $id = $data['ams_page_id'];
        } else {
            $id = $this->id;
        }

        if (!$id) {
            return false;
        }

        $sql = "Select count(p.ams_page_id) as total from #__ams_pages p inner join #__ams_nodes n on p.ams_page_id = n.ams_page_id where"
                . " p.status = 1 and p.namespace='blog.post' and n.node='categories' and n.content like '%\"" . $id . "\"%'";


        $query = $this->_db->query($sql);
        return $query->row['total'];
    }

}
