<?php

class ModelMarketingForm extends \Core\Model {

    public function getForm($form_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM #__formcreator WHERE form_id = '" . (int) $form_id . "'");

        return $query->row;
    }

    public function getForms() {

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

    public function getActiveForms() {

        $category_data = array();
        $query = $this->db->query("SELECT * FROM #__formcreator WHERE status = 1 ORDER BY form_id, title ASC");

        foreach ($query->rows as $result) {
            $category_data[] = array(
                'form_id' => $result['form_id'],
                'title' => $result['title'],
                'url' => $this->url->link('information/creator', 'form_id=' . $result['form_id'], 'SSL'),
                'status' => $result['status'],
            );
        }


        return $category_data;
    }

    public function getTotalForms() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM #__formcreator");

        return $query->row['total'];
    }

    public function getFormShow($fieldlist) {
        $fieldreturn = array();
        $inputidx = 1;
        foreach ($fieldlist as $row) {
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
            $tmparr['name'] = 'formfield_' . $inputidx++;
            $tmparr['key'] = $key;
            $tmparr['required'] = !empty($tmparr['required'])?'required':'';
            $tmparr['type'] = $tmp[2];

         
            if($tmparr['type'] == 'upload'){
                $tmparr['file'] = (!empty($this->request->files[$tmparr['name']]))? $this->request->files[$tmparr['name']] : array();
            }
            
            
            $tmparr['post'] = (isset($this->request->post[str_replace(' ', '_', $tmparr['name'])])) ? $this->request->post[str_replace(' ', '_', $tmparr['name'])] : '';
            $fieldreturn[] = $tmparr;
            
        }
        return $fieldreturn;
    }

