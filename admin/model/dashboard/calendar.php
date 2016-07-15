<?php

/**
 * ModelCalendarPHCalendar Class
 *
 * @author Gustavo Fernandes
 * @date 05/17/2016
 * @version 1.0.0
 */
class ModelDashboardCalendar extends \Core\Model {
    /* Public Methods */

    public function getData($start, $end, $where = '') {

        $result = $this->db->query("SELECT * FROM #__mod_calendar WHERE AgeUserId = '" . $this->session->data['user_id'] . "' " . $where . " ORDER BY AgeId");

        $data = [];
        foreach ($result->rows as $key => $value) {

            switch ($value['AgeRepetir']) {

                // Only Once
                case '0':
                    $data = $this->checkOnlyOnce($start, $end, $value, $data);
                    break;

                // Daily
                case '1':
                    $data = $this->checkDaily($start, $end, $value, $data);
                    break;

                // Weekly
                case '2':
                    $data = $this->checkWeekly($start, $end, $value, $data);
                    break;

                // Each two weeks
                case '3':
                    $data = $this->checkEachTwoWeeks($start, $end, $value, $data);
                    break;

                // Monthly
                case '4':
                    $data = $this->checkMonthly($start, $end, $value, $data);
                    break;

                // Semesterly
                case '5':
                    $data = $this->checkEachSemester($start, $end, $value, $data);
                    break;

                // Yearly
                case '6':
                    $data = $this->checkYearly($start, $end, $value, $data);
                    break;
            }
        }

        return $data;
    }
    
    public function getTodayCount(){
        $data = $this->getData(DATE("Y-m-d"), DATE("Y-m-d"));

        return count($data);
    }

    public function addData($data) {

        $dataInicial = explode("T", $data['start']);
        $dataFinal = explode("T", $data['end']);

        $insert = [
            'AgeUserId' => $this->session->data['user_id'],
            'AgeNomeEvento' => $data['title'],
            'AgeDescricao' => $data['desc'],
            'AgeCor' => $data['backgroundColor'],
            'AgeDataInicial' => $dataInicial[0],
            'AgeDataFinal' => $dataFinal[0],
            'AgeRepetir' => $data['repetir'],
            'AgeStatus' => '1',
            'AgeInsertDate' => date('Y-m-d H:i:s', time()),
            'AgeUpdateDate' => date('Y-m-d H:i:s', time())
        ];

        if (isset($dataInicial[1]) && isset($dataFinal[1]) && $dataInicial != '' && $dataFinal != '') {
            $insert['AgeHoraInicial'] = $dataInicial[1];
            $insert['AgeHoraFinal'] = $dataFinal[1];
        }

        $fields = [];
        $values = [];
        foreach ($insert as $key => $value) {
            array_push($fields, $key);
            array_push($values, "'" . $this->db->escape($value) . "'");
        }

        $result = $this->db->query("INSERT INTO `#__mod_calendar` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")");
        if ($result) {
            return 1;
        }
        return 0;
    }

    public function editData($data) {

        $dataInicial = explode("T", $data['start']);
        $dataFinal = explode("T", $data['end']);

        $update = [
            'AgeNomeEvento' => $data['title'],
            'AgeDescricao' => $data['desc'],
            'AgeCor' => $data['backgroundColor'],
            'AgeDataInicial' => $dataInicial[0],
            'AgeDataFinal' => $dataFinal[0],
            'AgeRepetir' => $data['repetir'],
            'AgeUpdateDate' => date('Y-m-d H:i:s', time())
        ];

        if (isset($dataInicial[1]) && isset($dataFinal[1]) && $dataInicial != '' && $dataFinal != '') {
            $update['AgeHoraInicial'] = $dataInicial[1];
            $update['AgeHoraFinal'] = $dataFinal[1];
        }

        $sql = [];
        foreach ($update as $key => $value) {
            array_push($sql, $key . "='" . $this->db->escape($value) . "'");
        }

        $result = $this->db->query("UPDATE `#__mod_calendar` SET " . implode(", ", $sql) . " WHERE AgeId = '" . (int) $data['id'] . "'");
        if ($result) {
            return 1;
        }
        return 0;
    }

    public function delData($id) {
        $result = $this->db->query("DELETE FROM `#__mod_calendar` WHERE AgeId = '" . (int) $id . "' AND AgeUserId = '" . $this->session->data['user_id'] . "'");
        if ($result) {
            return 1;
        }
        return 0;
    }

    /* Private Methods */

