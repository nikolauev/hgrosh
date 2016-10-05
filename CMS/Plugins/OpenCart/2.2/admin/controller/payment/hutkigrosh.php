<?php
class ControllerPaymentHutkiGrosh extends Controller {
    private $error = array();
    public function index() {

        $this->language->load('payment/hutkigrosh');

        $this->document->setTitle = $this->language->get('heading_title');

        $this->load->model('setting/setting');

        // Сохранение или обновление данных
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->load->model('setting/setting');

            $this->model_setting_setting->editSetting('hutkigrosh', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        // Установка языковых констант
        $this->document->setTitle($this->language->get('heading_title'));
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_payment']  = $this->language->get('text_payment');
        $data['text_success']  = $this->language->get('text_success');
        $data['text_storeid']  = $this->language->get('text_storeid');
        $data['text_store']    = $this->language->get('text_store');
        $data['text_test']     = $this->language->get('text_test');
        $data['text_login']    = $this->language->get('text_login');
        $data['text_pswd']     = $this->language->get('text_pswd');
        $data['text_status']   = $this->language->get('text_status');
        $data['text_enabled']  = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['button_save']   = $this->language->get('text_save');
        $data['button_cancel'] = $this->language->get('text_cancel');
        $data['text_sort_order'] = $this->language->get('sort_order');

        // Предупреждение об ошибках
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        // Генерация хлебных крошек
        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_payment'),
            'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('payment/hutkigrosh', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        if (isset($this->request->post['hutkigrosh_storeid'])) {
            $data['hutkigrosh_storeid'] = $this->request->post['hutkigrosh_storeid'];
        } else {
            $data['hutkigrosh_storeid'] = $this->config->get('hutkigrosh_storeid');
        }

        if (isset($this->request->post['hutkigrosh_store'])) {
            $data['hutkigrosh_store'] = $this->request->post['hutkigrosh_store'];
        } else {
            $data['hutkigrosh_store'] = $this->config->get('hutkigrosh_store');
        }

        if (isset($this->request->post['hutkigrosh_login'])) {
            $data['hutkigrosh_login'] = $this->request->post['hutkigrosh_login'];
        } else {
            $data['hutkigrosh_login'] = $this->config->get('hutkigrosh_login');
        }

        if (isset($this->request->post['hutkigrosh_pswd'])) {
            $data['hutkigrosh_pswd'] = $this->request->post['hutkigrosh_pswd'];
        } else {
            $data['hutkigrosh_pswd'] = $this->config->get('hutkigrosh_pswd');
        }

        if (isset($this->request->post['hutkigrosh_test'])) {
            $data['hutkigrosh_test'] = $this->request->post['hutkigrosh_test'];
        } else {
            $data['hutkigrosh_test'] = $this->config->get('hutkigrosh_test');
        }

        if (isset($this->request->post['hutkigrosh_status'])) {
            $data['hutkigrosh_status'] = $this->request->post['hutkigrosh_status'];
        } else {
            $data['hutkigrosh_status'] = $this->config->get('hutkigrosh_status');
        }

        $data['hutkigrosh_order_status_pending'] = isset($this->request->post['hutkigrosh_order_status_pending'])?$this->request->post['hutkigrosh_order_status_pending']:$this->config->get('hutkigrosh_order_status_pending');
        $data['hutkigrosh_order_status_payed'] = isset($this->request->post['hutkigrosh_order_status_payed'])?$this->request->post['hutkigrosh_order_status_payed']:$this->config->get('hutkigrosh_order_status_payed');
        $data['hutkigrosh_order_status_error'] = isset($this->request->post['hutkigrosh_order_status_error'])?$this->request->post['hutkigrosh_order_status_error']:$this->config->get('hutkigrosh_order_status_error');
        
        if (isset($this->request->post['hutkigrosh_sort_order'])) {
            $data['hutkigrosh_sort_order'] = $this->request->post['hutkigrosh_sort_order'];
        } else {
            $data['hutkigrosh_sort_order'] = $this->config->get('hutkigrosh_sort_order');
        }

        // Кнопки
        $data['action'] = $this->url->link('payment/hutkigrosh', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

// Рендеринг шаблона
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/hutkigrosh.tpl', $data));



    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'payment/hutkigrosh')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}