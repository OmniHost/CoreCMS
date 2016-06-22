<?php

class ModelMarketingForm extends \Core\Model {

    public function addForm($data) {
        $settings = array();
        if (is_array($data['form_settings'])) {

            foreach ($data['form_settings'] as $row) {
                if (isset($row[0]) && isset($row[1]))
                    $settings[$row[0]] = $row[1];
            }
            if (!isset($data['form_data']))
                $data['form_data'] = array();
            $this->db->query("INSERT INTO #__formcreator SET title = '" . $this->db->escape($settings['title']) . "', `url` = '" . $this->db->escape($settings['url']) . "', `email` = '" . $this->db->escape($settings['email']) . "', success_msg = '" . $this->db->escape($settings['success_msg']) . "', status = '" . (int) $settings['status'] . "', formdata = '" . $this->db->escape(serialize($data['form_data'])) . "', date_added = " . time());

            $form_id = $this->db->getLastId();

            if ($settings['url']) {
                $this->db->query("INSERT INTO #__url_alias SET query = 'form_id=" . (int) $form_id . "', keyword = '" . $this->db->escape($settings['url']) . "'");
            }

            $this->cache->delete('category');
        }
        return $form_id;
    }

    public function editForm($form_id, $data) {

        if (empty($data['form_data'])) {
            $data['form_data'] = array();
        }

        $settings = array();
        if (is_array($data['form_settings'])) {

            foreach ($data['form_settings'] as $row) {
                if (isset($row[0]) && isset($row[1]))
                    $settings[$row[0]] = $row[1];
            }
            $this->db->query("UPDATE #__formcreator SET title = '" . $this->db->escape($settings['title']) . "', `url` = '" . $this->db->escape($settings['url']) . "', `email` = '" . $this->db->escape($settings['email']) . "', success_msg = '" . $this->db->escape($settings['success_msg']) . "', status = '" . (int) $this->db->escape($settings['status']) . "',
			 formdata = '" . $this->db->escape(serialize($data['form_data'])) . "'  WHERE form_id = '" . (int) $form_id . "'");


            $this->db->query("DELETE FROM #__url_alias WHERE query = 'form_id=" . (int) $form_id . "'");


            if ($settings['url']) {
                $this->db->query("INSERT INTO #__url_alias SET query = 'form_id=" . (int) $form_id . "', keyword = '" . $this->db->escape($settings['url']) . "'");
            }

            $this->cache->delete('category');
        }
    }

    public function deleteForm($form_id) {
        $this->db->query("DELETE FROM #__formcreator WHERE form_id = '" . (int) $form_id . "'");
        $this->db->query("DELETE FROM #__url_alias WHERE query = 'form_id=" . (int) $form_id . "'");

        $this->cache->delete('category');
    }

    public function getForm($form_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM #__formcreator WHERE form_id = '" . (int) $form_id . "'");

        return $query->row;
    }

    public function getForms($parent_id = 0) {

        $category_data = array();
        $query = $this->db->query("SELECT * FROM #__formcreator ORDER BY form_id, title ASC");

        foreach ($query->rows as $result) {
            $category_data[] = array(
                'form_id' => $result['form_id'],
                'title' => $result['title'],
                'status' => $result['status'],
            );
        }


        return $category_data;
    }

    public function getTotalForms() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__formcreator");

        return $query->row['total'];
    }