    private function formatDataEvent($value) {
        $data = [];
        $data['id'] = $value['AgeId'];
        $data['title'] = $value['AgeNomeEvento'];
        $data['start'] = $value['AgeDataInicial'];
        $data['end'] = $value['AgeDataFinal'];
        if ($value['AgeHoraInicial'] != NULL && $value['AgeHoraFinal'] != NULL) {
            $data['start'] .= 'T' . $value['AgeHoraInicial'];
            $data['end'] .= 'T' . $value['AgeHoraFinal'];
            $data['allDay'] = false;
        } else {
            $data['allDay'] = true;
        }
        $data['backgroundColor'] = $value['AgeCor'];
        $data['borderColor'] = $value['AgeCor'];
        $data['desc'] = $value['AgeDescricao'];
        $data['repetir'] = $value['AgeRepetir'];
        return $data;
    }

    private function checkOnlyOnce($start, $end, $value, $data) {
        $initial = strtotime($value['AgeDataInicial']);
        $final = strtotime($value['AgeDataFinal']);
        $start = strtotime(date('Y-m-d', strtotime($start)));
        $end = strtotime(date('Y-m-d', strtotime($end)));
        if ($initial >= $start && $initial <= $end) {
            array_push($data, $this->formatDataEvent($value));
        } else if ($final >= $start && $final <= $end) {
            array_push($data, $this->formatDataEvent($value));
        }
        return $data;
    }

    private function checkDaily($start, $end, $value, $data) {

        $dataStart = new \DateTime($start);
        $dataEnd = new \DateTime($end);
        $interval = $dataStart->diff($dataEnd);
        $day = new \DateInterval('P1D');

        while ((int) $interval->format('%r%a') >= 0) {

            $dataInicial = new \DateTime($value['AgeDataInicial']);
            $dataFinal = new \DateTime($value['AgeDataFinal']);
            $diff = $dataInicial->diff($dataFinal);

            $diffInicialStart = $dataInicial->diff($dataStart);
            if ($diffInicialStart->format('%r') != '-') {
                $value['AgeDataInicial'] = $dataStart->format('Y-m-d');
                $dataStart->add($diff);
                $value['AgeDataFinal'] = $dataStart->format('Y-m-d');
                $dataStart->sub($diff);
                array_push($data, $this->formatDataEvent($value));
            }
            $dataStart->add($day);
            $interval = $dataStart->diff($dataEnd);
        }
        return $data;
    }

    private function checkWeekly($start, $end, $value, $data) {

        $dataStart = new \DateTime($start);
        $dataEnd = new \DateTime($end);
        $interval = $dataStart->diff($dataEnd);
        $week = new \DateInterval('P7D');

        while ((int) $interval->format('%r%a') >= 0) {
            $dataInicial = new \DateTime($value['AgeDataInicial']);
            $dataFinal = new \DateTime($value['AgeDataFinal']);
            $diff = $dataInicial->diff($dataFinal);

            $diffInicialStart = $dataInicial->diff($dataStart);
            $weeksFromInicial = ceil((int) $diffInicialStart->format('%r%a') / 7);
            if ($weeksFromInicial > 0) {
                $dataInicial->add(new \DateInterval('P' . ($weeksFromInicial * 7) . 'D'));
            } else {
                $dataStart = $dataInicial;
            }

            $value['AgeDataInicial'] = $dataInicial->format('Y-m-d');
            $dataInicial->add($diff);
            $value['AgeDataFinal'] = $dataInicial->format('Y-m-d');
            $dataInicial->sub($diff);
            array_push($data, $this->formatDataEvent($value));

            $dataStart->add($week);
            $interval = $dataStart->diff($dataEnd);
        }
        return $data;
    }

    private function checkEachTwoWeeks($start, $end, $value, $data) {
        $dataStart = new \DateTime($start);
        $dataEnd = new \DateTime($end);
        $interval = $dataStart->diff($dataEnd);
        $month = new \DateInterval('P14D');

        while ((int) $interval->format('%r%a') >= 0) {
            $dataInicial = new \DateTime($value['AgeDataInicial']);
            $dataFinal = new \DateTime($value['AgeDataFinal']);
            $diff = $dataInicial->diff($dataFinal);

            $diffInicialStart = $dataInicial->diff($dataStart);
            $weeksFromInicial = ceil((int) $diffInicialStart->format('%r%a') / 14);
            if ($weeksFromInicial > 0) {
                $dataInicial->add(new \DateInterval('P' . ($weeksFromInicial * 14) . 'D'));
            } else {
                $dataStart = $dataInicial;
            }

            $value['AgeDataInicial'] = $dataInicial->format('Y-m-d');
            $dataInicial->add($diff);
            $value['AgeDataFinal'] = $dataInicial->format('Y-m-d');
            $dataInicial->sub($diff);
            array_push($data, $this->formatDataEvent($value));

            $dataStart->add($month);
            $interval = $dataStart->diff($dataEnd);
        }
        return $data;
    }

