# Testing
## Create Account
## Login
## User Status
## API Requests
### Movies
#### get Movies
this should return a json string of all movies in the database. API key is not required
URL is like:
```
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/movies
```
returns like :
```json
[
  {
    "movieID": 1,
    "title": "Avatar",
    "release_date": "2009-12-10",
    "vote_average": "7.2",
    "vote_count": 11804,
    "runtime": 162,
    "description": "In the 22nd century, a paraplegic Marine is dispatched to the moon Pandora on a unique mission, but becomes torn between following orders and protecting an alien civilization."
  },
  {
    "movieID": 2,
    "title": "Pirates of the Caribbean: At World's End",
    "release_date": "2007-05-19",
    "vote_average": "6.9",
    "vote_count": 4500,
    "runtime": 169,
    "description": "Captain Barbossa, long believed to be dead, has come back to life and is headed to the edge of the Earth with Will Turner and Elizabeth Swann. But nothing is quite as it seems."
  },
  {
    "movieID": 3,
    "title": "Spectre",
    "release_date": "2015-10-26",
    "vote_average": "6.3",
    "vote_count": 4466,
    "runtime": 148,
    "description": "A cryptic message from Bonds past sends him on a trail to uncover a sinister organization. While M battles political forces to keep the secret service alive, Bond peels back the layers of deceit to reveal the terrible truth behind SPECTRE."
  },
```
### To Watch List
### Completed Watch List
### Users
