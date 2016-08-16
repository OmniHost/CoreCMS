<?php

class ModelPaymentBankTransfer extends \Core\Model {

    public function getMethod($address, $total) {
        $this->load->language('payment/bank_transfer');

        if ($this->config->get('bank_transfer_total') > 0 && $this->config->get('bank_transfer_total') > $total) {

            $status = false;
        } elseif (!$this->config->get('bank_transfer_geo_zone_id')) {
            $status = true;
        } elseif ($address) {
            $query = $this->db->query("SELECT * FROM #__zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('bank_transfer_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");
            if ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }


        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'bank_transfer',
                'title' => $this->language->get('text_title'),
                'terms' => '',
                'sort_order' => $this->config->get('bank_transfer_sort_order')
            );
        }

        return $method_data;
    }

}
