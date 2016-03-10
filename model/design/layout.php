<?php

class ModelDesignLayout extends \Core\Model {

    public function getLayout($route) {
        $query = $this->db->query("SELECT * FROM #__layout_route WHERE '" . $this->db->escape($route) . "' LIKE CONCAT(route, '%') ORDER BY route DESC LIMIT 1");

        if ($query->num_rows) {
            return $query->row['layout_id'];
        } else {
            return 0;
        }
    }

    public function getAmsLayout($ams_page_id) {
        $query = $this->db->query("select layout_id from #__ams_pages where ams_page_id = '" . (int) $ams_page_id . "'");

        if ($query->num_rows) {
            return $query->row['layout_id'];
        } else {
            return 0;
        }
    }

    public function getTemplate($layout_id) {
        if ($layout_id) {
            return $this->db->query("Select template from #__layout where layout_id='" . (int) $layout_id . "'")->row['template'];
        }
        return '';
    }

    public function getLayoutModules($layout_id, $position) {
        $modules = $this->db->query("SELECT * FROM #__layout_module WHERE layout_id = '" . (int) $layout_id . "' AND position = '" . $this->db->escape($position) . "' ORDER BY sort_order")->rows;
        $all_layouts_id = $this->model_design_layout->getLayout('*');
        if ($all_layouts_id) {
            $allModules = $this->db->query("SELECT * FROM #__layout_module WHERE layout_id = '" . (int) $all_layouts_id . "' AND position = '" . $this->db->escape($position) . "' ORDER BY sort_order")->rows;
            return array_merge($allModules, $modules);
        }
        return $modules;
    }

}
