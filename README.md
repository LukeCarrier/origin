Origin
======

http://getorig.in/ - A PHP portability toolkit.

Introduction
------------

All code has to originate from somewhere. Origin provides web application developers with a wealth of libraries to speed
up the initial development process. Its MVC architecture is designed to promote perfomant, secure and maintainable
development practices.

License
-------

We're fans of the BSD license. Duplicate, redistribute and modify to your heart's content, but do try to get your bug
fixes into core!

Rationale
---------

* Do things properly. Follow accepted standards and conventions throughout all stages of the software lifecycle.
* Do things fast. Development should be iterative, and developing full-blown features and components should be fast. At
  runtime, provide the necessary tools to cache as much as we can to avoid wasting CPU cycles on mundane tasks.
* Be predictable and extensible. We should extract all of our interfaces from our implementations so as to allow
  developers to swap out bits of the framework as their specific requirements demand it.
* Keep with the times. Advances in developer workflow and application performance shouldn't be kept on the backburner
  just to support lazy sysadmins and their decrepid software stacks. ;-)

Under (extremely) active development
------------------------------------

Origin is currently under very intense development as we shape it to better meet our needs. As a result, many of the
APIs are feature-incomplete and subject to change without warning. Deprecation warnings will start to appear after our
first stable release.

Components
----------

| Component             | Description                                                                                  |
| --------------------- | -------------------------------------------------------------------------------------------- |
| ```Origin\Autoload``` | Class autoloader. Instances can manage multiple namespaces, with multiple source directories |
| ```Origin\Cache```    | Disk cache library                                                                           |

Documentation
-------------

Documentation can be built from inline documentation block comments. To build the documentation:

    $ make doc

When you're done, just point your browser at the ```doc``` directory of your working tree.

Style compliance
----------------

Fire up a terminal and build the compliance logs with ```PHP_CodeSniffer```:

    $ make check

When you're done, view the ```phpcs.log``` file inside your working tree with your pager/editor of choice. Please note
that the compliance rules themselves are still being defined -- take its output with a pinch of salt.

Requirements
------------

* PHP 5.4 (short array syntax, binding $this in closures, callable typehint)
* Composer
