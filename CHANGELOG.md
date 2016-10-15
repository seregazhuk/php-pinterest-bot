# Change Log
All notable changes to this project will be documented in this file.

## [4.13.1] - 2016-10-15
### Fixed:
 - Fail on empty server response 

## [4.13.0] - 2016-10-09
### Added:
 - User *resetPassword* and *sendPasswordResetLink* methods to reset password.
 - HttpClient *getCurrentUrl* method
 - HttpClient *setCookiesPath* method to change default store for cookie files.
 - User *invite* method to invite people by email.

## [4.12.7] - 2016-09-26
### Fixed:
 - Removed output cookie file path to console
 - Boards *info* method now returns only specified board

## [4.12.6] - 2016-09-26
### Fixed:
 - Creating cookie file if does not exist
 - User *register* default country is GB

### Changed:
 - *reload* param added to Bot *getClientContext* method.

## [4.12.5] - 2016-09-25
### Fixed:
 - Bot *getLastError* method.
 - Random cookie file name for non-authorized sessions.

## [4.12.4] - 2016-09-25
### Fixed:
 - Bot *getClientInfo* method with auto login.

## [4.12.3] - 2016-09-25
### Added:
- HttpClient *cookie* and *cookies* methods.
- Auto-login from a previously saved session.

### Fixed:
 - HttpClient *setOptions* does not override default Curl options, only adds additional ones.

### Removed:
 - HttpClient *setUserAgent* method, custom userAgent should be passed with 
 other Curl options via *setOptions* method.

## [4.11.2] - 2016-09-18
### Fixed:
 - Images upload

## [4.11.1] - 2016-09-18
### Added: 
 - Bot *getClientInfo* method.

## [4.11.0] - 2016-09-16
### Added:
 - User *changePassword*, *deactivate* methods.
 - AuthRequired exception.
 - Pins *getRelatedPins* method.
 - Pinners *likes* method.
 - News *all* method.
 - Boards *getTitleSuggestionsFor* method.
 
### Changed:
 - User *login* method does not throw an exception, now it returns *false* on fail.
 - Bot *getLastError* method returns string, not array.
 - News *last* method is deprecated.

## [4.10.1] - 2016-09-13
### Fixed:
 - Checks in *getUserName* and *isBanned* methods

## [4.10.0] - 2016-09-12
### Added
 - User *isBanned* and *getUserName* methods

## [4.9.0] - 2016-09-11
### Added:
 - Get user profile info

## [4.8.0] - 2016-09-11
### Added:
 - Get following boards/people/interests for a pinner

## [4.7.0] - 2016-09-10
### Added:
 - New Topics provider
### Changed:
 - Follow/unfollow methods moved from Interest to Topics provider
 - Added getRelatedTopics to Interests provider

## [4.6.1] - 2016-09-08
### Changed:
 - Provider::execGetRequest method now returns bool or array
 - Exceptions classes renamed
 - Helpers classes renamed
 - Removed HasFollowers trait
### Fixed:
 - Fixed *getUnFollowUrl()* method in Followable trait

## [4.6.0] - 2016-08-26
### Changed:
 - HttpClient cookie file name moved to property instead of constant
 - Custom curl options in HttpClient object
 - Removed deprecated methods (isLoggedIn, logout, login) from Bot class. Instead use User provider
 - Removed getRequest method from ProvidersContainer
 
## [4.5.4] - 2016-08-26
### Changed:
 - Http contract renamed to HttpClient
 - Curl specific methods moved to CurlHttpClient
 - Removed token logic from HttpClient, only parsing
 - Removed user agent logic from Request to HttpClient

### Fixed:
- Upload problems

## [4.5.3] - 2016-08-16
 - Fixed upload

## [4.5.2] - 2016-08-16

### Changed:
 - Renamed ProviderLoginCheckWrapper to ProviderWrapper
 - Refactored Response class
 - HttpInterface contract renamed to Http
 - HttpInterface contract renamed to HttpClient
 - PaginatedResponse contract added

## [4.5.1] - 2016-08-09

### Fixed:
 - Pins method *userFeed* required login

## [4.5.0] - 2016-08-09

### Added:
 - Interests: get main categories 
 - Interests: get category info
 - Pins: get user feed

## [4.4.2] - 2016-07-06

### Fixed: 
 - Pins *comment* method

## [4.4.1] - 2016-07-02

### Fixed:
 - Pins *create* method uploads images.

## [4.4.0] - 2016-07-02

### Added:
 - User *registerBusiness* for registration business accounts.
 - User *convertToBusiness* to convert simple account to a business one.

## [4.3.0] - 2016-06-27

### Added:
 - User *register* method
 
### Fixed: 
 - Pins *edit* method returns bool value
  
### Changed:
 - *login*, *isLoggedIn* and *logout* methods move to User provider
 - Bot *login*, *isLoggedIn* and *logout* methods are deprecated
 - removed *RequestInterface* and *ResponseInterface*
 

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