    public function getFormShow_old($fieldlist) {

        $html = '<fieldset>';
        $fieldset = $datepicker = $timepicker = 0;
        if (count($fieldlist))
            foreach ($fieldlist as $row):
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



                $xxx = isset($required) && $required == 1 ? '<span class="reqstar">*</span>' : '';
                $required = isset($required) && $required == 1 ? 'required' : '';

                $value_post = isset($_POST[str_replace(' ', '_', $title)]) ? $_POST[str_replace(' ', '_', $title)] : '';
                $width = isset($width) ? $width : 'col-xs-12';
                $opthtml = '';
                if (isset($option)) {
                    $chosen = '';
                    if ($key == 'form_tab_checkbox') {
                        foreach ($option as $opt) {
                            if (isset($_POST[str_replace(' ', '_', 'multi_1_' . $title)]))
                                foreach ($_POST[str_replace(' ', '_', 'multi_1_' . $title)] as $vp) {
                                    $value_post = isset($vp) ? $vp : '';
                                    if ($opt == $vp)
                                        $chosen = 'checked="checked"';
                                }
                            //    $opthtml .= '' . $opt . '<input ' . $chosen . ' type="checkbox" value="' . $opt . '" name="multi_1_' . $title . '[]" >&nbsp;&nbsp;';
                            $opthtml .= '<div class="checkbox">
  <label>
    <input ' . $chosen . ' type="checkbox" value="' . $opt . '" name="multi_1_' . $title . '[]" >
    ' . $opt . '
  </label>
</div>';
                            $chosen = '';
                        }
                    } else if ($key == 'form_tab_radio') {

                        foreach ($option as $opt) {
                            $value_post = isset($_POST[str_replace(' ', '_', $title)]) ? $_POST[str_replace(' ', '_', $title)] : '';
                            if ($opt == $value_post)
                                $chosen = 'checked="checked"';
                            // $opthtml .= '' . $opt . '<input ' . $chosen . ' type="radio" value="' . $opt . '" name="' . $title . '">&nbsp;&nbsp;';
                            $opthtml .= '<div class="radio">
  <label>
    <input ' . $chosen . ' type="radio" value="' . $opt . '" name="' . $title . '">
    ' . $opt . '
  </label>
</div>';
                            $chosen = '';
                        }
                    } else if ($key == 'form_tab_dropdown') {

                        $opthtml .= '<select name="' . $title . '" id="' . $title . '"  class="form-control">';
                        foreach ($option as $opt) {
                            $value_post = isset($_POST[str_replace(' ', '_', $title)]) ? $_POST[str_replace(' ', '_', $title)] : '';
                            if ($opt == $value_post)
                                $chosen = 'selected="selected"';
                            $opthtml .= '<option ' . $chosen . ' value="' . $opt . '">' . $opt . '</option>';
                            $chosen = '';
                        }
                        $opthtml .= '</select>';
                    } else if ($key == 'form_tab_multidropdow') {
                        $opthtml .= '<select class="form-control" name="multi_1_' . $title . '[]"  id="' . $title . '" multiple="multiple" >';
                        foreach ($option as $opt) {
                            if (isset($_POST[str_replace(' ', '_', 'multi_1_' . $title)]))
                                foreach ($_POST[str_replace(' ', '_', 'multi_1_' . $title)] as $vp) {
                                    $value_post = isset($vp) ? $vp : '';
                                    if ($opt == $vp)
                                        $chosen = 'selected="selected"';
                                }
                            $opthtml .= '<option ' . $chosen . ' value="' . $opt . '">' . $opt . '</option>';
                            $chosen = '';
                        }
                        $opthtml .= '</select>';
                    }
                }

                switch ($key) {

                    case 'form_tab_title' : {


                            if ($fieldset ++) {
                                $html .= '';
                                $html .= '<legend>' . $title . '</legend>';
                                $html .= '';
                            } else {
                                $html .= '<legend>' . $title . '</legend>';
                                $html .= '';
                            }

                            break;
                        }
                    case 'form_tab_paragraph' :
                        $html .= '<p>' . nl2br($paragraph) . '</p>';
                        break;

                    case 'form_tab_email' :




                        $html .= '<div class="' . $width . '">
						<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="email_1_' . $title . '">' . $title . ' ' . $xxx . '</label>
            <div class="col-sm-9">
            <input  type="email" id="email_1_' . $title . '" name="email_1_' . $title . '" value="' . $value_post . '"    class="form-control" >
            </div>
          </div></div>';


                        break;
                    case 'form_tab_signlelinetext' :

                        $html .= '<div class="' . $width . '">'
                                . '<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="' . $title . '">' . $title . ' ' . $xxx . '</label>
            <div class="col-sm-9">
          <input  type="text" id="' . $title . '" name="' . $title . '" value="' . $value_post . '"  class="form-control"  >
            </div>
          </div></div>';
                        break;
                    case 'form_tab_multilinetext' :
                        $html .= '
						<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="' . $title . '">' . $title . ' ' . $xxx . '</label>
            <div class="col-sm-9">
       <textarea  cols="' . $cols . '" rows="' . $rows . '" id="' . $title . '" name="' . $title . '"  class="form-control">' . $value_post . '</textarea>
            </div>
          </div>';
                        break;
                    case 'form_tab_checkbox' :
                        $html .= '
							<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="input-name">' . $title . ' ' . $xxx . '</label>
            <div class="col-sm-9">
            
            
    ' . $opthtml . '
            </div>
          </div>';
                        break;
                    case 'form_tab_radio' :
                        $html .= '
						<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="input-name">' . $title . ' ' . $xxx . '</label>
            <div class="col-sm-9">
    ' . $opthtml . '
            </div>
          </div>';

                        break;
                    case 'form_tab_dropdown' :
                        $html .= '<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="' . $title . '">' . $title . ' ' . $xxx . '</label>
            <div class="col-sm-9">
    ' . $opthtml . '
            </div>
          </div>';

                        break;
                    case 'form_tab_multidropdow' :
                        $html .= '
						<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="' . $title . '">' . $title . ' ' . $xxx . '</label>
            <div class="col-sm-9">
    ' . $opthtml . '
            </div>
          </div>';

                        break;
                    case 'form_tab_upload' :
                        $html .= '
							<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="' . $title . '">' . $title . ' ' . $xxx . '</label>
            <div class="col-sm-9">
     <input type="file" style="width:' . $width . 'px " name="upload_1_' . $title . '"  class="form-control"><input type="hidden" name="filecount[]" value="1">
            </div>
          </div>';
                        break;
                    case 'form_tab_captcha' :
                        $html .= '<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="captcha_1_' . $title . '">' . $title . ' ' . $xxx . '</label>
            <div class="col-sm-9">
             <div class="input-group ">
                                    <input type="text" name="captcha_1_' . $title . '" id="captcha_1_' . $title . '" class="form-control" />
                                    <span class="input-group-addon input-captcha" ><img src="index.php?p=common/captcha" alt="" id="captcha" /></span>
                                    <span class="input-group-btn" ><button id="img_cap_reload" type="button" class="btn btn-default"><i class="fa fa-refresh"></i></button></span>

                                </div>
  </div>
          </div>';
                        break;
                    case 'form_tab_date' :
                        $html .= '<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="datepicker_' . $datepicker . '">' . $title . ' ' . $xxx . '</label>
            <div class="col-sm-9">
  <input type="text" class="form-control" style="width:' . $width . 'px " id="datepicker_' . $datepicker++ . '" name="' . $title . '" value="' . $value_post . '">
            </div>
          </div>';
                        break;
                    case 'form_tab_time' :
                        $html .= '<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="timepicker_' . $timepicker . '">' . $title . ' ' . $xxx . '</label>
            <div class="col-sm-9">
 <input type="text" class="form-control" style="width:' . $width . 'px " id="timepicker_' . $timepicker++ . '" name="' . $title . '" value="' . $value_post . '" ><div style="clear:both;"></div>
            </div>
          </div>
		  ';
                        break;
                    case 'form_tab_submit' :
                        $html .= '
						<div class="form-group ' . $required . '">
            <label class="col-sm-3 control-label" for="input-name"></label>
            <div class="col-sm-9">
 <input type="submit" style="width:' . $width . 'px " class="btn btn-primary" value="' . $title . '" onclick="return checkValidate();" name="submit_1_' . $title . '" ><div style="clear:both;"></div>
            </div>
          </div>';
                        break;

                    default : break;
                }
            endforeach;

        $html .= ' </ul>';
        return $html;
    }

}
