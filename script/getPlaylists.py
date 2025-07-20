from ytmusicapi import YTMusic
import sys

"""
Suche nach Playlistenin Youtube Music
1. initialisert den unauthorisierten Youtube-Music-Modus.
2. Sucht nach Playlisten
3. gibt Playliste:Titel,Playliste:Titel,... aus
"""

def listToString(s):
 
    # initialize an empty string
    str1 = ""
 
    # traverse in the string
    for ele in s:
        str1 += ele
 
    # return string
    return str1

yt = YTMusic()
results = yt.search(listToString(sys.argv[1:]), 'playlists')
for r in results:
  try:
    print(r['videoId'], end='')
    print(":", end='')
    print(r['title']+' - '+r['author'], end='')
    print(",", end='')
  except:
    try:
      print(r['browseId'], end='')
      print(":", end='')
      print(r['title']+' - '+r['author'], end='')
      print(",", end='')
    except:
      print('', end='')