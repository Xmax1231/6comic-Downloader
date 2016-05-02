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
print("download id="+cid+" ch="+str(ch1)+"-"+str(ch2))
m=re.search("var cs='(.+?)';",t)
code=m.group(1)
hashc=code[0:3]
if os.path.isdir("downloads")==False:
	os.mkdir("downloads")
if os.path.isdir("downloads/"+cid)==False:
	os.mkdir("downloads/"+cid)
for i in range(int(ch1),int(ch2)+1):
	prestr=hashc+str(i)
	prestr=prestr[-4:]
	startid=code.find(prestr)
	if startid==-1:
		print("ch "+str(i)+" not found.")
		continue
	if os.path.isdir("downloads/"+cid+"/"+str(i))==False:
		os.mkdir("downloads/"+cid+"/"+str(i))
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
		f=open("downloads/"+cid+"/"+str(i)+"/"+str(j)+".jpg","wb")
		f.write(img)
		f.close()
