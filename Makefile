# STATUS: wip

DIRS = api core cron setup tests tools views/admin views/core views/error views/profiler views/tools views/user

lint-internal:
	@for DIR in $(DIRS); do \
		for FILE in ./$$DIR/*.php; do \
			(php --syntax-check "$$FILE";) || exit 1; \
		done \
	done

doxy:
	doxygen Doxyfile
