<?php

include_once dirname(__FILE__).'/../../classes/PosVegamenuSubmenuItemClass.php';
class AdminPosvegamenuController extends ModuleAdminController {
    public function __construct() {
		$this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
        $this->meta_title = $this->l('Pos Vegamenu');
        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }
    public function ajaxProcessSave()
    {
        $data = Tools::getValue('data');
        $id_column = Tools::getValue('id_column');
        $id = Tools::getValue('id');
        //echo $id; die;
        $title = array();
        $errors = array();
        if ($id) {
            $model = new PosVegamenuSubmenuItemClass($id);
        } else {
            $model = new PosVegamenuSubmenuItemClass();
            $model->position = PosVegamenuSubmenuItemClass::getLastPosition() + 1;
            $model->active = 1;
        }
        
        foreach ($data as $param) {
            if ($param['name'] == 'type_link') {
                $model->type_link = pSQL($param['value']);
            }
            if ($param['name'] == 'category_tree') {
                $model->category_tree = pSQL($param['value']);
            }
            if ($param['name'] == 'ps_link') {
                $model->ps_link = pSQL($param['value']);
            }
            if ($param['name'] == 'type_item') {
                $model->type_item = pSQL($param['value']);
            }
            if ($param['name'] == 'id_product') {
                $model->id_product = pSQL($param['value']);
            }
            if ($param['name'] == 'id_manufacturer') {
                $model->id_manufacturer = pSQL($param['value']);
            }
            if ($param['name'] == 'active_mobile') {
                $model->active_mobile = pSQL($param['value']);
            }
            $languages = Language::getLanguages(false);
            foreach ($languages as $language)
            {   
                if ($param['name'] == 'customlink_title_'.$language['id_lang']) {
                    $model->customlink_title[$language['id_lang']] = pSQL($param['value']);
                }
                if ($param['name'] == 'customlink_link_'.$language['id_lang']) {
                    $model->customlink_link[$language['id_lang']] = pSQL($param['value']);
                }
                if ($param['name'] == 'htmlcontent_'.$language['id_lang']) {
                    $model->htmlcontent[$language['id_lang']] = $param['value'];
                }
                if ($param['name'] == 'image_'.$language['id_lang']) {
                    $model->image[$language['id_lang']] = pSQL($param['value']);
                }
                if ($param['name'] == 'image_link_'.$language['id_lang']) {
                    $model->image_link[$language['id_lang']] = pSQL($param['value']);
                }
            }
            
        }
        $model->id_posvegamenu_submenu_column = $id_column;
        if ($errors) {
            die(json_encode(array(
                'success' => 0,
                'errors' => $errors
            )));
        }
        if ($id) {
            $model->save();
        }else{
            $model->add();
        }
        $this->module->clearCache();
        die(json_encode(array(
            'success' => 1,
            'errors' => $errors,
            'model' => $model
        )));
    }
    public function ajaxProcessSwitch()
    {
        $id = Tools::getValue('id');
        $model = new PosVegamenuSubmenuItemClass($id);
        $model->active = !$model->active;
        $model->save();
        $this->module->clearCache();
        die(json_encode(array(
            'active' => (int)$model->active
        )));
    }
    public function ajaxProcessEdit()
    {
        $id = Tools::getValue('id');
        $model = new PosVegamenuSubmenuItemClass($id);
        if($model->id_product > 0) {

            $model->product_name = $this->getProductnameById($model->id_product).' - ID: '.$model->id_product; 
        }else{
            $model->product_name = '';
        }
        die(json_encode($model));
    }
    public function getProductnameById($id_prod)
    {
        $id_lang = (int)$this->context->language->id;   
        $name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT pl.name
        FROM '._DB_PREFIX_.'product_lang pl
        WHERE pl.id_product ='.$id_prod.'
        AND pl.id_lang = '.$id_lang.'');
        return $name['name'];
    }
    public function ajaxProcessDelete(){
        $id = Tools::getValue('id');
        $model = new PosVegamenuSubmenuItemClass($id);
        die(json_encode(array(
            'success' => $model->delete()
        )));
    }
    public function ajaxProcessReload()
    {
        die(json_encode(array(
            'content' => $this->module->renderSubmenu()
        )));
    }
    public function ajaxProcessEditColumn()
    {
        $id = Tools::getValue('id');
        $model = new PosVegamenuSubmenuColumnClass($id);
        die(json_encode($model));
    }
    public function ajaxProcessSaveColumn(){
        $data = Tools::getValue('data');
        $id_row = Tools::getValue('id_row');
        $id = Tools::getValue('id');
        
        $errors = array();
        if ($id) {
            $model = new PosVegamenuSubmenuColumnClass($id);
        } else {
            $model = new PosVegamenuSubmenuColumnClass();
            $model->position = PosVegamenuSubmenuColumnClass::getLastPosition() + 1;
            $model->active = 1;
        }
        
        foreach ($data as $param) {
            if ($param['name'] == 'column_width') {
                $model->width = pSQL($param['value']);
            }
            if ($param['name'] == 'column_class') {
                $model->class = pSQL($param['value']);
            }
            if ($param['name'] == 'column_type_link') {
                $model->type_link = pSQL($param['value']);
            }
            if ($param['name'] == 'column_link') {
                $model->link = pSQL($param['value']);
            }
            if ($param['name'] == 'active_mobile') {
                $model->active_mobile = pSQL($param['value']);
            }
            $languages = Language::getLanguages(false);
            foreach ($languages as $language)
            {   
                if ($param['name'] == 'column_title_'.$language['id_lang']) {
                    $model->title[$language['id_lang']] = pSQL($param['value']);
                }
                if ($param['name'] == 'column_custom_link_'.$language['id_lang']) {
                    $model->custom_link[$language['id_lang']] = pSQL($param['value']);
                }
            }
 
        }
        $model->id_row = $id_row;
        if ($errors) {
            die(json_encode(array(
                'success' => 0,
                'errors' => $errors
            )));
        }
        if ($id) {
            $model->save();
        }else{
            $model->add();
        }
        $this->module->clearCache();
        die(json_encode(array(
            'success' => 1,
            'errors' => $errors,
            'model' => $model
        )));
    }
    public function ajaxProcessDeleteColumn(){
        $id = Tools::getValue('id');
        $model = new PosVegamenuSubmenuColumnClass($id);
        die(json_encode(array(
            'success' => $model->delete()
        )));
    }
    // Row functions
    public function ajaxProcessEditRow()
    {
        $id = Tools::getValue('id');
        $model = new PosVegamenuSubmenuRowClass($id);
        die(json_encode($model));
    }
    public function ajaxProcessSaveRow(){
        $data = Tools::getValue('data');
        $id_posvegamenu_item = Tools::getValue('id_posvegamenu_item');
        $id = Tools::getValue('id');
        
        $errors = array();
        if ($id) {
            $model = new PosVegamenuSubmenuRowClass($id);
        } else {
            $model = new PosVegamenuSubmenuRowClass();
            $model->position = PosVegamenuSubmenuRowClass::getLastPosition() + 1;
            $model->active = 1;
        }
        
        foreach ($data as $param) {
            if ($param['name'] == 'column_class') {
                $model->class = pSQL($param['value']);
            }
 
        }
        $model->id_posvegamenu_item = $id_posvegamenu_item;
        if ($errors) {
            die(json_encode(array(
                'success' => 0,
                'errors' => $errors
            )));
        }
        if ($id) {
            $model->save();
        }else{
            $model->add();
        }
        $this->module->clearCache();
        die(json_encode(array(
            'success' => 1,
            'errors' => $errors,
            'model' => $model
        )));
    }
    public function ajaxProcessSwitchRow()
    {
        $id = Tools::getValue('id');
        $model = new PosVegamenuSubmenuRowClass($id);
        $model->active = !$model->active;
        $model->save();
        $this->module->clearCache();
        die(json_encode(array(
            'success' => 1,
        )));
    }
    public function ajaxProcessDeleteRow(){
        $id = Tools::getValue('id');
        $model = new PosVegamenuSubmenuRowClass($id);
        die(json_encode(array(
            'success' => $model->delete()
        )));
    }
}