    public function getFormEdit($fieldlist) {
        $html = '';

        if (is_array($fieldlist))
            foreach ($fieldlist as $index => $row):
                $tmparr = array();
                $tmp = explode("_", $row[0]);
                $key = implode("_", array($tmp[0], $tmp[1], $tmp[2]));

                foreach ($row[1] as $field) {
                    if ($field['name'] == 'option[]') {
                        $tmparr['option'][] = $field['value'];
                    } else {
                        $tmparr[$field['name']] = $field['value'];
                    }
                }

                extract($tmparr, EXTR_OVERWRITE);

                $required = isset($required) && $required == 1 ? 'checked' : '';

                if (isset($option)) {
                    $opthtml = '<table class="checkboxlist"><tbody>';
                    foreach ($option as $opt) {
                        $rand = rand(999, 9999);
                        $opthtml .= '<tr id="checkbox_' . $rand . '"><td><input type="text" value="' . $opt . '" name="option[]"></td><td><a onclick="remove_this(\'checkbox\',\'' . $rand . '\')" href="javascript:void(0);">Remove</a></td></tr>';
                    }
                    $opthtml .= '</tbody></table>';
                } else {
                    $opthtml = '<table class="checkboxlist"></table>';
                }

                switch ($key) {

                    case 'form_tab_title' :
                        $html .= '
                    <li id="form_tab_title_' . $index . '"><form id="52_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                    <div class="form-group">
                                        <label for="">Title</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </li>
                   ';
                        break;
                    case 'form_tab_paragraph' :
                        $html .= '
                    <li id="form_tab_paragraph_' . $index . '">
                        <form id="44_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">New Paragraph</td><td></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                    <div class="form-group">
                                        <label for="">Paragraph</label>
                                        <textarea name="paragraph" class="widefat  val_title" rows="4" cols="10">' . $paragraph . '</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </li>
                  ';
                        break;
                    case 'form_tab_email' :
                        $html .= '
                    <li id="form_tab_email_' . $index . '"><form id="91_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td><input type="text" class="boxhidden"></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                    <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat  val_title" value="' . $title . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Help Text</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </p>
                                    <div class="form-group">
                                        <label for="">Required <span class="red">*</span></label>
                                        <input type="checkbox" name="required" ' . $required . ' class="widefat" value="1">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </li>
                       ';
                        break;
                    case 'form_tab_signlelinetext' :
                        $html .= '
                    <li id="form_tab_signlelinetext_' . $index . '"><form id="62_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td><input type="text" class="boxhidden"></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                    <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Help Text</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Required <span class="red">*</span></label>
                                        <input type="checkbox" name="required" ' . $required . ' class="widefat" value="1">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </li>
                       ';
                        break;
                    case 'form_tab_multilinetext' :
                        $html .= '
                    <li id="form_tab_multilinetext_' . $index . '"><form id="56_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td><textarea class="boxhidden"></textarea></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                    <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Help Text</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Rows</label>
                                        <input type="text" name="rows" class="widefat" value="' . $rows . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Cols</label>
                                        <input type="text" name="cols" class="widefat" value="' . $cols . '">
                                    </div>  
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Required <span class="red">*</span></label>
                                        <input type="checkbox" name="required" class="widefat" ' . $required . ' value="1">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </li>
                       ';
                        break;
                    case 'form_tab_checkbox' :
                        $html .= '
                    <li id="form_tab_checkbox_' . $index . '"><form id="35_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td><input type="checkbox"> &nbsp; <input type="checkbox"></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                    <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefa val_titlet" value="' . $title . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Help Text</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Required <span class="red">*</span></label>
                                        <input type="checkbox" name="required" class="widefat" ' . $required . ' value="1">
                                    </div>
                                    <div class="form-group">
                                        <label for="">List Items</label>
                                        <input type="button" onclick="add_checkbox(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\'))" class="widefat" value="Enter items as text">
                                    </div>' . $opthtml . '
                                    <p></p>

                                </div>
                            </div>
                        </form>
                    </li>
                       ';
                        break;
                    case 'form_tab_radio' :
                        $html .= '
                    <li id="form_tab_radio_' . $index . '"><form id="78_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td><input type="radio"> &nbsp;<input type="radio"></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                    <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Help Text</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Required <span class="red">*</span></label>
                                        <input type="checkbox" name="required" class="widefat" ' . $required . '  value="1">
                                    </div>
                                   <div class="form-group">
                                        <label for="">List Items</label>
                                        <input type="button" onclick="add_checkbox(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\'))" class="widefat" value="Enter items as text">
                                    </div>' . $opthtml . '
                                    <p></p>

                                </div>
                            </div>
                        </form>
                    </li>
                       ';
                        break;
                    case 'form_tab_dropdown' :
                        $html .= '
                    <li id="form_tab_dropdown_' . $index . '"><form id="15_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td><select disabled="" style="width: 200px !important;"><option>Select</option></select></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                    <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Help Text</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Required <span class="red">*</span></label>
                                        <input type="checkbox" name="required" class="widefat" ' . $required . ' value="1">
                                    </div>
                                    <div class="form-group">
                                        <label for="">List Items</label>
                                        <input type="button" onclick="add_checkbox(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\'))" class="widefat" value="Enter items as text">
                                    </div>' . $opthtml . '
                                    <p></p>

                                </div>
                            </div>
                        </form>
                    </li>
                       ';
                        break;
                    case 'form_tab_multidropdow' :
                        $html .= '
                    <li id="form_tab_multidropdow_' . $index . '"><form id="70_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td><select disabled="" style="width: 200px !important;"><option>Select</option></select></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                    <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Help Text</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Required <span class="red">*</span></label>
                                        <input type="checkbox" name="required" class="widefat" ' . $required . ' value="1">
                                    </div>
                                    <div class="form-group">
                                        <label for="">List Items</label>
                                        <input type="button" onclick="add_checkbox(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\'))" class="widefat" value="Enter items as text">
                                    </div>' . $opthtml . '
                                    <p></p>

                                </div>
                            </div>
                        </form>
                    </li>
                       ';
                        break;
                    case 'form_tab_upload' :
                        $html .= '
                    <li id="form_tab_upload_' . $index . '"><form id="52_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td><input type="file" disabled=""></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                    <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Help Text</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </div>
                                   <div class="form-group">
                                        <label for="">Required <span class="red">*</span></label>
                                        <input type="checkbox" name="required" class="widefat" ' . $required . ' value="1">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </li>
                       ';
                        break;
                    case 'form_tab_date' :
                        $html .= '
                    <li id="form_tab_date_' . $index . '"><form id="52_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td><input type="text" class="boxhidden"></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                   <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Help Text</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </div>
                                   <div class="form-group">
                                        <label for="">Required <span class="red">*</span></label>
                                        <input type="checkbox" name="required" class="widefat" ' . $required . ' value="1">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </li>
                       ';
                        break;
                    case 'form_tab_time' :
                        $html .= '
                    <li id="form_tab_time_' . $index . '"><form id="52_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td><input type="text" class="boxhidden"></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                  <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Help Text</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Required <span class="red">*</span></label>
                                        <input type="checkbox" name="required" class="widefat" ' . $required . ' value="1">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </li>
                       ';
                        break;
                    case 'form_tab_captcha' :
                        $html .= '
                    <li id="form_tab_captcha_' . $index . '"><form id="60_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td><input type="text" class="boxhidden"></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                    <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Help Text</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Required <span class="red">*</span></label>
                                        <input type="checkbox" name="required" class="widefat" checked value="1">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </li>
                       ';
                        break;
                    case 'form_tab_submit' :
                        $html .= '
                    <li id="form_tab_submit_' . $index . '"><form id="65_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td></td><td><span onclick="showedit(jQuery(this).parentsUntil(\'li\').parent().attr(\'id\')) " class="edit">Edit</span> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                   <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                </div>
                                <div class="form-group">
                                        <label for="">Row Class</label>
                                        <input type="text" value="' . $help . '" class="form-control widefat" name="help">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Button Class</label>
                                        <input type="text" name="width" class="widefat" value="' . $width . '">
                                    </div>
                            </div>
                        </form>
                    </li>
                    ';
                        break;
                    
                    case "form_tab_linebreak":
                        $html .= '
                    <li id="form_tab_linebreak_' . $index . '"><form id="95_id">
                                                        <div class="widget-content well well-sm">
                                <table class="table"><tbody><tr><td class="tab_title">' . $title . '</td><td></td><td> &nbsp;&nbsp; <span onclick="jQuery(this).parentsUntil(\'li\').parent().remove()" class="delete">Delete</span></td></tr></tbody></table>
                                <div class="expand">
                                   <div class="form-group">
                                        <label for="">Label</label>
                                        <input type="text" name="title" class="widefat val_title" value="' . $title . '">
                                    </div>
                                </div>
                                
                            </div>
                        </form>
                    </li>
                    ';
                        break;

                    default : break;
                }
            endforeach;
        return $html;
    }

}

?>