    private function checkMonthly($start, $end, $value, $data) {
        $dataStart = new \DateTime($start);
        $dataEnd = new \DateTime($end);
        $interval = $dataStart->diff($dataEnd);
        $month = new \DateInterval('P1M');
        $previousMonths = -1;

        while ((int) $interval->format('%r%a') >= 0) {
            $dataInicial = new \DateTime($value['AgeDataInicial']);
            $dataFinal = new \DateTime($value['AgeDataFinal']);
            $diff = $dataInicial->diff($dataFinal);

            $diffInicialStart = $dataInicial->diff($dataStart);
            $months = 0;

            if ($diffInicialStart->format('%r') != '-') {
                $months += ((int) $diffInicialStart->format('%y') * 12) + (int) $diffInicialStart->format('%m');
                if ((int) $diffInicialStart->format('%d') > 0) {
                    $months++;
                }
            }

            $dataInicial->add(new \DateInterval('P' . $months . 'M'));
            $diffFinal = $dataInicial->diff($dataEnd);
            if ($diffFinal->format('%r') == '' && $previousMonths != $months) {
                $previousMonths = $months;
                $value['AgeDataInicial'] = $dataInicial->format('Y-m-d');
                $dataInicial->add($diff);
                $value['AgeDataFinal'] = $dataInicial->format('Y-m-d');
                $dataInicial->sub($diff);
                array_push($data, $this->formatDataEvent($value));
            }

            $dataStart->add($month);
            $interval = $dataStart->diff($dataEnd);
        }
        return $data;
    }

    private function checkEachSemester($start, $end, $value, $data) {
        $dataStart = new \DateTime($start);
        $dataEnd = new \DateTime($end);
        $interval = $dataStart->diff($dataEnd);
        $month = new \DateInterval('P6M');

        while ((int) $interval->format('%r%a') >= 0) {
            $dataInicial = new \DateTime($value['AgeDataInicial']);
            $dataFinal = new \DateTime($value['AgeDataFinal']);
            $diff = $dataInicial->diff($dataFinal);

            $diffInicialStart = $dataInicial->diff($dataStart);
            $months = 0;
            if ($diffInicialStart->format('%r') != '-') {
                $months += ((int) $diffInicialStart->format('%y') * 12) + (int) $diffInicialStart->format('%m');
                $monthStart = (int) $dataStart->format('m');
                $monthEnd = (int) $dataEnd->format('m');
                if ((int) $diffInicialStart->format('%d') > 0) {
                    $months++;
                }
                if ($months > 0 && ($monthStart + $monthEnd) % 2 == 0) {
                    if ((int) $dataInicial->format('d') >= (int) $dataStart->format('d')) {
                        $months++;
                    }
                }
            }

            if ($months % 6 == 0) {
                $dataInicial->add(new \DateInterval('P' . $months . 'M'));
                $value['AgeDataInicial'] = $dataInicial->format('Y-m-d');
                $dataInicial->add($diff);
                $value['AgeDataFinal'] = $dataInicial->format('Y-m-d');
                $dataInicial->sub($diff);
                array_push($data, $this->formatDataEvent($value));
            }

            $dataStart->add($month);
            $interval = $dataStart->diff($dataEnd);
        }
        return $data;
    }

    private function checkYearly($start, $end, $value, $data) {
        $dataStart = new \DateTime($start);
        $dataEnd = new \DateTime($end);
        $interval = $dataStart->diff($dataEnd);
        $month = new \DateInterval('P12M');

        while ((int) $interval->format('%r%a') >= 0) {
            $dataInicial = new \DateTime($value['AgeDataInicial']);
            $dataFinal = new \DateTime($value['AgeDataFinal']);
            $diff = $dataInicial->diff($dataFinal);

            $diffInicialStart = $dataInicial->diff($dataStart);
            $months = 0;
            if ($diffInicialStart->format('%r') != '-') {
                $months += ((int) $diffInicialStart->format('%y') * 12) + (int) $diffInicialStart->format('%m');
                $monthStart = (int) $dataStart->format('m');
                $monthEnd = (int) $dataEnd->format('m');
                if ((int) $diffInicialStart->format('%d') > 0) {
                    $months++;
                }
                if ($months > 0 && ($monthStart + $monthEnd) % 2 == 0) {
                    if ((int) $dataInicial->format('d') >= (int) $dataStart->format('d')) {
                        $months++;
                    }
                }
            }

            if ($months % 12 == 0) {
                $dataInicial->add(new \DateInterval('P' . $months . 'M'));
                $value['AgeDataInicial'] = $dataInicial->format('Y-m-d');
                $dataInicial->add($diff);
                $value['AgeDataFinal'] = $dataInicial->format('Y-m-d');
                $dataInicial->sub($diff);
                array_push($data, $this->formatDataEvent($value));
            }

            $dataStart->add($month);
            $interval = $dataStart->diff($dataEnd);
        }
        return $data;
    }

}
