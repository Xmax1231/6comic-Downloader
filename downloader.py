import io
import os
import urllib.request
import re
import sys
import math
if len(sys.argv)<2:
	sys.exit("no id")
else:
	cid=sys.argv[1]
if cid.isdigit()==False:
	sys.exit("id not a number")
t=urllib.request.urlopen("http://www.6comic.com/comic/readmanga_"+cid+".html?ch=1-1").read().decode('BIG5')
cname = re.search('<title>(.+?)</title>', t)
if cname is None:
	sys.exit('comic name is not found')
else:
	cname = str.rstrip(cname.group(1).split('-')[0])[:-2]
	cname = re.sub('\\|\/|\:|\*|\?|\"|<|>|\|', ' ', cname)	# Fix filename incorrect
m=re.search("var chs=(\d+)",t)
ch1=1
ch2=m.group(1)
if len(sys.argv)>=3:
	m=re.search("^(\d+)-(\d+)$",sys.argv[2])
	if m!=None:
		ch1=m.group(1)
		ch2=m.group(2)
	elif sys.argv[2].isdigit():
		ch1=ch2=sys.argv[2]
	else:
		sys.exit("ch not a number")
print("download id="+cname+" ch="+str(ch1)+"-"+str(ch2))
m=re.search("var cs='(.+?)';",t)
code=m.group(1)
hashc=code[0:3]
if os.path.isdir("downloads")==False:
	os.mkdir("downloads")
if os.path.isdir("downloads/"+cname)==False:
	os.mkdir("downloads/"+cname)
for i in range(int(ch1),int(ch2)+1):
	prestr=hashc+str(i)
	prestr=prestr[-4:]
	startid=code.find(prestr)
	if startid==-1:
		print("ch "+str(i)+" not found.")
		continue
	if os.path.isdir("downloads/"+cname+"/"+str(i))==False:
		os.mkdir("downloads/"+cname+"/"+str(i))
	domain=code[startid+5:startid+6]
	folder=code[startid+6:startid+7]
	m=re.search("(\d+)$",code[startid+7:startid+10])
	page=m.group(1)
	for j in range(1,int(page)+1):
		print("downloading ch="+str(i)+" page="+str(j))
		k=(j-1)%100+1;
		startid2=startid+10+(k-1)%10*3+math.floor((k-1)/10)
		imghash=code[startid2:startid2+3]
		url="http://img"+domain+".6comic.com:99/"+folder+"/"+cid+"/"+str(i)+"/"+(str(j).zfill(3))+"_"+imghash+".jpg"
		img=urllib.request.urlopen(url).read()
		f=open("downloads/"+cname+"/"+str(i)+"/"+str(j)+".jpg","wb")
		f.write(img)
		f.close()
