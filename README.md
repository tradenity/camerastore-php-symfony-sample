
Welcome to the Symfony CameraStore sample application for Tradenity PHP SDK
===========================================================================


## Live demo

Here you can find live demo of the [Camera store sample application](http://camera-store-sample.tradenity.com/).
This is the application we are going to build here.


## Prerequisites

(Will install automatically via maven)

-  Symfony framework > version 3 (other versions may work but not tested)
-  [Tradenity PHP SDK](https://github.com/tradenity/php-sdk)
-  [Symfony extensions for the Java SDK](https://github.com/tradenity/php-sdk-symfony-ext)



## Setup your credentials

First of all, you have to get API keys for your store, you can find it in your store `Edit` page.
To get there navigate to the stores list page, click on the `Edit` button next to your store name, scroll down till you find the `API Keys` section.


## Initialize the library

Add your Store keys to `app/config/config.yml` file, also your stripe public key.



```yml

parameters:
    locale: en
    tradenity_key: sk_xxxxxxxxxxxxxxxxxxxxx
    stripe_public_key: pk_xxxxxxxxxxxxxxxxxx

```



## Tutorials and sample applications


We also provide a detailed explanation of the code of this sample applications in the form of a step by step tutorials:

[Camera store for spring mvc tutorial](http://docs.tradenity.com/kb/tutorials/php/symfony).



## Run

In console, type:

`php bin/console server:start`
