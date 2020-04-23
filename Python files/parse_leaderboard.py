from bs4 import BeautifulSoup
with open("leaderboard.html", 'r') as f:
    webpage = f.read()
soup = BeautifulSoup(webpage, "html.parser")
# a = soup.findAll("tr")[3:13]
a = soup.findAll("tr")[14:]
count=0
for i in a:
    b=i.findAll("td")
    if not (b[1].find("div")):
        print(b[4].contents[0])
        count+=1
