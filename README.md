<p align="center">
  <img src="https://raw.githubusercontent.com/luyadev/luya/master/docs/logo/luya-logo-0.2x.png" alt="LUYA Logo"/>
</p>

# LUYA Mailjet

[![LUYA](https://img.shields.io/badge/Powered%20by-LUYA-brightgreen.svg)](https://luya.io)

LUYA and Yii Framework integration for mailjet service.

Contains:

+ Yii Framework BaseMailer for Transaction E-Mails trough API.
+ Interface for Subscription Mail Sync including CLI command for Synchronisation.

## Installation

Install the extension through composer:

```sh
composer require luyadev/luya-mailjet
```

Add to config:

```php
[
    'components' => [
        'mailjet' => [
            'class' => 'luya\mailjet\Client',
            'apiKey' => '...',
            'apiSecret' => '...',
        ],
        'mailer' => [
            'class' => 'luya\mailjet\Mailer',
        ],
    ]
]
```

## Usage

## Transactionals

Send transactional E-Mail:

```php
Yii::$app->mailer->compose()
    ->setFrom('from@domain.com')
    ->setTo('to@domain.com')
    ->setSubject('Message subject')
    ->setTextBody('Plain text content')
    ->setHtmlBody('<b>HTML content</b>')
    ->send();
```

## Transactional with Template

```php
$mail = $this->app->mailer->compose()
    ->setFrom('from@domain.com')
    ->setSubject('Hello!')
    ->setTemplate(484590)
    ->setVariables(['lastnbame' => 'Lastname Value'])
    ->setTo(['to@domain.com'])
    ->send();
```
