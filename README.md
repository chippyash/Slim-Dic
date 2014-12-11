# chippyash/Slim-Dic

## Quality Assurance

Coming soon!

## What?

Provides [Symfony Dependency Injection](http://symfony.com/doc/current/components/dependency_injection/introduction.html) 
for a [Slim Application](http://www.slimframework.com/)

Also provides a minimalist Controller pattern class for use in your applications.

## Why?

The Slim framework is great for lightweight sites but lacks the ease of creating
testable, adaptable code that can be found when adopting a strict DI approach to development.

This small library supports the integration of the easy to use, yet powerful
Symfony version of a DI container with the lightweight Slim Framework, giving 
you the ability to create great, maintainable and configurable web sites quickly.

## When

## How


## Changing the library

1.  fork it
2.  write the test
3.  amend it
4.  do a pull request

Found a bug you can't figure out?

1.  fork it
2.  write the test
3.  do a pull request

NB. Make sure you rebase to HEAD before your pull request

## Where?

The library is hosted at [Github](https://github.com/chippyash/Slim-Dic). It is
available at [Packagist.org](https://packagist.org/packages/chippyash/slim-dic)

### Installation

Install [Composer](https://getcomposer.org/)

#### For production

If you do not need GMP support, you can continue to use the V1.1 branch for the
time being.
 
add

<pre>
    "chippyash/slim-dic": "dev-master"
</pre>

to your composer.json "requires" section

#### For development

Clone this repo, and then run Composer in local repo root to pull in dependencies

<pre>
    git clone git@github.com:chippyash/Slim-Dic.git Slimdic
    cd Slimdic
    composer install --dev
</pre>

To run the tests:

<pre>
    cd Slimdic
    vendor/bin/phpunit -c test/phpunit.xml test/
</pre>

## History
