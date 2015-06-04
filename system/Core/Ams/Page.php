<?php

namespace Core\Ams;

Abstract class Page {

    /**
     * Id of the AMS Item
     * @var int $id
     */
    public $id;

    /**
     * Name of the  time
     * @var string $name
     */
    public $name;

    /**
     * Parent page id
     * @var int $parent_id
     */
    public $parent_id = 0;

    /**
     * item status (defaults to 0 / 1 off / on
     * @var int $status
     */
    public $status = 0;

    /**
     * 
     * @var int user id of author 
     */
    public $user_id = 0;

    /**
     *
     * @var string 
     */
    public $meta_title;

    /**
     *
     * @var string
     */
    public $meta_keywords;

    /**
     *
     * @var string
     */
    public $meta_description;

    /**
     * Allow comments on the item
     * @var int
     */
    public $comments = 1;

    /**
     * is this a public or internal page, public pages are  automatically added to the autocomplete / menus
     * @var int
     */
    public $public = 1;

    /**
     *
     * @var string $_namespace
     */
    protected $_namespace = 'page';

    /**
     *
     * @var cmsNode $_pageModel
     */
    protected $_pageModel;

    /**
     *
     * @var int Layout id for page 
     */
    public $layout_id = 0;

    /**
     *
     * @var \Core\Db
     */
    protected $_db;
    protected $_load;

    /**
     *
     * @var null - perhaps later
     */
    protected $_language;

    /**
     * 
     */
    const NO_SETTER = 'setter method does not exist';

    /**
     * Constructor
     *
     * @param string|int $ams_page_id 
     * @return void
     */
    public function __construct($ams_page_id = null) {
        $this->_db = \Core\Core::$registry->get('db');
        $this->_load = \Core\Core::$registry->get('load');
        $this->_language = \Core\Core::$registry->get('language');
        $this->_pageModel = new \Core\Ams\Node();
        if (null !== $ams_page_id) {
            $this->loadPageObject($ams_page_id);
        }
    }

    /**
     * Load parent cms_pages row
     *
     * @param int $id
     * @return RedBeanObject
     */
    protected function _getInnerRow($id = null) {
        if ($id == null) {
            $id = $this->id;
        }
        return $this->_db->fetchRow("select * from #__ams_pages where ams_page_id = '" . (int) $id . "'");
    }

    /**
     * Get Class properties.
     *
     * @return array <int string>
     */
    protected function _getProperties() {
        $propertyArray = array();
        $class = new \ReflectionClass($this);
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            if ($property->isPublic()) {
                $propertyArray[] = $property->getName();
            }
        }
        return $propertyArray;
    }

    /**
     * Magic setter method
     *
     * @param string $property
     * @param string $data
     * @return string 
     */
    protected function _callSetterMethod($property, $data) {
//create the method name

        $method = $this->_usToCCase($property);

        $methodName = '_set' . $method;

        if (method_exists($this, $methodName)) {
            return $this->$methodName($data);
        } else {
            return self::NO_SETTER;
        }
    }

    protected function _callPopulateMethod($property, $data) {
//create the method name

        $method = $this->_usToCCase($property);

        $methodName = '_populate' . $method;

        if (method_exists($this, $methodName)) {
            return $this->$methodName($data);
        } else {
            return self::NO_SETTER;
        }
    }

    /**
     * Convert to Camel Case.
     *
     * @param string $string
     * @return string 
     */
    protected function _usToCCase($string) {
        $separator = '_';
        $MatchPattern = array('#(?<=(?:[A-Z]))([A-Z]+)([A-Z][A-z])#', '#(?<=(?:[a-z0-9]))([A-Z])#');
        $Replacement = array('\1' . $separator . '\2', $separator . '\1');
        return preg_replace($MatchPattern, $Replacement, $string);
    }

    /**
     * Get dependent rowset from content_nodes
     *
     * @param int $id
     * @return array
     */
    protected function _findDependentRowset($id) {
        return $this->_db->fetchAll("select * from `#__ams_nodes` where ams_page_id = '" . (int) $id . "'");
    }

    /**
     * Load CMS object given id.
     *
     * @param type $id 
     * @throws Exception
     */
    public function loadPageObject($id) {
        $this->id = $id;

        $row = $this->_getInnerRow();

        if ($row) {
            if ($row['namespace'] != $this->_namespace) {
                throw new \Core\Exception('Unable to cast page type:' .
                $row->namespace . ' to type:' . $this->_namespace);
            }
            $this->name = $row['name'];
            $this->parent_id = $row['parent_id'];
            $this->status = $row['status'];

            $nodes = $this->_findDependentRowset($id);

            if ($nodes) {
                $properties = $this->_getProperties();
                foreach ($nodes as $node) {
                    $key = $node['node'];
                    if (in_array($key, $properties)) {
// try to call the setter method
                        $value = $this->_callSetterMethod($key, $node);
                        if ($value === self::NO_SETTER) {
                            $value = $node['content'];
                        }
                        $this->$key = $value;
                    }
                }
            }
        } 
        return $this;
    }

    public function loadParent($id) {

        $path = DIR_APPLICATION;

        $row = $this->_db->query("select * from #__ams_pages where ams_page_id='" . (int) $id . "'");
        // var_dump($row);

        $file = $path . 'model/' . str_replace(".", "/", $row->row['namespace']) . '.php';
        $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $row->row['namespace']);

        if (file_exists($file)) {
            include_once(__modification($file));

            return new $class($id);
        }
        return false;
    }

    /**
     * Convert page object properties to array
     *
     * @return array
     */
    public function toArray() {
        $properties = $this->_getProperties();
        foreach ($properties as $property) {
            $array[$property] = $this->$property;
        }
        return $array;
    }

    public function populate($array) {
        $properties = $this->_getProperties();
        foreach ($properties as $property) {
            if (isset($array[$property])) {
                $value = $this->_callPopulateMethod($property, $array);
                if ($value === self::NO_SETTER) {
                    $value = $array[$property];
                }
                $this->$property = $value;
            }
        }
    }

    /**
     * Save CMS Object
     * @return int
     */
    public function save() {
        if (isset($this->id)) {
            return $this->_update();
        } else {
            return $this->_insert();
        }
    }

    /**
     * Insert CMS object
     * @return int
     */
    protected function _insert() {
        $pageId = $this->_pageModel->createPage($this->name, $this->_namespace, $this->parent_id, $this->status, $this->layout_id, $this->public);

        $this->id = $pageId;

        return $this->_update();
    }

    /**
     * Update CMS object
     * @return int
     */
    protected function _update() {
        $data = $this->toArray();
        return $this->_pageModel->updatePage($this->id, $data);
    }

    /**
     * Delete CMS Object
     * @return void
     * @throws Exception if $this->id not set.
     */
    public function delete() {
        if (isset($this->id)) {
            $this->_pageModel->deletePage($this->id);
        } else {
            throw new Exception('Unable to delete item; the item is empty!');
        }
    }

    public function getPages($filter = array(), $orderby = 'name', $orderdir = 'asc', $page = 1, $limit = 0) {
        if ($limit > 0) {
            $query = "Select SQL_CALC_FOUND_ROWS * from #__ams_pages where namespace = '" . $this->_db->escape($this->_namespace) . "' ";
        } else {
            $query = "Select * from #__ams_pages where namespace = '" . $this->_db->escape($this->_namespace) . "' ";
        }

        $props = $this->_getProperties();
        $where = array();
        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                if ($key == 'id') {
                    $key = 'ams_page_id';
                }
                if ($key == 'ams_page_id') {
                    $query .= " and ams_page_id = '" . (int) $value . "' ";
                } elseif ($key == 'name') {
                    $query .= " and `name` like '%" . $this->_db->escape($value) . "' ";
                } elseif ($key == 'parent_id') {
                    $query .= " and parent_id = '" . (int) $value . "' ";
                } elseif ($key == 'status') {
                    $query .= " and `status` = '" . (int) $value . "' ";
                } elseif (isset($props[$key])) {
                    $query .= " and ams_page_id in (select distinct(ams_page_id) from #__ams_nodes "
                            . "where node = '" . $this->_db->escape($key) . "' and content like '%" . $this->_db->escape($value) . "%') ";
                }
            }
        }
        $orderbys = array(
            'ams_page_id',
            'name',
            'parent_id',
            'status',
            'date_created',
            'date_modified'
        );
        $order = (in_array($orderby, $orderbys)) ? $orderby : 'name';
        $dir = (strtolower($orderdir) == 'desc') ? 'desc' : 'asc';

        $query .= " order by $order $dir";



        if ($limit > 0) {
            if ($page < 1) {
                $page = 1;
            }
            $start = ($page - 1) * $limit;
            $query .= " limit $start, $limit";
        }

        $res = $this->_db->query($query);
        $res->total = $res->num_rows;
        if ($limit > 0) {
            $totals = $this->_db->fetchRow("select FOUND_ROWS() as total");
            $res->total = $totals['total'];
        }

        return $res;
    }

    public function getFormFields($tabs) {
        return $tabs;
    }

    public function getNamespace() {
        return $this->_namespace;
    }

    public function updateViews() {
        $this->_db->query("update #__ams_pages set views = views + 1 where ams_page_id = '" . $this->id . "'");
    }

}
