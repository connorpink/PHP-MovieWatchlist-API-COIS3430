# Testing
## Create Account
this is what the database looks like before testing the create account function
![alt text](testing-screenshots/databaseBeforeUser.png)
### missing data
the system will give errors if any field is missing an input
![alt text](testing-screenshots/noUsername.png)
![alt text](testing-screenshots/noEmail.png)
![alt text](testing-screenshots/noPassword.png)
### Bad Data
system will give errors if any data is invalid/taken
![alt text](testing-screenshots/badEmail.png)
![alt text](testing-screenshots/badPasswords.png)
![alt text](testing-screenshots/usernameTaken.png)
![alt text](testing-screenshots/emailTaken.png)
### result
once user is crated database looks like this
![alt text](testing-screenshots/databaseWithUser.png)
## Login
give an error when user tries to log in with incorrect information
![alt text](testing-screenshots/usernameNotFound.png)
![alt text](testing-screenshots/incorrectPassword.png)

## Profile
user can use profile to view and request a new api key
![alt text](testing-screenshots/oldKey.png)
![alt text](testing-screenshots/newKey.png)


## User Status
## API Requests
### API Key testing
for all API requests that require an API key, here is the validation and testing associated:
API key is passed as a header variable like X-API-KEY. If this variable is not present the request will return the following:
![alt text](testing-screenshots/noAPIKey.png)
if the key is present but there is no user in the DB with that key then you will get this message
![alt text](testing-screenshots/invalidAPIKey.png)
otherwise it will return the appropriate results


### Movies
#### get Movies
this should return a json string of all movies in the database. API key is not required
URL is like:
```
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/movies
```
returns like :
![alt text](testing-screenshots/allMovies.png)
if no movies in data base:
![alt text](testing-screenshots/failedToQueryDatabase.png)

##### filter movie title
example url https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/movies?name=pirate
name=pirate
output:
![alt text](testing-screenshots/pirateMovies.png)
if no movies with title
![alt text](testing-screenshots/failedToQueryDatabase.png)

##### filter movie rating
this should return json string of movies whos rating is above or equal to a certain number passed in the URL
example:
```https
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/movies?rating=8
```
rating = 8
output:
![alt text](testing-screenshots/rating8.png)
if no movies with that rating or greater:
![alt text](testing-screenshots/failedToQueryDatabase.png)

##### filter movie title and rating
here when a name and rating are passed it returns results with ratings = or greater than the number and a title like the name passed.
example URL 
```http
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/movies?rating=7&name=dark
```
output:
![alt text](testing-screenshots/ratingAndNameFilter.png)
if no movies with that title match and rating or greater
![alt text](testing-screenshots/failedToQueryDatabase.png)

#### get movies with ID
returns movies with a particular ID that is in the URL. Accesses the MovieID from the DB.
URL like : 
```HTTPS
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/movies/99/
```
output:
![alt text](testing-screenshots/movieID.png)
if no movie with ID exists
![alt text](testing-screenshots/failedToQueryDatabase.png)
#### get movie rating by movie ID
returns the rating for a movie with a particular ID
url like: 
```Https
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/movies/99/rating/
```
returns:
![alt text](testing-screenshots/ratingById.png)
### To Watch List
#### Get all watch list entries
get all watch list entries with a URL like:
```http
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/towatchlist/entries
```
output:
![alt text](testing-screenshots/watchlistEntries.png)
##### filter watch list by priority
returns watch list entries with a filter on priority
example url: 
```http
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/towatchlist/entries?priority=2
```
output:
![alt text](testing-screenshots/filterPriorityEntries.png)
if no entries given for priority error 500:
![alt text](testing-screenshots/filterPriorityNoResults.png)
if priority is not a number an error is returned
![alt text](testing-screenshots/notANumber.png)
#### Post new watch list entry
Insert new entry in the toWatchListEntries table. requires API key. takes a body with form encoded data {movieID, priority, notes}. There is error checking to make sure the movieID is a number, the priority is a number, and that notes is a string.
example input body {movieID=99,priority=2,notes="watch this now"}
example URL
```http
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/towatchlist/entries/
```
output:
![alt text](testing-screenshots/successfulAddToWatchlist.png)
if movieID, notes, or priority is not included an error is returned:
![alt text](testing-screenshots/insertCompletedMissingInfo.png)
if movieID is not a number:
![alt text](testing-screenshots/movieIdMustBeNumber.png)
if priority is not a number:
![alt text](testing-screenshots/priortyMustBeANumber.png)
if notes is not a string:
![alt text](testing-screenshots/priortyMustBeANumber.png)
#### Put watch new list entry with ID
requires API key
creates a new watch list entry with a given movieID that is passed in the URL
takes a body with form encoded data () priority, notes}. There is error checking to make sure the , the priority is a number, and that notes is a string.
if the entry at that movieID already exists it is overwritten, if it does not exist then a new one is inserted
example input body {priority=2,notes="watch this now"}
example URL
```http
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/towatchlist/entries/1
```
output if entry did not exist:
![alt text](testing-screenshots/toWatchListInserted.png)
output if entry did exist and was overwritten:
![alt text](testing-screenshots/toWatchListUpdated.png)
if the body form data is not included an error is sent:
![alt text](testing-screenshots/insertCompletedMissingInfo.png)
error if priority is not a number
![alt text](testing-screenshots/priorityMustBeNumber.png)


