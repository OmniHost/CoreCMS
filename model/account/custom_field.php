<?php

class ModelAccountCustomField extends \Core\Model {

    public function getCustomField($custom_field_id) {
        $query = $this->db->query("SELECT * FROM `#__custom_field` WHERE status = '1' AND custom_field_id = '" . (int) $custom_field_id . "'");

        return $query->row;
    }

    public function getCustomFields($customer_group_id = 0) {
        $custom_field_data = array();

        if (!$customer_group_id) {
            $custom_field_query = $this->db->query("SELECT * FROM `#__custom_field` WHERE status = '1' AND status = '1' ORDER BY sort_order ASC");
        } else {
            if(is_array($customer_group_id)){
                $customer_group_id = implode(",", $customer_group_id);
            }
            $custom_field_query = $this->db->query("SELECT * FROM `#__custom_field_customer_group` cfcg LEFT JOIN `#__custom_field` cf ON (cfcg.custom_field_id = cf.custom_field_id) WHERE cf.status = '1'  AND cfcg.customer_group_id in (" .  $customer_group_id . ") ORDER BY cf.sort_order ASC");
        }

        foreach ($custom_field_query->rows as $custom_field) {
            $custom_field_value_data = array();

            if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio' || $custom_field['type'] == 'checkbox') {
                $custom_field_value_query = $this->db->query("SELECT * FROM #__custom_field_value cfv WHERE cfv.custom_field_id = '" . (int) $custom_field['custom_field_id'] . "' ORDER BY cfv.sort_order ASC");

                foreach ($custom_field_value_query->rows as $custom_field_value) {
                    $custom_field_value_data[$custom_field_value['custom_field_value_id']] = array(
                        'custom_field_value_id' => $custom_field_value['custom_field_value_id'],
                        'name' => $custom_field_value['name']
                    );
                }
            }

            $custom_field_data[] = array(
                'custom_field_id' => $custom_field['custom_field_id'],
                'custom_field_value' => $custom_field_value_data,
                'name' => $custom_field['name'],
                'type' => $custom_field['type'],
                'value' => $custom_field['value'],
                'location' => $custom_field['location'],
                'required' => empty($custom_field['required']) || $custom_field['required'] == 0 ? false : true,
                'sort_order' => $custom_field['sort_order']
            );
        }

        return $custom_field_data;
    }

}
