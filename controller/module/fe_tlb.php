<?php

class ControllerModuleFeTlb extends \Core\Controller {

    public function index() {
        $this->language->load('module/fe_tlb');


        $data['style'] = $this->config->get('fe_tlb_style');
        $data['status'] = $this->config->get('fe_tlb_status');
        $data['links'] = $this->config->get('fe_tlb_clink');
        $data['fun_msg'] = $this->config->get('fe_tlb_fun');
        $data['mobile'] = $this->config->get('fe_tlb_mobile');
        
        if(isset($this->request->get['ams_page_id'])){
            $data['ams_page_id'] = $this->request->get['ams_page_id'];
        }
        


        
        

        $data['url'] = $this->config->get('config_url') . 'admin/index.php?p=';
        $data['token'] = '';
        if (!empty($this->session->data['token'])){
            $data['token'] = $this->session->data['token'];
        }
        
        $data['is_ams_page'] = isset($this->request->get['ams_page_id'])? $this->getAmsType($this->request->get['ams_page_id']) : false;
        
        
   
        
        $data['edit'] = $this->language->get('text_edit');
        $data['logout'] = $this->language->get('text_logout');
        $data['having_fun'] = $this->language->get('text_fun');
        $data['enjoy'] = $this->language->get('text_enjoy');
        $data['edit_settings'] = $this->language->get('edit_settings');
        $data['love_oc'] = $this->language->get('love_oc');
        $data['hello_admin'] = $this->language->get('hello_admin');
        $data['dashboard'] = $this->language->get('text_dashboard');
        
        
        $data['text_module'] = $this->language->get('text_module');
        $data['text_user'] = $this->language->get('Users');
        $data['text_setting'] = $this->language->get('text_setting');
        
        $data['text_cms_page'] = $this->language->get('Pages');
        $data['text_blog'] = $this->language->get('Blog Posts');
        
        
      
        $this->user = new \Core\User();
        $data['admin_logged'] = $this->user->isLogged();
        
        
        $data['hello_admin'] = $this->language->get('Hello'). ' ' . $this->user->getUserName();
        
        $this->document->addStyle('view/plugins/fe_tlb.css');
        
 
        return $this->render('module/fe_tlb.phtml', $data);
    }
    
    protected function getAmsType($ams_page_id){
        $this->load->model('cms/page');
        $child = $this->model_cms_page->loadParent($ams_page_id);
        $ns = str_replace(".", "/", $child->getNamespace());
        if($ns){
            $ns .= "/update&ams_page_id=" . $ams_page_id;
        }
        return $ns;
    }

}
