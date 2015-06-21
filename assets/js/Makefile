# npm install -g coffee-script uglify-js

ALL = main.js

all: $(ALL)

%.js: %.coffee
	coffee -o $(dir $@) --compile $<
	uglifyjs $@ --output $@.tmp
	mv -f $@.tmp $@

.PHONY: all clean

clean:
	rm -f $(ALL)

