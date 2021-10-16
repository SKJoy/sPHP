# sPHP
A very basic PHP application development framework written from the scratch to make it easier to just code and go live with the application you are planning to develop.
> *This is a framework, not an application. This alone cannot run, you can develop an application with the help of this framework that you can run.
The fundamental goal was to make a container/shell model for application development where libraries are loaded automatically and common tasks are taken care of automatically.*

sPHP is not backed by any organization, neither a product aimed at commercial benefits. This is basically a back yard gardening made available to you just in case if it makes your life easier and beautiful in some cases. You are free to use sPHP for any of personal or commercial cases, just don't held us responsible for anything you do that goes against the state & international laws, or anything you do that causes harm to anyone or anything. We provide you the tools, you are responsible what you do with it.

**Note**: *This is unlike the conventional frameworks out there, and no other framework has been followed. This might feel unfamilier in many cases, so it is adviced not compare it against anything else. We are not interested into any competition.*
## What do you need?
> You don't need a PhD in PHP or web application development, that is what sPHP is for, to assist you in the easiest way with as less knowledge as possible. Still, factors below are pretty much mandatory no matter what technology or tool you use to get into the game;
- A web server to host your PHP application, unless otherwise you are planning to develop a CLI apllication.
- If you plan to make a dynamic PHP application with a database behind, then you need a database server to make your PHP application dynamic. Default database type is MySQL (and also preferred).
- Basic and fundamental knowledge of PHP (please do not confuse this with knowledge of other frameworks, in fact, the less you know, the easier it will be for you to grab sPHP)
- For developing database driven dynamic application, clear understanding of database table, column & entity relationship is must, because you will be doing some SQL too in that case.
- Basic knowledge of HTML in required, unless otherwise you are going for CLI. Knowledge of CSS will be a really brilliant plus.
- We really hope that you do understand the REQUEST RESPONSE mechanism that web based applications use to talk to the server and generate the output. So yes, understanding of what is SERVER SIDE and what is CLIENT SIDE is required, not only for sPHP but also for anything you would want to do with web.
## Installation
- Download the framework and keep somewhere on the server, not under web root. This should not be accessible from the web.
Due to file size limitation, we were unable to include the GeoLiteCity database on GitHub. Please download it from MaxMind website and extract to '/library/3rdparty/maxmind' framework path manually.
- Download the sample model application (https://github.com/SKJoy/sPHPApp.git) built with sPHP framework for feature, functionality and usage demonstration and keep it somewhere under the web root on the server, where this can be accessed from the web.
- Link the application to the framework using the 'index.php' script at application root. This will load the framework and your application will be ready to use the framework. In 'index.php', point to the framework's 'engine.php' script where you saved the sPHP framework. The path can be abolute path or relative path to your application, as long as your application has at least READ permission to that path.
## What next?
Configute your application using the 'system/configuration.php' script.

Access your application using a web browser as you normally would do, your application should already be running up and live!
## That is pretty much all
Now it is time for you to check out the 'template/header.php' & 'template/footer.php' scripts to customize your visual aspects. Also take a look at the 'style' path for CSS customizations.

All your main PHP scripts that does the work reside in the 'script' path. You can organize your scripts in subfolders as you want it.

> *Please consider the 'script/management/generic' path a prebuilt store with some sample PHP scripts for generic CRUD operations for database entities. You can modify them according to your needs, and you can also copy one to create another script for another database entity you want to manage.*
## Help & support
Help & support is available only over GIT at this moment. We have every dream to wide spread it with all expected support channels, but that will take some time and effort for the people behind sPHP as we also need to maintain our lives and hunger.
## What do we need?
Are you willing to help us? Just help others to learn sPHP, we need nothing more than that at this good moment :)
## Gratitude
> **FMZ Hossain**: The person with the ability to seed knowledge and foresight.

> **MS Islam** & **ZS Chowdhury**: Dared to try out sPHP for enterprise level solutions with a blind faith.

> **SI Faisal** & **A Mondal**: Who came forward to hold sPHP up without asking for anything in return.