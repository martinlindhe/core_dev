# STATUS: wip

DIRS = api core cron setup tests tools views/admin views/core views/error views/profiler views/tools views/user

syntax:
	@for DIR in $(DIRS); do \
		for FILE in ./$$DIR/*.php; do \
			(/usr/bin/php5 --syntax-check "$$FILE";) || exit 1; \
		done \
	done

doxy:
	doxygen Doxyfile

test:
	phpunit --colors tests/
