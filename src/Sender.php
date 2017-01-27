<?php

namespace Jurager\Sender;

use Illuminate\Support\Facades\Config;

class Sender
{

    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;


    /**
     * Settings
     *
     */
    public $agent;   
    public $username;
    public $password;
    public $name;  


    private $errors = [
        '-1'  => 'недостаточно средств',
        '-2'  => 'неправильный логин или пароль (ошибка при аутентификации)',
        '-3'  => 'отсутствует текст сообщения',
        '-4'  => 'некорректное значение номера получателя',
        '-5'  => 'некорректное значение отправителя сообщения',
        '-6'  => 'отсутствует логин',
        '-7'  => 'отсутствует пароль',
        '-10' => 'сервис временно недоступен',
        '-11' => 'некорректное значение ID сообщения',
        '-12' => 'другая ошибка',
        '-13' => 'заблокировано',
        '-14' => 'запрос не укладывается в ограничения по времени на отправку SMS',
        '-15' => 'некорректное значение даты отправки рассылки',
    ];

    private $statuses = [
        'Queued'    => 'В очереди',
        'Sent'      => 'Отправлено',
        'Delivered' => 'Доставлено',
        'Expired'   => 'Просрочено',
        'Rejected'  => 'Отклонено',
        'Unknown'   => 'Неизвестен',
        'Failed'    => 'Не отправлено',
    ];


    /**
     * Create a new confide instance.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function __construct($app)
    {
        $this->app      = $app;

        $this->agent    = Config::get('sender.agent');
        $this->username = Config::get('sender.username');
        $this->password = Config::get('sender.password');
        $this->name     = Config::get('sender.name');
    }


    private static function send ($url, $data = false) {

        $ch = curl_init( $url );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_POST, 1);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, 1200);

        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Content-Type: text/xml";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: "; // browsers keep this blank.

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );

        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;

        return $header;
    }

    /**
     * @param $phones
     * @param $message
     * @return array
     */
    public static function sendBulk($phones, $message){

        $response = [];

        $source   = 'https://userarea.sms-assistent.by/api/v1/xml';

        $data  = '<?xml version="1.0" encoding="utf-8" ?>';
        $data .= '<package login="'.$this->username.'" password="'.$this->password.'">';
        $data .= '<message>';

        foreach ($phones as $phone) {
            $data .= '<msg recipient="'.$phone.'" sender="'.$this->sender.'" validity_period="86400">'.$message.'</msg>';
        }

        $data .= '</message>';
        $data .= '</package>';

        $proceed = $this->send($source,$data);

        $xml = json_decode(json_encode(simplexml_load_string($proceed['content'])),true);

        if(isset($xml['error'])){

            $response['error']['code'] = $xml['error'];
            $response['error']['description'] = $this->errors[$xml['error']];

            return $response;
        }

        if (strlen($proceed['content']) > 1) {
            foreach ($xml['message']['msg'] as $key => $message) {
                $response[$key]['id']    = $message['@attributes']['sms_id'];
                $response[$key]['phone'] = $phones[$key];
            }
        }

        return $response;
    }

    /**
     * @param $phone
     * @param $message
     * @return array
     */
    public static function sendOne($phone, $message){

        $response = [];

        $params = [
            'user'      => $this->username,
            'password'  => $this->password,
            'recipient' => $phone,
            'message'   => $message,
            'sender'    => $this->sender,
        ];

        // Endpoint
        $source   = 'https://userarea.sms-assistent.by/api/v1/send_sms/plain?'.http_build_query($params);
        $proceed  = $this->send($source);

        if(in_array($proceed['content'], array_keys($this->errors))){

            $response['error']['code'] = $proceed['content'];
            $response['error']['description'] = $this->errors[$proceed['content']];

            return $response;
        }

        // Если ошибок нет, обрабатываем тело ответа

        $response[0]['id']    = $proceed['content'];
        $response[0]['phone'] = $phone;

        return $response;
    }

    /**
     * @param $ids
     * @return array
     */
    public static function getStatus($ids) {

        $response = [];

        $array = is_array($ids);

        $source = 'https://userarea.sms-assistent.by/api/v1/xml';

        $data   = '<?xml version="1.0" encoding="utf-8" ?>';
        $data  .= '<package login="'.$this->username.'" password="'.$this->password.'">';
        $data  .= '<status>';

        if(!$array) $data  .= '<msg sms_id="'.$ids.'"/>';
        else {
            foreach ($ids as $id) {
                $data  .= '<msg sms_id="'.$id.'"/>';
            }
        }


        $data  .= '</status>';
        $data  .= '</package>';

        $proceed = $this->send($source,$data);

        $xml = json_decode(json_encode(simplexml_load_string($proceed['content'])),true);

        if(isset($xml['error'])){

            $response['error']['code']        = $xml['error'];
            $response['error']['description'] = $this->errors[$xml['error']];

            return $response;
        }

        if (strlen($proceed['content']) > 1) {

            if (!$array) {
                $response[0]['id']      = $ids;
                $response[0]['status']  = $xml['status']['msg'];
            } else {
                foreach ($xml['status']['msg'] as $id => $status) {
                    $response[$id]['id']     = $id;
                    $response[$id]['status'] = $status;
                }
            }

        }

        return $response;
    }
}
