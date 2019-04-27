# quickSendEmail
- 基于 PHPMailer 的发送邮件扩展类
- Pendant <pendant59@qq.com>
- QQ群 316497602

### 安装
```
composer require pendant59/quicksendmail
```
### 配置参数解释
```
### 可更改配置参数
$smtp_config = [
        'necessary' => [
            'SMTPAuth' => true,                 # 必须为true, smtp需要鉴权 验证授权账号和授权码
            'Host' => 'smtp.qq.com',            # 发送邮件服务器：smtp.qq.com
            'Username' => null,                 # smtp 发送邮件的登录账号
            'Password' => null,                 # smtp 发送邮件的授权码
            'SenderEmailAddress' => null,       # 发件人邮箱地址
            'SenderName' => null,               # 发件人名称
        ],
        'optional' => [
            'IsHtml' => true,                   # 内容是否为html格式 - 默认即可(纯文本的内容也没有影响)
        ]
    ];
```    
### 方法解释
```
/**
 * 添加html内容图片
 * @param array $image_msg  [ ['文件完整路径包含文件名', 'cid名称', '覆盖附件名称'] [,....]]
 * @return $this
 */
public function addHtmlImage(array $image_msg){}

/**
 * 发送邮件
 * @param array|string $recipient           收件人地址 aaa@qq.com 或者 ['aaa@qq.com', 'bbb@qq.com', 'ccc@qq.com',....]
 * @param string $subject                   邮件主题
 * @param string $content                   邮件内容
 * @param null|array|string $attachment     附件完整路径 ['excel.xlsx', 'logo.png'，.....]
 * @return array
 * @throws \PHPMailer\PHPMailer\Exception
 */
public function sendSmtp($recipient, string $subject, string $content, $attachment = null){}

/**
 * 设置配置参数
 * @param array $self_config
 * @return $this
 */
public function setConfig(array $self_config) {}

```
### 使用示例 - SMTP

- example 1  (无附件纯文本):
```
# 配置参数(config parameter)
$smtp_config = [
    'necessary' => [
        'SMTPAuth' => true,                             # 必须为true, smtp需要鉴权 验证授权账号和授权码
        'Host' => 'smtp.qq.com',                        # 发送邮件服务器：smtp.qq.com
        'Username' => 'xxxxx@qq.com',                   # smtp 发送邮件的登录账号
        'Password' => 'xxxxxx',                         # smtp 发送邮件的授权码
        'SenderEmailAddress' => 'xxxx@qq.com',          # 发件人邮箱地址 - 等同于发送邮件的登录账号
        'SenderName' => '我是邮件发送人',               # 发件人名称 - 用于显示
    ],
    'optional' => [
        'IsHtml' => true,                   # 内容是否为html格式 - 默认即可(纯文本的内容也没有影响)
    ]
];

$mail = new QuickSendEmail($smtp_config);
$result = $mail->sendSmtp('收件人邮箱地址', '邮件标题', '邮件正文内容');
print_r($result);

Array
(
    [code] => 200
    [message] => Success
)

```

- example 2 (邮件内容html内嵌logo图片):
```
# 配置参数(config parameter)
$smtp_config = [
    'necessary' => [
        'SMTPAuth' => true,                             # 必须为true, smtp需要鉴权 验证授权账号和授权码
        'Host' => 'smtp.qq.com',                        # 发送邮件服务器：smtp.qq.com
        'Username' => 'xxxxx@qq.com',                   # smtp 发送邮件的登录账号
        'Password' => 'xxxxxx',                         # smtp 发送邮件的授权码
        'SenderEmailAddress' => 'xxxx@qq.com',          # 发件人邮箱地址 - 等同于发送邮件的登录账号
        'SenderName' => '我是邮件发送人',               # 发件人名称 - 用于显示
    ],
    'optional' => [
        'IsHtml' => true,                   # 内容是否为html格式 - 默认即可(纯文本的内容也没有影响)
    ]
];

$mail = new QuickSendEmail($smtp_config);
# 邮件内容为html且包含图片 内容部分img标签的src 必须写成 cid:xxx 这里的xxx 就是下面参数数组(比如：['./logo1.png','imglogo1','logo1.png'])中的第二个参数：imglogo1 
$result = $mail->addHtmlImage([
            ['./logo1.png','imglogo1','logo1.png'],
            ['./logo2.png','imglogo2','logo2.png']
            ])
            ->sendSmtp('收件人邮箱地址', '邮件标题', '<div><img src="cid:imglogo1"><br><img src="cid:imglogo2" ></div>');

print_r($result);
```


- example 3 (包含logo和附件):
```
# 配置参数(config parameter)
$smtp_config = [
    'necessary' => [
        'SMTPAuth' => true,                             # 必须为true, smtp需要鉴权 验证授权账号和授权码
        'Host' => 'smtp.qq.com',                        # 发送邮件服务器：smtp.qq.com
        'Username' => 'xxxxx@qq.com',                   # smtp 发送邮件的登录账号
        'Password' => 'xxxxxx',                         # smtp 发送邮件的授权码
        'SenderEmailAddress' => 'xxxx@qq.com',          # 发件人邮箱地址 - 等同于发送邮件的登录账号
        'SenderName' => '我是邮件发送人',               # 发件人名称 - 用于显示
    ],
    'optional' => [
        'IsHtml' => true,                   # 内容是否为html格式 - 默认即可(纯文本的内容也没有影响)
    ]
];

$mail = new QuickSendEmail($smtp_config);
# 邮件内容为html且包含图片 内容部分img标签的src 必须写成 cid:xxx 这里的xxx 就是下面参数数组(比如：['./logo1.png','imglogo1','logo1.png'])中的第二个参数：imglogo1 
$result = $mail->addHtmlImage([
            ['./logo1.png','imglogo1','logo1.png'],
            ['./logo2.png','imglogo2','logo2.png']
            ])
            ->sendSmtp('此处填写收件人邮箱地址(数组或者字符串)', '邮件标题', '<div><img src="cid:imglogo1"><br><img src="cid:imglogo2" ></div>',['./logo1.png', './logo2.png']);

print_r($result);
```