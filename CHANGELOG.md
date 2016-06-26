# Change Log
All notable changes to this project will be documented in this file.

## [4.3.0] - ???

### Added:
 - User *register* method
### Fixed: 
 - Pins *edit* method returns bool value 
### Changed:
 - *login*, *isLoggedIn* and *logout* methods move to User provider
 - Bot *login*, *isLoggedIn* and *logout* methods are deprecated
 

## [4.2.2] - 2016-06-19
### Fixed:
 - keywords *recommendedFor* method returns an empty array if no results
 - Response *hasErrors* method returns true on errors
 - php version downgraded for 5.5.9
 - test changed for phpunit 4.0
 - Response *getBookmarks* method return empty array for no bookmarks

## [4.2.1] - 2016-06-14
### Fixed:
 - news *last* method login requirement check
 - pagination empty result check

## [4.2.0] - 2016-06-13
### Changed: 
 - functions with pagination accept a limit as a second argument, for example:
 ```php
 $bot->pins->search('cats', 2)
 ```
 will return only two pins for the search results. 
 The same is true about getting followers/following/pins for the user or board and getting pins from source.
  
 - *activity* method in pins provider (requires login).
	 

## [4.1.0] - 2016-06-12
### Added:
 - bot logout method

## [4.0.0] - 2016-06-12
### Changed:
 - generator objects now return an entity for each iteration, not an array of entities. For example, to 
 get search results there is no more need to make two nested loops:
 
 ```php
 foreach($bot->pins->search('cats') as $pin) {
 	echo $pin['id'], "\n";
 	// ...
 }
 
 The same is true about getting followers/following/pins for the user or board and getting pins from source. 

## [3.3.1] - 2016-06-12
### Added:
 - isLoggedIn method
 - Response and Request refactoring
 - Better exception messages in ProviderLoginCheckWrapper

## [3.2.3] - 2016-06-09
### Fixed:
 - Pins like/dislike
 - Response error check

## [3.2.2] - 2016-06-05
### Updated:
 - Keywords provider
 	- recommendedFor($query) method returns terms and their positions.

## [3.2.1] - 2016-06-05
### Updated:
 - Keywords provider
 	- recommendedFor($query) method returns items concatenated with the query request.

## [3.2.0] - 2016-06-04
### Added:
 - Keywords provider
 	- recommendedFor($query) method.

## [3.1.0] - 2016-06-04
### Added:
 - Change board (title, description, category and privacy)

## [3.0.0] - 2016-05-29
### Changed:
 - News *latest* method renamed to *last*
 - Request and providers refactoring
 
### Fixed:
 - Boards follow/unfollow request

## [2.6.0] - 2016-05-28
### Updated:
 - Pagination refactoring
 - Requests refactoring
 - version up due to the previous features

## [2.5.3] - 2016-05-28
### Added: 
- Edit profile image
- Get pins from specific source

## [2.5.2] - 2016-05-23
### Added: 
- Edit pin by id (change description, link and board)
- Move pin to a new board

## [2.5.1] - 2016-04-22
### Fixed:
- Cookie file now is placed in os PHP temporary directory

## [2.5.0] - 2016-03-12
### Added:
- Specify link when creating pin as third argument

## [2.4.2] - 2016-03-03
### Fixed:
- Login requirements check for some methods

## [2.4.1] - 2016-03-01
### Fixed:
- sendMessage, sendMail array bugs fix

## [2.4.0] - 2016-03-01
### Added:
1. Changes in sendMessage:
	- Many users at once
	- Add pin to message
2. Added sendMail method. Works like sendMessage, but pass email or array of emails instead of users ids.

## 2016-02-11
- Throws exception when login fails
- Tests refactoring

## 2016-02-06
- Providers refactoring (loginRequired check before every method call added)
- New providers: User for edit user settings (profile for example) and News for fetching user related news.
