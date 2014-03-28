# Path to the top directory (which contains the Makefile)
TOP := $(CURDIR)

# These targets aren't tied to generated files
.PHONY: check clean doc install test tidy

# Test code against our style guidelines
check:
	-$(TOP)/vendor/bin/phpcs -ps --extensions=php --report-file=$(TOP)/phpcs.log \
	                         --standard=$(TOP)/phpcs.xml $(TOP)/lib

# Remove all automatically generated files
clean: tidy
	rm -rf $(TOP)/doc $(TOP)/phpcs.log $(TOP)/phpunit-coverage

# Build the documentation from inline docblocks
doc:
	$(TOP)/vendor/bin/phpdoc.php

# Execute the test suite and write coverage information
test:
	$(TOP)/vendor/bin/phpunit $(TOP)/test

# Tidy up log files and other undesirable build artefacts
tidy:
	rm -rf $(TOP)/phpdoc*.log $(TOP)/test-temp
