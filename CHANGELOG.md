# Changelog

All notable changes to this project will be documented in this file. This project adheres to [Semantic Versioning](http://semver.org/).
In order to read more about upgrading and BC breaks have a look at the [UPGRADE Document](UPGRADE.md).

## 1.8.0 (15. March 2022)

+ [#19](https://github.com/luyadev/luya-mailjet/pull/19) Add new option to send message as bulk. Which is an array of messages.

## 1.7.1 (27. July 2021)

+ Allow LUYA Core Version 2.0

## 1.7.0 (8. June 2021)

+ [#15](https://github.com/luyadev/luya-mailjet/pull/15) Added exception if MJML parser contains errors, otherwise the request will silently fail.

## 1.6.3 (19. January 2021)

+ [#14](https://github.com/luyadev/luya-mailjet/pull/14) Fix json decode exception when the parameter is violated in the subscribe form widget.

## 1.6.2 (30. December 2020)

+ [#13](https://github.com/luyadev/luya-mailjet/pull/13) Fix issue when sending emails with enabled template error reporting

## 1.6.1 (1. December 2020)

+ [#11](https://github.com/luyadev/luya-mailjet/pull/11) Allow none scalar values as variable when using `setVariables()` in `MailerMessage`.

## 1.6.0 (25. August 2020)

+ [#9](https://github.com/luyadev/luya-mailjet/pull/9) Implemented the option to attache files to a given message with `attach()` or `attachContent()`.

## 1.5.0 (22. July 2020)

+ Options to increase client connection timeouts.

## 1.4.2 (22. July 2020)

+ Fix issue with `luya\mailjet\jobs\TemplateEmailSendJob` when no data is passed to the template.

## 1.4.1 (22. July 2020)

+ Replace Travis with GitHub Actions
+ Implement `yii\base\BaseObject` for `luya\mailjet\jobs\TemplateEmailSendJob` class. 

## 1.4.0 (23. April 2020)

+ [#7](https://github.com/luyadev/luya-mailjet/pull/7) Added new helpers to render MJML with variables and generate the HTML data on request instead of while saving/updating.
+ [#5](https://github.com/luyadev/luya-mailjet/pull/5) Fix bug with template loading by id instead of slog.

## 1.3.1 (23. February 2020)

+ Fix bug with same instance. This will lead into problems when runing sync command several times in the same "controller".

## 1.3.0 (27. January 2020)

+ [#4](https://github.com/luyadev/luya-mailjet/pull/4) Subscribe Form Widget which provides Double Opt In option.
+ [#3](https://github.com/luyadev/luya-mailjet/pull/3) Admin Module to parse MJML into HTML with optional variables. Add the module to your configure and define mjml.io API Keys (run migrate and import commands of LUYA)
+ Deprecated Client::sms(), Client::contacts() and Client::sections() and replaced with Client::$sms, Client::$contacts, Client::$sections.

## 1.2.0 (26. July 2019)

+ [#1](https://github.com/luyadev/luya-mailjet/pull/1) Added deduplicate flag to be able to send a mail more than once to a recipient in the same campaign, Added CustomCampaign and CustomId to provide additional tagging ability, Added setTemplateLanguage to avtivly set the TemplateLanguage-Flag (true to parse variables into template)
+ [#2](https://github.com/luyadev/luya-mailjet/pull/2) Added new unsubscribe and remove buttons for active window.

## 1.1.2 (6. May 2019)

+ Fixed bug with double replacements.

## 1.1.1 (6. May 2019)

+ Fixed bug with nested elements an `mj-text` also with `mj-text` tags with attributes like `<mj-text color="black">text</mj-text>`.

## 1.1.0 (6. May 2019)

+ Force raw elements to pass the content as CDATA. For example `<mj-text><a href="https://luya.io">Go</a></mj-text>` must be passed as link content instead of a child node of mj-text node.

## 1.0.0 (21. March 2019)

+ First stable release.
