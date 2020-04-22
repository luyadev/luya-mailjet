# Changelog

## 1.3.2

+ Fix bug with template loading by id instead of slog.

All notable changes to this project will be documented in this file. This project adheres to [Semantic Versioning](http://semver.org/).
In order to read more about upgrading and BC breaks have a look at the [UPGRADE Document](UPGRADE.md).

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
