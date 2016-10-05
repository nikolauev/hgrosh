<?php

class ControllerPaymentHutkiGrosh extends Controller {

    public function index() {

        $this->language->load('payment/hutkigrosh');
        $this->load->model('setting/setting');

        // Сохранение или обновление данных
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('hutkigrosh', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        // Установка языковых констант
        $this->document->setTitle($this->language->get('heading_title'));
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_payment']  = $this->language->get('text_payment');
        $this->data['text_success']  = $this->language->get('text_success');
        $this->data['text_storeid']  = $this->language->get('text_storeid');
        $this->data['text_store']    = $this->language->get('text_store');
        $this->data['text_test']     = $this->language->get('text_test');
        $this->data['text_login']    = $this->language->get('text_login');
        $this->data['text_pswd']     = $this->language->get('text_pswd');
        $this->data['text_status']   = $this->language->get('text_status');
        $this->data['text_enabled']  = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['button_save']   = $this->language->get('text_save');
        $this->data['button_cancel'] = $this->language->get('text_cancel');
        $this->data['text_sort_order'] = $this->language->get('sort_order');

        // Предупреждение об ошибках
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        // Генерация хлебных крошек
        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_payment'),
            'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('payment/hutkigrosh', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        if (isset($this->request->post['hutkigrosh_storeid'])) {
            $this->data['hutkigrosh_storeid'] = $this->request->post['hutkigrosh_storeid'];
        } else {
            $this->data['hutkigrosh_storeid'] = $this->config->get('hutkigrosh_storeid');
        }

        if (isset($this->request->post['hutkigrosh_store'])) {
            $this->data['hutkigrosh_store'] = $this->request->post['hutkigrosh_store'];
        } else {
            $this->data['hutkigrosh_store'] = $this->config->get('hutkigrosh_store');
        }

        if (isset($this->request->post['hutkigrosh_login'])) {
            $this->data['hutkigrosh_login'] = $this->request->post['hutkigrosh_login'];
        } else {
            $this->data['hutkigrosh_login'] = $this->config->get('hutkigrosh_login');
        }

        if (isset($this->request->post['hutkigrosh_pswd'])) {
            $this->data['hutkigrosh_pswd'] = $this->request->post['hutkigrosh_pswd'];
        } else {
            $this->data['hutkigrosh_pswd'] = $this->config->get('hutkigrosh_pswd');
        }

        if (isset($this->request->post['hutkigrosh_test'])) {
            $this->data['hutkigrosh_test'] = $this->request->post['hutkigrosh_test'];
        } else {
            $this->data['hutkigrosh_test'] = $this->config->get('hutkigrosh_test');
        }

        if (isset($this->request->post['hutkigrosh_status'])) {
            $this->data['hutkigrosh_status'] = $this->request->post['hutkigrosh_status'];
        } else {
            $this->data['hutkigrosh_status'] = $this->config->get('hutkigrosh_status');
        }

        $data['hutkigrosh_order_status_pending'] = isset($this->request->post['hutkigrosh_order_status_pending'])?$this->request->post['hutkigrosh_order_status_pending']:$this->config->get('hutkigrosh_order_status_pending');
        $data['hutkigrosh_order_status_payed'] = isset($this->request->post['hutkigrosh_order_status_payed'])?$this->request->post['hutkigrosh_order_status_payed']:$this->config->get('hutkigrosh_order_status_payed');
        $data['hutkigrosh_order_status_error'] = isset($this->request->post['hutkigrosh_order_status_error'])?$this->request->post['hutkigrosh_order_status_error']:$this->config->get('hutkigrosh_order_status_error');
        
        if (isset($this->request->post['hutkigrosh_sort_order'])) {
            $this->data['hutkigrosh_sort_order'] = $this->request->post['hutkigrosh_sort_order'];
        } else {
            $this->data['hutkigrosh_sort_order'] = $this->config->get('hutkigrosh_sort_order');
        }

        // Кнопки
        $this->data['action'] = $this->url->link('payment/hutkigrosh', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        // Рендеринг шаблона
        $this->template = 'payment/hutkigrosh.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'payment/pp_standard')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}