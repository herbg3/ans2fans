ans2fans
========

Converts ANSI art files to FANSI code segments for use with 8bitMUSH

Note that conversion may not be 100% accurate if your ansi file uses the 7h code, but its a managable fix, since 7h essentially sets the wrap limit of a line, and there is no way to predict that using the current methods.

Also, if the FANSI output is too long, you'll have to work some MUSHcode magic to reassemble it after splicing it up into smaller pieces.

Want to learn more about FANSI?  http://fansi.org
