<?php

namespace GbitStudio\Gdt;

/**
 * Класс Response - сервис для управления ответом приложения (Laravel-style)
 */
class Response
{
    /** @var object */
    protected $registry;

    /** @var object */
    protected $response;

    /**
     * @param object $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->response = $registry->get('response');
    }

    /**
     * Устанавливает JSON-ответ
     *
     * @param mixed $data
     * @param int $status
     * @return $this
     */
    public function json($data, $status = 200)
    {
        $this->header('Content-Type: application/json');
        $this->status($status);
        $this->content(json_encode($data));
        
        return $this;
    }

    /**
     * Устанавливает контент ответа
     *
     * @param string $content
     * @return $this
     */
    public function content($content)
    {
        $this->response->setOutput($content);
        return $this;
    }

    /**
     * Устанавливает HTTP-заголовок
     *
     * @param string $header
     * @return $this
     */
    public function header($header)
    {
        $this->response->addHeader($header);
        return $this;
    }

    /**
     * Устанавливает HTTP-статус
     *
     * @param int $status
     * @return $this
     */
    public function status($status)
    {
        if ($status !== 200) {
            $this->header('HTTP/1.1 ' . $status);
        }
        return $this;
    }

    /**
     * Выполняет перенаправление
     *
     * @param string $url
     * @param int $status
     * @return void
     */
    public function redirect($url, $status = 302)
    {
        $this->response->redirect($url, $status);
    }

    /**
     * Возвращает нативный объект ответа OpenCart
     *
     * @return object
     */
    public function native()
    {
        return $this->response;
    }
}
