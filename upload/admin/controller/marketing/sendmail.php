<?php

class ControllerMarketingSendmail extends Controller {
    protected $error = array();

    public function index() {
        $this->load->language('marketing/sendmail');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->send();

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketing/sendmail', 'user_token=' . $this->session->data['user_token'], true));
        }

        $data['email'] = !empty($this->request->post['email']) ? $this->request->post['email'] : '';
        $data['subject'] = !empty($this->request->post['subject']) ? $this->request->post['subject'] : '';
        $data['message'] = !empty($this->request->post['message']) ? $this->request->post['message'] : '';

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }

        if (isset($this->error['subject'])) {
            $data['error_subject'] = $this->error['subject'];
        } else {
            $data['error_subject'] = '';
        }

        if (isset($this->error['message'])) {
            $data['error_message'] = $this->error['message'];
        } else {
            $data['error_message'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('marketing/sendmail', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('marketing/sendmail', 'user_token=' . $this->session->data['user_token'], true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('marketing/sendmail', $data));
    }

    public function send() {
        $email = !empty($this->request->post['email']) ? $this->request->post['email'] : '';
        $subject = !empty($this->request->post['subject']) ? $this->request->post['subject'] : '';
        $message = !empty($this->request->post['message']) ? $this->request->post['message'] : '';

        $mail = new Mail($this->config->get('config_mail_engine'));
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

        $mail->setTo($email);
        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
        $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
        $mail->setHtml($message);

        if (file_exists(DIR_APPLICATION . 'model/tool/pro_email.php')) {
            $this->load->model('tool/pro_email');
            $this->model_tool_pro_email->generate(array(
                'mail' => $mail,
                'name' => 'sale.contact',
                'content' => $message
            ));
        } else {
            $mail->send();
        }
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'marketing/sendmail')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['email']) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if (empty($this->request->post['subject'])) {
            $this->error['subject'] = $this->language->get('error_subject');
        }

        if (empty($this->request->post['message'])) {
            $this->error['message'] = $this->language->get('error_message');
        }

        return !$this->error;
    }
}
