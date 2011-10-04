# Ikariem
*An incomplete clone of Ikariam*

Please note that this Readme does not intend to be an installation guide, I won't be able to provide an installation guide as well so please figure things out yourself.

### Prerequisites

1. PHP 5.2 or higher
2. MySQL 5.1 or higher
3. Zend Framework 1.10 or higher
4. Pear library [System_Daemon](http://pear.php.net/package/System_Daemon)
5. A lot of patience

*Please note that currently images and css are included in the repository, if this causes problems I might take them off eventually*

### Basic Description

This is a clone targetted on Ikariam 0.3.5, so newer features are not included since I don't have the time and resource to develop it.

Currently most of the basics are already laid down, the only part that needs work is interaction between players (mainly the hostile part like combats and etc).

It also uses daemon to update building queue, research queue or any types of things that requires waiting (which of course I know is unnecessary). There are a few cron jobs to be configured as well, which should basically seem clear enough.

There are many more catches which you might need to figure out yourself as well (such as which directory you need to set chmod 0777). So take care.