#### Patch priority for to watch list entry
requires API key. takes form encoded body {priority} as an integer.
URL like : 
```http
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/towatchlist/entries/5/priority
```
output:
![alt text](testing-screenshots/patchPriority.png)
if no priority is submit
![alt text](testing-screenshots/noPriority.png)
if priority is not a number
![alt text](testing-screenshots/priorityMustBeNumber.png)
if entry does not exist in watch list
![alt text](testing-screenshots/entryDoesNotExistInWatchList.png)

#### Delete watch list entry with ID
requires API key. takes form encoded body like {movieID}. 
URL like:
```http
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/towatchlist/entries/3/
```
output:
![alt text](testing-screenshots/movieRemoved.png)
if movieID does not exist in toWatchList:
![alt text](testing-screenshots/movieDoesNotExist.png)
if movieID is not a number
![alt text](testing-screenshots/movieIdMustBeNumber.png)

### Completed Watch List
#### Get completed watch list entries
return all entries for user in completed watch list. User ID is retrieved based on the API key that is passed
URL like:
```HTTP
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/completedwatchlist/entries
```
output:
![alt text](testing-screenshots/completedWatchListEntries.png)
if there are no entries for the user :
![alt text](testing-screenshots/noCompletedWatchListEntries.png)
##### optional filter on number of times watched
filter the completed watch list results on number of times watched where times watched is equal to or greater than the times_watched variable passed. 
url like 
```HTTP
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/completedwatchlist/entries?times_watched=2
```
output: 
![alt text](testing-screenshots/timesWatchedFilter.png)
error if times watched is not a number:
![alt text](testing-screenshots/timesWatchedNotANumber.png)
#### Get completed watch list rating by movieID
returns movieID and rating for completed wathchlist entry with the movieID passed in the URL
URL like: 
```http
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/completedwatchlist/entries/1/rating
```
![alt text](testing-screenshots/completedWatchListRating.png)
error if no entries:
![alt text](testing-screenshots/completedWatchListNoEntries.png)

#### Post completed watch list entry
inserts new entry in completed watch list table
requires API key.
This request also updates the rating of the movie in the movies table accounting for the number of times the person watched it for weighted effect.
URL like :
takes form-encoded body data:
{
    movieID,rating,notes,date_initially_watched,date_last_watched,times_watched
}
date values are formatted like YYYY-MM-DD
example URL is 
```http
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/completedwatchlist/entries
```
output:
![alt text](testing-screenshots/insertCompletedWatchlist.png)
if movieID does not exist in movies table
![alt text](testing-screenshots/movieNotInMoviesTable.png)
if movieID already exists in completed watch list table
![alt text](testing-screenshots/movieAlreadyExist.png)
 times watched must be a integer
![alt text](testing-screenshots/timesWatchedNotANumber.png)
movieID must be a integer
![alt text](testing-screenshots/movieIdMustBeNumber.png)
rating must be a integer
![alt text](testing-screenshots/ratingAsNumber.png)

rating must be between 1 and 10
![alt text](testing-screenshots/ratingBetween1and10.png)
date initially watched must be a date string
![alt text](testing-screenshots/dateInitiallyWatchedFormat.png)
date last watched must be a date string
![alt text](testing-screenshots/dateLastWatchedFormat.png)

#### Patch completed watch list rating
updates the rating for given movieID for user in completed watch list table. takes MovieID in URL and times-watched as form-encoded body. 
This request also updates the rating for the movie in the Movie table accounting for the number of times the person watched it for weighted average.

requires API key.
URL like :
```http
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/completedwatchlist/entries/1/rating
```
output:
![alt text](testing-screenshots/updateRating.png)
if no rating provided
![alt text](testing-screenshots/ratingRequired.png)
if rating is not an integer
![alt text](testing-screenshots/ratingAsNumber.png)
rating must be a positive integer between 1 and 10
![alt text](testing-screenshots/ratingBetween1and10.png)


#### Patch adding times watched to completed watch list entry
adds number of times a movie is watched by the user with movieId passed in the URL
API key is required
URL like:
```
https://loki.trentu.ca/~mackenzieneill/3430/assn/cois-3430-2024su-a2-BigBeill/api/completedwatchlist/entries/1/times-watched
```
returns:
![alt text](testing-screenshots/timesWatchedAdded.png)
error if invalid id:
![alt text](testing-screenshots/timesWatchedBadId.png)
if no times watched provided
![alt text](testing-screenshots/noTimesWatched.png)
if times watched is not an integer
![alt text](testing-screenshots/timesWatchedNotANumber.png)
times watched must be a positive integer
![alt text](testing-screenshots/positiveTimesWatched.png)
#### Delete deleting item from completed Watch List
deletes item from Completed Watch List table with movieId passed in the URL
API key is required
URL like:
```http
https://loki.trentu.ca/~mackenzieneill/3430/assn/cois-3430-2024su-a2-BigBeill/api/completedwatchlist/entries/1
```
returns:
![alt text](testing-screenshots/completedWatchListDeleteGood.png)
if incorrect id:
![alt text](testing-screenshots/completedWatchListDeleteBadId.png)
### Users
#### See User Stats
URL like 
```http
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/users/1/stats
``` 
returns:
1. date of first movies watched
2. first movies watched title
3. average movie rating
4. planned movie watch time
5. actual movie time watched
example:
![alt text](testing-screenshots/userStats.png)
if not enough information returns error:
![alt text](testing-screenshots/notEnoughInfoUser.png)
if accessing the user info of a user ID that does not match your API key's associated user ID then returns an error like this:
![alt text](testing-screenshots/misMatchUserCred.png)
### Incorrect Endpoint
![alt text](testing-screenshots/WrongEndpoint.png)
