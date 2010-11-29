# STATUS: wip

DIRS = core tests api cron setup tools

syntax:
	@for DIR in $(DIRS); do \
		for FILE in ./$$DIR/*.php; do \
			(/usr/bin/php5 --syntax-check "$$FILE";) || exit 1; \
		done \
	done

test:
	@for FILE in ./tests/test.*.php; do \
		(echo "Running $$FILE"; /usr/bin/php5 "$$FILE";) || exit 1; \
	done
