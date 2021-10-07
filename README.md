<p align="center">
  <img src="https://raw.githubusercontent.com/luyadev/luya/master/docs/logo/luya-logo-0.2x.png" alt="LUYA Logo"/>
</p>

# LUYA Mailjet

[![LUYA](https://img.shields.io/badge/Powered%20by-LUYA-brightgreen.svg)](https://luya.io)
![Tests](https://github.com/luyadev/luya-mailjet/workflows/Tests/badge.svg)
[![Total Downloads](https://poser.pugx.org/luyadev/luya-mailjet/downloads)](https://packagist.org/packages/luyadev/luya-mailjet)
[![Latest Stable Version](https://poser.pugx.org/luyadev/luya-mailjet/v/stable)](https://packagist.org/packages/luyadev/luya-mailjet)
[![Test Coverage](https://api.codeclimate.com/v1/badges/79087433986c16d7f41d/test_coverage)](https://codeclimate.com/github/luyadev/luya-mailjet/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/79087433986c16d7f41d/maintainability)](https://codeclimate.com/github/luyadev/luya-mailjet/maintainability)

LUYA and Yii Framework integration for [Mailjet](https://mailjet.com) service.

Contains:

+ Yii Framework BaseMailer for Transaction E-Mails trough API.
+ Interface for Subscription Mail Sync including CLI command for Synchronisation.
+ A PHP library to convert MJML content into Mailjet Passport json format.
+ LUYA Admin Module to convert MJML into HTML based on MJML.io API.
+ LUYA Active Window to retrieve informations about a given User E-Mail.
+ A Widget to subscribe to a List with double opt in (can be disabled).
+ SMS Sending helpers
+ Yii 2 Queue Job to send mails with a template

## Installation

Install the extension through composer:

```sh
composer require luyadev/luya-mailjet
```

Add to config:

```php
'components' => [
    //...
    'mailjet' => [
        'class' => 'luya\mailjet\Client',
        'apiKey' => '...',
        'apiSecret' => '...',
    ],
    'mailer' => [
        'class' => 'luya\mailjet\Mailer',
    ],
]
```

## Basic Send Mail

Sending transactional E-Mail:

```php
Yii::$app->mailer->compose()
    ->setFrom('from@domain.com')
    ->setTo('to@domain.com')
    ->setSubject('Message subject')
    ->setTextBody('Plain text content')
    ->setHtmlBody('<b>HTML content</b>')
    ->send();
```

Send a transactional E-Mail based on the Template id stored in Mailjet:

```php
Yii::$app->mailer->compose()
    ->setTemplate(484590)
    ->setVariables(['lastname' => 'Lastname Value'])
    ->setTo(['to@domain.com'])
    ->send();
```

## MJML to HTML

With version 1.3 of LUYA Mailjet library there is an admin module you can configured in order to parser MJML into HTML, therefore add the module to your configuration and provide mjml.io API keys:

```php
'modules' => [
    //...
    'mailjetadmin' => [
        'class' => 'luya\mailjet\admin\Module',
        'mjmlApiApplicationId' => 'ApplicationIdFromMjml.io',
        'mjmlApiSecretKey' => 'ApplicationSecretFromMjml.io',
    ]
]
```

Afterwards you can retrieve and render the HTML of MJML template with:

```php
luya\mailjet\models\Template::renderHtml('slug', ['foo' => 'bar']);
```
