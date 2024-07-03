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

##### filter movie title
example url https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/movies?name=pirate
name=pirate
output:
![alt text](testing-screenshots/pirateMovies.png)
##### filter movie rating
this should return json string of movies whos rating is above or equal to a certain number passed in the URL
example:
https://loki.trentu.ca/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/movies?rating=8
rating = 8
output:
![alt text](testing-screenshots/rating8.png)

#### get movies with ID
### To Watch List
### Completed Watch List
### Users
### Incorrect Endpoint
![alt text](testing-screenshots/WrongEndpoint.png)
