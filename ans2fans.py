file = "8bit.ans"

with open(file, 'r') as content_file:
    content = content_file.read()

print "Content:\n", content #debug
print "--" #debug

string = list(content)
print "string =", string #debug
leng = len(string)

print "leng =", leng #debug

fore = 7
back = 0
highlight = False
underline = False
blinking = False
inverted = False
hidden = False

out = []
i = 0
while i < leng:
    o = (string[i][0])
    if o == 0 or o == 7 or o == 13:
        pass
    elif o == '\n':
        out.append("%R")
    elif o == '\x1b': # ESC - 0x1b - 21
        type = "m"
        code = ""
        j = i + 1
        stop = False
        while (j+1) < leng and stop == False:
            piece = string[j+1]
            if piece == '[':
                pass
            elif piece == '?':
                type = '?'
            elif piece == 'h':
                type = 'h'
                stop = True
            elif piece == 'm':
                type = 'm'
                stop = True
            elif piece == 'C':
                type = 'C'
                stop = True
            else:
                code += piece
            j += 1 # unsure of where to put this...
        i = j #- 1
        print "Code: " + code + " Type: " + type + " "
        parts = code.split(';')
        for part in parts:
            part = int(part)
        if type == 'm':
            pieces = len(parts)
            for k in xrange(pieces):
                value = int(parts[k])
                if value == 1:
                    highlight=True
                    fore += 8
                    print "Bold"
                elif value == 0:
                    fore = 7
                    back = 0
                    highlight = False
                    underline = False
                    blinking = False
                    inverted = False
                    hidden = False
                    print "Normal"
                elif value == 4:
                    underline = not underline
                elif value == 5:
                    blinking = not blinking
                elif value == 7:
                    inverted = not inverted
                elif value == 8:
                    hidden= not hidden
                elif value >= 30 and value < 40:
                    fore = value - 30
                    if highlight == True:
                        print " Highlighting", fore, "-> "
                        fore += 8
                        print fore
                    else:
                        print " Setting foreground to", fore
                elif value >= 40 and value < 50:
                    back = value - 40
                    print " Setting background to", back
        elif type == 'C':
            number = int(parts[0])
            out.append([fore, back, 32, parts[0]])
        elif type == 'h':  # ignores code 7h
            pass
        elif type == '?':  # ignores 'screen mode' code ?#
            pass
        print '\n'
        print parts
    elif o == ' ':
        out.append([fore, back, 32, 1])
    else:
        out.append([fore, back, ord(o), 1])
        
    i += 1


final = ""
total = len(out)
for i in xrange(total):
    if isinstance(out[i], str):
        final += out[i]
    elif not isinstance(out[i], basestring):
        fore = out[i][0]
        back = out[i][1]
        if i == total-1:
            if out[i][3] == 1:
                final += "[color("+str(fore)+","+str(back)+",[c("+str(out[i][2])+")])]"
            else:
                final += "[color("+str(fore)+","+str(back)+",[c("+str(out[i][2])+""+str(out[i][3])+")])]"
        elif isinstance(out[i+1], basestring): # not array
            if out[i][3] == 1:
                final += "[color("+str(fore)+","+str(back)+",[c("+str(out[i][2])+")])]"
            else:
                final += "[color("+str(fore)+","+str(back)+",[c("+str(out[i][2])+""+str(out[i][3])+")])]"
        elif not isinstance(out[i+1], basestring): # is array
            if out[i][2] != out[i+1][2] or out[i][1] != out[i+1][1] or out[i][0] != out[i+1][0]:
                if out[i][3] == 1:
                    final += "[color("+str(fore)+","+str(back)+",[c("+str(out[i][2])+")])]"
                else:
                    final += "[color("+str(fore)+","+str(back)+",[c("+str(out[i][2])+""+str(out[i][3])+")])]"
            else:
                out[i+1][3] += out[i][3]

print "---\n"
print final
