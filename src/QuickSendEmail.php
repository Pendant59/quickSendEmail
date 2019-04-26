<?php
namespace quicksendemail;

use PHPMailer\PHPMailer\PHPMailer;

class QuickSendEmail
{
    /**
     * @var null
     */
    protected $mail = null;
    # html 图片标志位
    public $has_image = false;

    /**
     * @var array
     */
    protected $smtp_config = [
        'necessary' => [
            'SMTPAuth' => true,                 # 必须为true, smtp需要鉴权 验证授权账号和授权码
            'Host' => 'smtp.qq.com',            # 发送邮件服务器：smtp.qq.com
            'Username' => null,                 # smtp 发送邮件的登录账号
            'Password' => null,                 # smtp 发送邮件的授权码
            'SenderEmailAddress' => null,       # 发件人邮箱地址
            'SenderName' => null,               # 发件人名称
        ],
        'optional' => [
            'IsHtml' => true,                   # 内容是否为html格式
        ]
    ];

    /**
     * QuickSendEmail constructor.
     * @param array $self_config
     */
    public function  __construct(array $self_config = [] )
    {
        $this->mail = new PHPMailer();
        $this->smtp_config = array_merge($this->smtp_config, $self_config);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->smtp_config;
    }

    /**
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function checkConfig(){
        foreach ($this->smtp_config['necessary'] as $key=>$value) {
            if (is_null($value)) {
                return false;
            }
        }
        $this->mail->CharSet        = 'utf-8';
        $this->mail->SMTPAuth       = $this->smtp_config['necessary']['SMTPAuth'];
        $this->mail->Host           = $this->smtp_config['necessary']['Host'];
        $this->mail->Username       = $this->smtp_config['necessary']['Username'];
        $this->mail->Password       = $this->smtp_config['necessary']['Password'];
        $this->mail->setFrom($this->smtp_config['necessary']['SenderEmailAddress'],
            $this->smtp_config['necessary']['SenderName']);

        if ($this->smtp_config['optional']['IsHtml']) {
            $this->mail->isHTML(true);
        }
        return true;
    }


    /**
     * 添加html内容图片
     * @param array $image_msg
     * @return $this
     */
    public function addImage(array $image_msg)
    {
        if (is_array($image_msg) && !empty($image_msg)) {
            foreach ($image_msg as $image) {
                if (file_exists($image[0])) {
                    $this->mail->addembeddedimage($image[0], $image[1], $image[2]);
                    $this->has_image = true;
                }
            }
        }
        return $this;
    }

    /**
     * 发送邮件
     * @param array| string $recipient          收件人地址
     * @param string $subject                   邮件主题
     * @param string $content                   邮件内容
     * @param null|array|string $attachment     附件完整路径
     * @return array
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function sendSmtp($recipient, string $subject, string $content, $attachment = null)
    {
        if (!$this->checkConfig()) {
            return $this->api_return(400, '参数不可为空');
        }

        $this->mail->isSMTP();

        if (empty($recipient)) {
            return $this->api_return(
                400,
                '收件人邮箱不可为空'
            );
        }

        # 标题和内容
        $this->mail->Subject = $subject;

        if ($this->has_image){
            $this->mail->msgHTML($content);
        } else {
            $this->mail->Body = $content;
        }

        # 附件处理
        if (is_array($attachment) && !empty($attachment)) {
            foreach ($attachment as $file) {
                if (file_exists($file)) {
                    $this->mail->addAttachment($file);
                }
            }
        }else if (is_string($attachment)) {
            if (file_exists($attachment)) {
                $this->mail->addAttachment($attachment);
            }
        }

        $err_msg = [];
        # 收件人处理
        if (is_array($recipient) && !empty($recipient)) {
            foreach ($recipient as $receiver) {
                $this->mail->addAddress($receiver);
                $result = $this->mail->send();
                if ($result) {
                    return $this->api_return(200);
                }else{
                    $err_msg[$receiver] = $this->mail->ErrorInfo;
                }
            }
        }else if (is_string($recipient)) {
            $this->mail->addAddress($recipient);
            $result = $this->mail->send();
            if ($result) {
                return $this->api_return(200);
            }else{
                $err_msg[$recipient] = $this->mail->ErrorInfo;
            }
        } else {
            return $this->api_return(
                400,
                '收件人地址错误'
            );
        }
        return $this->api_return(200, '', $err_msg);
    }

    /**
     * 返回
     * @param int $code             状态标识 401 200
     * @param string $message       提示信息
     * @param array $data           返回数据
     * @return array
     */
    public function api_return(int $code, string $message = '', array $data = []):array
    {
        $return = [
            'code' => $code,
            'message'  => $message ?: ($code == 200 ? 'Success' : 'Error'),
        ];
        if (!empty($data)){
            $return['data'] = $data;
        }
        return $return;
    }


}
