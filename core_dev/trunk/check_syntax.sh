#!/bin/bash
#
# runs all php script code through php lint mode to check for syntax errors
#

for f in ./core/*.php; do php --syntax-check "$f"; done
for f in ./api/*.php; do php --syntax-check "$f"; done

for f in ./cron/*.php; do php --syntax-check "$f"; done
for f in ./setup/*.php; do php --syntax-check "$f"; done

for f in ./tests/*.php; do php --syntax-check "$f"; done
for f in ./tools/*.php; do php --syntax-check "$f"; done

for f in ./views/admin/*.php; do php --syntax-check "$f"; done
for f in ./views/core/*.php; do php --syntax-check "$f"; done
for f in ./views/error/*.php; do php --syntax-check "$f"; done
for f in ./views/profiler/*.php; do php --syntax-check "$f"; done
for f in ./views/tools/*.php; do php --syntax-check "$f"; done
for f in ./views/user/*.php; do php --syntax-check "$f"; done
