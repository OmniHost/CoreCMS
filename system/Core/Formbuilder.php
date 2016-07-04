<?php

namespace Core;

class Formbuilder extends \Core\Model {

    /**
     * Returns object from registry
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return \Core\Core::$registry->get($key);
    }

    /**
     * Sets object to the registry
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        \Core\Core::$registry->set($key, $value);
    }

    public function render($field) {

        $field['class'] = (empty($field['class'])) ? 'form-control' : $field['class'] . ' form-control';

        if (!empty($field['required']) && $field['required']) {
            $field['class'] .= ' required';
        }

        if (empty($field['id'])) {
            $field['id'] = slug($field['key']) . 'Input';
        }


        $params = '';
        if (!empty($field['params'])) {
            foreach ($field['params'] as $pk => $pv) {
                $params .= $pk . '="' . $pv . '" ';
            }
        }

        $field['params'] = $params;



        if (method_exists($this, $field['type'])) {

            return call_user_func(array($this, $field['type']), $field);
        } else {
            return call_user_func(array($this, 'text'), $field);
        }
    }

    public function html($field) {

        $this->init_wysiwyg();
        $ed = $this->textarea($field);
        $ed .= '<script>docReady(function() { EditorOn("' . $field['id'] . '"); });</script>';
        return $ed;
    }

    public function textarea($field) {

        $field['rows'] = (empty($field['rows'])) ? 5 : $field['rows'];
        $ed = '<textarea  ' . $field['params'] . ' id="' . $field['id'] . '" name="' . $field['key'] . '" rows="' . $field['rows'] . '" class="' . $field['class'] . '">';
        $ed .= $field['value'];
        $ed .= '</textarea>';

        return $ed;
    }

    public function text($field) {
        return '<input ' . $field['params'] . '  type="' . $field['type'] . '" name="' . $field['key'] . '" class="' . $field['class'] . '" value="' . $field['value'] . '" />';
    }

    public function multitext($field) {

        $this->document->addScript('//code.jquery.com/ui/1.11.4/jquery-ui.min.js');

        $html = '<div class="input-group">'
                . '<input type="text" data-key="' . $field['key'] . '" data-target="' . $field['id'] . '" value="" placeholder="' . $field['label'] . '" id="input-' . $field['id'] . '" class="form-control" />'
                . '<span class="input-group-btn">'
                . ' <button data-target="' . $field['id'] . '" class="btn btn-primary btn-add-multitext" type="button"><i class="fa fa-plus"></i></button>'
                . '</span></div>'
                . '<div id="' . $field['id'] . '" class="well well-sm autocomplete-list sortable" style="height: 150px; overflow: auto;">';

        foreach ($field['value'] as $k => $val) {
            $html .= '<div class="list-group-item" id="' . $field['id'] . '-' . $k . '"><div class="input-group"><span class="input-group-btn"><button class="btn btn-default btn-minus-circle" type="button"><i class="fa fa-minus-circle text-danger"></i></button></span><input class="form-control" type="text" name="' . $field['key'] . '[]" value="' . $val . '"></div></div>';
        }


        $html .= '</div>';

        $html .= '<script>docReady(function() {
    $( "#' . $field['id'] . '" ).sortable();
    $( "#' . $field['id'] . '" ).disableSelection();
  });</script>';

        return $html;
    }

    function autocomplete($field) {

        $value_id = !empty($field['value']['id']) ? $field['value']['id'] : '';
        $value_name = !(empty($field['value']['name'])) ? $field['value']['name'] : '';

        return '<input type="hidden" name="' . $field['key'] . '" value="' . $value_id . '" id="' . $field['id'] . '">'
                . '<input data-target="' . $field['id'] . '" data-limit="1" data-url="' . $field['url'] . '" type="' . $field['type'] . '" name="' . $field['key'] . '_display" class="' . $field['class'] . '" value="' . $value_name . '" />';
    }

    function autocomplete_list_addable($field) {
        $this->document->addScript('//code.jquery.com/ui/1.11.4/jquery-ui.min.js');

        $html = '<div class="input-group"><input type="autocomplete" data-limit="0" data-key="' . $field['key'] . '" data-target="' . $field['id'] . '" data-url="' . $field['url'] . '" value="" placeholder="' . $field['label'] . '" id="input-' . $field['id'] . '" class="form-control" /> <span class="input-group-btn">
        <button id="' . $field['key'] . '-addnew" class="btn btn-default" type="button">' . $this->language->get("Add New") . '</button>
      </span></div>';

        $html .= '<div id="' . $field['id'] . '" class="well well-sm autocomplete-list sortable" style="height: 150px; overflow: auto;">';

        if (!empty($field['value']) && is_array($field['value'])) {
            foreach ($field['value'] as $download) {
                $html .= '<div class="list-group-item" id="' . $field['id'] . '-' . $download['id'] . '"><div class="input-group"><span class="input-group-btn"><button class="btn btn-default btn-minus-circle" type="button"><i class="fa fa-minus-circle text-danger"></i></button></span><span class="form-control"> ' . $download['name'] . '</span><input type="hidden" name="' . $field['key'] . '[]" value="' . $download['id'] . '"></div></div>';
            }
        }

        $html .= '</div>';

        $html .= '<script>docReady(function() {
    $( "#' . $field['id'] . '" ).sortable();
    $( "#' . $field['id'] . '" ).disableSelection();
$(document).on("click","#' . $field['key'] . '-addnew", function(e) { e.preventDefault(); 
    var el = $(this).closest(".input-group").find("input");
    if(el.val() != ""){
        var newtag = el.val();
        $.ajax(
        {
        url: "' . fixajaxurl($field['addable']) . '",
            datatype: "json",
            method: "POST",
            data: {name: newtag},
            success: function(json) {
                if(json.id > 0){
                   el.val("");
                   $("#" + el.data("target")).append(\'<div class="list-group-item" id="\' + el.attr(\'data-target\') + \'-\' + json.id + \'"><div class="input-group"><span class="input-group-btn"><button class="btn btn-default btn-minus-circle" type="button"><i class="fa fa-minus-circle text-danger"></i></button></span><span class="form-control">\' + json.name + \'</span><input type="hidden" name="\' + el.attr(\'data-key\') + \'[]" value="\' + json.id + \'" /></div></div>\');    
                 }
            }
        });

    }
                    
    
}        );

  });</script>';


        return $html;
    }

    function autocomplete_list($field) {

        if ($field['addable']) {
            return $this->autocomplete_list_addable($field);
        }

        $this->document->addScript('//code.jquery.com/ui/1.11.4/jquery-ui.min.js');

        $html = '<input type="autocomplete" data-limit="0" data-key="' . $field['key'] . '" data-target="' . $field['id'] . '" data-url="' . $field['url'] . '" value="" placeholder="' . $field['label'] . '" id="input-' . $field['id'] . '" class="form-control" />';
        $html .= '<div id="' . $field['id'] . '" class="well well-sm autocomplete-list sortable" style="height: 150px; overflow: auto;">';

        if (!empty($field['value']) && is_array($field['value'])) {
            foreach ($field['value'] as $download) {
                $html .= '<div class="list-group-item" id="' . $field['id'] . '-' . $download['id'] . '"><div class="input-group"><span class="input-group-btn"><button class="btn btn-default btn-minus-circle" type="button"><i class="fa fa-minus-circle text-danger"></i></button></span><span class="form-control"> ' . $download['name'] . '</span><input type="hidden" name="' . $field['key'] . '[]" value="' . $download['id'] . '"></div></div>';
            }
        }

        $html .= '</div>';

        $html .= '<script>docReady(function() {
    $( "#' . $field['id'] . '" ).sortable();
    $( "#' . $field['id'] . '" ).disableSelection();
  });</script>';

        return $html;
    }

    function scrollbox($field) {

        $return = '<div class="scrollbox">
                <ul class="list-group">';
        foreach ($field['options'] as $option) {
            $return .= '<li class="list-group-item">';
            if (in_array($option['ams_page_id'], $field['value'])) {
                $return .= '<input type="checkbox" name="' . $field['key'] . '[]" value="' . $option['ams_page_id'] . '" checked="checked"> ' . $option['name'];
            } else {
                $return .= '<input type="checkbox" name="' . $field['key'] . '[]" value="' . $option['ams_page_id'] . '"> ' . $option['name'];
            }
            $return .= '</li>';
        }
        $return .= '</ul></div>';
        return $return;
    }

    function select($field) {
        $return = '<select  ' . $field['params'] . '  name="' . $field['key'] . '" id="' . $field['key'] . 'Input" class="' . $field['class'] . '">';
        foreach ($field['options'] as $ok => $ov) {
            if ($ok == $field['value']) {
                $return .= '<option selected value="' . $ok . '">' . $ov . '</option>';
            } else {
                $return .= '<option value="' . $ok . '">' . $ov . '</option>';
            }
        }
        $return .= '</select>';
        return $return;
    }

    function image($field) {
        return '<a href="" id="thumb-' . slug($field['key']) . '" data-toggle="image" class="img-thumbnail">'
                . '<img src="' . $field['thumb'] . '" alt="" title="" data-placeholder="' . $field['placeholder'] . '" /></a>'
                . '<input type="hidden" name="' . $field['key'] . '" value="' . $field['value'] . '" id="input-' . slug($field['key']) . '" />';
    }

    function date($field, $time = false) {
        $this->document->addScript('view/plugins/datetimepicker/moment.min.js');
        $this->document->addScript('view/plugins/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/plugins/datetimepicker/bootstrap-datetimepicker.min.css');

        $id = !empty($field['id']) ? $field['id'] : slug('input-' . $field['key']);

        if ($time) {
            $dateformat = dateformat_PHP_to_MomentJs($this->language->get('date_time_format_short'));
            // $field['class'] .= 'datetimeinput'
            $script = '$(\'#' . $id . '\').datetimepicker({format: "' . $dateformat . '"});});';
        } else {
            $dateformat = dateformat_PHP_to_MomentJs($this->language->get('date_format_short'));
            // $field['class'] .= 'dateinput'
            $script = '$(\'#' . $id . '\').datepicker({format: "' . $dateformat . '"});});';
        }



        return '<input ' . $field['params'] . '  id="' . $id . '" data-date-format="' . $dateformat . '" type="text" name="' . $field['key'] . '" class="' . $field['class'] . '" value="' . $field['value'] . '" />'
                . ''
                . '<script>docReady(function () {'
                . $script
                . '</script>';
    }

    function display($field) {
        return '<span class="' . $field['class'] . '">' . $field['value'] . '</span><input type="hidden" name="' . $field['key'] . '" class="' . $field['class'] . '" value="' . $field['value'] . '" />';
    }

    function datetime($field) {
        return $this->date($field, true);
    }

    function builder($field) {
        $this->init_wysiwyg();
        $this->document->addScript('//code.jquery.com/ui/1.11.4/jquery-ui.min.js');
        $this->document->addScript('view/plugins/grid-editor/dist/jquery.grideditor.js');
        $this->document->addStyle('view/plugins/grid-editor/dist/grideditor.css');

        if (empty($field['id'])) {
            $field['id'] = slug($field['key']) . 'Input';
        }

        $gridId = $field['id'] . 'Grid';

        $html = '<div class="builder-container-wrapper">
                    <div class="builder-container-topwrapper">
                        <div class="container">&nbsp;</div>
                    </div>    
                    <div class="builder-container">
                        <div class="container">
                        <textarea name="' . $field['key'] . '" id="' . $field['id'] . '" class="' . $field['id'] . '">' . $field['value'] . '</textarea>
                            <div id="' . $gridId . '">' . $field['value'] . '</div>
                        </div>
                    </div>
                </div>';

        $html .= '<script>
        docReady(function() {
            builderOn();
            // Initialize grid editor
            $(\'#' . $gridId . '\').gridEditor({
                content_types: [\'ckeditor\'],
                source_textarea: "textarea.' . $field['id'] . '",
               row_classes       : [
               { label: "Well", cssClass: "well"},
               { label: "Primary Background", cssClass: "bg-primary"},
               { label: "Success Background", cssClass: "bg-success"},
               { label: "Info Background", cssClass: "bg-info"},
               { label: "Warning Background", cssClass: "bg-warning"},
               { label: "Danger Background", cssClass: "bg-danger"}
               ],
               col_classes       : [
               { label: "Well", cssClass: "well"},
               { label: "Primary Background", cssClass: "bg-primary"},
               { label: "Success Background", cssClass: "bg-success"},
               { label: "Info Background", cssClass: "bg-info"},
               { label: "Warning Background", cssClass: "bg-warning"},
               { label: "Danger Background", cssClass: "bg-danger"}
               ],
            });
            $(\'body\').addClass("sidebar-collapse");
           
        });
    </script>';
        return $html;
    }

    function custom($field) {

        //    protected function getChild($child, &$args = array()) {
        $action = new \Core\Action($field['callable'], $field);

        if (file_exists($action->getFile())) {
            require_once(__modification($action->getFile()));

            $class = $action->getClass();

            $controller = new $class();

            $controller->{$action->getMethod()}($action->getArgs());
            return $controller->getOutput();
        } else {
            throw new \Core\Exception('Error: Could not load controller ' . $child . '!');
            exit();
        }
        //}
        //     return call_user_func_array($field['callable'], $field);
    }

    function init_wysiwyg() {
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $uri = HTTPS_CATALOG;
        } else {
            $uri = HTTP_CATALOG;
        }

        $this->document->addScript('view/plugins/ckeditor/ckeditor.js?');
        $this->document->addScript($uri . '?p=cms/page/ajaxlist');
        $this->document->addScript($uri . '?p=cms/page/downloadlist');
    }

}
